<?php
/*
 * $Id$
 *
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2004 Free Software Foundation
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
class PriorityController extends AbstractController
{
	public function __construct()
	{
		parent::__construct();
		
		$this->model = new PriorityModel();
		$this->sKeyField = 'id';
		$this->sDescField = 'name';
		$this->Entity = DCL_ENTITY_PRIORITY;
	}

	public function Index()
	{
		$presenter = new PriorityPresenter();
		$presenter->Index();
	}

	function Create()
	{
		$presenter = new PriorityPresenter();
		$presenter->Create();
	}

	function Insert()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRIORITY, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$this->model->InitFrom_POST();
		$this->model->Add();

		SetRedirectMessage('Success', 'New priority added successfully.');
		RedirectToAction('Priority', 'Index');
	}

	function Edit()
	{
		if (($id = @Filter::ToInt($_REQUEST['id'])) === null)
			throw new InvalidDataException();
		
		if ($this->model->Load($id) == -1)
			throw new InvalidEntityException();

		$presenter = new PriorityPresenter();
		$presenter->Edit($this->model);
	}

	function Update()
	{
		global $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_PRIORITY, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$this->model->InitFrom_POST();
		$this->model->Edit();

		SetRedirectMessage('Success', 'Priority updated successfully.');
		RedirectToAction('Priority', 'Index');
	}
	
	function Delete()
	{
		if (($id = @Filter::ToInt($_REQUEST['id'])) === null)
			throw new InvalidDataException();
		
		if ($this->model->Load($id) == -1)
			throw new InvalidEntityException();

		$presenter = new PriorityPresenter();
		$presenter->Delete($this->model);
	}

	function Destroy()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRIORITY, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		if (($id = @Filter::ToInt($_REQUEST['id'])) === null)
			throw new InvalidDataException();
		
		parent::Destroy(array('id' => $id));

		SetRedirectMessage('Success', 'Priority deleted successfully.');
		RedirectToAction('Priority', 'Index');
	}
}
