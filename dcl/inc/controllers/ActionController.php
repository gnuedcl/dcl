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

class ActionController
{
	public function Index()
	{
		$presenter = new ActionPresenter();
		$presenter->Index();
	}

	public function Create()
	{
		$presenter = new ActionPresenter();
		$presenter->Create();
	}

	public function Insert()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_ACTION, DCL_PERM_ADD))
		{
			throw new Exception(STR_CMMN_PERMISSIONDENIED);
		}

		$model = new ActionModel();
		$model->InitFrom_POST();
		$model->Add();

		SetRedirectMessage('Success', 'Action added successfully.');
		RedirectToAction('Action', 'Index');
	}

	public function Edit()
	{
		if (($id = @Filter::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}

		$model = new ActionModel();
		if ($model->Load($id) == -1)
		{
			throw new InvalidEntityException();
		}

		$presenter = new ActionPresenter();
		$presenter->Edit($model);
	}

	public function Update()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_ACTION, DCL_PERM_MODIFY))
		{
			throw new PermissionDeniedException();
		}

		$model = new ActionModel();
		$model->InitFrom_Post();
		$model->Edit();
		
		SetRedirectMessage('Success', 'Action updated successfully.');
		RedirectToAction('Action', 'Index');
	}

	public function Delete()
	{
		$model = new ActionModel();
		if (($id = @Filter::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if ($model->Load($id) == -1)
		{
			throw new InvalidEntityException();
		}

		$presenter = new ActionPresenter();
		$presenter->Delete($model);
	}

	public function Destroy()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_ACTION, DCL_PERM_DELETE))
		{
			throw new PermissionDeniedException();
		}

		$model = new ActionModel();
		if (($id = @Filter::ToInt($_POST['id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if ($model->Load($id) == -1)
		{
			throw new InvalidEntityException();
		}

		if (!$model->HasFKRef($id))
		{
			$model->Delete();
			SetRedirectMessage('Success', 'Action was deleted successfully.');
		}
		else
		{
			$model->SetActive($id, false);
			SetRedirectMessage('Success', 'Action was deactivated because other items reference it.');
		}

		RedirectToAction('Action', 'Index');
	}
}
