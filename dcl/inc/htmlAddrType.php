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

LoadStringResource('wo');
LoadStringResource('cfg');

class htmlAddrType
{
	var $public;

	function htmlAddrType()
	{
		$this->public = array('add', 'modify', 'delete', 'submitAdd', 'submitModify', 'submitDelete', 'PrintAll');
	}

	function GetCombo($default = 0, $cbName = 'addr_type_id', $size = 0, $activeOnly = true)
	{
		$filter = '';
		$table = 'dcl_addr_type';
		$order = 'addr_type_name';

		$obj = new htmlSelect();
		$obj->SetOptionsFromDb($table, 'addr_type_id', 'addr_type_name', $filter, $order);
		$obj->vDefault = $default;
		$obj->sName = $cbName;
		$obj->iSize = $size;
		$obj->sZeroOption = STR_CMMN_SELECTONE;

		return $obj->GetHTML();
	}

	function ShowAll($orderBy = 'addr_type_name')
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ADDRTYPE, DCL_PERM_VIEW))
			return PrintPermissionDenied();

		$o = new boView();
		$o->table = 'dcl_addr_type';
		$o->title = sprintf('Address Types');
		$o->AddDef('columns', '', array('addr_type_id', 'addr_type_name'));
		$o->AddDef('columnhdrs', '', array(STR_CMMN_ID, STR_CMMN_NAME));
		$o->AddDef('order', '', $orderBy);

		$oDB = new dbAddrType();
		if ($oDB->query($o->GetSQL()) == -1)
			return;
			
		$allRecs = $oDB->FetchAllRows();

		$oTable = new htmlTable();
		$oTable->setCaption('Address Types');
		$oTable->addColumn(STR_CMMN_ID, 'numeric');
		$oTable->addColumn(STR_CMMN_NAME, 'string');

		if ($g_oSec->HasPerm(DCL_ENTITY_ADDRTYPE, DCL_PERM_ADD))
			$oTable->addToolbar(menuLink('', 'menuAction=htmlAddrType.add'), STR_CMMN_NEW);

		if ($g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW))
			$oTable->addToolbar(menuLink('', 'menuAction=boAdmin.ShowSystemConfig'), DCL_MENU_SYSTEMSETUP);

		if (count($allRecs) > 0 && $g_oSec->HasAnyPerm(array(DCL_ENTITY_ADDRTYPE => array($g_oSec->PermArray(DCL_PERM_MODIFY), $g_oSec->PermArray(DCL_PERM_DELETE)))))
		{
			$oTable->addColumn(STR_CMMN_OPTIONS, 'html');
			for ($i = 0; $i < count($allRecs); $i++)
			{
				$options = '';
				if ($g_oSec->HasPerm(DCL_ENTITY_ADDRTYPE, DCL_PERM_MODIFY))
					$options = '<a href="' . menuLink('', 'menuAction=htmlAddrType.modify&addr_type_id=' . $allRecs[$i][0]) . '">' . STR_CMMN_EDIT . '</a>';

				if ($g_oSec->HasPerm(DCL_ENTITY_ADDRTYPE, DCL_PERM_DELETE))
				{
					if ($options != '')
						$options .= '&nbsp;|&nbsp;';

					$options .= '<a href="' . menuLink('', 'menuAction=htmlAddrType.delete&addr_type_id=' . $allRecs[$i][0]) . '">' . STR_CMMN_DELETE . '</a>';
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
		if (!$g_oSec->HasPerm(DCL_ENTITY_ADDRTYPE, DCL_PERM_ADD))
			return PrintPermissionDenied();

		$this->ShowEntryForm();
		print('<p>');
		$this->ShowAll();
	}

	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ADDRTYPE, DCL_PERM_MODIFY))
			return PrintPermissionDenied();
			
		if (($id = DCL_Sanitize::ToInt($_REQUEST['addr_type_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		$obj = new dbAddrType();
		if ($obj->Load($id) != -1)
			$this->ShowEntryForm($obj);
	}

	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ADDRTYPE, DCL_PERM_DELETE))
			return PrintPermissionDenied();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['addr_type_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		$obj = new dbAddrType();
		if ($obj->Load($id) != -1)
			ShowDeleteYesNo('Address Type', 'htmlAddrType.submitDelete', $obj->addr_type_id, $obj->addr_type_name);
	}

	function submitAdd()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ADDRTYPE, DCL_PERM_ADD))
			return PrintPermissionDenied();

		$obj = new boAddrType();
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
		if (!$g_oSec->HasPerm(DCL_ENTITY_ADDRTYPE, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		$obj = new boAddrType();
		CleanArray($_REQUEST);
		$obj->modify($_REQUEST);

		$this->ShowAll();
	}

	function submitDelete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ADDRTYPE, DCL_PERM_DELETE))
			return PrintPermissionDenied();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		$obj = new boAddrType();

		$aKey = array('addr_type_id' => $id);
		$obj->delete($aKey);

		$this->ShowAll();
	}

	function ShowEntryForm($obj = '')
	{
		global $dcl_info, $g_oSec;

		$isEdit = is_object($obj);
		if ($isEdit && !$g_oSec->HasPerm(DCL_ENTITY_ADDRTYPE, DCL_PERM_MODIFY))
			return PrintPermissionDenied();
		else if (!$isEdit && !$g_oSec->HasPerm(DCL_ENTITY_ADDRTYPE, DCL_PERM_ADD))
			return PrintPermissionDenied();

		$t = CreateSmarty();

		if ($isEdit)
		{
			if (($id = DCL_Sanitize::ToInt($_REQUEST['addr_type_id'])) === null)
			{
				trigger_error('Data sanitize failed.');
				return;
			}
		
			$t->assign('TXT_FUNCTION', 'Edit Address Type');
			$t->assign('menuAction', 'htmlAddrType.submitModify');
			$t->assign('addr_type_id', $id);
			$t->assign('VAL_NAME', $obj->addr_type_name);
		}
		else
		{
			$t->assign('TXT_FUNCTION', 'Add Address Type');
			$t->assign('menuAction', 'htmlAddrType.submitAdd');
		}

		SmartyDisplay($t, 'htmlAddrTypeForm.tpl');
	}
}
