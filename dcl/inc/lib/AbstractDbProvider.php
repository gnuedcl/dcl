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

include_once(DCL_ROOT . 'inc/helpers/DateTimeHelper.php');

/**
 * API - All Classes Relating to DCL API
 * @package api
 * @subpackage database
 * @copyright Copyright &copy; 1999-2004 Free Software Foundation
 */

/**
 * Provides common methods for database access.  Cannot be used directly, must instantiate {@link DbProvider}
 * @package dcl
 * @subpackage api
 * @copyright Copyright (C) 1999-2004 Free Software Foundation
 */
abstract class AbstractDbProvider
{
	/**
	 * Connection ID
	 * @var integer
	 */
	var $conn;
	/**
	 * Current result set ID
	 * @var integer
	 */
	var $res;
	/**
	 * Current object ID as a result of an INSERT query
	 * @var integer
	 */
	var $oid;
	/**
	 * Current row pointer in result set
	 * @var integer
	 */
	var $cur;
	/**
	 * Reverse lookup for foreign keys.  Shows what tables this table has links to.
	 * @var array
	 */
	var $foreignKeys;
	/**
	 * Cache for lookup items to reduce SQL queries
	 * @var array
	 */
	var $cache;
	/**
	 * Set to true to enable lookup caching
	 * @var boolean
	 */
	var $cacheEnabled;
	/**
	 * Instance of a {@link DCLDate} object
	 */
	var $objDate;
	/**
	 * Instance of a {@link DCLTimestamp} object
	 */
	var $objTimestamp;
	/**
	 * The current record of the result set.  This is an associative array with both field name and ordinal keys.
	 * @var array
	 */
	var $Record;
	/**
	 * The virtual cursor position for partial result sets
	 * @var integer
	 */
	var $vcur;
	/**
	 * Native SQL representation of an empty timestamp
	 * @var string
	 */
	var $emptyTimestamp;
	/**
	 * The keyword(s) used for performing an INNER JOIN
	 * @var string
	 */
	var $JoinKeyword;
    /**
   	 * The keyword(s) used for performing a case-insensitive LIKE
   	 * @var string
   	 */
   	var $LikeKeyword;
	/**
	 * The character used to escape single quotes in SQL queries
	 * @var string
	 */
	var $EscapeQuoteChar;
	/**
	 * The name of the table this class provides an interface for
	 * @var string
	 */
	var $TableName;
	/**
	 * Auditing of records
	 * @var boolean
	 */
	var $AuditEnabled;
	/**
	 * Subqueries supported?
	 */
	var $SubqueriesSupported;

	/**
	 * Constructor.  Initializes all members and calls Connect() in derived class
	 */
	public function __construct()
	{
		$this->objDate = new DateHelper;
		$this->objTimestamp = new TimestampHelper;
		$this->conn = 0;
		$this->res = 0;
		$this->oid = 0;
		$this->cur = -1;
		$this->vcur = -1;
		$this->Record = array();
		$this->foreignKeys = array();

		$this->JoinKeyword = 'INNER JOIN';
        $this->LikeKeyword = 'LIKE';
		$this->emptyTimestamp = 'null';
		$this->EscapeQuoteChar = "'";
		$this->TableName = '';
		$this->cacheEnabled = false;

		$this->AuditEnabled = false;
		$this->SubqueriesSupported = true;

		$this->Connect();
	}

	/**
	 * Retrieve a field value from the current record
	 * @param string|integer
	 */
	public function f($sName)
	{
		return $this->Record[$sName];
	}

	/**
	 * Tests if a field value is NULL
	 * @param string|integer The field to test for the presence of NULL
	 * @return boolean|integer -1 if no result set, true if the field is NULL or empty string, otherwise false
	 */
	public function IsFieldNull($thisField)
	{
		if ($this->res)
		{
			if (count($this->Record) > 0)
				return $this->Record[$thisField] == NULL;

			return ($this->GetField($thisField) == '');
		}
		else
			return -1;
	}

	/**
	 * Initializes field member variables from $_POST super global array.  Used for copying form contents into class.
	 */
	public function InitFrom_POST()
	{
		foreach ($GLOBALS['phpgw_baseline'][$this->TableName]['fd'] as $sFieldName => $aFieldInfo)
		{
			if (IsSet($_POST[$sFieldName]))
				$this->$sFieldName = $this->GPCStripSlashes($_POST[$sFieldName]);
		}
	}

