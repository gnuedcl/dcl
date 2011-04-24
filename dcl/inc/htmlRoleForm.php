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

class htmlRoleForm
{
	var $public;

	function htmlRoleForm()
	{
		$this->public = array('add', 'modify', 'delete', 'submitAdd', 'submitModify', 'submitDelete');
	}

	function add()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ROLE, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$this->ShowEntryForm();
	}

	function copy()
	{
		global $g_oSec;
		
		commonHeader();
		
		if (($id = DCL_Sanitize::ToInt($_REQUEST['role_id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_ROLE, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$obj = new RoleModel();
		$obj->role_id = $id;
		$this->ShowEntryForm($obj, true);
	}

	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		if (($id = DCL_Sanitize::ToInt($_REQUEST['role_id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_ROLE, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$obj = new RoleModel();
		if ($obj->Load($id) == -1)
			return;
			
		$this->ShowEntryForm($obj);
	}

	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (($id = DCL_Sanitize::ToInt($_REQUEST['role_id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_ROLE, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		$obj = new RoleModel();
		if ($obj->Load($id) == -1)
			return;
			
		ShowDeleteYesNo('Role', 'htmlRoleForm.submitDelete', $obj->role_id, $obj->role_desc);
	}

	function submitAdd()
	{
		global $dcl_info, $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ROLE, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$obj = new boRole();
		CleanArray($_REQUEST);
		$obj->add(array(
					'role_desc' => $_REQUEST['role_desc'],
					'active' => $_REQUEST['active'],
					'rolePerms' => isset($_REQUEST['rolePerms']) ? $_REQUEST['rolePerms'] : array()
				)
			);

		$oRole = new htmlRole();
		$oRole->show();
	}

	function submitModify()
	{
		global $g_oSec;
		
		commonHeader();
		if (($id = DCL_Sanitize::ToInt($_REQUEST['role_id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_ROLE, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$obj = new boRole();
		CleanArray($_REQUEST);
		$obj->modify(array(
					'role_id' => $id,
					'role_desc' => $_REQUEST['role_desc'],
					'active' => 'Y',
					'rolePerms' => isset($_REQUEST['rolePerms']) ? $_REQUEST['rolePerms'] : array()
				)
			);

		$oRole = new htmlRole();
		$oRole->show();
	}

	function submitDelete()
	{
		global $g_oSec;
		
		commonHeader();
		if (($id = DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_ROLE, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		$obj = new boRole();
		$obj->delete(array('role_id' => $id));

		$oRole = new htmlRole();
		$oRole->show();
	}

	function ShowEntryForm($obj = '', $bCopy = false)
	{
		global $dcl_info, $g_oSec;

		$isEdit = is_object($obj) && !$bCopy;
		if (!$g_oSec->HasPerm(DCL_ENTITY_ROLE, $isEdit ? DCL_PERM_MODIFY : DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$Template = new DCL_Smarty();
		$Template->assign('VAL_FORMACTION', menuLink());

		$oRole = new RoleModel();
		if ($isEdit)
		{
			$Template->assign('menuAction', 'htmlRoleForm.submitModify');
			$Template->assign('VAL_TITLE', 'Edit Role');
			$Template->assign('VAL_ROLEID', $obj->role_id);
			$Template->assign('VAL_ROLEDESC', $obj->role_desc);
			$Template->assign('VAL_ROLEACTIVE', $obj->active);
			$Template->assign('Permissions', $oRole->GetPermissions($obj->role_id));
		}
		else
		{
			$Template->assign('menuAction', 'htmlRoleForm.submitAdd');
			$Template->assign('VAL_TITLE', 'Add New Role');
			$Template->assign('VAL_ROLEACTIVE', 'Y');
			$Template->assign('Permissions', $oRole->GetPermissions($bCopy ? $obj->role_id : -1));
		}

		$Template->Render('htmlRoleForm.tpl');
	}
}
