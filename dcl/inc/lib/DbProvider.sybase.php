<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2011 Free Software Foundation
 *
 * Double Choco Latte is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * Double Choco Latte is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * Select License Info from the Help menu to view the terms and conditions of this license.
 */

include_once(DCL_ROOT . 'inc/lib/AbstractDbProvider.php');

class DbProvider extends AbstractDbProvider
{
	public function __construct()
	{
		parent::__construct();
		$this->JoinKeyword = 'JOIN';
	}

	public function Connect($conn = '')
	{
		global $dcl_domain_info, $dcl_domain;

		if ($conn == '')
		{
			if (!defined('DCL_DB_CONN'))
			{
				$this->conn = @sybase_connect($dcl_domain_info[$dcl_domain]['dbHost'], 
						$dcl_domain_info[$dcl_domain]['dbUser'], 
						$dcl_domain_info[$dcl_domain]['dbPassword']);

				@sybase_select_db($dcl_domain_info[$dcl_domain]['dbName'], $this->conn);
				define('DCL_DB_CONN', $this->conn);
			}
			else
				$this->conn = DCL_DB_CONN;
		}
		else
			$this->conn = $conn;

		return $this->conn;
	}

	public function CanConnectServer()
	{
		global $dcl_domain_info, $dcl_domain;

		$conn = sybase_connect($dcl_domain_info[$dcl_domain]['dbHost'],
				$dcl_domain_info[$dcl_domain]['dbUser'],
				$dcl_domain_info[$dcl_domain]['dbPassword']);

		$bConnect = ($conn > 0);
		sybase_close($conn);

		return $bConnect;
	}

	public function CanConnectDatabase()
	{
		global $dcl_domain_info, $dcl_domain;

		$conn = sybase_connect($dcl_domain_info[$dcl_domain]['dbHost'],
				$dcl_domain_info[$dcl_domain]['dbUser'],
				$dcl_domain_info[$dcl_domain]['dbPassword']);

		if ($conn > 0)
		{
			$bRetVal = sybase_select_db($dcl_domain_info[$dcl_domain]['dbName'], $conn);
			sybase_close($conn);

			return $bRetVal;
		}

		return false;
	}

	public function CreateDatabase()
	{
		global $dcl_domain_info, $dcl_domain;

		$conn = sybase_connect($dcl_domain_info[$dcl_domain]['dbHost'],
				$dcl_domain_info[$dcl_domain]['dbUser'],
				$dcl_domain_info[$dcl_domain]['dbPassword']);

		$query = sprintf('Create Database %s', $dcl_domain_info[$dcl_domain]['dbName']);

		return (sybase_query($query, $conn) > 0);
	}

	public function TableExists($sTableName)
	{
		return ($this->ExecuteScalar("select count(*) from sysobjects where name='$sTableName' and type='u'") > 0);
	}

	public function Query($query)
	{
		$this->res = 0;
		$this->oid = 0;
		$this->cur = -1;
		$this->vcur = -1;
		$this->Record = array();

		if ($this->conn)
		{
			$this->res = sybase_query($query, $this->conn);
			if ($this->res)
			{
				$this->cur = 0;
				return $this->res;
			}
			else
			{
				trigger_error("Error executing query: $query");
				return -1;
			}
		}
		else
			return -1;
	}

	public function LimitQuery($query, $offset, $rows)
	{
		$this->res = 0;
		$this->oid = 0;
		$this->cur = -1;
		$this->vcur = -1;
		$this->Record = array();

		if ($this->conn)
		{
			@$this->res = sybase_query($query, $this->conn);
			if ($this->res)
			{
				$this->cur = $offset;
				// Push cursor to appropriate row in case next_record() is used
				if ($offset > 0)
					@sybase_data_seek($this->res, $offset);

				$this->vcur = $offset + $rows - 1;

				return $this->res;
			}
			else
			{
				trigger_error("Error executing query: $query");
				return -1;
			}
		}
		else
			return -1;
	}

	public function Execute($query)
	{
		if ($this->conn)
		{
			sybase_query($query, $this->conn) or trigger_error("Could not execute query: $query");
			return 1;
		}

		return -1;
	}

	// Execute row returning query and return first row, first field
	public function ExecuteScalar($sql)
	{
		$retVal = null;

		if (!$this->conn)
			$this->Connect();

		$res = sybase_query($sql, $this->conn);
		if ($res)
		{
			if (sybase_num_rows($res) > 0)
				$retVal = sybase_result($res, 0, 0);

			sybase_free_result($res);
		}

		return $retVal;
	}

