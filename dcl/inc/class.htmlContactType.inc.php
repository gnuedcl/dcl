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
class htmlContactType
{
	var $public;

	function htmlContactType()
	{
		$this->public = array('add', 'modify', 'delete', 'submitAdd', 'submitModify', 'submitDelete', 'ShowAll');
	}

	function GetCombo($default = 0, $cbName = 'contact_type_id', $size = 0, $activeOnly = true)
	{
		$filter = '';
		$table = 'dcl_contact_type';
		$order = 'contact_type_name';

		$obj = new htmlSelect();
		$obj->SetOptionsFromDb($table, 'contact_type_id', 'contact_type_name', $filter, $order);
		$obj->vDefault = $default;
		$obj->sName = $cbName;
		$obj->iSize = $size;
		$obj->sZeroOption = STR_CMMN_SELECTONE;

		return $obj->GetHTML();
	}

	function ShowAll($orderBy = 'contact_type_name')
	{
		global $dcl_info, $g_oSec;

		commonHeader();

		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACTTYPE, DCL_PERM_VIEW))
			return PrintPermissionDenied();

		$o = new boView();
		$o->table = 'dcl_contact_type';
		$o->title = STR_ADMIN_CONTACTTYPES;
		$o->AddDef('columns', '', array('contact_type_id', 'contact_type_name', 'contact_type_is_main'));
		$o->AddDef('columnhdrs', '', array(STR_CMMN_ID, STR_CMMN_NAME, 'Main'));
		$o->AddDef('order', '', $orderBy);

		$oDB = new dbContactType();
		if ($oDB->query($o->GetSQL()) == -1)
			return;
			
		$allRecs = $oDB->FetchAllRows();

		$oTable = new htmlTable();
		$oTable->setCaption(sprintf(STR_ADMIN_CONTACTTYPES, $orderBy));
		$oTable->addColumn(STR_CMMN_ID, 'numeric');
		$oTable->addColumn(STR_CMMN_NAME, 'string');
		$oTable->addColumn('Main', 'string');

		if ($g_oSec->HasPerm(DCL_ENTITY_CONTACTTYPE, DCL_PERM_ADD))
			$oTable->addToolbar(menuLink('', 'menuAction=htmlContactType.add'), STR_CMMN_NEW);

		if ($g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW))
			$oTable->addToolbar(menuLink('', 'menuAction=boAdmin.ShowSystemConfig'), DCL_MENU_SYSTEMSETUP);

		if (count($allRecs) > 0 && $g_oSec->HasAnyPerm(array(DCL_ENTITY_CONTACTTYPE => array($g_oSec->PermArray(DCL_PERM_MODIFY), $g_oSec->PermArray(DCL_PERM_DELETE)))))
		{
			$oTable->addColumn(STR_CMMN_OPTIONS, 'html');
			for ($i = 0; $i < count($allRecs); $i++)
			{
				$options = '';
				if ($g_oSec->HasPerm(DCL_ENTITY_CONTACTTYPE, DCL_PERM_MODIFY))
					$options = '<a href="' . menuLink('', 'menuAction=htmlContactType.modify&contact_type_id=' . $allRecs[$i][0]) . '">' . STR_CMMN_EDIT . '</a>';

				if ($g_oSec->HasPerm(DCL_ENTITY_CONTACTTYPE, DCL_PERM_DELETE))
				{
					if ($options != '')
						$options .= '&nbsp;|&nbsp;';

					$options .= '<a href="' . menuLink('', 'menuAction=htmlContactType.delete&contact_type_id=' . $allRecs[$i][0]) . '">' . STR_CMMN_DELETE . '</a>';
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
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACTTYPE, DCL_PERM_ADD))
			return PrintPermissionDenied();

		$this->ShowEntryForm();
		print('<p>');
		$this->ShowAll();
	}

	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACTTYPE, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['contact_type_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$obj = new dbContactType();
		if ($obj->Load($id) == -1)
			return;
			
		$this->ShowEntryForm($obj);
	}

	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACTTYPE, DCL_PERM_DELETE))
			return PrintPermissionDenied();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['contact_type_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$obj = new dbContactType();
		if ($obj->Load($id) == -1)
			return;
			
		ShowDeleteYesNo(STR_ADMIN_CONTACTTYPES, 'htmlContactType.submitDelete', $obj->contact_type_id, $obj->contact_type_name);
	}

	function submitAdd()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACTTYPE, DCL_PERM_ADD))
			return PrintPermissionDenied();

		$obj = new boContactType();
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
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACTTYPE, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		$obj = new boContactType();
		CleanArray($_REQUEST);
		$obj->modify($_REQUEST);

		$this->ShowAll();
	}

	function submitDelete()
	{
		global $g_oSec;
		
		commonHeader();

		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACTTYPE, DCL_PERM_DELETE))
			return PrintPermissionDenied();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$obj = new boContactType();
		$aKey = array('contact_type_id' => $id);
		$obj->delete($aKey);

		$this->ShowAll();
	}

	function ShowEntryForm($obj = '')
	{
		global $dcl_info, $g_oSec;

		$isEdit = is_object($obj);
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACTTYPE, $isEdit ? DCL_PERM_MODIFY : DCL_PERM_ADD))
			return PrintPermissionDenied();

		$t = CreateSmarty();

		if ($isEdit)
		{
			if (($id = DCL_Sanitize::ToInt($_REQUEST['contact_type_id'])) === null)
			{
				trigger_error('Data sanitize failed.');
				return;
			}
		
			$t->assign('TXT_FUNCTION', 'Edit Contact Type');
			$t->assign('menuAction', 'htmlContactType.submitModify');
			$t->assign('contact_type_id', $id);
			$t->assign('VAL_MAIN', $obj->contact_type_is_main);
			$t->assign('VAL_NAME', $obj->contact_type_name);
		}
		else
		{
			$t->assign('TXT_FUNCTION', 'Add Contact Type');
			$t->assign('menuAction', 'htmlContactType.submitAdd');
		}

		SmartyDisplay($t, 'htmlContactTypeForm.tpl');
	}
}
