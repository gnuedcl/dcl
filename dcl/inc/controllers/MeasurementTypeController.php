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

class MeasurementTypeController
{
	public function Index()
	{
		RequirePermission(DCL_ENTITY_MEASUREMENTTYPE, DCL_PERM_VIEW);

		$presenter = new MeasurementTypePresenter();
		$presenter->Index();
	}

	public function Create()
	{
		RequirePermission(DCL_ENTITY_MEASUREMENTTYPE, DCL_PERM_ADD);

		$presenter = new MeasurementTypePresenter();
		$presenter->Create();
	}

	public function Insert()
	{
		RequirePost();
		RequirePermission(DCL_ENTITY_MEASUREMENTTYPE, DCL_PERM_ADD);

		$model = new MeasurementTypeModel();
		$validator = new MeasurementTypeValidationHelper($_POST);
		if (!$validator->IsValid())
		{
			$model->measurement_unit_id = $_POST['measurement_unit_id'];
			$model->measurement_name = $_POST['measurement_name'];

			$presenter = new MeasurementTypePresenter();
			$presenter->Create($model, $validator->Errors());

			return;
		}

		$model->measurement_unit_id = $_POST['measurement_unit_id'];
		$model->measurement_name = $_POST['measurement_name'];
		$model->create_dt = DCL_NOW;
		$model->create_by = DCLID;
		$model->update_dt = DCL_NOW;
		$model->update_by = DCLID;

		$model->Add();

		SetRedirectMessage('Success', 'Measurement type added successfully.');
		RedirectToAction('MeasurementType', 'Index');
	}

	public function Edit()
	{
		RequirePermission(DCL_ENTITY_MEASUREMENTTYPE, DCL_PERM_MODIFY);

		$model = new MeasurementTypeModel();
		$model->Load($_REQUEST['id']);

		$presenter = new MeasurementTypePresenter();
		$presenter->Edit($model);
	}

	public function Update()
	{
		RequirePost();
		RequirePermission(DCL_ENTITY_MEASUREMENTTYPE, DCL_PERM_MODIFY);

		$id = @Filter::RequireInt($_POST['id']);

		$model = new MeasurementTypeModel();
		$model->Load($id);

		$validator = new MeasurementTypeValidationHelper($_POST);
		if (!$validator->IsValid())
		{
			$presenter = new MeasurementTypePresenter();
			$presenter->Edit($model, $validator->Errors());
			return;
		}

		$model->measurement_unit_id = $_POST['measurement_unit_id'];
		$model->measurement_name = $_POST['measurement_name'];
		$model->update_dt = DCL_NOW;
		$model->update_by = DCLID;

		$model->Edit();

		SetRedirectMessage('Success', 'Measurement type updated successfully.');
		RedirectToAction('MeasurementType', 'Index');
	}

	public function Delete()
	{
		RequirePermission(DCL_ENTITY_MEASUREMENTTYPE, DCL_PERM_DELETE);

		$id = @Filter::RequireInt($_REQUEST['id']);

		$model = new MeasurementTypeModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		$presenter = new MeasurementTypePresenter();
		$presenter->Delete($model);
	}

	public function Destroy()
	{
		RequirePost();
		RequirePermission(DCL_ENTITY_MEASUREMENTTYPE, DCL_PERM_DELETE);

		$id = @Filter::RequireInt($_POST['id']);

		$model = new MeasurementTypeModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		if (!$model->HasFKRef($id))
		{
			$model->Delete(array('measurement_type_id' => $id));
			SetRedirectMessage('Success', 'Measurement type was deleted successfully.');
		}
		else
		{
			SetRedirectMessage('Success', 'Measurement type was not deleted because other items reference it.');
		}

		RedirectToAction('MeasurementType', 'Index');
	}
}