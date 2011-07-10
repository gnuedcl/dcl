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
		$this->emptyTimestamp = "''";
		$this->EscapeQuoteChar = "\\";
	}

	public function mysql_die($error = '')
	{
		if (empty($error))
			trigger_error(mysql_error());
		else
			trigger_error($error);
	}

	public function Connect($conn = '')
	{
		global $dcl_domain_info, $dcl_domain;

		if ($conn == '')
		{
			if (!defined('DCL_DB_CONN'))
			{
				$connString = $dcl_domain_info[$dcl_domain]['dbHost'];
				if ($dcl_domain_info[$dcl_domain]['dbPort'] != '')
					$connString .= ':' . $dcl_domain_info[$dcl_domain]['dbPort'];
				$this->conn = mysql_connect($connString, $dcl_domain_info[$dcl_domain]['dbUser'],
						$dcl_domain_info[$dcl_domain]['dbPassword']) or $this->mysql_die();

				define('DCL_DB_CONN', $this->conn);
			}
			else
				$this->conn = DCL_DB_CONN;
		}
		else
			$this->conn = $conn;

		mysql_select_db($dcl_domain_info[$dcl_domain]['dbName'], $this->conn) or $this->mysql_die();

		return $this->conn;
	}

	public function CanConnectServer()
	{
		global $dcl_domain_info, $dcl_domain;

		$connString = $dcl_domain_info[$dcl_domain]['dbHost'];
		if ($dcl_domain_info[$dcl_domain]['dbPort'] != '')
			$connString .= ':' . $dcl_domain_info[$dcl_domain]['dbPort'];

		$conn = mysql_connect($connString, $dcl_domain_info[$dcl_domain]['dbUser'],
				$dcl_domain_info[$dcl_domain]['dbPassword'], true);

		$bConnect = false;
		if ($conn !== false)
		{
			$bConnect = ($conn > 0);
			mysql_close($conn);
		}

		return $bConnect;
	}

	public function CanConnectDatabase()
	{
		global $dcl_domain_info, $dcl_domain;

		$connString = $dcl_domain_info[$dcl_domain]['dbHost'];
		if ($dcl_domain_info[$dcl_domain]['dbPort'] != '')
			$connString .= ':' . $dcl_domain_info[$dcl_domain]['dbPort'];

		$conn = mysql_connect($connString, $dcl_domain_info[$dcl_domain]['dbUser'],
				$dcl_domain_info[$dcl_domain]['dbPassword'], true);

		if ($conn !== false && $conn > 0)
		{
			$bRetVal = mysql_select_db($dcl_domain_info[$dcl_domain]['dbName'], $conn);
			mysql_close($conn);

			return $bRetVal;
		}

		return false;
	}

	public function CreateDatabase()
	{
		global $dcl_domain_info, $dcl_domain;

		$connString = $dcl_domain_info[$dcl_domain]['dbHost'];
		if ($dcl_domain_info[$dcl_domain]['dbPort'] != '')
			$connString .= ':' . $dcl_domain_info[$dcl_domain]['dbPort'];

		$conn = mysql_connect($connString, $dcl_domain_info[$dcl_domain]['dbUser'],
				$dcl_domain_info[$dcl_domain]['dbPassword']);

		$query = sprintf('Create Database %s', $dcl_domain_info[$dcl_domain]['dbName']);

		return (mysql_query($query, $conn) > 0);
	}

	public function TableExists($sTableName)
	{
		$oDB = new DbProvider;
		$oDB->Connect();
		$oDB->Query("show tables");
		while ($oDB->next_record())
		{
			if ($oDB->f(0) == $sTableName)
				return true;
		}

		return false;
	}

	public function Query($query)
	{
		$this->res = 0;
		$this->oid = 0;
		$this->cur = -1;
		$this->Record = array();

		if ($this->conn)
		{
			$this->res = mysql_query($query, $this->conn) or $this->mysql_die();
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
		$this->Record = array();

		if ($this->conn)
		{
			$this->res = mysql_query($query . ' LIMIT ' . $offset . ',' . $rows, $this->conn) or $this->mysql_die();
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

	public function Execute($query)
	{
		if ($this->conn)
		{
			mysql_query($query, $this->conn) or $this->mysql_die();
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

		$res = mysql_query($sql, $this->conn) or $this->mysql_die();
		if ($res)
		{
			if (mysql_num_rows($res) > 0)
				$retVal = mysql_result($res, 0, 0);

			mysql_free_result($res);
		}

		return $retVal;
	}

	public function Insert($query)
	{
		$this->res = 0;
		$this->oid = 0;
		$this->cur = -1;
		$this->Record = array();

		if ($this->conn)
		{
			$this->res = mysql_query($query, $this->conn);
			if ($this->res)
				return $this->oid = mysql_insert_id($this->conn);
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
			mysql_free_result($this->res);

		$this->res = 0;
	}

	public function BeginTransaction()
	{
        // Might as well be sending this to /dev/null
		//return $this->Execute("BEGIN");
		return 1;
	}

	public function EndTransaction()
	{
        // Might as well be sending this to /dev/null
		//return $this->Execute("COMMIT");
		return 1;
	}

	public function RollbackTransaction()
	{
        // Might as well be sending this to /dev/null
		//return $this->Execute("ROLLBACK");
		return 1;
	}

	public function NumFields()
	{
		if ($this->res)
			return mysql_num_fields($this->res);
		else
			return -1;
	}

	// from phpGW/phpLib db classes - sort of
	public function next_record()
	{
		// bump up if just ran query
		if ($this->cur == -1)
			$this->cur = 0;

		if ($this->res)
			$this->Record = mysql_fetch_array($this->res);
		else
			return false;

		$this->cur++;
		//$this->Errno  = mysql_errno();
		//$this->Error  = mysql_error();


		$stat = is_array($this->Record);
		if (!$stat)
			$this->FreeResult();

		return $stat;
	}

	public function GetFieldName($fieldIndex)
	{
		// Seems the official call is mysql_field_name, but mysql_fieldname is for backward compatability
		if ($this->res)
			return mysql_fieldname($this->res, $fieldIndex);

		return '';
	}

	public function FetchAllRows()
	{
		$retVal = array();
		$i = 0;
		// bump up if just ran query
		if ($this->cur == -1)
			$this->cur = 0;

		if ($this->res)
		{
			while ($a = mysql_fetch_row($this->res))
			{
				$this->cur++;
				$retVal[$i++] = $a;
			}
		}

		return $retVal;
	}

	public function GetNewIDSQLForTable($tableName)
	{
		return '';
	}

	public function GetDateSQL()
	{
		return 'now()';
	}

	public function GetLastInsertID($sTable)
	{
		return mysql_insert_id($this->conn);
	}

	public function ConvertDate($sExpression, $sField)
	{
		if ($sExpression == $sField)
			return $sField;

		return "$sExpression AS $sField";
	}

	public function ConvertTimestamp($sExpression, $sField)
	{
		if ($sExpression == $sField)
			return $sField;

		return "$sExpression AS $sField";
	}

	public function IsDate($vField)
	{
		return ($this->res > 0 && mysql_field_type($this->res, $vField) == 'date');
	}

	public function IsTimestamp($vField)
	{
		$sFieldType = mysql_field_type($this->res, $vField);
		return ($this->res > 0 && ($sFieldType == 'timestamp' || $sFieldType == 'datetime'));
	}

	public function FieldExists($sTable, $sField)
	{
		$oDB = new DbProvider;
		$oDB->Connect();
		$oDB->Query("describe $sTable");
		while ($oDB->next_record())
		{
			if ($oDB->f(0) == $sField)
				return true;
		}

		return false;
	}

	public function GetMinutesElapsedSQL($sBeginDateSQL, $sEndDateSQL, $sAsField)
	{
		$sRetVal = "(unix_timestamp($sEndDateSQL) - unix_timestamp($sBeginDateSQL)) / 60";

		if ($sAsField == '')
			return $sRetVal;

		return "$sRetVal AS $sAsField";
	}
}
