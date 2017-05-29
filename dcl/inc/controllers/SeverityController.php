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
class SeverityController extends AbstractController
{
	public function __construct()
	{
		parent::__construct();

		$this->model = new SeverityModel();
		$this->Entity = DCL_ENTITY_SEVERITY;
	}

	public function Index()
	{
		$presenter = new SeverityPresenter();
		$presenter->Index();
	}

	public function Create()
	{
		$presenter = new SeverityPresenter();
		$presenter->Create();
	}

	public function Insert()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_SEVERITY, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$this->model->InitFrom_POST();
		$this->model->Add();

		SetRedirectMessage('Success', 'Severity added successfully.');
		RedirectToAction('Severity', 'Index');
	}

	public function Edit()
	{
		if (($id = @Filter::ToInt($_REQUEST['id'])) === null)
			throw new InvalidDataException();
		
		if ($this->model->Load(array('id' => $id)) == -1)
			throw new InvalidEntityException();

		$presenter = new SeverityPresenter();
		$presenter->Edit($this->model);
	}

	public function Update()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_SEVERITY, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$this->model->InitFrom_POST();
		$this->model->Edit();

		SetRedirectMessage('Success', 'Severity updated successfully.');
		RedirectToAction('Severity', 'Index');
	}

	public function Delete()
	{
		if (($id = @Filter::ToInt($_REQUEST['id'])) === null)
			throw new InvalidDataException();
		
		if ($this->model->Load(array('id' => $id)) == -1)
			throw new InvalidEntityException();

		$presenter = new SeverityPresenter();
		$presenter->Delete($this->model);
	}

	public function Destroy()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_SEVERITY, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		if (($id = @Filter::ToInt($_REQUEST['id'])) === null)
			throw new InvalidDataException();
		
		parent::Destroy(array('id' => $id));

		SetRedirectMessage('Success', 'Severity deleted successfully.');
		RedirectToAction('Severity', 'Index');
	}
}
