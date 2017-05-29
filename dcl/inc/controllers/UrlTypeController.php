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

class UrlTypeController extends AbstractController
{
	public function __construct()
	{
		parent::__construct();
		
		$this->model = new UrlTypeModel();
		$this->sKeyField = 'url_type_id';
		$this->Entity = DCL_ENTITY_URLTYPE;
	}

	public function Index()
	{
		$presenter = new UrlTypePresenter();
		$presenter->Index();
	}

	public function Create()
	{
		$presenter = new UrlTypePresenter();
		$presenter->Create();
	}

	public function Insert()
	{
		parent::InsertFromArray($_POST);

		SetRedirectMessage('Success', 'Url type added successfully.');
		RedirectToAction('UrlType', 'Index');
	}

	public function Edit()
	{
		if (($urlTypeId = @Filter::ToInt($_REQUEST['url_type_id'])) == -1)
			throw new InvalidDataException();

		if ($this->model->Load($urlTypeId) == -1)
			throw new InvalidEntityException();

		$presenter = new UrlTypePresenter();
		$presenter->Edit($this->model);
	}

	public function Update()
	{
		parent::UpdateFromArray($_POST);

		SetRedirectMessage('Success', 'Url type updated successfully.');
		RedirectToAction('UrlType', 'Index');
	}

	public function Delete()
	{
		if (($urlTypeId = @Filter::ToInt($_REQUEST['url_type_id'])) == -1)
			throw new InvalidDataException();

		if ($this->model->Load($urlTypeId) == -1)
			throw new InvalidEntityException();

		$presenter = new UrlTypePresenter();
		$presenter->Delete($this->model);
	}

	public function Destroy()
	{
		if (($urlTypeId = @Filter::ToInt($_REQUEST['id'])) == -1)
			throw new InvalidDataException();

		parent::DestroyFromArray(array('url_type_id' => $urlTypeId));

		SetRedirectMessage('Success', 'Url type deleted successfully.');
		RedirectToAction('UrlType', 'Index');
	}
}
