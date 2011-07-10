<?php
/*
 * Original implementation by Urmet Janes.  Many thanks!
 *
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

/**
 * API - All Classes Relating to DCL API
 * @package api
 * @subpackage database
 * @copyright Copyright &copy; 1999-2004 Free Software Foundation
 * @version $Id$
 */
   
/**
 * Provides support for Microsoft SQL server
 * @package api
 * @subpackage database
 * @copyright Copyright (C) 1999-2004 Free Software Foundation
 * @version $Id$
 */
class DbProvider extends AbstractDbProvider 
{
	public function __construct()
	{		
		parent::__construct();
	}

	public function Connect($conn = '')
	{
		global $dcl_domain_info, $dcl_domain;

		if ($conn == '')
		{
			if (!defined('DCL_DB_CONN'))
			{
				ini_set('mssql.textsize', '2147483647');
				ini_set('mssql.textlimit', '2147483647');
				$this->conn = mssql_connect($dcl_domain_info[$dcl_domain]['dbHost'],
						$dcl_domain_info[$dcl_domain]['dbUser'],
						$dcl_domain_info[$dcl_domain]['dbPassword']);

				if (!mssql_select_db($dcl_domain_info[$dcl_domain]['dbName'], $this->conn))
				{
					// Couldn't select database - close connection and return 0
					mssql_close($this->conn);
					$this->conn = 0;
					return 0;
				}

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

		$conn = mssql_connect($dcl_domain_info[$dcl_domain]['dbHost'],
				$dcl_domain_info[$dcl_domain]['dbUser'],
				$dcl_domain_info[$dcl_domain]['dbPassword']);

		$bConnect = ($conn > 0);
		mssql_close($conn);

		return $bConnect;
	}

	public function CanConnectDatabase()
	{
		global $dcl_domain_info, $dcl_domain;

		$conn = mssql_connect($dcl_domain_info[$dcl_domain]['dbHost'],
				$dcl_domain_info[$dcl_domain]['dbUser'],
				$dcl_domain_info[$dcl_domain]['dbPassword']);

		if ($conn > 0)
		{
			$bRetVal = mssql_select_db($dcl_domain_info[$dcl_domain]['dbName'], $conn);
			mssql_close($conn);

			return $bRetVal;
		}

		return false;
	}

	public function CreateDatabase()
	{
		global $dcl_domain_info, $dcl_domain;

		$conn = mssql_connect($dcl_domain_info[$dcl_domain]['dbHost'],
				$dcl_domain_info[$dcl_domain]['dbUser'],
				$dcl_domain_info[$dcl_domain]['dbPassword']);

		$query = sprintf('Create Database %s', $dcl_domain_info[$dcl_domain]['dbName']);

		return (mssql_query($query, $conn) > 0);
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
			$this->res = mssql_query($query, $this->conn);
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
			@$this->res = mssql_query($query, $this->conn);
			if ($this->res)
			{
				$this->cur = $offset;
				// Push cursor to appropriate row in case next_record() is used
				if ($offset > 0)
					@mssql_data_seek($this->res, $offset);

				$this->vcur = $offset + $rows - 1;

				return $this->res;
			}
			else
			{
				trigger_error('Server Returned: [' . mssql_get_last_message() . '] for query: ' . $query);
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
			mssql_query($query, $this->conn);
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

		$res = mssql_query($sql, $this->conn);
		if ($res)
		{
			if (mssql_num_rows($res) > 0)
				$retVal = mssql_result($res, 0, 0);

			mssql_free_result($res);
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
			$this->res = mssql_query($query, $this->conn);
			$oidRes = mssql_query('SELECT @@identity', $this->conn);
			if ($this->res || $oidRes)
			{
				if ($oidRes)
					$this->oid = mssql_result($oidRes, 0, 0);
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
			@mssql_free_result($this->res);

		$this->res = 0;
	}

	public function BeginTransaction()
	{
		return $this->Execute('BEGIN TRAN');
	}

	public function EndTransaction()
	{
		return $this->Execute('COMMIT TRAN');
	}

	public function RollbackTransaction()
	{
		return $this->Execute('ROLLBACK TRAN');
	}

	public function NumFields()
	{
		if ($this->res)
			return mssql_num_fields($this->res);
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
			$this->Record = @mssql_fetch_array($this->res);
		else
			$this->Record = NULL;

		$stat = is_array($this->Record);
		if (!$stat)
			$this->FreeResult();

		return $stat;
	}

	public function GetFieldName($fieldIndex)
	{
		if ($this->res)
			return mssql_field_name($this->res, $fieldIndex);

		return '';
	}

	public function FetchAllRows()
	{
		$retVal = array();
		$i = 0;
		// bump up if just ran query
		if ($this->cur == -1)
			$this->cur = 0;

		while ($a = @mssql_fetch_row($this->res))
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

	public function GetLastInsertID($sTable)
	{
		$res = mssql_query('SELECT @@identity', $this->conn);
		if ($res)
		{
			$Record = @mssql_fetch_array($res);
			mssql_free_result($res);
			return $Record[0];
		}

		trigger_error('Could not retrieve @@identity of newly inserted record!!');
		return -1;
	}

	public function ConvertDate($sExpression, $sField)
	{
		return "Replace(convert(varchar(10), $sExpression, 111), '/', '-') AS $sField";
	}

	public function ConvertTimestamp($sExpression, $sField)
	{
		return "convert(varchar(20), $sExpression, 20) AS $sField";
	}

	public function IsDate($vField)
	{
		return ($this->res > 0 && mssql_field_type($this->res, $vField) == 'smalldatetime');
	}

	public function IsTimestamp($vField)
	{
		return ($this->res > 0 && mssql_field_type($this->res, $vField) == 'datetime');
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
