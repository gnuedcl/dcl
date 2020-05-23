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

	class SchemaManager
	{
		var $m_oTranslator;
		var $m_oDeltaProc;
		var $m_odb;
		var $m_aTables;
		var $m_bDeltaOnly;

		public function __construct($dbms)
		{
			$schemaProcDbms = 'SchemaManager' . ucfirst($dbms);
			$this->m_oTranslator = new $schemaProcDbms;
			$this->m_oDeltaProc = new SchemaManagerArray();
			$this->m_aTables = array();
			$this->m_bDeltaOnly = False; // Default to false here in case it's just a CreateTable script
		}

		public function GenerateScripts($aTables, $bOutputHTML=False)
		{
			if (!is_array($aTables))
			{
				return False;
			}

			$this->m_aTables = $aTables;

			$sAllTableSQL = '';
			foreach ($this->m_aTables as $sTableName => $aTableDef)
			{
				$sSequenceSQL = '';
				if($this->_GetTableSQL($sTableName, $aTableDef, $sTableSQL, $sSequenceSQL))
				{
					$sTableSQL = "CREATE TABLE $sTableName (\n$sTableSQL\n)"
						. $this->m_oTranslator->m_sStatementTerminator;
					if($sSequenceSQL != '')
					{
						$sAllTableSQL .= $sSequenceSQL . "\n";
					}
					$sAllTableSQL .= $sTableSQL . "\n\n";
				}
				else
				{
					if($bOutputHTML)
					{
						print('<br>Failed generating script for <b>' . $sTableName . '</b><br>');
					}

					return false;
				}
			}

			if($bOutputHTML)
			{
				print('<pre>' . $sAllTableSQL . '</pre><br><br>');
			}

			return True;
		}

		public function ExecuteScripts($aTables, $bOutputHTML=False)
		{
			if(!is_array($aTables) || !IsSet($this->m_odb))
			{
				if ($bOutputHTML)
					echo '<br>' . _NGIMG . '&nbsp;Tables not passed in proper format or database object not created.';
				return False;
			}

			reset($aTables);
			$this->m_aTables = $aTables;

			foreach ($aTables as $sTableName => $aTableDef)
			{
				if($this->CreateTable($sTableName, $aTableDef))
				{
					if($bOutputHTML)
					{
						echo '<br>' . _OKIMG . '&nbsp;Create Table <b>' . $sTableName . '</b>';
					}
				}
				else
				{
					if($bOutputHTML)
					{
						echo '<br>' . _NGIMG . '&nbsp;Create Table Failed For <b>' . $sTableName . '</b>';
					}

					return False;
				}
			}

			return True;
		}

		public function DropAllTables($aTables, $bOutputHTML=False)
		{
			if(!is_array($aTables) || !isset($this->m_odb))
			{
				return False;
			}

			$this->m_aTables = $aTables;

			foreach ($this->m_aTables as $sTableName => $aTableDef)
			{
				if($this->DropTable($sTableName))
				{
					if($bOutputHTML)
					{
						echo '<br>Drop Table <b>' . $sTableSQL . '</b>';
					}
				}
				else
				{
					return False;
				}
			}

			return True;
		}

		public function DropPrimaryKey($sTableName)
		{
			$retVal = $this->m_oDeltaProc->DropPrimaryKey($this, $this->m_aTables, $sTableName);
			if($this->m_bDeltaOnly)
			{
				return $retVal;
			}

			return $retVal && $this->m_oTranslator->DropPrimaryKey($this, $this->m_aTables, $sTableName);
		}

		public function CreatePrimaryKey($sTableName, $aFields)
		{
			$retVal = $this->m_oDeltaProc->CreatePrimaryKey($this, $this->m_aTables, $sTableName, $aFields);
			if($this->m_bDeltaOnly)
			{
				return $retVal;
			}

			return $retVal && $this->m_oTranslator->CreatePrimaryKey($this, $this->m_aTables, $sTableName, $aFields);
		}

		public function DropTable($sTableName)
		{
			$retVal = $this->m_oDeltaProc->DropTable($this, $this->m_aTables, $sTableName);
			if($this->m_bDeltaOnly)
			{
				return $retVal;
			}

			return $retVal && $this->m_oTranslator->DropTable($this, $this->m_aTables, $sTableName);
		}

		public function DropColumn($sTableName, $aTableDef, $sColumnName, $bCopyData = true)
		{
			$retVal = $this->m_oDeltaProc->DropColumn($this, $this->m_aTables, $sTableName, $aTableDef, $sColumnName, $bCopyData);
			if($this->m_bDeltaOnly)
			{
				return $retVal;
			}

			return $retVal && $this->m_oTranslator->DropColumn($this, $this->m_aTables, $sTableName, $aTableDef, $sColumnName, $bCopyData);
		}

		public function RefreshTable($sTableName)
		{
			// This function just refreshes a table, useful if base types and names don't change, but columns
			// may be expanded.  It probably won't work if you're shrinking columns
			if ($this->m_bDeltaOnly)
				return true;

			if (!isset($this->m_aTables[$sTableName]))
				return false;

			return $this->m_oTranslator->RefreshTable($this, $sTableName, $this->m_aTables[$sTableName]);
		}

		public function RenameTable($sOldTableName, $sNewTableName)
		{
			$retVal = $this->m_oDeltaProc->RenameTable($this, $this->m_aTables, $sOldTableName, $sNewTableName);
			if($this->m_bDeltaOnly)
			{
				return $retVal;
			}

			return $retVal && $this->m_oTranslator->RenameTable($this, $this->m_aTables, $sOldTableName, $sNewTableName);
		}

		public function RenameColumn($sTableName, $sOldColumnName, $sNewColumnName, $bCopyData=True)
		{
			$retVal = $this->m_oDeltaProc->RenameColumn($this, $this->m_aTables, $sTableName, $sOldColumnName, $sNewColumnName, $bCopyData);
			if($this->m_bDeltaOnly)
			{
				return $retVal;
			}

			return $retVal && $this->m_oTranslator->RenameColumn($this, $this->m_aTables, $sTableName, $sOldColumnName, $sNewColumnName, $bCopyData);
		}

		public function AlterColumn($sTableName, $sColumnName, $aColumnDef, $bCopyData=True)
		{
			$retVal = $this->m_oDeltaProc->AlterColumn($this, $this->m_aTables, $sTableName, $sColumnName, $aColumnDef, $bCopyData);
			if($this->m_bDeltaOnly)
			{
				return $retVal;
			}

			return $retVal && $this->m_oTranslator->AlterColumn($this, $this->m_aTables, $sTableName, $sColumnName, $aColumnDef, $bCopyData);
		}

		public function AddColumn($sTableName, $sColumnName, $aColumnDef)
		{
			$retVal = $this->m_oDeltaProc->AddColumn($this, $this->m_aTables, $sTableName, $sColumnName, $aColumnDef);
			if($this->m_bDeltaOnly)
			{
				return $retVal;
			}

			return $retVal && $this->m_oTranslator->AddColumn($this, $this->m_aTables, $sTableName, $sColumnName, $aColumnDef);
		}

		public function CreateTable($sTableName, $aTableDef)
		{
			$retVal = $this->m_oDeltaProc->CreateTable($this, $this->m_aTables, $sTableName, $aTableDef);
			if($this->m_bDeltaOnly)
			{
				return $retVal;
			}

			return $retVal && $this->m_oTranslator->CreateTable($this, $this->m_aTables, $sTableName, $aTableDef);
		}
		
		public function CreateIndex($sTableName, $sIndexName, $aColumns)
		{
			$retVal = $this->m_oDeltaProc->CreateIndex($this, $this->m_aTables, $sTableName, $sIndexName, $aColumns);
			if ($this->m_bDeltaOnly)
				return $retVal;
				
			return $retVal && $this->m_oTranslator->CreateIndex($this, $this->m_aTables, $sTableName, $sIndexName, $aColumns);
		}

		public function DropIndex($sTableName, $sIndexName)
		{
			$retVal = $this->m_oDeltaProc->DropIndex($this, $this->m_aTables, $sTableName, $sIndexName);
			if ($this->m_bDeltaOnly)
				return $retVal;
				
			return $retVal && $this->m_oTranslator->DropIndex($this, $this->m_aTables, $sTableName, $sIndexName);
		}

		public function f($value)
		{
			if($this->m_bDeltaOnly)
			{
				// Don't care, since we are processing deltas only
				return False;
			}

			return $this->m_odb->f($value);
		}

		public function num_rows()
		{
			if($this->m_bDeltaOnly)
			{
				// If not False, we will cause while loops calling us to hang
				return False;
			}

			return $this->m_odb->num_rows();
		}

		public function next_record()
		{
			if($this->m_bDeltaOnly)
			{
				// If not False, we will cause while loops calling us to hang
				return False;
			}

			return $this->m_odb->next_record();
		}

		public function query($sQuery, $line='', $file='')
		{
			if($this->m_bDeltaOnly)
			{
				// Don't run this query, since we are processing deltas only
				return True;
			}

			return $this->m_odb->query($sQuery, $line, $file);
		}

		public function execute($sQuery, $line='', $file='')
		{
			if($this->m_bDeltaOnly)
			{
				// Don't run this query, since we are processing deltas only
				return True;
			}

			return $this->m_odb->Execute($sQuery, $line, $file);
		}

		public function _GetTableSQL($sTableName, $aTableDef, &$sTableSQL, &$sSequenceSQL)
		{
			global $DEBUG;

			if(!is_array($aTableDef))
			{
				return False;
			}

			$sTableSQL = '';
			foreach ($aTableDef['fd'] as $sFieldName => $aFieldAttr)
			{
				$sFieldSQL = '';
				if($this->_GetFieldSQL($aFieldAttr, $sFieldSQL))
				{
					if($sTableSQL != '')
					{
						$sTableSQL .= ",\n";
					}

					$sTableSQL .= "$sFieldName $sFieldSQL";

					if($aFieldAttr['type'] == 'auto')
					{
						$this->m_oTranslator->GetSequenceSQL($sTableName, $sSequenceSQL);
						if($sSequenceSQL != '')
						{
							$sTableSQL .= sprintf(" DEFAULT nextval('seq_%s')", $sTableName);
						}
					}
				}
				else
				{
					if($DEBUG) { echo 'GetFieldSQL failed for ' . $sFieldName; }
					return False;
				}
			}

			$sUCSQL = '';
			$sPKSQL = '';

			if(count($aTableDef['pk']) > 0)
			{
				if(!$this->_GetPK($aTableDef['pk'], $sPKSQL))
				{
					if($bOutputHTML)
					{
						print('<br>Failed getting primary key<br>');
					}

					return False;
				}
			}

			if(count($aTableDef['uc']) > 0)
			{
				if (!isset($aTableDef['uc'][0]))
				{
					// allow array of unique constraints
                    foreach ($aTableDef['uc'] as $ucName => $ucFieldSpec)
					{
						if ($sUCSQL != '')
							$sUCSQL .= ",\n";

						if(!$this->_GetUC($ucFieldSpec, $sUCSQL))
						{
							if($bOutputHTML)
							{
								print('<br>Failed getting unique constraint<br>');
							}

							return False;
						}
					}
				}
				else if(!$this->_GetUC($aTableDef['uc'], $sUCSQL))
				{
					if($bOutputHTML)
					{
						print('<br>Failed getting unique constraint<br>');
					}

					return False;
				}
			}

			if($sPKSQL != '')
			{
				$sTableSQL .= ",\n" . $sPKSQL;
			}

			if($sUCSQL != '')
			{
				$sTableSQL .= ",\n" . $sUCSQL;
			}

			return True;
		}

		// Get field DDL
		public function _GetFieldSQL($aField, &$sFieldSQL)
		{
			global $DEBUG;
			if($DEBUG) { echo'<br>_GetFieldSQL(): Incoming ARRAY: '; var_dump($aField); }
			if(!is_array($aField))
			{
				return false;
			}

			$sType = '';
			$iPrecision = 0;
			$iScale = 0;
			$sDefault = '';
			$bNullable = true;

			foreach ($aField as $sAttr => $vAttrVal)
			{
				switch ($sAttr)
				{
					case 'type':
						$sType = $vAttrVal;
						break;
					case 'precision':
						$iPrecision = (int)$vAttrVal;
						break;
					case 'scale':
						$iScale = (int)$vAttrVal;
						break;
					case 'default':
						$sDefault = $vAttrVal;
						if($DEBUG) { echo'<br>_GetFieldSQL(): Default="' . $sDefault . '"'; }
						break;
					case 'nullable':
						$bNullable = $vAttrVal;
						break;
				}
			}

			// Translate the type for the DBMS
			if ($sFieldSQL = $this->m_oTranslator->TranslateType($sType, $iPrecision, $iScale))
			{
				if ($sDefault != '')
				{
					if($DEBUG) { echo'<br>_GetFieldSQL(): Calling TranslateDefault for "' . $sDefault . '"'; }
					// Get default DDL - useful for differences in date defaults (eg, now() vs. getdate())
					$sFieldSQL .= ' ' . $this->m_oTranslator->TranslateDefault($sDefault, $aField['type']);
				}

				if ($bNullable == false)
				{
					$sFieldSQL .= ' NOT NULL';
				}
				else
				{
					$sFieldSQL .= ' NULL';
				}

				if($DEBUG) { echo'<br>_GetFieldSQL(): Outgoing SQL:   ' . $sFieldSQL; }
				return true;
			}

			if($DEBUG) { echo '<br>Failed to translate field: type[' . $sType . '] precision[' . $iPrecision . '] scale[' . $iScale . ']<br>'; }

			return false;
		}

		public function _GetPK($aFields, &$sPKSQL)
		{
			$sPKSQL = '';
			if(count($aFields) < 1)
			{
				return true;
			}

			$sFields = '';
			foreach ($aFields as $key => $sField)
			{
				if($sFields != '')
				{
					$sFields .= ',';
				}
				$sFields .= $sField;
			}

			$sPKSQL = $this->m_oTranslator->GetPKSQL($sFields);

			return true;
		}

		public function _GetUC($aFields, &$sUCSQL)
		{
			$sUCSQL = '';
			if(count($aFields) < 1)
			{
				return True;
			}

			$sFields = '';
			foreach ($aFields as $key => $sField)
			{
				if($sFields != '')
				{
					$sFields .= ',';
				}
				$sFields .= $sField;
			}

			$sUCSQL .= $this->m_oTranslator->GetUCSQL($sFields);

			return true;
		}

		public function UpdateSequence($sTableName, $sFieldName)
		{
			if ($this->m_bDeltaOnly)
				return true;
				
			return $this->m_oTranslator->UpdateSequence($this, $sTableName, $sFieldName);
		}
	}
