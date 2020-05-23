<?php
  /**************************************************************************\
  * phpGroupWare - Setup                                                     *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  * This file written by Michael Dean<mdean@users.sourceforge.net>           *
  *  and Miles Lott<milosch@phpgroupware.org>                                *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	class SchemaManagerMysql
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
			$sTranslated = '';
			switch($sType)
			{
				case 'auto':
					$sTranslated = 'int(11) auto_increment';
					break;
				case 'blob':
					$sTranslated = 'blob';
					break;
				case 'bool':
					$sTranslated = 'tinyint(1)';
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
					$sTranslated =  'date';
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
							$sTranslated = 'double';
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
							$sTranslated = 'int';
							break;
						case 8:
							$sTranslated = 'bigint';
							break;
					}
					break;
				case 'longtext':
					$sTranslated = 'longtext';
					break;
				case 'text':
					$sTranslated = 'text';
					break;
				case 'timestamp':
					$sTranslated = 'datetime';
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

		public function TranslateDefault($sDefault, $sType)
		{
			switch ($sDefault)
			{
				case 'current_date':
				case 'current_timestamp':
					$sDefault = 'now()';
					break;
			}

			switch ($sType)
			{
				case 'int':
				case 'float':
				case 'bool':
				case 'decimal':
					return "DEFAULT $sDefault";
				case 'timestamp':
				case 'date':
					if (mb_strtolower($sDefault) == 'now()')
						return "DEFAULT '$sDefault'";
			}

			return "DEFAULT '$sDefault'";
		}

		/* Inverse of above, convert sql column types to array info */
		public function rTranslateType($sType, $iPrecision = 0, $iScale = 0)
		{
			$sTranslated = '';
			if ($sType == 'int' || $sType == 'tinyint' ||  $sType == 'smallint' || $sType == 'bigint')
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
				case 'bigint':
					$sTranslated = "'type' => 'int', 'precision' => 8";
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
				case 'datetime':
					$sTranslated = "'type' => 'timestamp'";
					break;
				case 'enum':
					/* Here comes a nasty assumption */
					/* $sTranslated =  "'type' => 'varchar', 'precision' => 255"; */
					$sTranslated =  "'type' => 'varchar', 'precision' => $iPrecision";
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
				case 'longtext':
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

		public function _GetColumns($oProc, $sTableName, &$sColumns, $sDropColumn = '')
		{
			$sColumns = '';
			$this->pk = array();
			$this->fk = array();
			$this->ix = array();
			$this->uc = array();

			/* Field, Type, Null, Key, Default, Extra */
			$oProc->m_odb->query("describe $sTableName");
			while ($oProc->m_odb->next_record())
			{
				$type = $default = $null = $nullcomma = $prec = $scale = $ret = $colinfo = $scales = '';
				if ($sColumns != '')
				{
					$sColumns .= ',';
				}
				$sColumns .= $oProc->m_odb->f(0);

				/* The rest of this is used only for SQL->array */
				$colinfo = explode('(',$oProc->m_odb->f(1));
				$prec = preg_replace('/[\)]/', '', $colinfo[1]);
				$scales = explode(',',$prec);

				if($colinfo[0] == 'enum')
				{
					/* set prec to length of longest enum-value */
                    $prec = 0;
                    foreach ($scales as $nul => $name)
					{
						if($prec < (mb_strlen($name) - 2))
						{
							/* -2 as name is like "'name'" */
							$prec = (mb_strlen($name) - 2);
						}
					}
				}
				elseif ($scales[1])
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
				if ($oProc->m_odb->f(4) != '')
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
			$this->sCol[count($this->sCol) - 1] = mb_substr($this->sCol[count($this->sCol) - 1],0,-2) . "\n";

			return false;
		}

		public function RefreshTable($oProc, $sTableName, &$aTableDef)
		{
			$sSequenceSQL = '';
			$sTableSQL = '';

			$oProc->_GetTableSQL($sTableName, $aTableDef, $sTableSQL, $sSequenceSQL);
			if ($sTableSQL == '')
				return false;

			$aArray = array();

			$oProc->query('START TRANSACTION');
			$bRetVal = $this->_DropAllConstraints($oProc, $aArray, $sTableName);
			if ($bRetVal)
				$bRetVal = !!($oProc->query("ALTER TABLE $sTableName RENAME tmp_$sTableName"));

			if ($bRetVal)
				$bRetVal = !!($oProc->query("CREATE TABLE $sTableName ($sTableSQL) ENGINE=InnoDB"));

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

		public function _DropAllConstraints($oProc, &$aTables, $sTable)
		{
			// Drop all constraints in preparation for a table schema refresh
			global $DEBUG;
			if ($DEBUG) { echo '<br>_DropAllConstraints ', $sTable; }

			$oDB = $oProc->m_odb;
			$oProc->m_odb->query("SHOW INDEX FROM $sTable");

			$aPK = array();
			while ($oProc->m_odb->next_record())
			{
				if (mb_strtoupper($oProc->m_odb->f(2)) == 'PRIMARY')
				{
					array_push($aPK, $oProc->m_odb->f(4));
					continue;
				}
				
				// returns one row per field in an index, so skip everything but the first field
				if ($oProc->m_odb->f(3) != 1)
					continue;

				$oDB->query("ALTER TABLE $sTable DROP INDEX " . $oProc->m_odb->f(2));
			}
			
			if (count($aPK) > 0)
			{
				if (count($aPK) > 1)
				{
					// If we have more than 1 field in the key, it's not an auto incrementing field
					// MySQL will not drop the primary key for auto incrementing fields
					$oDB->query("ALTER TABLE $sTable DROP PRIMARY KEY");
				}
				else
				{
					$oProc->m_odb->query("DESCRIBE $sTable");
					while ($oProc->m_odb->next_record())
					{
						if (in_array($oProc->m_odb->f(0), $aPK) && $oProc->m_odb->f(5) != 'auto_increment')
						{
							$oDB->query("ALTER TABLE $sTable DROP PRIMARY KEY");
							break;
						}
					}
				}
			}

			return true;
		}

		public function DropPrimaryKey($oProc, &$aTables, $sTable)
		{
			return !!($oProc->m_odb->query("ALTER TABLE $sTable DROP PRIMARY KEY"));
		}

		public function CreatePrimaryKey($oProc, &$aTables, $sTable, &$aFields)
		{
			if (count($aFields) < 1)
				return true;

			return !!($oProc->m_odb->query("ALTER TABLE $sTable ADD PRIMARY KEY (" . join(',', $aFields) . ')'));
		}

		public function DropTable($oProc, &$aTables, $sTableName)
		{
			return !!($oProc->m_odb->query("DROP TABLE " . $sTableName));
		}

		public function DropColumn($oProc, &$aTables, $sTableName, $aNewTableDef, $sColumnName, $bCopyData = true)
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

		public function RenameTable($oProc, &$aTables, $sOldTableName, $sNewTableName)
		{
			return !!($oProc->m_odb->query("ALTER TABLE $sOldTableName RENAME $sNewTableName"));
		}

		public function RenameColumn($oProc, &$aTables, $sTableName, $sOldColumnName, $sNewColumnName, $bCopyData = true)
		{
			/*
			 TODO: This really needs testing - it can affect primary keys, and other table-related objects
			 like sequences and such
			*/
			global $DEBUG;
			if ($DEBUG) { echo '<br>RenameColumn: calling _GetFieldSQL for ' . $sNewColumnName; }
			if ($oProc->_GetFieldSQL($aTables[$sTableName]["fd"][$sNewColumnName], $sNewColumnSQL))
			{
				return !!($oProc->m_odb->query("ALTER TABLE $sTableName CHANGE $sOldColumnName $sNewColumnName " . $sNewColumnSQL));
			}
			return false;
		}

		public function AlterColumn($oProc, &$aTables, $sTableName, $sColumnName, &$aColumnDef, $bCopyData = true)
		{
			global $DEBUG;
			if ($DEBUG) { echo '<br>AlterColumn: calling _GetFieldSQL for ' . $sNewColumnName; }
			if ($oProc->_GetFieldSQL($aTables[$sTableName]["fd"][$sColumnName], $sNewColumnSQL))
			{
				return !!($oProc->m_odb->query("ALTER TABLE $sTableName MODIFY $sColumnName " . $sNewColumnSQL));
				/* return !!($oProc->m_odb->query("ALTER TABLE $sTableName CHANGE $sColumnName $sColumnName " . $sNewColumnSQL)); */
			}

			return false;
		}

		public function AddColumn($oProc, &$aTables, $sTableName, $sColumnName, &$aColumnDef)
		{
			$oProc->_GetFieldSQL($aColumnDef, $sFieldSQL);
			$query = "ALTER TABLE $sTableName ADD COLUMN $sColumnName $sFieldSQL";

			return !!($oProc->m_odb->query($query));
		}

		public function GetSequenceSQL($sTableName, &$sSequenceSQL)
		{
			$sSequenceSQL = '';
			return true;
		}

		public function CreateTable($oProc, &$aTables, $sTableName, $aTableDef)
		{
			if ($oProc->_GetTableSQL($sTableName, $aTableDef, $sTableSQL, $sSequenceSQL))
			{
				$query = "CREATE TABLE $sTableName ($sTableSQL) ENGINE=InnoDB";
				$retVal = ($oProc->m_odb->query($query) !== -1);
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
			
			return ($oProc->m_odb->Query($sSQL) !== -1);
		}

		public function DropIndex($oProc, $aTables, $sTableName, $sIndexName)
		{
			return ($oProc->m_odb->Query("DROP INDEX $sIndexName") !== -1);
		}

		public function UpdateSequence($oProc, $sTableName, $sSeqField)
		{
			return true;
		}
	}
