<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2014 Free Software Foundation
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

class PersonnelController
{
	public function Index()
	{
		$presenter = new PersonnelPresenter();
		$presenter->Index();
	}
	
	public function Detail()
	{
		$presenter = new PersonnelPresenter();
		$presenter->Detail();
	}

	public function Create()
	{
		$presenter = new PersonnelPresenter();
		$presenter->Create();
	}

	public function Insert()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$model = new PersonnelModel();
		$model->InitFrom_POST();
		$model->active = @Filter::ToYN($_POST['active']);
		$model->pwd_change_required = @Filter::ToYN($_POST['pwd_change_required']);
		$model->is_locked = @Filter::ToYN($_POST['is_locked']);
		$model->lock_expiration = null;

		$validator = PersonnelModel::GetPasswordValidator($_POST['pwd'], $_POST['pwd2'], $model);
		if (!$validator->validate())
		{
			$presenter = new PersonnelPresenter();
			$presenter->Create($validator->errors());
			return;
		}

		$model->Encrypt();
		$model->Add();

		$aRoles = @Filter::ToIntArray($_POST['roles']);
		if (count($aRoles) > 0)
		{
			// Set up global user roles
			$oUserRole = new UserRoleModel();
			$oUserRole->personnel_id = $model->id;
			$oUserRole->entity_type_id = DCL_ENTITY_GLOBAL;
			$oUserRole->entity_id1 = 0;
			$oUserRole->entity_id2 = 0;

			foreach ($aRoles as $oUserRole->role_id)
				$oUserRole->add();
		}

		SetRedirectMessage('Success', 'New user added successfully.');
		RedirectToAction('Personnel', 'Index');
	}

	public function Edit()
	{
		if (($id = @Filter::ToInt($_REQUEST['id'])) === null)
			throw new InvalidDataException();
		
		$model = new PersonnelModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		$presenter = new PersonnelPresenter();
		$presenter->Edit($model);
	}

	public function Update()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$model = new PersonnelModel();
		$model->InitFrom_POST();
		$model->active = @Filter::ToYN($_POST['active']);
		$model->pwd_change_required = @Filter::ToYN($_POST['pwd_change_required']);
		$model->is_locked = @Filter::ToYN($_POST['is_locked']);
		if ($model->is_locked == 'N')
			$model->lock_expiration = null;

		$model->Edit();

		$oUserRole = new UserRoleModel();
		$oUserRole->DeleteGlobalRolesNotIn($model->id);
		
		$aRoles = @Filter::ToIntArray($_REQUEST['roles']);
		if (count($aRoles) > 0)
		{
			// Set up global user roles
			$oUserRole->personnel_id = $model->id;
			$oUserRole->entity_type_id = DCL_ENTITY_GLOBAL;
			$oUserRole->entity_id1 = 0;
			$oUserRole->entity_id2 = 0;

			foreach ($aRoles as $oUserRole->role_id)
				$oUserRole->add();
		}

		SetRedirectMessage('Success', 'User updated successfully.');
		RedirectToAction('Personnel', 'Index');
	}

	public function Delete()
	{
		if (($id = @Filter::ToInt($_REQUEST['id'])) === null)
			throw new InvalidDataException();

		$model = new PersonnelModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		$presenter = new PersonnelPresenter();
		$presenter->Delete($model);
	}

	public function Destroy()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		if (($id = @Filter::ToInt($_REQUEST['id'])) === null)
			throw new InvalidDataException();
		
		$model = new PersonnelModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		if (!$model->HasFKRef($id))
		{
			$model->Delete(array('id' => $id));
			SetRedirectMessage('Success', 'The user was deleted successfully.');
		}
		else
		{
			$model->SetActive(array('id' => $id), false);
			SetRedirectMessage('Success', 'The user account was deactivated because other items reference it.');
		}

		RedirectToAction('Personnel', 'Index');
	}

	function EditPassword()
	{
		$presenter = new PersonnelPresenter();
		$presenter->EditPassword(DCLID);
	}

	function UpdatePassword()
	{
		global $g_oSec;

		RequirePost();
		
		$iID = DCLID;
		if (isset($_REQUEST['userid']))
		{
			if (($iID = @Filter::ToInt($_REQUEST['userid'])) === null)
			{
				throw new InvalidDataException();
			}
		}

		Filter::RequireNotNullOrWhitespace(@$_POST['new']);
		Filter::RequireNotNullOrWhitespace(@$_POST['confirm']);

		if (!$g_oSec->HasPerm(DCL_ENTITY_PREFS, DCL_PERM_PASSWORD) || (!$g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_PASSWORD) && DCLID != $iID))
			throw new PermissionDeniedException();

		$model = new PersonnelModel();
		$model->Load($iID);

		$validator = PersonnelModel::GetPasswordValidator($_POST['new'], $_POST['confirm'], $model);
		if (!$validator->validate())
		{
			$presenter = new PersonnelPresenter();
			$presenter->EditPassword($iID, $validator->errors());
		}
		else
		{
			$objDBPersonnel = new PersonnelModel();
			$sOriginal = '';
			if (isset($_POST['original']))
				$sOriginal = $_POST['original'];

			if (!$objDBPersonnel->ChangePassword($iID, $sOriginal, $_POST['new']))
			{
				ShowError('Could not change password.');
				$presenter = new PersonnelPresenter();
				$presenter->EditPassword($iID);
				return;
			}
			else
			{
				ShowInfo(STR_DB_PWDCHGSUCCESS);
			}
		}
	}

	public function ForcePasswordChange()
	{
		$presenter = new PersonnelPresenter();
		$presenter->ForcePasswordChange();
	}

	public function ForcePasswordChangePost()
	{
		global $g_oSec, $g_oSession;

		RequirePost();
		RequirePermission(DCL_ENTITY_PREFS, DCL_PERM_PASSWORD);

		$model = new PersonnelModel();
		$model->Load(DCLID);

		$validator = PersonnelModel::GetPasswordValidator($_POST['new'], $_POST['confirm'], $model);

		if (!$validator->validate())
		{
			ShowError(STR_BO_PASSWORDERR);
			$presenter = new PersonnelPresenter();
			$presenter->ForcePasswordChange();
		}
		else
		{
			$model->SetUserPassword(DCLID, $_POST['new']);

			$g_oSession->Unregister('ForcePasswordChange');
			$g_oSession->Edit();

			SetRedirectMessage('Success', 'Password changed successfully.');
			RedirectToAction('HomePage', 'Index');
		}
	}
}
