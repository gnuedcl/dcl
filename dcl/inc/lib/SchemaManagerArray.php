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

	class SchemaManagerArray
	{
		var $m_sStatementTerminator;

		public function __construct()
		{
			$this->m_sStatementTerminator = ';';
		}

		/* Return a type suitable for DDL abstracted array */
		public function TranslateType($sType, $iPrecision = 0, $iScale = 0)
		{
			$sTranslated = $sType;
			return $sTranslated;
		}

		public function TranslateDefault($sDefault)
		{
			return $sDefault;
		}

		public function GetPKSQL($sFields)
		{
			return '';
		}

		public function GetUCSQL($sFields)
		{
			return '';
		}

		public function _GetColumns($oProc, &$aTables, $sTableName, &$sColumns, $sDropColumn='')
		{
			$sColumns = '';
			foreach ($aTables[$sTableName]['fd'] as $sName => $aJunk)
			{
				if($sColumns != '')
				{
					$sColumns .= ',';
				}
				$sColumns .= $sName;
			}

			return True;
		}

		public function DropPrimaryKey($oProc, &$aTables, $sTable)
		{
			if (isset($aTables[$sTable]))
				$aTables[$sTable]['pk'] = array();

			return true;
		}

		public function CreatePrimaryKey($oProc, &$aTables, $sTable, &$aFields)
		{
			if (!is_array($aFields) || !isset($aTables[$sTable]))
				return false;

			$aTables[$sTable]['pk'] = $aFields;

			return true;
		}

		public function DropTable($oProc, &$aTables, $sTableName)
		{
			if(isset($aTables[$sTableName]))
			{
				unset($aTables[$sTableName]);
			}

			return True;
		}

		public function DropColumn($oProc, &$aTables, $sTableName, $aNewTableDef, $sColumnName, $bCopyData=True)
		{
			if(isset($aTables[$sTableName]))
			{
				if (is_array($sColumnName))
				{
					foreach ($sColumnName as $sColumn)
					{
						if(isset($aTables[$sTableName]['fd'][$sColumn]))
							unset($aTables[$sTableName]['fd'][$sColumn]);
					}
				}
				else if(isset($aTables[$sTableName]['fd'][$sColumnName]))
				{
					unset($aTables[$sTableName]['fd'][$sColumnName]);
				}
			}

			return True;
		}

		public function RenameTable($oProc, &$aTables, $sOldTableName, $sNewTableName)
		{
			$aNewTables = array();
			foreach ($aTables as $sTableName => $aTableDef)
			{
				if($sTableName == $sOldTableName)
				{
					$aNewTables[$sNewTableName] = $aTableDef;
				}
				else
				{
					$aNewTables[$sTableName] = $aTableDef;
				}
			}

			$aTables = $aNewTables;

			return True;
		}

		public function RenameColumn($oProc, &$aTables, $sTableName, $sOldColumnName, $sNewColumnName, $bCopyData=True)
		{
			if (isset($aTables[$sTableName]))
			{
				$aNewTableDef = array();
				foreach ($aTables[$sTableName]['fd'] as $sColumnName => $aColumnDef)
				{
					if($sColumnName == $sOldColumnName)
					{
						$aNewTableDef[$sNewColumnName] = $aColumnDef;
					}
					else
					{
						$aNewTableDef[$sColumnName] = $aColumnDef;
					}
				}

				$aTables[$sTableName]['fd'] = $aNewTableDef;

				foreach ($aTables[$sTableName]['pk'] as $key => $sColumnName)
				{
					if($sColumnName == $sOldColumnName)
					{
						$aTables[$sTableName]['pk'][$key] = $sNewColumnName;
					}
				}

				foreach ($aTables[$sTableName]['uc'] as $key => $sColumnName)
				{
					if($sColumnName == $sOldColumnName)
					{
						$aTables[$sTableName]['uc'][$key] = $sNewColumnName;
					}
				}
			}

			return True;
		}

		public function AlterColumn($oProc, &$aTables, $sTableName, $sColumnName, &$aColumnDef, $bCopyData=True)
		{
			if(isset($aTables[$sTableName]))
			{
				if(isset($aTables[$sTableName]['fd'][$sColumnName]))
				{
					$aTables[$sTableName]['fd'][$sColumnName] = $aColumnDef;
				}
			}

			return True;
		}

		public function AddColumn($oProc, &$aTables, $sTableName, $sColumnName, &$aColumnDef)
		{
			if(isset($aTables[$sTableName]))
			{
				if(!isset($aTables[$sTableName]['fd'][$sColumnName]))
				{
					$aTables[$sTableName]['fd'][$sColumnName] = $aColumnDef;
				}
			}

			return True;
		}

		public function CreateTable($oProc, &$aTables, $sTableName, $aTableDef)
		{
			if(!isset($aTables[$sTableName]))
			{
				$aTables[$sTableName] = $aTableDef;
			}

			return True;
		}
		
		public function CreateIndex($oProc, &$aTables, $sTableName, $sIndexName, $aColumns)
		{
			if (isset($aTables[$sTableName]))
			{
				$aTables[$sTableName]['ix'][$sIndexName] = $aColumns;
			}
			
			return true;
		}
		
		public function DropIndex($oProc, &$aTables, $sTableName, $sIndexName)
		{
			if (isset($aTables[$sTableName]) && isset($aTables[$sTableName]['ix'][$sIndexName]))
				unset($aTables[$sTableName]['ix'][$sIndexName]);
				
			return true;
		}

		public function UpdateSequence($oProc, $sTableName, $sSeqField)
		{
			return true;
		}
	}
