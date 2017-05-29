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

class PhoneTypeController extends AbstractController
{
	public function __construct()
	{
		parent::__construct();
		
		$this->model = new PhoneTypeModel();
		$this->sKeyField = 'phone_type_id';
		$this->Entity = DCL_ENTITY_PHONETYPE;
	}

	public function Index()
	{
		$presenter = new PhoneTypePresenter();
		$presenter->Index();
	}

	public function Create()
	{
		$presenter = new PhoneTypePresenter();
		$presenter->Create();
	}

	public function Insert()
	{
		parent::InsertFromArray($_POST);

		SetRedirectMessage('Success', 'Phone type added successfully.');
		RedirectToAction('PhoneType', 'Index');
	}

	public function Edit()
	{
		if (($phoneTypeId = @Filter::ToInt($_REQUEST['phone_type_id'])) == -1)
			throw new InvalidDataException();

		if ($this->model->Load($phoneTypeId) == -1)
			throw new InvalidEntityException();

		$presenter = new PhoneTypePresenter();
		$presenter->Edit($this->model);
	}

	public function Update()
	{
		parent::UpdateFromArray($_POST);

		SetRedirectMessage('Success', 'Phone type updated successfully.');
		RedirectToAction('PhoneType', 'Index');
	}

	public function Delete()
	{
		if (($phoneTypeId = @Filter::ToInt($_REQUEST['phone_type_id'])) == -1)
			throw new InvalidDataException();

		if ($this->model->Load($phoneTypeId) == -1)
			throw new InvalidEntityException();

		$presenter = new PhoneTypePresenter();
		$presenter->Delete($this->model);
	}

	public function Destroy()
	{
		if (($phoneTypeId = @Filter::ToInt($_REQUEST['id'])) == -1)
			throw new InvalidDataException();

		parent::DestroyFromArray(array('phone_type_id' => $phoneTypeId));

		SetRedirectMessage('Success', 'Phone type deleted successfully.');
		RedirectToAction('PhoneType', 'Index');
	}
}
