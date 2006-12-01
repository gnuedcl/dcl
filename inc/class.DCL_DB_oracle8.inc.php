<?php
/*
 * $Id$
 *
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2004 Free Software Foundation
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

include_once(DCL_ROOT . 'inc/class.DCL_DB_Core.inc.php');

class dclDB extends DCL_DB_Core
{
	function dclDB()
	{
		parent::DCL_DB_Core();
		$this->JoinKeyword = 'JOIN';
		$this->EscapeQuoteChar = "\\";
	}

	function ReportError($res = null, $retVal = -1)
	{
		$a = OCIError($res);
		if ($a)
			printf('OCI Error %d: %s', $a['code'], $a['message']);

		return $retVal;
	}

	function Connect($conn = '')
	{
		global $dcl_domain_info, $dcl_domain;

		if ($conn == "")
		{
			if (!defined('DCL_DB_CONN'))
			{
				$this->conn = OCILogon($dcl_domain_info[$dcl_domain]['dbUser'], $dcl_domain_info[$dcl_domain]['dbPassword'], $dcl_domain_info[$dcl_domain]['dbName']);
				if ($this->conn)
				{
					$this->Execute("alter session set NLS_DATE_FORMAT='YYYY-MM-DD HH24:MI:SS'");
					define('DCL_DB_CONN', $this->conn);
				}
			}
			else
				$this->conn = DCL_DB_CONN;

			if (!$this->conn)
				return $this->ReportError(null, 0);

			return $this->conn;
		}
	}

	function Query($query)
	{
		$this->FreeResult();
		$this->oid = 0;
		$this->cur = -1;
		$this->vcur = -1;
		$this->Record = array();

		if ($this->conn)
		{
			if (!($this->res = OCIParse($this->conn, $query)))
			{
				trigger_error("Error parsing query: $query");
				return $this->ReportError($this->conn);
			}

			if (OCIExecute($this->res))
			{
				$this->cur = 0;
				return $this->res;
			}
			else
			{
				trigger_error("Error executing query: $query");
				return $this->ReportError($this->res);
			}
		}
		else
			return -1;
	}

	function LimitQuery($query, $offset, $rows)
	{
		$this->FreeResult();
		$this->oid = 0;
		$this->cur = -1;
		$this->vcur = -1;
		$this->Record = array();

		if ($this->conn)
		{
			if (!($this->res = OCIParse($this->conn, $query)))
			{
				trigger_error("Error parsing query: $query");
				return $this->ReportError($this->conn);
			}

			if (OCIExecute($this->res))
			{
				$this->cur = 0;
				// Push cursor to appropriate row in case next_record() is used
				while ($this->cur < $offset)
				{
					OCIFetch($this->res);
					$this->cur++;
				}

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

	function Execute($query)
	{
		return $this->Query($query);
	}

	function ExecuteScalar($sql)
	{
		$retVal = null;

		if (!$this->conn)
			$this->Connect();

		if (!($res = OCIParse($this->conn, $sql)))
		{
			trigger_error("Error parsing query: $sql");
			return $this->ReportError($this->conn);
		}

		if (OCIExecute($res))
		{
			@OCIFetchInto($res, $Record, OCI_NUM | OCI_RETURN_NULLS);

			$stat = is_array($Record);
			if ($stat)
				$retVal = $Record[0];

			OCIFreeStatement($res);
		}

		return $retVal;
	}

	function Insert($query)
	{
		return $this->Query($query);
	}

	function FreeResult()
	{
		$this->Record = array();
		if ($this->res != 0)
			@OCIFreeStatement($this->res);

		$this->res = 0;
	}

	function BeginTransaction()
	{
		return $this->Execute('BEGIN');
	}

	function EndTransaction()
	{
		return $this->Execute('COMMIT');
	}

	function RollbackTransaction()
	{
		return $this->Execute('ROLLBACK');
	}

	function NumFields()
	{
		if ($this->res)
			return OCINumCols($this->res);
		else
			return -1;
	}

	function next_record()
	{
		// bump up if just ran query
		if ($this->cur == -1)
			$this->cur = 0;

		@OCIFetchInto($this->res, $this->Record, OCI_NUM | OCI_ASSOC | OCI_RETURN_NULLS);

		$stat = is_array($this->Record);
		if ($stat)
		{
			// need to lowercase all field names - reset upper case name, set as lower case
			for ($i = 0; $i < OCINumCols($this->res); $i++)
			{
				$k = OCIColumnName($this->res, $i + 1);
				$v = $this->Record[$i];
				unset($this->Record[$k]);
				$this->Record[strtolower($k)] = $v;
			}
		}
		else
			$this->FreeResult();

		return $stat;
	}

	function GetFieldName($fieldIndex)
	{
		if ($this->res)
			return strtolower(OCIColumnName($this->res, $fieldIndex));

		return '';
	}

	function IsFieldNull($thisField)
	{
		if ($this->res)
		{
			if (count($this->Record) > 0)
				return $this->Record[$thisField] == NULL;

			return OCIColumnIsNULL($this->res, $this->cur, $thisField);
		}
		else
			return -1;
	}

	function FetchAllRows()
	{
		$retVal = array();
		$i = 0;
		// bump up if just ran query
		if ($this->cur == -1)
			$this->cur = 0;

		while (@OCIFetchInto($this->res, $a, OCI_NUM | OCI_RETURN_NULLS))
			$retVal[$i++] = $a;

		return $retVal;
	}

	function GetNewIDSQLForTable($tableName)
	{
		$seqName = '';
		switch($tableName)
		{
			case 'severities':
				$seqName = 'seq_severity';
			case 'workorders':
				$seqName = 'seq_jcn';
			case 'dcl_projects':
				$seqName = 'seq_projects';
			default:
				$seqName = "seq_$tableName";
		}

		if ($this->Query('select ' . $seqName . '.nextval from dual') != -1)
		{
			if ($this->next_record())
			{
				// Save the ID here in case we need it later since
				// there's no good way to get this ID later for this
				// particular connection
				$this->oid = $this->f(0);
				return $this->f(0);
			}
		}

		return '';
	}

	function GetDateSQL()
	{
		return 'sysdate';
	}

	function DisplayToSQL($thisDate)
	{
		global $dcl_info;

		$eregStr = str_replace('m', '([0-9]{2})', $dcl_info['DCL_DATE_FORMAT']);
		$eregStr = str_replace('d', '([0-9]{2})', $eregStr);
		$eregStr = str_replace('Y', '([0-9]{4})', $eregStr);
		if(ereg('^' . $eregStr . ' ([0-9]{2}).([0-9]{2}).([0-9]{2})$', $thisDate))
			return "to_date('" . $this->ArrangeTimeStampForInsert($thisDate) . "', 'YYYY-MM-DD 24HH:MI:SS')";
		else
			return "to_date('" . $this->ArrangeDateForInsert($thisDate) . "', 'YYYY-MM-DD')";
	}

	function GetUpperSQL($text)
	{
		return sprintf('upper(%s)', $text);
	}

	function GetLastInsertID($sTable)
	{
		if (!($res = OCIParse($this->conn, "select currval(seq_$sTable)")))
		{
			trigger_error("Error parsing insert ID query!");
			return $this->ReportError($this->conn);
		}

		if (OCIExecute($res))
		{
			@OCIFetchInto($res, $Record, OCI_NUM | OCI_ASSOC | OCI_RETURN_NULLS);
			@OCIFreeStatement($res);
			return $Record[0];
		}

		trigger_error("Error executing insert ID query!");
		return $this->ReportError($res);
	}

	function ConvertDate($sExpression, $sField)
	{
		if ($sExpression == $sField)
			return $sField;

		return "$sExpression AS $sField";
	}

	function ConvertTimestamp($sExpression, $sField)
	{
		if ($sExpression == $sField)
			return $sField;

		return "$sExpression AS $sField";
	}

	function IsDate($vField)
	{
		// FIXME
		return ($this->res > 0 && pg_fieldtype($this->res, $vField) == 'date');
	}

	function IsTimestamp($vField)
	{
		// FIXME
		// substr because it could be timestamp or timestamptz
		return ($this->res > 0 && substr(pg_fieldtype($this->res, $vField), 0, 9) == 'timestamp');
	}

	function index_names()
	{
		// FIXME
		global $dcl_domain, $dcl_domain_info;

		$this->query("SELECT relname FROM pg_class WHERE NOT relname ~ 'pg_.*' AND relkind ='i' ORDER BY relname");
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

	function FieldExists($sTable, $sField)
	{
		// FIXME
		return ($this->ExecuteScalar("select count(*) from pg_attribute a join pg_class b on a.attrelid = b.oid where b.relname = '$sTable' and a.attname = '$sField'") == 1);
	}

	function GetMinutesElapsedSQL($sBeginDateSQL, $sEndDateSQL, $sAsField)
	{
		$sRetVal = "round(to_number($sEndDateSQL - $sBeginDateSQL) * 1440)";

		if ($sAsField == '')
			return $sRetVal;

		return "$sRetVal AS $sAsField";
	}
}
?>