	/**
	 * Initializes field member varialbes from provided array.  Used for copying array contents into class.
	 * @param array The source array to copy values from.  Keys must match field member variable names.
	 */
	public function InitFromArray($aSource)
	{
		foreach ($GLOBALS['phpgw_baseline'][$this->TableName]['fd'] as $sFieldName => $aFieldInfo)
		{
			if (IsSet($aSource[$sFieldName]))
				$this->$sFieldName = $aSource[$sFieldName];
		}
	}

	/**
	 * Escape a string to make it safe for SQL DML
	 * @param string The string to escape
	 * @return string The escaped string
	 * @see db_addslashes()
	 */
	public function DBAddSlashes($thisString)
	{
		if (!IsSet($thisString) || $thisString == '')
			return '';

		return str_replace("'", $this->EscapeQuoteChar . "'", $thisString);
	}

	/**
	 * Unescapes extra slashes from single-quotes if magic quotes gpc is enabled
	 * @param string The string to unescape
	 * @return string The unescaped string
	 */
	public function GPCStripSlashes($thisString)
	{
		if (get_magic_quotes_gpc() == 0)
			return $thisString;

		return stripslashes($thisString);
	}

	/**
	 * Cache the current field member values
	 * @param string The key to store this record under
	 * @return integer -1 if caching disabled or the key is already set, otherwise 0
	 * @see LoadCache()
	 */
	public function CacheRow($key)
	{
		if (!$this->cacheEnabled || IsSet($this->cache[$key]))
			return -1;

		foreach ($GLOBALS['phpgw_baseline'][$this->TableName]['fd'] as $field => $fieldDef)
		{
			if (IsSet($this->$field))
				$this->cache[$key][$field] = $this->$field;
		}

		return 0;
	}

	/**
	 * Load a record from the cache
	 * @param string The key to retrieve from the cache
	 * @return integer -1 if caching disabled or the key is not set, otherwise 0
	 * @see CacheRow()
	 */
	public function LoadCache($key)
	{
		if (!$this->cacheEnabled || !IsSet($this->cache[$key]))
			return -1;

		foreach ($GLOBALS['phpgw_baseline'][$this->TableName]['fd'] as $field => $fieldDef)
		{
			if (IsSet($this->cache[$key][$field]))
				$this->$field = $this->cache[$key][$field];
		}

		return 0;
	}

	/**
	 * Convert the preferred display date/timestamp format into SQL format
	 * @param string The date or timestamp to format
	 * @return string Reformatted and quoted date/timestamp string suitable for SQL
	 * @see ArrangeTimeStampForInsert()
	 * @see ArrangeDateForInsert()
	 */
	public function DisplayToSQL($thisDate)
	{
		global $dcl_info;

		$regexStr = str_replace('m', '([0-9]{2})', $dcl_info['DCL_DATE_FORMAT']);
		$regexStr = str_replace('d', '([0-9]{2})', $regexStr);
		$regexStr = str_replace('Y', '([0-9]{4})', $regexStr);
		if(preg_match('#^' . $regexStr . ' ([0-9]{2}).([0-9]{2}).([0-9]{2})\.{0,1}[0-9]*$#', $thisDate))
			return "'" . $this->ArrangeTimeStampForInsert($thisDate) . "'";
		
		return "'" . $this->ArrangeDateForInsert($thisDate) . "'";
	}

	/**
	 * Convert the preferred display date format into SQL format
	 * @param string The date to format
	 * @return string Reformatted date string suitable for SQL
	 * @see ArrangeTimeStampForInsert()
	 * @see DisplayToSQL()
	 */
	public function ArrangeDateForInsert($thisDate)
	{
		$this->objDate->SetFromDisplay($thisDate);
		return $this->objDate->ToDB();
	}

	/**
	 * Convert the preferred display timestamp format into SQL format
	 * @param string The timestamp to format
	 * @return string Reformatted timestamp string suitable for SQL
	 * @see DisplayToSQL()
	 * @see ArrangeDateForInsert()
	 */
	public function ArrangeTimeStampForInsert($thisStamp)
	{
		$this->objTimestamp->SetFromDisplay($thisStamp);
		return $this->objTimestamp->ToDB();
	}

	/**
	 * Convert the SQL date format into preferred display format
	 * @param string The date to format
	 * @return string Reformatted date string according to user preference or empty string if not valid
	 */
	public function FormatDateForDisplay($thisDate)
	{
		if ($thisDate != '' || substr($thisDate, 0, 4) == '0000')
		{
			$this->objDate->SetFromDB($thisDate);
			return $this->objDate->ToDisplay();
		}

		return '';
	}

