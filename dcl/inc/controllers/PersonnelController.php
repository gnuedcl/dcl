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
		if (isset($_POST['active']))
			$model->active = 'Y';
		else
			$model->active = 'N';

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
		if (isset($_REQUEST['active']))
			$model->active = 'Y';
		else
			$model->active = 'N';

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
			$model->Delete();
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
		$presenter->EditPassword();
	}

	function UpdatePassword()
	{
		global $g_oSec;
		
		$iID = DCLID;
		if (isset($_REQUEST['userid']))
		{
			if (($iID = @Filter::ToInt($_REQUEST['userid'])) === null)
			{
				throw new InvalidDataException();
			}
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PREFS, DCL_PERM_PASSWORD) || (!$g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_PASSWORD) && DCLID != $iID))
			throw new PermissionDeniedException();

		if ($_POST['confirm'] != $_POST['new'] || $_POST['new'] == '')
		{
			ShowError(STR_BO_PASSWORDERR);
			$presenter = new PersonnelPresenter();
			$presenter->EditPassword();
		}
		else
		{
			$objDBPersonnel = new PersonnelModel();
			$sOriginal = '';
			if (isset($_POST['original']))
				$sOriginal = $_POST['original'];

			$objDBPersonnel->ChangePassword($iID, $sOriginal, $_POST['new'], $_POST['confirm']);
		}
	}
}
