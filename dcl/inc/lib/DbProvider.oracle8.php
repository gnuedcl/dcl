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
		$this->EscapeQuoteChar = "\\";
	}

	public function ReportError($res = null, $retVal = -1)
	{
		$a = OCIError($res);
		if ($a)
			printf('OCI Error %d: %s', $a['code'], $a['message']);

		return $retVal;
	}

	public function Connect($conn = '')
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

	public function Query($query)
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
				LogError("Error parsing query: $query", __FILE__, __LINE__, debug_backtrace());
				return $this->ReportError($this->conn);
			}

			if (OCIExecute($this->res))
			{
				$this->cur = 0;
				return $this->res;
			}
			else
			{
				LogError("Error executing query: $query", __FILE__, __LINE__, debug_backtrace());
				return $this->ReportError($this->res);
			}
		}
		else
			return -1;
	}

	public function LimitQuery($query, $offset, $rows)
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
				LogError("Error parsing query: $query", __FILE__, __LINE__, debug_backtrace());
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
				LogError("Error executing query: $query", __FILE__, __LINE__, debug_backtrace());
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
			if (!($res = OCIParse($this->conn, $query)))
			{
				LogError("Error parsing query: $query", __FILE__, __LINE__, debug_backtrace());
				return $this->ReportError($this->conn);
			}

			if (OCIExecute($res))
			{
				return 1;
			}
			else
			{
				LogError("Error executing query: $query", __FILE__, __LINE__, debug_backtrace());
				return $this->ReportError($res);
			}
		}
		
		return -1;
	}

	public function ExecuteScalar($sql)
	{
		$retVal = null;

		if (!$this->conn)
			$this->Connect();

		if (!($res = OCIParse($this->conn, $sql)))
		{
			LogError("Error parsing query: $sql", __FILE__, __LINE__, debug_backtrace());
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

	public function Insert($query)
	{
		return $this->Query($query);
	}

	public function FreeResult()
	{
		$this->Record = array();
		if ($this->res != 0)
			@OCIFreeStatement($this->res);

		$this->res = 0;
	}

	public function BeginTransaction()
	{
		return $this->Execute('BEGIN');
	}

	public function EndTransaction()
	{
		return $this->Execute('COMMIT');
	}

	public function RollbackTransaction()
	{
		return $this->Execute('ROLLBACK');
	}

	public function NumFields()
	{
		if ($this->res)
			return OCINumCols($this->res);
		else
			return -1;
	}

	public function next_record()
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
				$this->Record[mb_strtolower($k)] = $v;
			}
		}
		else
			$this->FreeResult();

		return $stat;
	}

	public function GetFieldName($fieldIndex)
	{
		if ($this->res)
			return mb_strtolower(OCIColumnName($this->res, $fieldIndex));

		return '';
	}

	public function IsFieldNull($thisField)
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

	public function FetchAllRows()
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

	public function GetNewIDSQLForTable($tableName)
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

	public function GetDateSQL()
	{
		return 'sysdate';
	}

	public function DisplayToSQL($thisDate)
	{
		global $dcl_info;

		$regexStr = str_replace('m', '([0-9]{2})', $dcl_info['DCL_DATE_FORMAT']);
		$regexStr = str_replace('d', '([0-9]{2})', $regexStr);
		$regexStr = str_replace('Y', '([0-9]{4})', $regexStr);
		if(preg_match('#^' . $regexStr . ' ([0-9]{2}).([0-9]{2}).([0-9]{2})$#', $thisDate))
			return "to_date('" . $this->ArrangeTimeStampForInsert($thisDate) . "', 'YYYY-MM-DD 24HH:MI:SS')";

		return "to_date('" . $this->ArrangeDateForInsert($thisDate) . "', 'YYYY-MM-DD')";
	}

	public function GetUpperSQL($text)
	{
		return sprintf('upper(%s)', $text);
	}

	public function GetLastInsertID($sTable)
	{
		if (!($res = OCIParse($this->conn, "select currval(seq_$sTable)")))
		{
			LogError("Error parsing insert ID query!", __FILE__, __LINE__, debug_backtrace());
			return $this->ReportError($this->conn);
		}

		if (OCIExecute($res))
		{
			@OCIFetchInto($res, $Record, OCI_NUM | OCI_ASSOC | OCI_RETURN_NULLS);
			@OCIFreeStatement($res);
			return $Record[0];
		}

		LogError("Error executing insert ID query!", __FILE__, __LINE__, debug_backtrace());
		return $this->ReportError($res);
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
		// FIXME
		return ($this->res > 0 && pg_fieldtype($this->res, $vField) == 'date');
	}

	public function IsTimestamp($vField)
	{
		// FIXME
		// mb_substr because it could be timestamp or timestamptz
		return ($this->res > 0 && mb_substr(pg_fieldtype($this->res, $vField), 0, 9) == 'timestamp');
	}

	public function index_names()
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

	public function FieldExists($sTable, $sField)
	{
		// FIXME
		return ($this->ExecuteScalar("select count(*) from pg_attribute a join pg_class b on a.attrelid = b.oid where b.relname = '$sTable' and a.attname = '$sField'") == 1);
	}

	public function GetMinutesElapsedSQL($sBeginDateSQL, $sEndDateSQL, $sAsField)
	{
		$sRetVal = "round(to_number($sEndDateSQL - $sBeginDateSQL) * 1440)";

		if ($sAsField == '')
			return $sRetVal;

		return "$sRetVal AS $sAsField";
	}
}