	/**
	 * Convert the SQL date format into preferred display format
	 * @param string The date to format
	 * @return string Reformatted date string according to user preference or empty string if not valid
	 */
	public function FormatDateForQuarterDisplay($thisDate)
	{
		if ($thisDate != '' || substr($thisDate, 0, 4) == '0000')
		{
			$this->objDate->SetFromDB($thisDate);
			return 'Q' . ceil(date('n', $this->objDate->time) / 3) . ' ' . date('Y', $this->objDate->time);
		}

		return '';
	}

	/**
	 * Convert the SQL timestamp format into preferred display format
	 * @param string The timestamp to format
	 * @return string Reformatted timestamp string according to user preference or empty string if not valid
	 */
	public function FormatTimeStampForDisplay($thisStamp)
	{
		if ($thisStamp != '' && substr($thisStamp, 0, 4) != '0000')
		{
			$this->objTimestamp->SetFromDB($thisStamp);
			return $this->objTimestamp->ToDisplay();
		}

		return '';
	}

	/**
	 * Returns a SQL fragment to upper case a string for case-insensitive compare
	 * @param string The text or field to upper case.  Literals should be enclosed in '' and escaped with {@link DBAddSlashes()}
	 * @return string The necessary SQL to convert the text/field to upper case
	 */
	public function GetUpperSQL($text)
	{
		return $text;
	}

	/**
	 * Return a SQL fragment to right trim a field or text
	 * @param string The expression to right trim.  Literals should be enclosed in '' and escaped with {@link DBAddSlashes()}
	 * @return string The necessary SQL to right trim the field or text
	 */
	public function GetRTrimSQL($text)
	{
		return sprintf('rtrim(%s)', $text);
	}

	/**
	 * Checks all defined {@link $foreignKeys} to see if the supplied id is referenced
	 * @param integer The ID of the subject record
	 * @return boolean true if the record has a reference in another table, otherwise false
	 */
	public function HasFKRef($id)
	{
		$bHasRef = false;
		$oKey = new DbProvider;
		reset($this->foreignKeys);
		while ((list($sTable, $sField) = each($this->foreignKeys)) && !$bHasRef)
		{
			if (is_array($sField)) // More than one field in here references this key
			{
				reset($sField);
				while ((list($sDummyKey, $sOneField) = each($sField)) && !$bHasRef)
				{
					if ($oKey->ExecuteScalar("SELECT COUNT(*) FROM $sTable WHERE $sOneField=$id") > 0)
						$bHasRef = true;
				}
			}
			else
			{
				if ($oKey->ExecuteScalar("SELECT COUNT(*) FROM $sTable WHERE $sField=$id") > 0)
					$bHasRef = true;
			}
		}

		return $bHasRef;
	}

	/**
	 * Writes an audit record before doing an update or delete operation
	 * @param array an array of values representing the primary key of the record to audit
	 */
	public function Audit($aID)
	{
		if (!$this->AuditEnabled)
			return;

		$sPK = '';
		$bFirstPK = true;
		foreach ($GLOBALS['phpgw_baseline'][$this->TableName]['pk'] as $sFieldName)
		{
			if (!$bFirstPK)
				$sPK .= ' AND ';
			else
				$bFirstPK = false;

			$sPK .= $sFieldName . '=' . $this->FieldValueToSQL($sFieldName, $aID[$sFieldName]);
		}

		if ($this->SubqueriesSupported)
			$sVersionSQL = '(SELECT COALESCE(MAX(audit_version) + 1, 1) FROM ' . $this->TableName . '_audit WHERE ' . $sPK . ')';
		else
		{
			$oDB = new DbProvider;
			if ($oDB->Query('SELECT COALESCE(MAX(audit_version) + 1, 1) FROM ' . $this->TableName . '_audit WHERE ' . $sPK) != -1)
			{
				if ($oDB->next_record())
					$sVersionSQL = $oDB->f(0);
				else
					trigger_error('Could not get next version for audit record.');
			}
			else
				return;
		}

		$sColumns = join(', ', array_keys($GLOBALS['phpgw_baseline'][$this->TableName]['fd']));
		$sSQL = 'INSERT INTO ' . $this->TableName . '_audit SELECT ';
		$sSQL .= $sColumns . ', ' . $this->GetDateSQL() . ', ' . $GLOBALS['DCLID'];
		$sSQL .= ', ' . $sVersionSQL;
		$sSQL .= ' FROM ' . $this->TableName . ' WHERE ' . $sPK;

		return $this->Execute($sSQL);
	}

