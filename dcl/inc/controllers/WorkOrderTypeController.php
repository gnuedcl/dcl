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

class WorkOrderTypeController extends AbstractController
{
	public function __construct()
	{
		parent::__construct();
		$this->model = new WorkOrderTypeModel();
		$this->sKeyField = 'wo_type_id';
		$this->sDescField = 'type_name';
		$this->Entity = DCL_ENTITY_WORKORDERTYPE;
	}

	public function Index()
	{
		$presenter = new WorkOrderTypePresenter();
		$presenter->Index();
	}

	public function Create()
	{
		$presenter = new WorkOrderTypePresenter();
		$presenter->Create();
	}

	public function Insert()
	{
		parent::InsertFromArray($_POST);

		SetRedirectMessage('Success', 'Work order type added successfully.');
		RedirectToAction('WorkOrderType', 'Index');
	}

	public function Edit()
	{
		if (($woTypeId = @Filter::ToInt($_REQUEST['wo_type_id'])) == -1)
			throw new InvalidDataException();

		if ($this->model->Load($woTypeId) == -1)
			throw new InvalidEntityException();

		$presenter = new WorkOrderTypePresenter();
		$presenter->Edit($this->model);
	}

	public function Update()
	{
		parent::UpdateFromArray($_POST);

		SetRedirectMessage('Success', 'Work order type updated successfully.');
		RedirectToAction('WorkOrderType', 'Index');
	}

	public function Delete()
	{
		if (($woTypeId = @Filter::ToInt($_REQUEST['wo_type_id'])) == -1)
			throw new InvalidDataException();

		if ($this->model->Load($woTypeId) == -1)
			throw new InvalidEntityException();

		$presenter = new WorkOrderTypePresenter();
		$presenter->Delete($this->model);
	}

	public function Destroy()
	{
		if (($woTypeId = @Filter::ToInt($_REQUEST['id'])) == -1)
			throw new InvalidDataException();

		parent::DestroyFromArray(array('wo_type_id' => $woTypeId));

		SetRedirectMessage('Success', 'Work order type deleted successfully.');
		RedirectToAction('WorkOrderType', 'Index');
	}
}
