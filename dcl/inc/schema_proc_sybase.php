<?php
/*
 * $Id$
 *
 * Double Choco Latte - Source Configuration Management System
 * Copyright (C) 1999  Michael L. Dean & Tim R. Norman
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
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

class schema_proc_sybase
{
	var $m_sStatementTerminator;
	/* Following added to convert sql to array */
	var $sCol = array();
	var $pk = array();
	var $fk = array();
	var $ix = array();
	var $uc = array();

	function schema_proc_sybase()
	{
		$this->m_sStatementTerminator = ';';
	}

	/* Return a type suitable for DDL */
	function TranslateType($sType, $iPrecision = 0, $iScale = 0)
	{
		$sTranslated = '';
		switch($sType)
		{
			case 'auto':
				$sTranslated = 'numeric(11, 0) identity';
				break;
			case 'blob':
				$sTranslated = 'image'; /* wonder how well PHP will support this??? */
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
			case 'date':
				$sTranslated = 'smalldatetime';
				break;
			case 'decimal':
				$sTranslated =  sprintf("decimal(%d,%d)", $iPrecision, $iScale);
				break;
			case 'float':
				switch ($iPrecision)
				{
					case 4:
						$sTranslated = 'float';
						break;
					case 8:
						$sTranslated = 'real';
						break;
				}
				break;
			case 'int':
				switch ($iPrecision)
				{
					case 2:
						$sTranslated = 'smallint';
						break;
					case 4:
					case 8:
						$sTranslated = 'int';
						break;
				}
				break;
			case 'longtext':
			case 'text':
				$sTranslated = 'text';
				break;
			case 'timestamp':
				$sTranslated = 'datetime';
				break;
			case 'bool':
				$sTranslated = 'bit';
				break;
			case 'varchar':
				if ($iPrecision > 0 && $iPrecision < 256)
				{
					$sTranslated =  sprintf("varchar(%d)", $iPrecision);
				}
				if ($iPrecision > 255)
				{
					$sTranslated =  'text';
				}
				break;
		}
		return $sTranslated;
	}

	function TranslateDefault($sDefault, $sType)
	{
		switch ($sDefault)
		{
			case 'current_date':
			case 'current_timestamp':
			case 'now()':
				$sDefault = 'GetDate()';
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
				if (strtolower($sDefault) == 'getdate()')
					return "DEFAULT ($sDefault)";
		}

		return "DEFAULT ('$sDefault')";
	}

	// Inverse of above, convert sql column types to array info
	function rTranslateType($sType, $iPrecision = 0, $iScale = 0)
	{
		$sTranslated = '';
		if ($sType == 'int' || $sType == 'tinyint' ||  $sType == 'smallint')
		{
			if ($iPrecision > 8)
			{
				$iPrecision = 8;
			}
			elseif($iPrecision > 4)
			{
				$iPrecision = 4;
			}
			else
			{
				$iPrecision = 2;
			}
		}
		switch($sType)
		{
			case 'tinyint':
			case 'smallint':
				$sTranslated = "'type' => 'int', 'precision' => 2";
				break;
			case 'int':
				$sTranslated = "'type' => 'int', 'precision' => 4";
				break;
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
			case 'decimal':
				$sTranslated = "'type' => 'decimal', 'precision' => $iPrecision, 'scale' => $iScale";
				break;
			case 'float':
			case 'double':
				$sTranslated = "'type' => 'float', 'precision' => $iPrecision";
				break;
			case 'smalldatetime':
				$sTranslated = "'type' => 'date'";
				break;
			case 'datetime':
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
			case 'image':
				$sTranslated = "'type' => 'blob'";
				break;
			case 'text':
				$sTranslated = "'type' => '$sType'";
				break;
			case 'bit':
				$sTranslated = "'type' => 'bool'";
				break;
		}
		return $sTranslated;
	}

	function GetPKSQL($sFields)
	{
		return "PRIMARY KEY($sFields)";
	}

	function GetUCSQL($sFields)
	{
		return "UNIQUE($sFields)";
	}

	function _GetColumns($oProc, $sTableName, &$sColumns, $sDropColumn = '')
	{
		$sColumns = '';
		$this->pk = array();
		$this->fk = array();
		$this->ix = array();
		$this->uc = array();

		// Field, Type, Null, Key, Default, Extra
		$oProc->m_odb->query("exec sp_columns '$sTableName'");
		while ($oProc->m_odb->next_record())
		{
			$type = $default = $null = $nullcomma = $prec = $scale = $ret = $colinfo = $scales = '';
			if ($sColumns != '')
			{
				$sColumns .= ',';
			}
			$sColumns .= $oProc->m_odb->f(0);

			// The rest of this is used only for SQL->array
			$colinfo = explode('(',$oProc->m_odb->f(1));
			$prec = preg_replace('/[\)]/', '', $colinfo[1]);
			$scales = explode(',',$prec);
			if ($scales[1])
			{
				$prec  = $scales[0];
				$scale = $scales[1];
			}
			$type = $this->rTranslateType($colinfo[0], $prec, $scale);

			if ($oProc->m_odb->f(2) == 'YES')
			{
				$null = "'nullable' => True";
			}
			else
			{
				$null = "'nullable' => False";
			}
			if ($oProc->m_odb->f(4))
			{
				$default = "'default' => '".$oProc->m_odb->f(4)."'";
				$nullcomma = ',';
			}
			else
			{
				$default = '';
				$nullcomma = '';
			}
			if ($oProc->m_odb->f(5))
			{
				$type = "'type' => 'auto'";
			}
			$this->sCol[] = "\t\t\t\t'" . $oProc->m_odb->f(0)."' => array(" . $type . ',' . $null . $nullcomma . $default . '),' . "\n";
			if ($oProc->m_odb->f(3) == 'PRI')
			{
				$this->pk[] = $oProc->m_odb->f(0);
			}
			if ($oProc->m_odb->f(3) == 'UNI')
			{
				$this->uc[] = $oProc->m_odb->f(0);
			}
			/* Hmmm, MUL could also mean unique, or not... */
			if ($oProc->m_odb->f(3) == 'MUL')
			{
				$this->ix[] = $oProc->m_odb->f(0);
			}
		}
		/* ugly as heck, but is here to chop the trailing comma on the last element (for php3) */
		$this->sCol[count($this->sCol) - 1] = substr($this->sCol[count($this->sCol) - 1],0,-2) . "\n";

		return false;
	}

	function RefreshTable($oProc, $sTableName, &$aTableDef)
	{
		$sSequenceSQL = '';
		$sTableSQL = '';

		$oProc->_GetTableSQL($sTableName, $aTableDef, $sTableSQL, $sSequenceSQL);
		if ($sTableSQL == '')
			return false;

		$aArray = array();

		$oProc->query('BEGIN TRAN');
		$bRetVal = $this->_DropAllConstraints($oProc, $aArray, $sTableName);
		if ($bRetVal)
			$bRetVal = !!($oProc->query("EXEC sp_rename '$sTableName', 'tmp_$sTableName'"));

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

	function _DropAllConstraints($oProc, &$aTables, $sTable)
	{
		// Drop all constraints in preparation for a table schema refresh
		global $DEBUG;
		if ($DEBUG) { echo '<br>_DropAllConstraints ', $sTable; }

		$oDB = $oProc->m_odb;
		$oProc->m_odb->query("select [name] from sysobjects where parent_obj = object_id('$sTable') and xtype in ('PK', 'C', 'D', 'F')");

		while ($oProc->m_odb->next_record())
		{
			$oDB->query("ALTER TABLE $sTable DROP CONSTRAINT " . $oProc->m_odb->f(0));
		}

		return true;
	}

	function DropPrimaryKey($oProc, &$aTables, $sTable)
	{
		global $DEBUG;
		if ($DEBUG) { echo '<br>DropPrimaryKey ', $sTable; }

		$sPrimaryKey = '';
		$oProc->m_odb->query("select [name] from sysobjects where parent_obj = object_id('$sTable') and xtype = 'PK'");
		if ($oProc->m_odb->next_record())
		{
			$sPrimaryKey = $oProc->m_odb->f(0);
			$oProc->m_odb->query("ALTER TABLE $sTable DROP CONSTRAINT $sPrimaryKey");
		}

		return true;
	}

	function CreatePrimaryKey($oProc, &$aTables, $sTable, &$aFields)
	{
		if (count($aFields) < 1)
			return true;

		return !!($oProc->m_odb->query("ALTER TABLE $sTable ADD CONSTRAINT PRIMARY KEY (" . join(',', $aFields) . ')'));
	}

	function DropTable($oProc, &$aTables, $sTableName)
	{
		return !!($oProc->m_odb->query("DROP TABLE " . $sTableName));
	}

	function DropColumn($oProc, &$aTables, $sTableName, $aNewTableDef, $sColumnName, $bCopyData = true)
	{
		if (is_array($sColumnName))
		{
			$retVal = true;
			foreach ($sColumnName as $sColumn)
			{
				$retVal = !!($oProc->m_odb->query("ALTER TABLE $sTableName DROP COLUMN $sColumn"));
				if (!$retVal)
					break;
			}
			
			return $retVal;
		}
			
		return !!($oProc->m_odb->query("ALTER TABLE $sTableName DROP COLUMN $sColumnName"));
	}

	function RenameTable($oProc, &$aTables, $sOldTableName, $sNewTableName)
	{
		return !!($oProc->m_odb->query("EXEC sp_rename '$sOldTableName', '$sNewTableName'"));
	}

	function RenameColumn($oProc, &$aTables, $sTableName, $sOldColumnName, $sNewColumnName, $bCopyData = true)
	{
		// This really needs testing - it can affect primary keys, and other table-related objects
		// like sequences and such
		global $DEBUG;
		if ($DEBUG) { echo '<br>RenameColumn: calling _GetFieldSQL for ' . $sNewColumnName; }
		if ($oProc->_GetFieldSQL($aTables[$sTableName]["fd"][$sNewColumnName], $sNewColumnSQL))
		{
			return !!($oProc->m_odb->query("EXEC sp_rename '$sTableName.$sOldColumnName', '$sNewColumnName'"));
		}
		return false;
	}

	function AlterColumn($oProc, &$aTables, $sTableName, $sColumnName, &$aColumnDef, $bCopyData = true)
	{
		global $DEBUG;
		if ($DEBUG) { echo '<br>AlterColumn: calling _GetFieldSQL for ' . $sNewColumnName; }
		if ($oProc->_GetFieldSQL($aTables[$sTableName]["fd"][$sColumnName], $sNewColumnSQL))
		{
			return !!($oProc->m_odb->query("ALTER TABLE $sTableName ALTER COLUMN $sColumnName " . $sNewColumnSQL));
		}

		return false;
	}

	function AddColumn($oProc, &$aTables, $sTableName, $sColumnName, &$aColumnDef)
	{
		$oProc->_GetFieldSQL($aColumnDef, $sFieldSQL);
		$query = "ALTER TABLE $sTableName ADD $sColumnName $sFieldSQL";

		return !!($oProc->m_odb->query($query));
	}

	function GetSequenceSQL($sTableName, &$sSequenceSQL)
	{
		$sSequenceSQL = '';
		return true;
	}

	function CreateTable($oProc, &$aTables, $sTableName, $aTableDef)
	{
		if ($oProc->_GetTableSQL($sTableName, $aTableDef, $sTableSQL, $sSequenceSQL))
		{
			// create sequence first since it will be needed for default
			if ($sSequenceSQL != '')
			{
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

	function CreateIndexes($oProc, $sTableName, $aIndexDef)
	{
		$retVal = true;
		if (is_array($aIndexDef) && count($aIndexDef) > 0)
		{
			foreach ($aIndexDef as $sIndexName => $aIndexColumns)
				$retVal = $this->CreateIndex($oProc, null, $sTableName, $sIndexName, $aIndexColumns);
		}
		
		return $retVal;
	}

	function CreateIndex($oProc, $aTables, $sTableName, $sIndexName, $aColumns)
	{
		$sColumns = join($aColumns, ',');
		$sSQL = "CREATE INDEX $sIndexName ON $sTableName ($sColumns)";
		
		return ($oProc->m_odb->Query($sSQL) != -1);
	}

	function DropIndex($oProc, $aTables, $sTableName, $sIndexName)
	{
		return ($oProc->m_odb->Query("DROP INDEX $sIndexName") != -1);
	}

	function UpdateSequence($oProc, $sTableName, $sSeqField)
	{
		return true;
	}
}
?>
