<?php
/*
 * $Id: class.boChecklists.inc.php,v 1.1.1.1 2006/11/27 05:30:44 mdean Exp $
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

class boChecklists
{
	function GetChklstPath($id, $bCreate = false)
	{
		global $dcl_info;

		$filePath = $dcl_info['DCL_FILE_PATH'] . '/checklists';
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
		if (!$g_oSec->HasPerm(DCL_ENTITY_FORMS, DCL_PERM_VIEW))
			return PrintPermissionDenied();
					
		$filterStatus = @DCL_Sanitize::ToInt($_REQUEST['filterStatus']);
		$filterType = @DCL_Sanitize::ToInt($_REQUEST['filterType']);

		$oView =& CreateObject('dcl.boView');

		$oView->table = 'dcl_chklst';
		$oView->style = 'report';
		$oView->title = STR_CHK_INITIATEDCHECKLISTS;
		$oView->AddDef('columnhdrs', '', array(STR_CMMN_ID, STR_CHK_SUMMARY, STR_CHK_STATUS));
		$oView->AddDef('columns', '', array('dcl_chklst_id', 'dcl_chklst_summary', 'dcl_chklst_status'));
		
		if ($filterStatus !== null)
			$oView->AddDef('filter', 'dcl_chklst_status', $filterStatus);
		
		if ($filterType !== null)
			$oView->AddDef('filter', 'dcl_chklst_tpl_id', $filterType);

		$oDB =& CreateObject('dcl.dbChklst');
		if ($oDB->query($oView->GetSQL()) == -1)
			return;
			
		$allRecs = $oDB->FetchAllRows();

		$oTable =& CreateObject('dcl.htmlTable');
		$oTable->setCaption(STR_CHK_INITIATEDCHECKLISTS);
		$oTable->addColumn(STR_CMMN_ID, 'numeric');
		$oTable->addColumn(STR_CHK_SUMMARY, 'string');
		$oTable->addColumn(STR_CHK_STATUS, 'string');

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
				if ($g_oSec->HasPerm(DCL_ENTITY_FORMS, DCL_PERM_VIEW, $allRecs[$i][0]))
					$options = '<a href="' . menuLink('', 'menuAction=boChecklists.view&dcl_chklst_id=' . $allRecs[$i][0]) . '">' . STR_CMMN_VIEW . '</a>';
		
				if ($g_oSec->HasPerm(DCL_ENTITY_FORMS, DCL_PERM_MODIFY, $allRecs[$i][0]))
				{
					if ($options != '')
						$options .= '&nbsp;|&nbsp;';

					$options .= '<a href="' . menuLink('', 'menuAction=boChecklists.modify&dcl_chklst_id=' . $allRecs[$i][0]) . '">' . STR_CMMN_EDIT . '</a>';
				}
		
				if ($g_oSec->HasPerm(DCL_ENTITY_FORMS, DCL_PERM_DELETE, $allRecs[$i][0]))
				{
					if ($options != '')
						$options .= '&nbsp;|&nbsp;';

					$options .= '<a href="' . menuLink('', 'menuAction=boChecklists.delete&dcl_chklst_id=' . $allRecs[$i][0]) . '">' . STR_CMMN_DELETE . '</a>';
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
		if (!$g_oSec->HasPerm(DCL_ENTITY_FORMS, DCL_PERM_ADD))
			return PrintPermissionDenied();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['dcl_chklst_tpl_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$o =& CreateObject('dcl.dbChklstTpl');
		if ($o->Load($iID) != -1)
		{
			$t = CreateSmarty();
			$t->assign('VAL_TPLID', $iID);
			$t->assign('dcl_chklst_tpl_id', $o->dcl_chklst_tpl_id);
			$t->assign('VAL_TPLNAME', $o->dcl_chklst_tpl_name);
			$t->assign('VAL_SUMMARY', '');

			SmartyDisplay($t, 'htmlNewChecklist.tpl');
		}
		else
			printf(STR_CHK_ERRLOADINGTPLID, $iID);
	}

	function SetAutoValue(&$oXML, $sElement, $sAttribute, $sAttributeValue, $sValue)
	{
		$oXML->ClearList();
		$oXML->ListNodes($oXML->root, $sElement, $sAttribute, $sAttributeValue);
		if (count($oXML->nodes) > 0)
		{
			for ($i = 0; $i < count($oXML->nodes); $i++)
			{
				$oNode = &$oXML->nodes[$i];
				$oXML->FindChildNode($oNode, 'Value');
				if ($oXML->currentNode != NULL)
				{
					$oXML->currentNode->data = $sValue;
				}
				else
				{
					$nextID = count($oNode->childNodes);
					$oNode->childNodes[$nextID] = &CreateObject('dcl.xmlNode');
					$oNode->childNodes[$nextID]->name = 'Values';
					$oNode->childNodes[$nextID]->childNodes[0] = &CreateObject('dcl.xmlNode');
					$oNode->childNodes[$nextID]->childNodes[0]->name = 'Value';
					$oNode->childNodes[$nextID]->childNodes[0]->data = $sValue;
				}
			}
		}
	}

	function SetNodeValue(&$oXML, $sNodeName, $sNodeValue)
	{
		$oXML->FindChildNode($oXML->root, $sNodeName);
		if ($oXML->currentNode != NULL)
			$oXML->currentNode->data = $sNodeValue;
	}

	function dbadd()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FORMS, DCL_PERM_ADD))
			return PrintPermissionDenied();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['dcl_chklst_tpl_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$tpl =& CreateObject('dcl.boChecklistTpl');
		$tplFilePath = $tpl->GetTplPath($iID);
		$oXML =& CreateObject('dcl.xmlDoc');
		$oXML->ParseFile($tplFilePath);
		$oXML->FindChildNode($oXML->root, 'CurrentState');

		$oTpl =& CreateObject('dcl.dbChklstTpl');
		if ($oTpl->Load($iID) == -1)
			return;

		if ($oTpl->dcl_chklst_tpl_active != 'Y')
		{
			print(STR_CHK_TEMPLATEISINACTIVE);
			return;
		}

		$o =& CreateObject('dcl.dbChklst');
		$o->dcl_chklst_tpl_id = $iID;
		$o->dcl_chklst_summary = $o->GPCStripSlashes($_REQUEST['dcl_chklst_summary']);
		$o->dcl_chklst_createby = $GLOBALS['DCLID'];
		if ($oXML->currentNode != NULL)
			$o->dcl_chklst_status = $oXML->currentNode->data;

		$o->Add();
		if ($o->dcl_chklst_id > 0)
		{
			$this->SetAutoValue($oXML, 'Field', 'type', 'createby', $GLOBALS['DCLNAME']);
			$this->SetAutoValue($oXML, 'Field', 'type', 'createdate', date('Y-m-d'));
			$this->SetAutoValue($oXML, 'Field', 'type', 'autoincrement', $o->dcl_chklst_id);
			$this->SetNodeValue($oXML, 'InitiatedBy', $GLOBALS['DCLNAME']);
			$this->SetNodeValue($oXML, 'InitiatedOn', date('Y-m-d'));

			$initFilePath = $this->GetChklstPath($o->dcl_chklst_id, true);
			if ($oXML->ToFile($initFilePath))
			{
				$this->showform($o->dcl_chklst_id);
			}
			else
			{
				// couldn't copy it, so remove the record...
				$o->Delete();
				trigger_error(STR_CHK_COULDNOTCOPYTPL);
			}
		}
		else
			trigger_error(STR_CHK_ERRORDATABASEENTRY);
	}

	function modify()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FORMS, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['dcl_chklst_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$this->showform($iID);
	}

	function showform($id, $bIsView = false)
	{
		$oCL =& CreateObject('dcl.htmlChecklistForm');
		$oCL->show($id, $this->GetChklstPath($id), $bIsView);
	}

	function dbmodify()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FORMS, DCL_PERM_MODIFY))
			return PrintPermissionDenied();
			
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['dcl_chklst_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$oCL =& CreateObject('dcl.htmlChecklistForm');
		$sFilePath = $this->GetChklstPath($iID);

		$oCL->xml =& CreateObject('dcl.xmlDoc');
		$oCL->xml->ParseFile($sFilePath);
		$oCL->UpdateNodes($oCL->xml->root);
		$this->AddChange($oCL->xml, $GLOBALS['DCLNAME'], date('Y-m-d'), $_REQUEST['dcl_chklst_state']);
		$this->SetNodeValue($oCL->xml, 'CurrentState', $_REQUEST['dcl_chklst_status']);
		$oCL->xml->ToFile($sFilePath);

		$o =& CreateObject('dcl.dbChklst');
		if ($o->Load($iID) != -1)
		{
			$o->dcl_chklst_modifyby = $GLOBALS['DCLID'];
			$o->dcl_chklst_modifyon = date($dcl_info['DCL_TIMESTAMP_FORMAT_DB']);
			$o->dcl_chklst_status = $_REQUEST['dcl_chklst_status'];
			$o->Edit();
		}

		$this->show();
	}

	function delete()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FORMS, DCL_PERM_DELETE))
			return PrintPermissionDenied();
			
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['dcl_chklst_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$o =& CreateObject('dcl.dbChklst');
		if ($o->Load($iID) != -1)
			ShowDeleteYesNo('Checklist', 'boChecklists.dbdelete', $o->dcl_chklst_id, $o->dcl_chklst_summary, false, 'dcl_chklst_id');
	}

	function dbdelete()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FORMS, DCL_PERM_DELETE))
			return PrintPermissionDenied();
			
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['dcl_chklst_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$o =& CreateObject('dcl.dbChklst');
		$o->dcl_chklst_id = $iID;
		$o->Delete();
		$filePath = $this->GetChklstPath($iID);
		if (is_file($filePath) && is_readable($filePath))
			unlink($filePath);

		$this->show();
	}

	function view()
	{
		commonHeader();
		
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['dcl_chklst_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$this->showform($iID, true);
	}

	function AddChange(&$oXML, $sBy, $sOn, $sState)
	{
		$oXML->FindChildNode($oXML->root, 'Changes');
		if ($oXML->currentNode != NULL)
		{
			$a = array('ChangeBy' => $sBy, 'ChangeOn' => $sOn, 'ChangeState' => $sState);
			$o =& CreateObject('dcl.xmlNode');
			$o->name = 'Change';
			$nextid = 0;
			while (list($k, $v) = each($a))
			{
				$o->childNodes[$nextid] = &CreateObject('dcl.xmlNode');
				$o->childNodes[$nextid]->name = $k;
				$o->childNodes[$nextid]->data = $v;
				$nextid++;
			}
			$oXML->currentNode->childNodes[] = &$o;
		}
	}
}
?>
