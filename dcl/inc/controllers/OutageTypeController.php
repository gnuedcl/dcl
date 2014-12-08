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

class OutageTypeController
{
	public function Index()
	{
		RequirePermission(DCL_ENTITY_OUTAGETYPE, DCL_PERM_VIEW);

		$presenter = new OutageTypePresenter();
		$presenter->Index();
	}

	public function Create()
	{
		RequirePermission(DCL_ENTITY_OUTAGETYPE, DCL_PERM_ADD);

		$presenter = new OutageTypePresenter();
		$presenter->Create();
	}

	public function Insert()
	{
		RequirePermission(DCL_ENTITY_OUTAGETYPE, DCL_PERM_ADD);
		RequirePost();

		$model = new OutageTypeModel();
		$validator = new OutageTypeValidationHelper($_POST);
		if (!$validator->IsValid())
		{
			$model->is_down = Filter::ToYN($_POST['is_down']);
			$model->is_infrastructure = Filter::ToYN($_POST['is_infrastructure']);
			$model->is_planned = Filter::ToYN($_POST['is_planned']);
			$model->outage_type_name = $_POST['outage_type_name'];

			$presenter = new OutageTypePresenter();
			$presenter->Create($model, $validator->Errors());

			return;
		}

		$model->is_down = Filter::ToYN($_POST['is_down']);
		$model->is_infrastructure = Filter::ToYN($_POST['is_infrastructure']);
		$model->is_planned = Filter::ToYN($_POST['is_planned']);
		$model->outage_type_name = $_POST['outage_type_name'];
		$model->create_dt = DCL_NOW;
		$model->create_by = DCLID;
		$model->update_dt = DCL_NOW;
		$model->update_by = DCLID;

		$model->Add();

		SetRedirectMessage('Success', 'Outage type added successfully.');
		RedirectToAction('OutageType', 'Index');
	}

	public function Edit()
	{
		RequirePermission(DCL_ENTITY_OUTAGETYPE, DCL_PERM_MODIFY);

		$model = new OutageTypeModel();
		$model->Load($_REQUEST['id']);

		$presenter = new OutageTypePresenter();
		$presenter->Edit($model);
	}

	public function Update()
	{
		RequirePermission(DCL_ENTITY_OUTAGETYPE, DCL_PERM_MODIFY);
		RequirePost();

		$id = @Filter::RequireInt($_POST['id']);

		$model = new OutageTypeModel();
		$model->Load($id);

		$validator = new OutageTypeValidationHelper($_POST);
		if (!$validator->IsValid())
		{
			$presenter = new OutageTypePresenter();
			$presenter->Edit($model, $validator->Errors());
			return;
		}

		$model->is_down = Filter::ToYN($_POST['is_down']);
		$model->is_infrastructure = Filter::ToYN($_POST['is_infrastructure']);
		$model->is_planned = Filter::ToYN($_POST['is_planned']);
		$model->outage_type_name = $_POST['outage_type_name'];
		$model->update_dt = DCL_NOW;
		$model->update_by = DCLID;

		$model->Edit();

		SetRedirectMessage('Success', 'Outage type updated successfully.');
		RedirectToAction('OutageType', 'Index');
	}

	public function Delete()
	{
		RequirePermission(DCL_ENTITY_OUTAGETYPE, DCL_PERM_DELETE);

		$id = @Filter::RequireInt($_REQUEST['id']);

		$model = new OutageTypeModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		$presenter = new OutageTypePresenter();
		$presenter->Delete($model);
	}

	public function Destroy()
	{
		RequirePermission(DCL_ENTITY_OUTAGETYPE, DCL_PERM_DELETE);
		RequirePost();

		$id = @Filter::RequireInt($_POST['id']);

		$model = new OutageTypeModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		if (!$model->HasFKRef($id))
		{
			$model->Delete(array('outage_type_id' => $id));
			SetRedirectMessage('Success', 'Outage type was deleted successfully.');
		}
		else
		{
			$model->SetActive(array('outage_type_id' => $id), false);
			SetRedirectMessage('Success', 'Outage type was deactivated because other items reference it.');
		}

		RedirectToAction('OutageType', 'Index');
	}
}