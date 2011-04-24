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

LoadStringResource('bo');
LoadStringResource('chk');

class boChecklistTpl
{
	function GetTplPath($id, $bCreate = false)
	{
		global $dcl_info;

		$filePath = $dcl_info['DCL_FILE_PATH'] . '/checklists/templates';
		$dir[1] = $id % 10; // last digit
		$dir[2] = ($id - $dir[1]) % 100;
		$dir[3] = ($id - $dir[2] - $dir[1]) % 1000;
		$dir[4] = ($id - $dir[3] - $dir[2] - $dir[1]) % 10000;
		$dir[5] = ($id - $dir[4] - $dir[3] - $dir[2] - $dir[1]) % 100000;

		// make directories and build path as needed, except for last one ;-)
		for ($i = 5; $i > 2; $i--)
		{
			$filePath .= '/' . $dir[$i];
			if ($bCreate && !is_dir($filePath))
				mkdir($filePath, 0755);
		}

		$filePath .= '/' . $id . '.xml';

		return $filePath;
	}

	function show()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FORMTEMPLATES, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$oView = new boView();
		$oView->table = 'dcl_chklst_tpl';
		$oView->style = 'report';
		$oView->title = STR_CHK_CHECKLISTTEMPLATES;
		$oView->AddDef('columns', '', array('dcl_chklst_tpl_id', 'dcl_chklst_tpl_active', 'dcl_chklst_tpl_name'));
		$oView->AddDef('columnhdrs', '', array(STR_CMMN_ID, STR_CMMN_ACTIVE, STR_CHK_CHECKLISTNAME));

		$startrow = 0;
		$numrows = 25;
		if (IsSet($_REQUEST['startrow']))
		{
			if (($offset = DCL_Sanitize::ToInt($_REQUEST['startrow'])) === null)
				$offset = 0;
		}
			
		if (IsSet($_REQUEST['numrows']))
		{
			if (($numrows = DCL_Sanitize::ToInt($_REQUEST['numrows'])) === null)
				$numrows = 25;
		}

		$oView->startrow = $startrow;
		$oView->numrows = $numrows;

		$oDB = new ChecklistTemplateModel();
		if ($oDB->query($oView->GetSQL()) == -1)
			return;
			
		$allRecs = $oDB->FetchAllRows();

		$oTable = new TableHtmlHelper();
		$oTable->setCaption(STR_CHK_INITIATEDCHECKLISTS);
		$oTable->addColumn(STR_CMMN_ID, 'numeric');
		$oTable->addColumn(STR_CMMN_ACTIVE, 'string');
		$oTable->addColumn(STR_CHK_CHECKLISTNAME, 'string');

		if ($g_oSec->HasPerm(DCL_ENTITY_FORMS, DCL_PERM_VIEW))
			$oTable->addToolbar(menuLink('', 'menuAction=boChecklists.show'), STR_CMMN_BROWSE);

		if ($g_oSec->HasPerm(DCL_ENTITY_FORMTEMPLATES, DCL_PERM_VIEW))
			$oTable->addToolbar(menuLink('', 'menuAction=boChecklistTpl.show'), STR_CHK_TEMPLATES);
			
		if ($g_oSec->HasPerm(DCL_ENTITY_FORMTEMPLATES, DCL_PERM_ADD))
			$oTable->addToolbar(menuLink('', 'menuAction=boChecklistTpl.add'), STR_CHK_NEWTEMPLATE);

		if (count($allRecs) > 0 && $g_oSec->HasAnyPerm(array(DCL_ENTITY_FORMS => array($g_oSec->PermArray(DCL_PERM_MODIFY), $g_oSec->PermArray(DCL_PERM_DELETE)))))
		{
			$oTable->addColumn(STR_CMMN_OPTIONS, 'html');
			for ($i = 0; $i < count($allRecs); $i++)
			{
				$options = '';
				if ($g_oSec->HasPerm(DCL_ENTITY_FORMS, DCL_PERM_ADD))
				{
					$options = '<a href="' . menuLink('', 'menuAction=boChecklists.add&dcl_chklst_tpl_id=' . $allRecs[$i][0]) . '"';
					if ($allRecs[$i][1] != 'Y')
						$options .= ' disabled="disabled"';
						
					$options .= '>' . STR_CHK_INITIATE . '</a>';
				}
				
				if ($g_oSec->HasPerm(DCL_ENTITY_FORMTEMPLATES, DCL_PERM_VIEW, $allRecs[$i][0]))
				{
					if ($options != '')
						$options .= '&nbsp;|&nbsp;';

					$options .= '<a href="' . menuLink('', 'menuAction=boChecklistTpl.view&dcl_chklst_tpl_id=' . $allRecs[$i][0]) . '">' . STR_CMMN_VIEW . '</a>';
				}
		
				if ($g_oSec->HasPerm(DCL_ENTITY_FORMTEMPLATES, DCL_PERM_MODIFY, $allRecs[$i][0]))
				{
					if ($options != '')
						$options .= '&nbsp;|&nbsp;';

					$options .= '<a href="' . menuLink('', 'menuAction=boChecklistTpl.modify&dcl_chklst_tpl_id=' . $allRecs[$i][0]) . '">' . STR_CMMN_EDIT . '</a>';
				}
		
				if ($g_oSec->HasPerm(DCL_ENTITY_FORMTEMPLATES, DCL_PERM_DELETE, $allRecs[$i][0]))
				{
					if ($options != '')
						$options .= '&nbsp;|&nbsp;';

					$options .= '<a href="' . menuLink('', 'menuAction=boChecklistTpl.delete&dcl_chklst_tpl_id=' . $allRecs[$i][0]) . '">' . STR_CMMN_DELETE . '</a>';
				}

				$allRecs[$i][] = $options;
			}
		}
		