	/**
	 * Loads an audit trail for an item
	 * @param array an array of values representing the primary key of the record to load the audit trail for
	 * @return array 2 dimensional array representing the audit trail including the current version (if not deleted)
	 */
	public function AuditLoad($aID)
	{
		if (!$this->AuditEnabled)
			return;

		$sPK = '';
		$bFirstPK = true;
		foreach ($GLOBALS['phpgw_baseline'][$this->TableName]['pk'] as $sFieldName)
		{
			if (!$bFirstPK)
				$sPK .= ' AND ';
			else
				$bFirstPK = false;

			$sPK .= $sFieldName . '=' . $this->FieldValueToSQL($sFieldName, $aID[$sFieldName]);
		}

		$sColumns = $this->SelectAllColumns();
		$sSQL = "SELECT $sColumns, " . $this->ConvertTimestamp('audit_on', 'audit_on') . ', audit_by, audit_version FROM ' . $this->TableName . '_audit WHERE ' . $sPK . ' ORDER BY audit_version';

		$aRetVal = array();
		if ($this->Query($sSQL) != -1)
		{
			$iVersion = 0;
			while ($this->next_record())
			{
				$iVersion = $this->f('audit_version');
				$aRetVal[$iVersion] = array();
				foreach (array_merge(array_keys($GLOBALS['phpgw_baseline'][$this->TableName]['fd']), array('audit_on', 'audit_by', 'audit_version')) as $sField)
				{
					$aRetVal[$iVersion][$sField] = $this->FieldValueFromSQL($sField, $this->Record[$sField]);
				}
			}

			if ($this->Query("SELECT $sColumns, NULL AS audit_on, NULL AS audit_by, NULL AS audit_version FROM " . $this->TableName . ' WHERE ' . $sPK) != -1)
			{
				if ($this->next_record())
				{
					foreach (array_merge(array_keys($GLOBALS['phpgw_baseline'][$this->TableName]['fd']), array('audit_on', 'audit_by', 'audit_version')) as $sField)
					{
						$aRetVal[$iVersion + 1][$sField] = $this->FieldValueFromSQL($sField, $this->Record[$sField]);
					}
				}
			}
		}

		return $aRetVal;
	}

	/**
	 * Constructs and executes SQL to add a record to the table associated with this class.  Uses schema files for metadata.
	 * @return integer -1 on failure
	 */
	public function Add()
	{
		$sFieldList = '';
		$sValueList = '';
		$sAuto = '';
		$bFirst =true;

		foreach ($GLOBALS['phpgw_baseline'][$this->TableName]['fd'] as $sFieldName => $aFieldInfo)
		{
				if ($aFieldInfo['type'] == 'auto')
				{
					$sAuto = $sFieldName;
					continue;
				}

				if (!$bFirst)
				{
					$sFieldList .= ', ';
					$sValueList .= ', ';
				}

				$sFieldList .= $sFieldName;
				$sValueList .= $this->FieldValueToSQL($sFieldName, $this->$sFieldName);
				$bFirst = false;
		}

		$sql = 'INSERT INTO ' . $this->TableName . ' (' . $sFieldList . ') VALUES (' . $sValueList . ')';
		if ($this->Insert($sql) == -1)
		{
			trigger_error("Query failed: $sql", E_USER_ERROR);
			return -1;
		}

		if ($sAuto != '')
		{
			$this->$sAuto = $this->GetLastInsertID($this->TableName);
		}
	}

	/**
	 * Constructs and executes SQL to modify an existing record in the table associated with this class.  Uses schema files for metadata.
	 * @param array fields to skip in set statement.  Will be ignored for primary key fields.
	 * @return integer -1 on failure, otherwise 0
	 */
	public function Edit($aIgnoreFields = '')
	{
		$sFd = '';
		$sPK = '';
		$aPK = array();
		$bFirstPK = true;
		$bFirstFd = true;

		if (!is_array($aIgnoreFields))
			$aIgnoreFields = array();

		foreach ($GLOBALS['phpgw_baseline'][$this->TableName]['fd'] as $sFieldName => $aFieldInfo)
		{
			if (in_array($sFieldName, $GLOBALS['phpgw_baseline'][$this->TableName]['pk']))
			{
				if (!$bFirstPK)
					$sPK .= ' AND ';

				$sPK .= $sFieldName . '=' . $this->FieldValueToSQL($sFieldName, $this->$sFieldName);
				$aPK[$sFieldName] = $this->FieldValueToSQL($sFieldName, $this->$sFieldName);
				$bFirstPK = false;
			}
			else
			{
				if (in_array($sFieldName, $aIgnoreFields))
					continue;

				if (!$bFirstFd)
					$sFd .= ', ';

				$sFd .= $sFieldName . '=' . $this->FieldValueToSQL($sFieldName, $this->$sFieldName);
				$bFirstFd = false;
			}
		}

		if ($this->AuditEnabled && count($aPK) > 0)
			$this->Audit($aPK);

		$sql = sprintf('UPDATE %s SET %s WHERE %s', $this->TableName, $sFd, $sPK);

		return $this->Execute($sql);
	}

