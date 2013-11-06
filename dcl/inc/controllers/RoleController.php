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

class RoleController extends AbstractController
{
	public function __construct()
	{
		parent::__construct();

		$this->model = new RoleModel();
		$this->sKeyField = 'role_id';
		$this->Entity = DCL_ENTITY_ROLE;
	}

	public function Index()
	{
		RequirePermission(DCL_ENTITY_ROLE, DCL_PERM_VIEW);

		$presenter = new RolePresenter();
		$presenter->Index();
	}

	public function Create()
	{
		RequirePermission(DCL_ENTITY_ROLE, DCL_PERM_ADD);

		$presenter = new RolePresenter();
		$presenter->Create();
	}

	public function Copy()
	{
		RequirePermission(DCL_ENTITY_ROLE, DCL_PERM_ADD);

		$roleId = @Filter::ToInt($_REQUEST['role_id']);
		if ($roleId === null)
			throw new InvalidDataException();

		$presenter = new RolePresenter();
		$presenter->Copy($roleId);
	}

	public function Insert()
	{
		RequirePost();
		RequirePermission(DCL_ENTITY_ROLE, DCL_PERM_ADD);

		$roleId = parent::Insert($_POST);
		if (isset($_POST['rolePerms']) && is_array($_POST['rolePerms']))
		{
			$rolePermissionModel = new RolePermissionModel();
			foreach ($_POST['rolePerms'] as $entityPerm)
			{
				list($entityId, $permId) = explode('_', $entityPerm);
				$entityId = Filter::ToInt($entityId);
				$permId = Filter::ToInt($permId);

				if ($entityId === null || $permId === null)
					throw new InvalidDataException();

				$rolePermissionModel->InitFromArray(array('role_id' => $roleId, 'entity_id' => $entityId, 'perm_id' => $permId));
				$rolePermissionModel->Add();
			}
		}

		SetRedirectMessage('Success', 'Role added successfully.');
		RedirectToAction('Role', 'Index');
	}

	public function Edit()
	{
		RequirePermission(DCL_ENTITY_ROLE, DCL_PERM_MODIFY);

		$roleId = @Filter::ToInt($_REQUEST['role_id']);
		if ($roleId === null)
			throw new InvalidDataException();

		$model = new RoleModel();
		if ($model->Load($roleId) === -1)
			throw new InvalidEntityException();

		$presenter = new RolePresenter();
		$presenter->Edit($model);
	}

	public function Update()
	{
		RequirePost();
		RequirePermission(DCL_ENTITY_ROLE, DCL_PERM_MODIFY);

		if (($roleId = @Filter::ToInt($_REQUEST['role_id'])) === null)
			throw new InvalidDataException();

		parent::Update($_POST);
		$rolePermissionModel = new RolePermissionModel();
		$rolePermissionModel->DeleteRole($roleId);

		if (is_array($_POST['rolePerms']))
		{
			foreach ($_POST['rolePerms'] as $entityPerm)
			{
				list($entityId, $permId) = explode('_', $entityPerm);
				$rolePermissionModel->InitFromArray(array('role_id' => $roleId, 'entity_id' => (int)$entityId, 'perm_id' => (int)$permId));
				if ($rolePermissionModel->add() == -1)
					return -1;
			}
		}

		SetRedirectMessage('Success', 'Role updated successfully.');
		RedirectToAction('Role', 'Index');
	}

	public function Delete()
	{
		RequirePermission(DCL_ENTITY_ROLE, DCL_PERM_DELETE);

		$roleId = @Filter::RequireInt($_REQUEST['role_id']);

		$model = new RoleModel();
		if ($model->Load($roleId) == -1)
			throw new InvalidEntityException();

		$presenter = new RolePresenter();
		$presenter->Delete($model);
	}

	public function Destroy()
	{
		RequirePost();
		RequirePermission(DCL_ENTITY_ROLE, DCL_PERM_DELETE);

		$roleId = @Filter::RequireInt($_POST['id']);

		$rolePermissionModel = new RolePermissionModel();
		$rolePermissionModel->DeleteRole($roleId);

		parent::Destroy(array('role_id' => $roleId));

		SetRedirectMessage('Success', 'Role deleted successfully.');
		RedirectToAction('Role', 'Index');
	}
}