		$oTable->setData($allRecs);
		$oTable->setShowRownum(true);
		$oTable->render();
	}

	function add()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FORMTEMPLATES, DCL_PERM_ADD))
			throw new PermissionDeniedException();
			
		$obj = new htmlChklstTpl();
		$obj->add();
	}

	function dbadd()
	{
		global $dcl_info, $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FORMTEMPLATES, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$sFileName = DCL_Sanitize::ToFileName('userfile');
		if ($sFileName !== null)
		{
			$sName = '';

			$oXML = new xmlDoc();
			$oXML->ParseFile($sFileName);
			$oXML->FindChildNode($oXML->root, 'Name');
			if ($oXML->currentNode != NULL)
			{
				$sName = $oXML->currentNode->data;
				$oXML->FindChildNode($oXML->root, 'Version');
				if ($oXML->currentNode != NULL)
				{
					if ($sName != '')
						$sName .= ' ';

					$sName .= $oXML->currentNode->data;
				}

				echo htmlspecialchars($sName);
				$o = new ChecklistTemplateModel();
				$o->dcl_chklst_tpl_name = $sName;
				$o->Add();
				if (IsSet($o->dcl_chklst_tpl_id))
				{
					// Insert successful, now stow file in its place
					$filePath = $this->GetTplPath($o->dcl_chklst_tpl_id, true);

					if (!copy($sFileName, $filePath))
						echo STR_BO_UPLOADERR;
				}
			}
		}

		$this->show();
	}

	function modify()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FORMTEMPLATES, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$obj = new htmlChklstTpl();
		$obj->modify();
	}

	function dbmodify()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FORMTEMPLATES, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['dcl_chklst_tpl_id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$o = new ChecklistTemplateModel();
		if ($o->Load($iID) != -1)
		{
			$sFileName = @DCL_Sanitize::ToFileName('userfile');
			if ($sFileName !== null)
			{
				$sName = '';

				$oXML = new xmlDoc();
				$oXML->ParseFile($sFileName);
				$oXML->FindChildNode($oXML->root, 'Name');
				if ($oXML->currentNode != NULL)
				{
					$sName = $oXML->currentNode->data;
					$oXML->FindChildNode($oXML->root, 'Version');
					if ($oXML->currentNode != NULL)
					{
						if ($sName != '')
							$sName .= ' ';

						$sName .= $oXML->currentNode->data;
					}

					$o->dcl_chklst_tpl_name = $sName;
				}
			}

			$o->dcl_chklst_tpl_active = @DCL_Sanitize::ToYN($_REQUEST['dcl_chklst_tpl_active']);
			$o->BeginTransaction();
			$o->Edit();

			if ($sFileName !== null)
			{
				// Insert successful, now stow file in its place
				$filePath = $this->GetTplPath($o->dcl_chklst_tpl_id, true);

				if (copy($sFileName, $filePath))
				{
					$o->EndTransaction();
				}
				else
				{
					$o->RollbackTransaction();
					echo STR_BO_UPLOADERR;
				}
			}
			else
			{
				$o->EndTransaction();
			}
		}

		$this->show();
	}

	function delete()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FORMTEMPLATES, DCL_PERM_DELETE))
			throw new PermissionDeniedException();
			
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['dcl_chklst_tpl_id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$o = new ChecklistTemplateModel();
		if ($o->Load($iID) != -1)
			ShowDeleteYesNo(STR_CHK_CHECKLISTTEMPLATE, 'boChecklistTpl.dbdelete', $o->dcl_chklst_tpl_id, $o->dcl_chklst_tpl_name, true, 'dcl_chklst_tpl_id');
	}

	function dbdelete()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FORMTEMPLATES, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['dcl_chklst_tpl_id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$o = new ChecklistTemplateModel();
		if ($o->HasChecklists($iID))
		{
			// records? - deactivate it
			if ($o->Load($iID) != -1)
			{
				$o->dcl_chklst_tpl_active = 'N';
				$o->Edit();
			}
		}
		else
		{
			// no records, so delete it
			$o->dcl_chklst_tpl_id = $iID;
			$o->Delete();
			$filePath = $this->GetTplPath($iID);
			if (is_file($filePath) && is_readable($filePath))
				unlink($filePath);
		}

		$this->show();
	}

	function view()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FORMTEMPLATES, DCL_PERM_VIEW))
			throw new PermissionDeniedException();
			
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['dcl_chklst_tpl_id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$oCL = new htmlChecklistForm();
		$oCL->show($iID, $this->GetTplPath($iID), true);
	}
}
