<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2011 Free Software Foundation
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

class RolePresenter
{
	public function Index()
	{
		global $g_oSec;

		commonHeader();
        RequirePermission(DCL_ENTITY_ROLE, DCL_PERM_VIEW);

        $smartyHelper = new SmartyHelper();
        $smartyHelper->assign('PERM_ADD', $g_oSec->HasPerm(DCL_ENTITY_ROLE, DCL_PERM_ADD));
        $smartyHelper->assign('PERM_EDIT', $g_oSec->HasPerm(DCL_ENTITY_ROLE, DCL_PERM_MODIFY));
        $smartyHelper->assign('PERM_DELETE', $g_oSec->HasPerm(DCL_ENTITY_ROLE, DCL_PERM_DELETE));
        $smartyHelper->assign('PERM_ADMIN', $g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW));
        $smartyHelper->Render('RoleGrid.tpl');
	}

	public function Create()
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_ROLE, DCL_PERM_ADD);

		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('VAL_FORMACTION', menuLink());
		$smartyHelper->assign('menuAction', 'Role.Insert');
		$smartyHelper->assign('VAL_TITLE', 'Add New Role');
		$smartyHelper->assign('VAL_ROLEACTIVE', 'Y');

		$model = new RoleModel();
		$smartyHelper->assign('Permissions', $model->GetPermissions(-1));

		$smartyHelper->Render('RoleForm.tpl');
	}

	public function Copy($roleId)
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_ROLE, DCL_PERM_ADD);

		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('VAL_FORMACTION', menuLink());
		$smartyHelper->assign('menuAction', 'Role.Insert');
		$smartyHelper->assign('VAL_TITLE', 'Add New Role');
		$smartyHelper->assign('VAL_ROLEACTIVE', 'Y');

		$model = new RoleModel();
		$smartyHelper->assign('Permissions', $model->GetPermissions($roleId));

		$smartyHelper->Render('RoleForm.tpl');
	}

	public function Edit(RoleModel $model)
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_ROLE, DCL_PERM_MODIFY);

		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('VAL_FORMACTION', menuLink());

		$smartyHelper->assign('menuAction', 'Role.Update');
		$smartyHelper->assign('VAL_TITLE', 'Edit Role');
		$smartyHelper->assign('VAL_ROLEID', $model->role_id);
		$smartyHelper->assign('VAL_ROLEDESC', $model->role_desc);
		$smartyHelper->assign('VAL_ROLEACTIVE', $model->active);
		$smartyHelper->assign('Permissions', $model->GetPermissions($model->role_id));

		$smartyHelper->Render('RoleForm.tpl');
	}

	public function Delete(RoleModel $model)
	{
		commonHeader();
		ShowDeleteYesNo('Role', 'Role.Destroy', $model->role_id, $model->role_desc);
	}
}
