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

class SchemaManagerOracle8
{
	var $m_sStatementTerminator;
	/* Following added to convert sql to array */
	var $sCol = array();
	var $pk = array();
	var $fk = array();
	var $ix = array();
	var $uc = array();

	public function __construct()
	{
		$this->m_sStatementTerminator = ';';
	}

	/* Return a type suitable for DDL */
	public function TranslateType($sType, $iPrecision = 0, $iScale = 0)
	{
		switch($sType)
		{
			case 'auto':
				$sTranslated = 'int4';
				break;
			case 'blob':
				$sTranslated = 'text';
				break;
			case 'char':
				if ($iPrecision > 0 && $iPrecision < 256)
				{
					$sTranslated =  sprintf("char(%d)", $iPrecision);
				}
				if ($iPrecision > 255)
				{
					$sTranslated =  'text';
				}
				break;
			case 'decimal':
				$sTranslated =  sprintf("decimal(%d,%d)", $iPrecision, $iScale);
				break;
			case 'float':
				if ($iPrecision == 4 || $iPrecision == 8)
				{
					$sTranslated =  sprintf("float%d", $iPrecision);
				}
				break;
			case 'int':
				if ($iPrecision == 2 || $iPrecision == 4 || $iPrecision == 8)
				{
					$sTranslated =  sprintf("int%d", $iPrecision);
				}
				break;
			case 'longtext':
				$sTranslated = 'text';
				break;
			case 'varchar':
				if ($iPrecision > 0 && $iPrecision < 256)
				{
					$sTranslated =  sprintf("varchar2(%d)", $iPrecision);
				}
				if ($iPrecision > 255)
				{
					$sTranslated =  'text';
				}
				break;
			case 'date':
			case 'text':
			case 'timestamp':
			case 'bool':
				$sTranslated = $sType;
				break;
		}
		return $sTranslated;
	}

	public function TranslateDefault($sDefault, $sType)
	{
		switch ($sDefault)
		{
			case 'current_date':
			case 'current_timestamp':
				$sDefault = 'sysdate';
				break;
		}

		switch ($sType)
		{
			case 'int':
			case 'float':
			case 'bool':
			case 'decimal':
				return "DEFAULT ($sDefault)";
			case 'timestamp':
			case 'date':
				if (mb_strtolower($sDefault) == 'now()')
					return "DEFAULT ($sDefault)";
		}

		return "DEFAULT ('$sDefault')";
	}

	/* Inverse of above, convert sql column types to array info */
	public function rTranslateType($sType, $iPrecision = 0, $iScale = 0)
	{
		$sTranslated = '';
		switch($sType)
		{
			case 'serial':
				$sTranslated = "'type' => 'auto'";
				break;
			case 'int2':
				$sTranslated = "'type' => 'int', 'precision' => 2";
				break;
			case 'int4':
				$sTranslated = "'type' => 'int', 'precision' => 4";
				break;
			case 'int8':
				$sTranslated = "'type' => 'int', 'precision' => 8";
				break;
			case 'bpchar':
			case 'char':
				if ($iPrecision > 0 && $iPrecision < 256)
				{
					$sTranslated = "'type' => 'char', 'precision' => $iPrecision";
				}
				if ($iPrecision > 255)
				{
					$sTranslated =  "'type' => 'text'";
				}
				break;
			case 'numeric':
				/* Borrowed from phpPgAdmin */
				$iPrecision = ($iScale >> 16) & 0xffff;
				$iScale     = ($iScale - 4) & 0xffff;
				$sTranslated = "'type' => 'decimal', 'precision' => $iPrecision, 'scale' => $iScale";
				break;
			case 'float':
			case 'float4':
			case 'float8':
			case 'double':
				$sTranslated = "'type' => 'float', 'precision' => $iPrecision";
				break;
			case 'datetime':
			case 'timestamp':
				$sTranslated = "'type' => 'timestamp'";
				break;
			case 'varchar':
				if ($iPrecision > 0 && $iPrecision < 256)
				{
					$sTranslated =  "'type' => 'varchar', 'precision' => $iPrecision";
				}
				if ($iPrecision > 255)
				{
					$sTranslated =  "'type' => 'text'";
				}
				break;
			case 'text':
			case 'blob':
			case 'date':
				$sTranslated = "'type' => '$sType'";
				break;
		}
		return $sTranslated;
	}

	public function GetPKSQL($sFields)
	{
		return "PRIMARY KEY($sFields)";
	}

