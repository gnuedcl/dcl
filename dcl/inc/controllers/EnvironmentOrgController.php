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

class EnvironmentOrgController
{
	public function Index()
	{

	}

	public function Create()
	{
		$presenter = new EnvironmentOrgPresenter();
		$presenter->Create();
	}

	public function Insert()
	{
		RequirePost();
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_MODIFY);

		$model = new EnvironmentOrgModel();
		$model->InitFrom_POST();

		$validator = new EnvironmentOrgModelValidator($model);
		if (!$validator->Validate())
		{
			$presenter = new EnvironmentOrgPresenter();
			$presenter->Create($model, $validator->Errors());
			return;
		}

		$model->create_dt = DCL_NOW;
		$model->update_dt = DCL_NOW;
		$model->create_by = DCLID;
		$model->update_by = DCLID;
		$model->Add();

		SetRedirectMessage('Success', 'Environment added successfully to organization.');
		RedirectToAction('Organization', 'Detail', 'org_id=' . $model->org_id);
	}

	public function Edit()
	{
		$id = Filter::RequireInt($_REQUEST['environment_org_id']);

		$model = new EnvironmentOrgModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		$presenter = new EnvironmentOrgPresenter();
		$presenter->Edit($model);
	}

	public function Update()
	{
		RequirePost();
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_MODIFY);
		$id = Filter::RequireInt($_POST['id']);

		$model = new EnvironmentOrgModel();
		if ($model->Load($id) == -1)
		{
			ShowError('Organization environment reference not found.');
			return;
		}

		$model->InitFrom_POST();

		$validator = new EnvironmentOrgModelValidator($model);
		if (!$validator->Validate())
		{
			$presenter = new EnvironmentOrgPresenter();
			$presenter->Edit($model, $validator->Errors());
			return;
		}

		$model->update_dt = DCL_NOW;
		$model->update_by = DCLID;
		$model->Edit(array('create_by', 'create_dt', 'org_id'));

		SetRedirectMessage('Success', 'Environment updated successfully for organization.');
		RedirectToAction('Organization', 'Detail', 'org_id=' . $model->org_id);
	}

	public function Delete()
	{
		$id = Filter::RequireInt($_REQUEST['id']);

		$model = new EnvironmentOrgModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		$presenter = new EnvironmentOrgPresenter();
		$presenter->Delete($model);
	}

	public function Destroy()
	{
		RequirePost();
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_MODIFY);

		$environmentOrgId = Filter::RequireInt($_POST['environment_org_id']);

		$model = new EnvironmentOrgModel();
		if ($model->Load($environmentOrgId) == -1)
			throw new InvalidEntityException();

		$aKey = array('environment_org_id' => $environmentOrgId);
		$model->Delete($aKey);
	}
}