	/**
	 * Constructs and executes SQL to add a record to the table associated with this class.  Uses schema files for metadata.
	 * @param array The ID of the record to delete formatted as array('field' => value[, 'field2' => value, ..., 'fieldn' => value)
	 * @return integer -1 on failure, 0 on success
	 */
	public function Delete($aID)
	{
		$sPK = '';
		$aPK = array();
		$bFirstPK = true;

		foreach ($GLOBALS['phpgw_baseline'][$this->TableName]['pk'] as $sFieldName)
		{
			if (!$bFirstPK)
				$sPK .= ' AND ';

			$sPK .= $sFieldName . '=' . $this->FieldValueToSQL($sFieldName, $aID[$sFieldName]);
			$aPK[$sFieldName] = $this->FieldValueToSQL($sFieldName, $aID[$sFieldName]);
			$bFirstPK = false;
		}

		if ($this->AuditEnabled && count($aPK) > 0)
			$this->Audit($aPK);

		$sql = sprintf('DELETE FROM %s WHERE %s', $this->TableName, $sPK);

		return $this->Execute($sql);
	}

	/**
	 * Queries a table to see if a given primary key exists.
	 * @param array The ID of the record to check for, formatted as array('field' => value[, 'field2' => value, ..., 'fieldn' => value)
	 * @return boolean true if the record exists, otherwise false
	 */
	public function Exists($aID)
	{
		$sPK = '';
		$bFirstPK = true;

		foreach ($GLOBALS['phpgw_baseline'][$this->TableName]['pk'] as $sFieldName)
		{
			if (!$bFirstPK)
				$sPK .= ' AND ';

			$sPK .= $sFieldName . '=' . $this->FieldValueToSQL($sFieldName, $aID[$sFieldName]);
			$bFirstPK = false;
		}

		$sql = sprintf('SELECT COUNT(*) FROM %s WHERE %s', $this->TableName, $sPK);

		return ($this->ExecuteScalar($sql) > 0);
	}

	/**
	 * Gets a value enclosed in '' and escaped appropriately for SQL statements
	 * @param string The value to quote and escape
	 * @return string NULL if the supplied value is NULL or empty, otherwise quoted/escaped string
	 */
	public function Quote($sValue)
	{
		if ($sValue == NULL || $sValue == '')
			return 'NULL';

		return "'" . $this->DBAddSlashes($sValue) . "'";
	}

	/**
	 * Helper function to properly represent data for SQL statements
	 * @param string the field this value represents
	 * @param string the value of this field
	 * @return mixed NULL if the value is NULL or empty, otherwise proper SQL formatted value
	 */
	public function FieldValueToSQL($sField, $sValue)
	{
		$aField =& $GLOBALS['phpgw_baseline'][$this->TableName]['fd'][$sField];
		if (is_null($sValue) || trim($sValue) === '')
		{
			if ($aField['type'] == 'timestamp')
				return $this->emptyTimestamp;

			return 'NULL';
		}

		switch ($aField['type'])
		{
			case 'varchar':
			case 'char':
			case 'text':
				return $this->Quote($sValue);
			case 'date':
			case 'datetime':
			case 'timestamp':
				if ($sValue == DCL_NOW)
					return $this->GetDateSQL();

				return $this->DisplayToSQL($sValue);
			case 'int':
			case 'auto':
				return Filter::ToInt($sValue);
			case 'float':
				return Filter::ToDecimal($sValue);
			default:
				return $sValue;
		}
	}

	/**
	 * Helper function to properly format a field for the select clause
	 * @param string the field to format
	 * @return string the formatted SQL for the select clause
	 */
	public function SelectField($sField, $sTablePrefix = '')
	{
		if ($sField == '' or $sField === null)
			return '';
			
		if ($sTablePrefix != '' && substr($sTablePrefix, -1, 1) != '.')
			$sTablePrefix .= '.';

		switch ($GLOBALS['phpgw_baseline'][$this->TableName]['fd'][$sField]['type'])
		{
			case 'date':
				return $this->ConvertDate($sTablePrefix . $sField, $sField);
			case 'datetime':
			case 'timestamp':
				return $this->ConvertTimestamp($sTablePrefix . $sField, $sField);
			case 'int':
			case 'auto':
			case 'float':
			case 'varchar':
			case 'char':
			case 'text':
			default:
				return $sTablePrefix . $sField;
		}
	}