	public function GetUCSQL($sFields)
	{
		return "UNIQUE($sFields)";
	}

	public function _GetColumns($oProc, $sTableName, &$sColumns, $sDropColumn = '', $sAlteredColumn = '', $sAlteredColumnType = '')
	{
		$sdb = $oProc->m_odb;
		$sdc = $oProc->m_odb;

		$sColumns = '';
		$this->pk = array();
		$this->fk = array();
		$this->ix = array();
		$this->uc = array();

		$query = "SELECT a.attname,a.attnum FROM pg_attribute a,pg_class b WHERE ";
		$query .= "b.oid=a.attrelid AND a.attnum>0 and b.relname='$sTableName'";
		if ($sDropColumn != '')
		{
			$query .= " AND a.attname != '$sDropColumn'";
		}
		$query .= ' ORDER BY a.attnum';

		$oProc->m_odb->query($query);
		while ($oProc->m_odb->next_record())
		{
			if ($sColumns != '')
			{
				$sColumns .= ',';
			}

			$sFieldName = $oProc->m_odb->f(0);
			$sColumns .= $sFieldName;
			if ($sAlteredColumn == $sFieldName && $sAlteredColumnType != '')
			{
				$sColumns .= '::' . $sAlteredColumnType;
			}
		}
		//$qdefault = "SELECT substring(d.adsrc for 128) FROM pg_attrdef d, pg_class c "
		//	. "WHERE c.relname = $sTableName AND c.oid = d.adrelid AND d.adnum =" . $oProc->m_odb->f(1);
		$sql_get_fields = "
			SELECT
				a.attnum,
				a.attname AS field,
				t.typname AS type,
				a.attlen AS length,
				a.atttypmod AS lengthvar,
				a.attnotnull AS notnull
			FROM
				pg_class c,
				pg_attribute a,
				pg_type t
			WHERE
				c.relname = '$sTableName'
				and a.attnum > 0
				and a.attrelid = c.oid
				and a.atttypid = t.oid
				ORDER BY a.attnum";
		/* attnum field type length lengthvar notnull(Yes/No) */
		$sdb->query($sql_get_fields);
		while ($sdb->next_record())
		{
			$colnum  = $sdb->f(0);
			$colname = $sdb->f(1);

			if ($sdb->f(5) == 'Yes')
			{
				$null = "'nullable' => True";
			}
			else
			{
				$null = "'nullable' => False";
			}

			if ($sdb->f(2) == 'numeric')
			{
				$prec  = $sdb->f(3);
				$scale = $sdb->f(4);
			}
			elseif ($sdb->f(3) > 0)
			{
				$prec  = $sdb->f(3);
				$scale = 0;
			}
			elseif ($sdb->f(4) > 0)
			{
				$prec = $sdb->f(4) - 4;
				$scale = 0;
			}
			else
			{
				$prec = 0;
				$scale = 0;
			}

			$type = $this->rTranslateType($sdb->f(2), $prec, $scale);

			$sql_get_default = "
				SELECT d.adsrc AS rowdefault
					FROM pg_attrdef d, pg_class c
					WHERE
						c.relname = '$sTableName' AND
						c.oid = d.adrelid AND
						d.adnum = $colnum
				";
			$sdc->query($sql_get_default);
			$sdc->next_record();
			if ($sdc->f(0))
			{
				if (preg_match('/nextval/',$sdc->f(0)))
				{
					$default = '';
					$nullcomma = '';
				}
				else
				{
					$default = "'default' => '".$sdc->f(0)."'";
					$nullcomma = ',';
				}
			}
			else
			{
				$default = '';
				$nullcomma = '';
			}
			$default = preg_replace("/''/","'",$default);

			$this->sCol[] = "\t\t\t\t'" . $colname . "' => array(" . $type . ',' . $null . $nullcomma . $default . '),' . "\n";
		}
		$sql_pri_keys = "
			SELECT
				ic.relname AS index_name,
				bc.relname AS tab_name,
				ta.attname AS column_name,
				i.indisunique AS unique_key,
				i.indisprimary AS primary_key
			FROM
				pg_class bc,
				pg_class ic,
				pg_index i,
				pg_attribute ta,
				pg_attribute ia
			WHERE
				bc.oid = i.indrelid
				AND ic.oid = i.indexrelid
				AND ia.attrelid = i.indexrelid
				AND ta.attrelid = bc.oid
				AND bc.relname = '$sTableName'
				AND ta.attrelid = i.indrelid
				AND ta.attnum = i.indkey[ia.attnum-1]
			ORDER BY
				index_name, tab_name, column_name";
		$sdc->query($sql_pri_keys);
		while ($sdc->next_record())
		{
			//echo '<br> checking: ' . $sdc->f(4);
			if ($sdc->f(4) == 't')
			{
				$this->pk[] = $sdc->f(2);
			}
			if ($sdc->f(3) == 't')
			{
				$this->uc[] = $sdc->f(2);
			}
		}
		/* ugly as heck, but is here to chop the trailing comma on the last element (for php3) */
		$this->sCol[count($this->sCol) - 1] = mb_substr($this->sCol[count($this->sCol) - 1],0,-2) . "\n";

		return false;
	}

