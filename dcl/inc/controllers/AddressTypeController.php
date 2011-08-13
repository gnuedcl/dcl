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

class AddressTypeController extends AbstractController
{
	public function __construct()
	{
		parent::__construct();
		
		$this->model = new AddressTypeModel();
		$this->sKeyField = 'addr_type_id';
		$this->Entity = DCL_ENTITY_ADDRTYPE;
	}

	public function Index()
	{
		$presenter = new AddressTypePresenter();
		$presenter->Index();
	}

	public function Create()
	{
		$presenter = new AddressTypePresenter();
		$presenter->Create();
	}

	public function Insert()
	{
		parent::Insert($_POST);

		SetRedirectMessage('Success', 'Address type added successfully.');
		RedirectToAction('AddressType', 'Index');
	}

	public function Edit()
	{
		if (($id = @Filter::ToInt($_REQUEST['addr_type_id'])) === null)
		{
			throw new InvalidDataException();
		}

		$model = new AddressTypeModel();
		if ($model->Load($id) == -1)
		{
			throw new InvalidEntityException();
		}

		$presenter = new AddressTypePresenter();
		$presenter->Edit($model);
	}

	public function Update()
	{
		parent::Update($_POST);

		SetRedirectMessage('Success', 'Address type updated successfully.');
		RedirectToAction('AddressType', 'Index');
	}

	public function Delete()
	{
		if (($id = Filter::ToInt($_REQUEST['addr_type_id'])) === null)
		{
			throw new InvalidDataException();
		}

		$model = new AddressTypeModel();
		if ($model->Load($id) == -1)
		{
			throw new InvalidEntityException();
		}

		$presenter = new AddressTypePresenter();
		$presenter->Delete($model);
	}

	public function Destroy()
	{
		if (($id = Filter::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}

		parent::Destroy(array('addr_type_id' => $id));

		SetRedirectMessage('Success', 'Address type was deleted successfully.');
		RedirectToAction('AddressType', 'Index');
	}
}