	/**
	 * Helper function to return data properly formatted for display
	 * @param string the field this value represents
	 * @param string the value of this field
	 * @return mixed the properly formatted value for display
	 */
	public function FieldValueFromSQL($sField, $sValue)
	{
		if (!isset($GLOBALS['phpgw_baseline'][$this->TableName]['fd'][$sField]))
		{
			if ($sField == 'audit_on')
				return $this->FormatTimestampForDisplay($sValue);

			return $sValue;
		}

		switch ($GLOBALS['phpgw_baseline'][$this->TableName]['fd'][$sField]['type'])
		{
			case 'date':
				return $this->FormatDateForDisplay($sValue);
			case 'datetime':
			case 'timestamp':
				return $this->FormatTimestampForDisplay($sValue);
			default:
				return $sValue;
		}
	}

	/**
	 * Wipes all field member variables according to the schema.  Numerics are set to 0 and dates/text fields are set to empty string.
	 */
	public function Clear()
	{
		foreach ($GLOBALS['phpgw_baseline'][$this->TableName]['fd'] as $sFieldName => $aFieldInfo)
		{
			switch ($aFieldInfo['type'])
			{
				case 'auto':
				case 'decimal':
				case 'int':
				case 'float':
					$this->$sFieldName = NULL;
					break;
				default:
					$this->$sFieldName = '';
					break;
			}
		}
	}

	/**
	 * Retrieves the current record into the field member variables
	 */
	public function GetRow()
	{
		if (count($this->Record) > 0)
		{
			foreach ($GLOBALS['phpgw_baseline'][$this->TableName]['fd'] as $sFieldName => $aFieldInfo)
				$this->$sFieldName = $this->FieldValueFromSQL($sFieldName, $this->f($sFieldName));

			if ($this->cacheEnabled && count($GLOBALS['phpgw_baseline'][$this->TableName]['pk']) == 1)
			{
				$sPK = $GLOBALS['phpgw_baseline'][$this->TableName]['pk'][0];
				$this->CacheRow($this->$sPK);
			}
		}
		else
			$this->Clear();
	}

	/**
	 * Returns the current result as an array
	 @param boolean true to free the result before returning
	 @return array All records from cursor position to EOF
	 */
	public function ResultToArray($bFreeResult = true)
	{
		$aRetVal = array();
		while ($this->next_record())
			$aRetVal[] = $this->Record;

		if ($bFreeResult)
			$this->FreeResult();

		return $aRetVal;
	}

	public function GetOptions($sFieldID, $sFieldDesc, $sFieldActive = '', $bActiveOnly = true, $sPublicField = '', $sFilter = '')
	{
		global $g_oSec;

		$sWhere = '';

		if ($g_oSec->IsPublicUser() && $sPublicField != '')
			$sWhere = "$sPublicField = 'Y'";

		if ($bActiveOnly && $sFieldActive != '')
		{
			if ($sWhere != '')
				$sWhere .= ' AND ';

			$sWhere .= "$sFieldActive = 'Y'";
		}
		
		if ($sFilter != '')
		{
			if ($sWhere != '')
				$sWhere .= ' AND ';

			$sWhere .= $sFilter;
		}
		
		if ($sWhere != '')
			$this->Query("SELECT $sFieldID, $sFieldDesc FROM $this->TableName WHERE $sWhere ORDER BY $sFieldDesc");
		else
			$this->Query("SELECT $sFieldID, $sFieldDesc FROM $this->TableName ORDER BY $sFieldDesc");

		return $this->ResultToArray();
	}

	/**
	 * Constructs a list of columns for a select clause
	 * @param string The prefix to prepend to the field name, including the dot separator (i.e., "timecards.")
	 * @return string SQL column list
	 */
	public function SelectAllColumns($sTablePrefix = '')
	{
		$bFirstFd = true;
		$sFd = '';
		foreach ($GLOBALS['phpgw_baseline'][$this->TableName]['fd'] as $sFieldName => $aFieldInfo)
		{
			if (!$bFirstFd)
				$sFd .= ', ';

			$sFd .= $this->SelectField($sFieldName, $sTablePrefix);
			$bFirstFd = false;
		}

		return $sFd;
	}