	public function _CopyAlteredTable($oProc, &$aTables, $sSource, $sDest)
	{
		$oDB = $oProc->m_odb;
		$oProc->m_odb->query("select * from $sSource");
		while ($oProc->m_odb->next_record())
		{
			$sSQL = "INSERT INTO $sDest (";

			$sSQL .= join(',', array_keys($aTables[$sDest]['fd']));

			$sSQL .= ') VALUES (';

			$i = 0;
			foreach ($aTables[$sDest]['fd'] as $name => $arraydef)
			{
				if ($i++ > 0)
				{
					$sSQL .= ',';
				}

				if ($oProc->m_odb->f($name) != null)
				{
					switch ($arraydef['type'])
					{
						case 'blob':
						case 'char':
						case 'date':
						case 'text':
						case 'timestamp':
						case 'varchar':
							$sSQL .= "'" . $oProc->m_odb->db_addslashes($oProc->m_odb->f($name)) . "'";
							break;
						default:
							$sSQL .= $oProc->m_odb->f($name);
					}
				}
				else
				{
					$sSQL .= 'null';
				}
			}
			$sSQL .= ')';

			$oDB->query($sSQL);
		}

		return true;
	}

	public function RefreshTable($oProc, $sTableName, &$aTableDef)
	{
		$sSequenceSQL = '';
		$sTableSQL = '';

		$oProc->_GetTableSQL($sTableName, $aTableDef, $sTableSQL, $sSequenceSQL);
		if ($sTableSQL == '')
			return false;

		$aArray = array();

		$oProc->query('BEGIN');
		$bRetVal = $this->_DropAllConstraints($oProc, $aArray, $sTableName);
		if ($bRetVal)
			$bRetVal = !!($oProc->query("ALTER TABLE $sTableName RENAME TO tmp_$sTableName"));

		if ($bRetVal)
			$bRetVal = !!($oProc->query("CREATE TABLE $sTableName ($sTableSQL)"));

		if ($bRetVal)
			$bRetVal = !!($oProc->query("INSERT INTO $sTableName SELECT " . join(',', array_keys($aTableDef['fd'])) . " FROM tmp_$sTableName"));

		if ($bRetVal)
			$bRetVal = $this->CreateIndexes($oProc, $sTableName, $aTableDef['ix']);

		if ($bRetVal)
			$bRetVal = !!($oProc->query("DROP TABLE tmp_$sTableName"));

		if ($bRetVal)
			$oProc->query('COMMIT');
		else
			$oProc->query('ROLLBACK');

		return $bRetVal;
	}

	public function GetSequenceForTable($oProc,$table,&$sSequenceName)
	{
		global $DEBUG;
		if($DEBUG) { echo '<br>GetSequenceForTable: ' . $table; }

		$sSequenceName = '';
		$oProc->m_odb->query("SELECT relname FROM pg_class WHERE NOT relname ~ 'pg_.*' AND relname LIKE 'seq_$table' AND relkind='S' ORDER BY relname",__LINE__,__FILE__);
		if ($oProc->m_odb->next_record())
		{
			$sSequenceName = $oProc->m_odb->f(0);
		}
		return True;
	}

	public function GetSequenceFieldForTable($oProc,$table,&$sField)
	{
		global $DEBUG;
		if($DEBUG) { echo "<br>GetSequenceFieldForTable: $table"; }

		$sField = '';
		$oProc->m_odb->query("SELECT a.attname FROM pg_attribute a, pg_class c, pg_attrdef d WHERE c.relname='$table' AND c.oid=d.adrelid AND d.adsrc LIKE '%seq_$table%' AND a.attrelid=c.oid AND d.adnum=a.attnum");
		if ($oProc->m_odb->next_record())
		{
			$sField = $oProc->m_odb->f(0);
		}
		return True;
	}

