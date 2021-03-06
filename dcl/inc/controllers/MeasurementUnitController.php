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

class MeasurementUnitController
{
	public function Index()
	{
		RequirePermission(DCL_ENTITY_MEASUREMENTUNIT, DCL_PERM_VIEW);

		$presenter = new MeasurementUnitPresenter();
		$presenter->Index();
	}

	public function Create()
	{
		RequirePermission(DCL_ENTITY_MEASUREMENTUNIT, DCL_PERM_ADD);

		$presenter = new MeasurementUnitPresenter();
		$presenter->Create();
	}

	public function Insert()
	{
		RequirePost();
		RequirePermission(DCL_ENTITY_MEASUREMENTUNIT, DCL_PERM_ADD);

		$model = new MeasurementUnitModel();
		$validator = new MeasurementUnitValidationHelper($_POST);
		if (!$validator->IsValid())
		{
			$model->unit_name = $_POST['unit_name'];
			$model->unit_abbr = $_POST['unit_abbr'];

			$presenter = new MeasurementUnitPresenter();
			$presenter->Create($model, $validator->Errors());

			return;
		}

		$model->unit_name = $_POST['unit_name'];
		$model->unit_abbr = $_POST['unit_abbr'];
		$model->create_dt = DCL_NOW;
		$model->create_by = DCLID;
		$model->update_dt = DCL_NOW;
		$model->update_by = DCLID;

		$model->Add();

		SetRedirectMessage('Success', 'Measurement unit added successfully.');
		RedirectToAction('MeasurementUnit', 'Index');
	}

	public function Edit()
	{
		RequirePermission(DCL_ENTITY_MEASUREMENTUNIT, DCL_PERM_MODIFY);

		$model = new MeasurementUnitModel();
		$model->Load($_REQUEST['id']);

		$presenter = new MeasurementUnitPresenter();
		$presenter->Edit($model);
	}

	public function Update()
	{
		RequirePost();
		RequirePermission(DCL_ENTITY_MEASUREMENTUNIT, DCL_PERM_MODIFY);

		$id = @Filter::RequireInt($_POST['id']);

		$model = new MeasurementUnitModel();
		$model->Load($id);

		$validator = new MeasurementUnitValidationHelper($_POST);
		if (!$validator->IsValid())
		{
			$presenter = new MeasurementUnitPresenter();
			$presenter->Edit($model, $validator->Errors());
			return;
		}

		$model->unit_name = $_POST['unit_name'];
		$model->unit_abbr = $_POST['unit_abbr'];
		$model->update_dt = DCL_NOW;
		$model->update_by = DCLID;

		$model->Edit();

		SetRedirectMessage('Success', 'Measurement unit updated successfully.');
		RedirectToAction('MeasurementUnit', 'Index');
	}

	public function Delete()
	{
		RequirePermission(DCL_ENTITY_MEASUREMENTUNIT, DCL_PERM_DELETE);

		$id = @Filter::RequireInt($_REQUEST['id']);

		$model = new MeasurementUnitModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		$presenter = new MeasurementUnitPresenter();
		$presenter->Delete($model);
	}

	public function Destroy()
	{
		RequirePost();
		RequirePermission(DCL_ENTITY_MEASUREMENTUNIT, DCL_PERM_DELETE);

		$id = @Filter::RequireInt($_POST['id']);

		$model = new MeasurementUnitModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		if (!$model->HasFKRef($id))
		{
			$model->Delete(array('measurement_unit_id' => $id));
			SetRedirectMessage('Success', 'Measurement unit was deleted successfully.');
		}
		else
		{
			SetRedirectMessage('Success', 'Measurement unit was not deleted because other items reference it.');
		}

		RedirectToAction('MeasurementUnit', 'Index');
	}
}