	/**
	 * Load a record from the table this class represents
	 * @param array The ID of the record to load formatted as array('field' => value[, 'field2' => value, ..., 'fieldn' => value)
	 * @param boolean Specifies if an error should be triggered if the object is not found
	 * @return mixed Hmmm..returns result of {@link GetRow()}, but GetRow() does not return anything
	 */
	public function Load($id, $bTriggerErrorIfNotFound = true)
	{
		$this->Clear();

		if ($this->cacheEnabled && !is_array($id) && $this->LoadCache($id) != -1)
			return 0;

		$bFirstFd = true;
		$bFirstPK = true;
		$sPK = '';
		$sFd = '';
		foreach ($GLOBALS['phpgw_baseline'][$this->TableName]['fd'] as $sFieldName => $aFieldInfo)
		{
			if (in_array($sFieldName, $GLOBALS['phpgw_baseline'][$this->TableName]['pk']))
			{
				if (!$bFirstPK)
					$sPK .= ' AND ';

				if (is_array($id))
					$sPK .= $sFieldName . '=' . $this->FieldValueToSQL($sFieldName, $id[$sFieldName]);
				else
					$sPK .= $sFieldName . '=' . $this->FieldValueToSQL($sFieldName, $id);

				$bFirstPK = false;
			}

			if (!$bFirstFd)
				$sFd .= ', ';

			$sFd .= $this->SelectField($sFieldName);
			$bFirstFd = false;
		}

		$sql = sprintf('SELECT %s FROM %s WHERE %s', $sFd, $this->TableName, $sPK);
		if (!$this->Query($sql))
			return -1;

		if (!$this->next_record())
		{
			if ($bTriggerErrorIfNotFound)
			{
				if (is_array($id))
					trigger_error('Could not find id (' . join(', ', $id) . ') in table ' . $this->TableName, E_USER_ERROR);
				else
					trigger_error('Could not find id (' . $id . ') in table ' . $this->TableName, E_USER_ERROR);
			}
					
			return -1;
		}

		return $this->GetRow();
	}

	/**
	 * Toggles a Y/N field in the table
	 * @param array The ID of the record to load formatted as array('field' => value[, 'field2' => value, ..., 'fieldn' => value)
	 * @param boolean true to set to Y, false to set to N
	 * @param string the name of the field in the table to toggle
	 * @return -1 on error, 0 on success
	 */
	public function SetActive($id, $active, $sField = 'active')
	{
		$isActive = $active ? 'Y' : 'N';
		$sPK = '';
		$bFirstPK = true;

		foreach ($id as $sFieldName => $sFieldValue)
		{
			if (in_array($sFieldName, $GLOBALS['phpgw_baseline'][$this->TableName]['pk']))
			{
				if (!$bFirstPK)
					$sPK .= ' AND ';

				$sPK .= $sFieldName . '=' . $this->FieldValueToSQL($sFieldName, $sFieldValue);
				$bFirstPK = false;
			}
		}

		$sql = sprintf("UPDATE %s SET %s = '%s' WHERE %s", $this->TableName, $sField, $isActive, $sPK);

		return $this->Execute($sql);
	}