	public function _DropAllConstraints($oProc, &$aTables, $sTable)
	{
		// Drop all constraints in preparation for a table schema refresh
		global $DEBUG;
		if ($DEBUG) { echo '<br>_DropAllConstraints ', $sTable; }

		$sConstraintName = '';
		$oDB = $oProc->m_odb;
		$oProc->m_odb->query("select a.relname, i.indisprimary, i.indisunique from pg_class a, pg_class b, pg_index i where a.oid = i.indexrelid and b.oid = i.indrelid and b.relname = '$sTable'");

		while ($oProc->m_odb->next_record())
		{
			$sConstraintName = $oProc->m_odb->f(0);

			if ($oProc->m_odb->f(1) == 't' || $oProc->m_odb->f(2) == 't')
				$oDB->query("ALTER TABLE $sTable DROP CONSTRAINT $sConstraintName");
			else
				$oDB->query("DROP INDEX $sConstraintName");
		}

		return true;
	}

	public function DropPrimaryKey($oProc, &$aTables, $sTable)
	{
		global $DEBUG;
		if ($DEBUG) { echo '<br>DropPrimaryKey ', $sTable; }

		$sPrimaryKey = '';
		$oProc->m_odb->query("select a.relname, i.indisprimary from pg_class a, pg_class b, pg_index i where a.oid = i.indexrelid and (i.indisprimary = 't' or (i.indisunique = 't' and a.relname like '%_pkey')) and b.oid = i.indrelid and b.relname = '$sTable'");
		if ($oProc->m_odb->next_record())
		{
			$sPrimaryKey = $oProc->m_odb->f(0);

			if ($oProc->m_odb->f(1) == 't')
				$oProc->m_odb->query("ALTER TABLE $sTable DROP CONSTRAINT $sPrimaryKey");
			else
				$oProc->m_odb->query("DROP INDEX $sPrimaryKey");
		}

		return true;
	}

	public function CreatePrimaryKey($oProc, &$aTables, $sTable, &$aFields)
	{
		if (count($aFields) < 1)
			return true;

		return !!($oProc->m_odb->query("ALTER TABLE $sTable ADD PRIMARY KEY (" . join(',', $aFields) . ')'));
	}

	public function DropSequenceForTable($oProc,$table)
	{
		global $DEBUG;
		if($DEBUG) { echo '<br>DropSequenceForTable: ' . $table; }

		$this->GetSequenceForTable($oProc,$table,$sSequenceName);
		if ($sSequenceName)
		{
			$oProc->m_odb->query("DROP SEQUENCE " . $sSequenceName,__LINE__,__FILE__);
		}
		return True;
	}

	public function DropTable($oProc, &$aTables, $sTableName)
	{
		return $oProc->m_odb->query("DROP TABLE $sTableName") && $this->DropSequenceForTable($oProc, $sTableName);
	}

	public function DropColumn($oProc, &$aTables, $sTableName, $aNewTableDef, $sColumnName, $bCopyData = true)
	{
		if ($bCopyData)
		{
			$oProc->m_odb->query("SELECT * INTO $sTableName" . "_tmp FROM $sTableName");
		}

		$this->DropTable($oProc, $aTables, $sTableName);

		$bRet = $this->CreateTable($oProc, $aTables, $sTableName, $aNewTableDef, true);
		if (!$bCopyData || !$bRet)
			return $bRet;
			
		$this->_GetColumns($oProc, $sTableName, $sColumns);
		$query = "INSERT INTO $sTableName ($sColumns) SELECT $sColumns FROM $sTableName" . '_tmp';
		$bRet = !!($oProc->m_odb->query($query));

		// Update sequence to match
		$sSeqField = '';
		$this->GetSequenceFieldForTable($oProc, $sTableName, $sSeqField);
		if ($sSeqField != '')
		{
			$sSeqName = '';
			$this->GetSequenceForTable($oProc, $sTableName, $sSeqName);
			if ($sSeqName != '')
			{
				// we have a sequence name and the corresponding field, so it must be golden
				$sSQL = "SELECT SETVAL('$sSeqName', (SELECT MAX($sSeqField) FROM $sTableName))";
				$oProc->m_odb->query($sSQL);
			}
		}

		return ($bRet && $this->DropTable($oProc, $aTables, $sTableName . '_tmp'));
	}