	public function Insert($query)
	{
		$this->res = 0;
		$this->oid = 0;
		$this->cur = -1;
		$this->vcur = -1;
		$this->Record = array();

		if ($this->conn)
		{
			$this->res = sybase_query($query, $this->conn);
			if ($this->res)
			{
				$oidRes = sybase_query('SELECT @@identity', $this->conn);
				if ($oidRes)
					$this->oid = sybase_result($oidRes, 0, 0);
				else
					trigger_error('Could not retrieve @@identity of newly inserted record!!  Query: ' . $query);

				return $this->oid;
			}
			else
			{
				trigger_error("Error executing query: $query");
				return -1;
			}
		}
		else
		{
			trigger_error('No connection!');
			return -1;
		}
	}

	public function FreeResult()
	{
		$this->Record = array();
		if ($this->res != 0)
			@sybase_free_result($this->res);

		$this->res = 0;
	}

	public function BeginTransaction()
	{
		return $this->Execute('BEGIN TRANSACTION');
	}

	public function EndTransaction()
	{
		return $this->Execute('COMMIT');
	}

	public function RollbackTransaction()
	{
		return $this->Execute('ROLLBACK TRAN');
	}

	public function NumFields()
	{
		if ($this->res)
			return sybase_num_fields($this->res);
		else
			return -1;
	}

	// from phpGW/phpLib db classes - sort of
	public function next_record()
	{
		// bump up if just ran query
		if ($this->cur == -1)
			$this->cur = 0;

		if ($this->vcur == -1 || ($this->cur++ <= $this->vcur))
			$this->Record = @sybase_fetch_array($this->res);
		else
			$this->Record = NULL;

		//$this->Errno  = mysql_errno();
		//$this->Error  = mysql_error();

		$stat = is_array($this->Record);
		if (!$stat)
			$this->FreeResult();

		return $stat;
	}

	public function GetFieldName($fieldIndex)
	{
		if ($this->res)
		{
			$objInfo = sybase_fetch_field($this->res, $fieldIndex);
			return $objInfo->name;
		}

		return '';
	}

	public function FetchAllRows()
	{
		$retVal = array();
		$i = 0;
		// bump up if just ran query
		if ($this->cur == -1)
			$this->cur = 0;

		while ($a = @sybase_fetch_row($this->res))
		{
			$this->cur++;
			$retVal[$i++] = $a;
		}

		return $retVal;
	}

	public function GetNewIDSQLForTable($tableName)
	{
		return '';
	}

	public function GetDateSQL()
	{
		return 'GetDate()';
	}

	public function GetUpperSQL($text)
	{
		return $text;
	}

	public function GetLastInsertID($sTable)
	{
		$res = sybase_query('SELECT @@identity', $this->conn);
		if ($res)
		{
			$Record = @sybase_fetch_array($res);
			sybase_free_result($res);
			return $Record[0];
		}

		trigger_error('Could not retrieve @@identity of newly inserted record!!');
		return -1;
	}

	public function ConvertDate($sExpression, $sField)
	{
		return "str_replace(convert(varchar(10), $sExpression, 111), '/', '-') AS $sField";
	}

	public function ConvertTimestamp($sExpression, $sField)
	{
		return "str_replace(convert(varchar(10), $sExpression, 111), '/', '-') + ' ' + convert(varchar(8), $sExpression, 108) AS $sField";
	}

	public function IsDate($vField)
	{
		if (!$this->res)
			return false;

		$o = sybase_fetch_field($this->res, $vField);
		return ($o['type'] == 'datetime' && strlen($this->f($vField)) == 10);
	}

	public function IsTimestamp($vField)
	{
		if (!$this->res)
			return false;

		$o = sybase_fetch_field($this->res, $vField);
		return ($o['type'] == 'datetime' && strlen($this->f($vField)) > 10);
	}

	public function index_names()
	{
		global $dcl_domain, $dcl_domain_info;

		$this->query("SELECT name FROM sysobjects WHERE type = 'i' ORDER BY name");
		$i = 0;
		$return = array();
		while ($this->next_record())
		{
			$return[$i] = array();
			$return[$i]['index_name']		= $this->f(0);
			$return[$i]['tablespace_name']	= $dcl_domain_info[$dcl_domain]['dbName'];
			$return[$i++]['database']		= $dcl_domain_info[$dcl_domain]['dbName'];
		}

		return $return;
	}

	public function FieldExists($sTable, $sField)
	{
		return ($this->ExecuteScalar("select count(*) from syscolumns where id = object_id('$sTable') and name = '$sField'") == 1);
	}

	public function GetMinutesElapsedSQL($sBeginDateSQL, $sEndDateSQL, $sAsField)
	{
		$sRetVal = "datediff(mi, $sBeginDateSQL, $sEndDateSQL)";

		if ($sAsField == '')
			return $sRetVal;

		return "$sRetVal AS $sAsField";
	}
}