	// Virtual methods - sort of
	/**
	 * Connect to the database
	 * @param integer Connection ID of existing connection.  Not really needed as a global ID is set per client request.
	 * @return integer -1 on error, 0 on success
	 * @abstract
	 */
	public function Connect($conn = ''){}
	/**
	 * Execute a row-returning query against the database
	 * @param string a valid SQL query to execute
	 * @return integer -1 on error, 0 on success
	 * @abstract
	 */
	public function Query($query){}
	/**
	 * Execute a row-returning query against the database and return a partial result based on offset and rows
	 * @param string a valid SQL query to execute
	 * @param integer starting record (must adhere to order by clause)
	 * @param integer number of rows to fetch
	 * @return integer -1 on error, 0 on success
	 * @abstract
	 */
	public function LimitQuery($query, $offset, $rows){}
	/**
	 * Execute a no row-returning query against the database
	 * @param string a valid SQL query to execute
	 * @return integer -1 on error, 0 on success
	 * @abstract
	 */
	public function Execute($query){}
	/**
	 * Execute a row-returning query against the database and return the value of the first field of the first row
	 * @param string a valid SQL query to execute
	 * @return mixed value of the first field of the first row
	 * @abstract
	 */
	public function ExecuteScalar($sql){}
	/**
	 * Execute an insert query against the database
	 * @param string a valid SQL query to execute to insert a record
	 * @return integer -1 on error, 0 on success, > 0 for sequence/identity/autonumber fields
	 * @abstract
	 */
	public function Insert($query){}
	/**
	 * Free the result resource held by {@link $res}
	 * @abstract
	 */
	public function FreeResult(){}
	/**
	 * Start a SQL transaction
	 * @return integer -1 on error, 0 on success
	 * @abstract
	 */
	public function BeginTransaction(){}
	/**
	 * Commit a SQL transaction
	 * @return integer -1 on error, 0 on success
	 * @abstract
	 */
	public function EndTransaction(){}
	/**
	 * Rollback a SQL transaction
	 * @return integer -1 on error, 0 on success
	 * @abstract
	 */
	public function RollbackTransaction(){}
	/**
	 * Returns the number of fields in the current result set
	 * @return integer number of fields in the result set
	 * @abstract
	 */
	public function NumFields(){}
	/**
	 * Retrieves the next record from the result set
	 * @return boolean true if a record was retrieved, otherwise false
	 * @abstract
	 */
	public function next_record(){}
	/**
	 * Gets the name of the field at the specified index
	 * @param integer ordinal of the field to retrieve the field name for
	 * @return string the name of the field
	 * @abstract
	 */
	public function GetFieldName($fieldIndex){}
	/**
	 * Returns an array containing all fields and all rows of the current result set
	 * @return array A two dimensional array of all rows and columns.  Format: array[row][column]
	 * @abstract
	 */
	public function FetchAllRows(){}
	/**
	 * Returns SQL for retrieving the next ID of a table
	 * @param string the name of the table to retrieve the SQL for
	 * @return string empty string for no special SQL, or nonempty string for special ID SQL
	 * @abstract
	 */
	public function GetNewIDSQLForTable($tableName){}
	/**
	 * Returns a SQL command to get the current date/time
	 * @return string SQL command to get the current date/time
	 * @abstract
	 */
	public function GetDateSQL(){}
	/**
	 * Returns the last ID inserted into a table by this connection
	 * @param string the name of the table to get the last inserted ID for
	 * @return integer the last inserted ID for this table and connection
	 * @abstract
	 */
	public function GetLastInsertID($sTable){}
	/**
	 * Convert the date to a predictable format
	 * @param string expression to use for conversion
	 * @param string the field to name the converted date as
	 * @return string SQL fragment to convert the desired expression
	 * @abstract
	 */
	public function ConvertDate($sExpression, $sField){}
	/**
	 * Convert the timestamp to a predictable format
	 * @param string expression to use for conversion
	 * @param string the field to name the converted timestamp as
	 * @return string SQL fragment to convert the desired expression
	 * @abstract
	 */
	public function ConvertTimestamp($sExpression, $sField){}
	/**
	 * Is this a date field?
	 * @param string the field to check
	 * @return boolean true if this is a date field, otherwise false
	 * @abstract
	 */
	public function IsDate($vField){}
	/**
	 * Is this a timestamp field?
	 * @param string the field to check
	 * @return boolean true if this is a timestamp field, otherwise false
	 * @abstract
	 */
	public function IsTimestamp($vField){}
	/**
	 * Get the minutes elapsed between two timestamp espressions
	 * @param string expression to use for beginning date/time
	 * @param string expression to use for ending date/time
	 * @param string the field name to use in the result set
	 * @return string SQL fragment to calculate the time elapsed
	 * @abstract
	 */
	public function GetMinutesElapsedSQL($sBeginDateSQL, $sEndDateSQL, $sAsField){}

	// FIXME: Move these to SchemaManager
	/**
	 * Determine if a server is available
	 * @return boolean true if the server is available, otherwise false
	 * @abstract
	 */
	public function CanConnectServer(){}
	/**
	 * Determine if a database is available
	 * @return boolean true if the database is available, otherwise false
	 * @abstract
	 */
	public function CanConnectDatabase(){}
	/**
	 * Create a new database
	 * @return ?
	 * @abstract
	 */
	public function CreateDatabase(){}
	/**
	 * Determine if a table exists in the database
	 * @param string the name of the table to check
	 * @return boolean true if the table exists, otherwise false
	 * @abstract
	 */
	public function TableExists($sTableName){}
	/**
	 * Retrieve a list of index names
	 * @return array an array of index names
	 * @abstract
	 */
	public function index_names(){}
	/**
	 * Determine if a field exists in a table
	 * @param string the name of the table to check
	 * @param string the name of the field to check
	 * @return boolean true if the field exists, otherwise false
	 * @abstract
	 */
	public function FieldExists($sTable, $sField){}
}