	public function RenameTable($oProc, &$aTables, $sOldTableName, $sNewTableName)
	{
		global $DEBUG;
		if ($DEBUG) { echo '<br>RenameTable(): Fetching old sequence for: ' . $sOldTableName; }
		$this->GetSequenceForTable($oProc,$sOldTableName,$sSequenceName);
		if ($DEBUG) { echo ' - ' . $sSequenceName; }
		if ($DEBUG) { echo '<br>RenameTable(): Fetching sequence field for: ' . $sOldTableName; }
		$this->GetSequenceFieldForTable($oProc,$sOldTableName,$sField);
		if ($DEBUG) { echo ' - ' . $sField; }

		if ($sSequenceName)
		{
			$oProc->m_odb->query("SELECT last_value FROM seq_$sOldTableName",__LINE__,__FILE__);
			$oProc->m_odb->next_record();
			$lastval = $oProc->m_odb->f(0);

			if ($DEBUG) { echo '<br>RenameTable(): dropping old sequence: ' . $sSequenceName . ' used on field: ' . $sField; }
			$this->DropSequenceForTable($oProc,$sOldTableName);

			if ($lastval)
			{
				$lastval = ' start ' . $lastval;
			}
			$this->GetSequenceSQL($sNewTableName,$sSequenceSQL);
			if ($DEBUG) { echo '<br>RenameTable(): Making new sequence using: ' . $sSequenceSQL . $lastval; }
			$oProc->m_odb->query($sSequenceSQL . $lastval,__LINE__,__FILE__);

			if ($sField)
			{
				if ($DEBUG) { echo '<br>RenameTable(): Altering column default for: ' . $sField; }
				$oProc->m_odb->query("ALTER TABLE $sOldTableName ALTER $sField SET DEFAULT nextval('seq_" . $sNewTableName . "')",__LINE__,__FILE__);
			}
		}

		$indexnames = $oProc->m_odb->index_names();
		foreach ($indexnames as $key => $val)
		{
			$indexes[] = $val['index_name'];
		}
		if(!in_array($sOldTableName . '_pkey',$indexes))	// no idea how this can happen
		{
			$oProc->m_odb->query("DROP INDEX " . $sOldTableName . "_pkey",__LINE__,__FILE__);
		}
		else	// rename the index
		{
			$oProc->m_odb->query('ALTER TABLE '.$sOldTableName.'_pkey RENAME TO '.$sNewTableName.'_pkey');
		}

		return !!($oProc->m_odb->query("ALTER TABLE $sOldTableName RENAME TO $sNewTableName"));
	}

	public function RenameColumn($oProc, &$aTables, $sTableName, $sOldColumnName, $sNewColumnName, $bCopyData = true)
	{
		/*
		 This really needs testing - it can affect primary keys, and other table-related objects
		 like sequences and such
		*/
		if ($bCopyData)
		{
			$oProc->m_odb->query("SELECT * INTO $sTableName" . "_tmp FROM $sTableName");
		}

		$this->DropTable($oProc, $aTables, $sTableName);

		if (!$bCopyData)
		{
			return $this->CreateTable($oProc, $aTables, $sTableName, $oProc->m_aTables[$sTableName], false);
		}

		$this->CreateTable($oProc, $aTables, $sTableName, $aTables[$sTableName], True);
		$this->_GetColumns($oProc, $sTableName . "_tmp", $sColumns);
		$this->_GetColumns($oProc, $sTableName, $sNewColumns);
		$query = "INSERT INTO $sTableName SELECT $sColumns FROM $sTableName" . '_tmp';

		$bRet = !!($oProc->m_odb->query($query));
		return ($bRet && $this->DropTable($oProc, $aTables, $sTableName . "_tmp"));
	}

