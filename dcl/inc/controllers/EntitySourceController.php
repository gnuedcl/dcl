<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2010 Free Software Foundation
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

class EntitySourceController extends AbstractController
{
	public function __construct()
	{
		parent::__construct();
		$this->model = new EntitySourceModel();
		$this->sKeyField = 'entity_source_id';
		$this->sDescField = 'entity_source_name';
		$this->Entity = DCL_ENTITY_SOURCE;
	}

	public function Index()
	{
		$presenter = new EntitySourcePresenter();
		$presenter->Index();
	}

	public function Create()
	{
		$presenter = new EntitySourcePresenter();
		$presenter->Create();
	}

	public function Insert()
	{
		parent::Insert($_POST);

		SetRedirectMessage('Success', 'Entity source added successfully.');
		RedirectToAction('EntitySource', 'Index');
	}

	public function Edit()
	{
		if (($entitySourceId = @DCL_Sanitize::ToInt($_REQUEST['entity_source_id'])) == -1)
			throw new InvalidDataException();

		if ($this->model->Load($entitySourceId) == -1)
			throw new InvalidEntityException();

		$presenter = new EntitySourcePresenter();
		$presenter->Edit($this->model);
	}

	public function Update()
	{
		parent::Update($_POST);

		SetRedirectMessage('Success', 'Entity source updated successfully.');
		RedirectToAction('EntitySource', 'Index');
	}

	public function Delete()
	{
		if (($entitySourceId = @DCL_Sanitize::ToInt($_REQUEST['entity_source_id'])) == -1)
			throw new InvalidDataException();

		if ($this->model->Load($entitySourceId) == -1)
			throw new InvalidEntityException();

		$presenter = new EntitySourcePresenter();
		$presenter->Delete($this->model);
	}

	public function Destroy()
	{
		if (($entitySourceId = @DCL_Sanitize::ToInt($_REQUEST['id'])) == -1)
			throw new InvalidDataException();

		parent::Destroy(array('entity_source_id' => $entitySourceId));

		SetRedirectMessage('Success', 'Entity source deleted successfully.');
		RedirectToAction('EntitySource', 'Index');
	}
}
