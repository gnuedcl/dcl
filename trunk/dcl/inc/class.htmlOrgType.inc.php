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

LoadStringResource('admin');
class htmlOrgType
{
	var $public;

	function htmlOrgType()
	{
		$this->public = array('add', 'modify', 'delete', 'submitAdd', 'submitModify', 'submitDelete', 'ShowAll');
	}

	function GetCombo($default = 0, $cbName = 'org_type_id', $size = 0, $activeOnly = true)
	{
		$filter = '';
		$table = 'dcl_org_type';
		$order = 'org_type_name';

		$obj = CreateObject('dcl.htmlSelect');
		$obj->SetOptionsFromDb($table, 'org_type_id', 'org_type_name', $filter, $order);
		$obj->vDefault = $default;
		$obj->sName = $cbName;
		$obj->iSize = $size;
		$obj->sZeroOption = STR_CMMN_SELECTONE;

		return $obj->GetHTML();
	}

	function ShowAll($orderBy = 'org_type_name')
	{
		global $dcl_info, $g_oSec;

		commonHeader();

		$o = CreateObject('dcl.boView');
		$o->table = 'dcl_org_type';
		$o->title = STR_ADMIN_ORGTYPES;
		$o->AddDef('columns', '', array('org_type_id', 'org_type_name'));
		$o->AddDef('columnhdrs', '', array(STR_CMMN_ID, STR_CMMN_NAME));
		$o->AddDef('order', '', $orderBy);

		$oDB = CreateObject('dcl.dbOrgType');
		if ($oDB->query($o->GetSQL()) == -1)
			return;
			
		$allRecs = $oDB->FetchAllRows();

		$oTable =& CreateObject('dcl.htmlTable');
		$oTable->setCaption(sprintf(STR_ADMIN_ORGTYPES, $orderBy));
		$oTable->addColumn(STR_CMMN_ID, 'numeric');
		$oTable->addColumn(STR_CMMN_NAME, 'string');

		if ($g_oSec->HasPerm(DCL_ENTITY_ORGTYPE, DCL_PERM_ADD))
			$oTable->addToolbar(menuLink('', 'menuAction=htmlOrgType.add'), STR_CMMN_NEW);

		if ($g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW))
			$oTable->addToolbar(menuLink('', 'menuAction=boAdmin.ShowSystemConfig'), DCL_MENU_SYSTEMSETUP);

		if (count($allRecs) > 0 && $g_oSec->HasAnyPerm(array(DCL_ENTITY_ORGTYPE => array($g_oSec->PermArray(DCL_PERM_MODIFY), $g_oSec->PermArray(DCL_PERM_DELETE)))))
		{
			$oTable->addColumn(STR_CMMN_OPTIONS, 'html');
			for ($i = 0; $i < count($allRecs); $i++)
			{
				$options = '';
				if ($g_oSec->HasPerm(DCL_ENTITY_ORGTYPE, DCL_PERM_MODIFY))
					$options = '<a href="' . menuLink('', 'menuAction=htmlOrgType.modify&org_type_id=' . $allRecs[$i][0]) . '">' . STR_CMMN_EDIT . '</a>';

				if ($g_oSec->HasPerm(DCL_ENTITY_ORGTYPE, DCL_PERM_DELETE))
				{
					if ($options != '')
						$options .= '&nbsp;|&nbsp;';

					$options .= '<a href="' . menuLink('', 'menuAction=htmlOrgType.delete&org_type_id=' . $allRecs[$i][0]) . '">' . STR_CMMN_DELETE . '</a>';
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
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORGTYPE, DCL_PERM_ADD))
			return PrintPermissionDenied();

		$this->ShowEntryForm();
		print('<p>');
		$this->ShowAll();
	}

	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORGTYPE, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['org_type_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$obj = CreateObject('dcl.dbOrgType');
		if ($obj->Load($id) == -1)
			return;
			
		$this->ShowEntryForm($obj);
	}

	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORGTYPE, DCL_PERM_DELETE))
			return PrintPermissionDenied();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['org_type_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$obj = CreateObject('dcl.dbOrgType');
		if ($obj->Load($id) == -1)
			return;
			
		ShowDeleteYesNo(STR_ADMIN_ORGTYPES, 'htmlOrgType.submitDelete', $obj->org_type_id, $obj->org_type_name);
	}

	function submitAdd()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORGTYPE, DCL_PERM_ADD))
			return PrintPermissionDenied();

		$obj = CreateObject('dcl.boOrgType');
		CleanArray($_REQUEST);
		$obj->add($_REQUEST);

		$this->ShowEntryForm();
		print('<p>');
		$this->ShowAll();
	}

	function submitModify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORGTYPE, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		$obj = CreateObject('dcl.boOrgType');
		CleanArray($_REQUEST);
		$obj->modify($_REQUEST);

		$this->ShowAll();
	}

	function submitDelete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORGTYPE, DCL_PERM_DELETE))
			return PrintPermissionDenied();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$obj = CreateObject('dcl.boOrgType');
		$aKey = array('org_type_id' => $id);
		$obj->delete($aKey);

		$this->ShowAll();
	}

	function ShowEntryForm($obj = '')
	{
		global $dcl_info, $g_oSec;
		
		$isEdit = is_object($obj);
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORGTYPE, $isEdit ? DCL_PERM_MODIFY : DCL_PERM_ADD))
			return PrintPermissionDenied();

		$t = CreateSmarty();

		if ($isEdit)
		{
			if (($id = DCL_Sanitize::ToInt($_REQUEST['org_type_id'])) === null)
			{
				trigger_error('Data sanitize failed.');
				return;
			}
		
			$t->assign('TXT_FUNCTION', 'Edit Organization Type');
			$t->assign('menuAction', 'htmlOrgType.submitModify');
			$t->assign('org_type_id', $id);
			$t->assign('VAL_NAME', $obj->org_type_name);
		}
		else
		{
			$t->assign('TXT_FUNCTION', 'Add Organization Type');
			$t->assign('menuAction', 'htmlOrgType.submitAdd');
		}

		SmartyDisplay($t, 'htmlOrgTypeForm.tpl');
	}
}
?>