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

class NoteTypeController extends AbstractController
{
	public function __construct()
	{
		parent::__construct();
		
		$this->model = new NoteTypeModel();
		$this->sKeyField = 'note_type_id';
		$this->Entity = DCL_ENTITY_NOTETYPE;
	}

	public function Index()
	{
		$presenter = new NoteTypePresenter();
		$presenter->Index();
	}

	public function Create()
	{
		$presenter = new NoteTypePresenter();
		$presenter->Create();
	}

	public function Insert()
	{
		parent::InsertFromArray($_POST);

		SetRedirectMessage('Success', 'Note type added successfully.');
		RedirectToAction('NoteType', 'Index');
	}

	public function Edit()
	{
		if (($noteTypeId = @Filter::ToInt($_REQUEST['note_type_id'])) == -1)
			throw new InvalidDataException();

		if ($this->model->Load($noteTypeId) == -1)
			throw new InvalidEntityException();

		$presenter = new NoteTypePresenter();
		$presenter->Edit($this->model);
	}

	public function Update()
	{
		parent::UpdateFromArray($_POST);

		SetRedirectMessage('Success', 'Note type updated successfully.');
		RedirectToAction('NoteType', 'Index');
	}

	public function Delete()
	{
		if (($noteTypeId = @Filter::ToInt($_REQUEST['note_type_id'])) == -1)
			throw new InvalidDataException();

		if ($this->model->Load($noteTypeId) == -1)
			throw new InvalidEntityException();

		$presenter = new NoteTypePresenter();
		$presenter->Delete($this->model);
	}

	public function Destroy()
	{
		if (($noteTypeId = @Filter::ToInt($_REQUEST['id'])) == -1)
			throw new InvalidDataException();

		parent::DestroyFromArray(array('note_type_id' => $noteTypeId));

		SetRedirectMessage('Success', 'Note type deleted successfully.');
		RedirectToAction('NoteType', 'Index');
	}
}
