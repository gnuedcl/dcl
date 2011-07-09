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

class DCLDataImport
{
	var $oDB;
	var $hFile;

	function DCLDataImport()
	{
		$this->hFile = null;

		$this->oDB = new DbProvider;
		$this->oDB->Connect();

		$GLOBALS['__import_content__'] = '';
	}

	function ImportTable($sTableName, $sLang = 'en')
	{
		$this->hFile = fopen(DCL_ROOT . 'setup/data/' . $sLang . '.' . $sTableName . '.txt', 'r');
		if (!$this->hFile)
		{
			$GLOBALS['__import_content__'] .= '<br/>' . _NGIMG . "&nbsp;<b>Import Table $sTableName Failed.  File Open Failed.</b>";
			return false;
		}

		LoadSchema($sTableName);

		$bFirstRow = true;
		$aFields = array();
		$sSQL = "INSERT INTO $sTableName (";
		while ($aRow = fgetcsv($this->hFile, 1000, "\t"))
		{
			if ($bFirstRow)
			{
				$aFields = $aRow;
				$sCols = '';

				reset($aRow);
				while (list(, $sField) = each($aRow))
				{
					if ($sCols != '')
						$sCols .= ', ';

					$sCols .= $sField;
				}

				$sSQL .= "$sCols) VALUES (";

				$bFirstRow = false;
			}
			else
			{
				$sInsert = $sSQL;
				$sValues = '';
				for ($i = 0; $i < count($aFields); $i++)
				{
					if ($i >= count($aRow) || $aRow[$i] == '')
					{
						// if field is blank or we don't have all fields listed, append null
						if ($sValues != '')
							$sValues .= ', ';

						$sValues .= 'NULL';
					}
					else
					{
						if ($sValues != '')
							$sValues .= ', ';
						
						$sType = $GLOBALS['phpgw_baseline'][$sTableName]['fd'][$aFields[$i]]['type'];
						if ($sType == 'int' || $sType == 'float')
						{
							$sValues .= $aRow[$i];
						}
						else if ($sType == 'date' || $sType == 'timestamp')
						{
							if ($aRow[$i] == 'now()')
								$sValues .= $this->oDB->GetDateSQL();
							else
								$sValues .= "'" . $aRow[$i] . "'";
						}
						else
						{
							$sValues .= "'" . $aRow[$i] . "'";
						}
					}
				}

				$sInsert .= $sValues . ')';

				$this->oDB->Query($sInsert);
			}
		}

		fclose($this->hFile);

		$GLOBALS['__import_content__'] .= '<br/>' . _OKIMG . "&nbsp;<b>Import Table $sTableName Successful</b>";
		return true;
	}

	function FinalizeData()
	{
		$this->oDB->query('INSERT INTO attributesetsmap SELECT 1, 1, id, 1 FROM actions');
		$this->oDB->query('INSERT INTO attributesetsmap SELECT 1, 2, id, weight FROM priorities');
		$this->oDB->query('INSERT INTO attributesetsmap SELECT 1, 3, id, weight FROM severities');
		$this->oDB->query('INSERT INTO attributesetsmap SELECT 1, 4, id, 1 FROM statuses');

		$this->oDB->query('INSERT INTO attributesetsmap SELECT 2, 1, id, 1 FROM actions');
		$this->oDB->query('INSERT INTO attributesetsmap SELECT 2, 2, id, weight FROM priorities');
		$this->oDB->query('INSERT INTO attributesetsmap SELECT 2, 3, id, weight FROM severities');
		$this->oDB->query('INSERT INTO attributesetsmap SELECT 2, 4, id, 1 FROM statuses');

		$GLOBALS['__import_content__'] .= '<br/>' . _OKIMG . "&nbsp;<b>Finalize Data Successful</b>";
	}
}

function dcl_import_default_data()
{
	$o = new DCLDataImport;
	$o->ImportTable('actions');
	$o->ImportTable('attributesets');
	$o->ImportTable('dcl_config');
	$o->ImportTable('dcl_status_type');
	$o->ImportTable('dcl_contact');
	$o->ImportTable('personnel');
	$o->ImportTable('statuses');
	$o->ImportTable('dcl_entity');
	$o->ImportTable('dcl_perm');
	$o->ImportTable('dcl_entity_perm');
	$o->ImportTable('dcl_role');
	$o->ImportTable('dcl_role_perm');
	$o->ImportTable('dcl_user_role');

	$o->FinalizeData();
}

dcl_import_default_data();
?>