	public function AlterColumn($oProc, &$aTables, $sTableName, $sColumnName, &$aColumnDef, $bCopyData = true)
	{
		if ($bCopyData)
		{
			$oProc->m_odb->query("SELECT * INTO $sTableName" . "_tmp FROM $sTableName");
		}

		$this->DropTable($oProc, $aTables, $sTableName);

		if (!$bCopyData)
		{
			return $this->CreateTable($oProc, $aTables, $sTableName, $aTables[$sTableName], True);
		}

		$this->CreateTable($oProc, $aTables, $sTableName, $aTables[$sTableName], True);
		$this->_GetColumns($oProc, $sTableName . "_tmp", $sColumns, '', $sColumnName, $aColumnDef['type'] == 'auto' ? 'int4' : $aColumnDef['type']);

		/*
		 TODO: analyze the type of change and determine if this is used or _CopyAlteredTable
		 this is a performance consideration only, _CopyAlteredTable should be safe
		 $query = "INSERT INTO $sTableName SELECT $sColumns FROM $sTableName" . "_tmp";
		 $bRet = !!($oProc->m_odb->query($query));
		*/

		$bRet = $this->_CopyAlteredTable($oProc, $aTables, $sTableName . '_tmp', $sTableName);

		return ($bRet && $this->DropTable($oProc, $aTables, $sTableName . "_tmp"));
	}

	public function AddColumn($oProc, &$aTables, $sTableName, $sColumnName, &$aColumnDef)
	{
		if (isset($aColumnDef['default']))	// pgsql cant add a colum with a default
		{
			$default = $aColumnDef['default'];
			unset($aColumnDef['default']);
		}
		if (isset($aColumnDef['nullable']) && !$aColumnDef['nullable'])	// pgsql cant add a column not nullable
		{
			$notnull = !$aColumnDef['nullable'];
			unset($aColumnDef['nullable']);
		}
		$oProc->_GetFieldSQL($aColumnDef, $sFieldSQL);
		$query = "ALTER TABLE $sTableName ADD COLUMN $sColumnName $sFieldSQL";

		if (($Ok = !!($oProc->m_odb->query($query))) && isset($default))
		{
			$query = "ALTER TABLE $sTableName ALTER COLUMN $sColumnName SET DEFAULT '$default';\n";

			$query .= "UPDATE $sTableName SET $sColumnName='$default';\n";

			$Ok = !!($oProc->m_odb->query($query));

			if ($OK && $notnull)
			{
				// unfortunally this is pgSQL >= 7.3
				//$query .= "ALTER TABLE $sTableName ALTER COLUMN $sColumnName SET NOT NULL;\n";
				//$Ok = !!($oProc->m_odb->query($query));
				// so we do it the slow way
				AlterColumn($oProc, $aTables, $sTableName, $sColumnName, $aColumnDef);
			}
		}
		return $Ok;
	}

	public function GetSequenceSQL($sTableName, &$sSequenceSQL)
	{
		$sSequenceSQL = sprintf("CREATE SEQUENCE seq_%s", $sTableName);
		return true;
	}

	public function CreateTable($oProc, $aTables, $sTableName, $aTableDef, $bCreateSequence = true)
	{
		global $DEBUG;
		if ($oProc->_GetTableSQL($sTableName, $aTableDef, $sTableSQL, $sSequenceSQL))
		{
			/* create sequence first since it will be needed for default */
			if ($bCreateSequence && $sSequenceSQL != '')
			{
				if ($DEBUG) { echo '<br>Making sequence using: ' . $sSequenceSQL; }
				$oProc->m_odb->query($sSequenceSQL);
			}

			$query = "CREATE TABLE $sTableName ($sTableSQL)";
			$retVal = ($oProc->m_odb->query($query) != -1);
			if ($retVal)
				$retVal = $this->CreateIndexes($oProc, $sTableName, $aTableDef['ix']);
				
			return $retVal;
		}

		return false;
	}

	public function CreateIndexes($oProc, $sTableName, $aIndexDef)
	{
		$retVal = true;
		if (is_array($aIndexDef) && count($aIndexDef) > 0)
		{
			foreach ($aIndexDef as $sIndexName => $aIndexColumns)
				$retVal = $this->CreateIndex($oProc, null, $sTableName, $sIndexName, $aIndexColumns);
		}
		
		return $retVal;
	}

	public function CreateIndex($oProc, $aTables, $sTableName, $sIndexName, $aColumns)
	{
		$sColumns = join($aColumns, ',');
		$sSQL = "CREATE INDEX $sIndexName ON $sTableName ($sColumns)";
		
		return ($oProc->m_odb->Query($sSQL) != -1);
	}

	public function DropIndex($oProc, $aTables, $sTableName, $sIndexName)
	{
		return ($oProc->m_odb->Query("DROP INDEX $sIndexName") != -1);
	}

	public function UpdateSequence($oProc, $sTableName, $sSeqField)
	{
		return true;
	}
}
