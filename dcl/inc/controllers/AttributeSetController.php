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

class AttributeSetController
{
	public function Index()
	{
		$presenter = new AttributeSetPresenter();
		$presenter->Index();
	}

	public function Create()
	{
		$presenter = new AttributeSetPresenter();
		$presenter->Create();
	}

	public function Insert()
	{
		global $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$model = new AttributeSetModel();
		$model->InitFrom_POST();
		$model->Add();

		SetRedirectMessage('Success', 'New attribute set added successfully.');
		RedirectToAction('AttributeSet', 'Index');
	}

	public function Edit()
	{
		if (($id = @Filter::ToInt($_REQUEST['id'])) === null)
			throw new InvalidDataException();

		$model = new AttributeSetModel();
		if ($model->Load(array('id' => $id)) == -1)
			throw new InvalidEntityException();

		$presenter = new AttributeSetPresenter();
		$presenter->Edit($model);
	}

	public function Update()
	{
		global $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$model = new AttributeSetModel();
		$model->InitFrom_POST();
		$model->Edit();

		SetRedirectMessage('Success', 'Updated attribute set successfully.');
		RedirectToAction('AttributeSet', 'Index');
	}

	function Delete()
	{
		if (($id = @Filter::ToInt($_REQUEST['id'])) === null)
			throw new InvalidDataException();
		
		$model = new AttributeSetModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		$presenter = new AttributeSetPresenter();
		$presenter->Delete($model);
	}

	function Destroy()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		if (($id = @Filter::ToInt($_REQUEST['id'])) === null)
			throw new InvalidDataException();
		
		$model = new AttributeSetModel();
		if ($model->Load(array('id' => $id)) == -1)
			throw new InvalidEntityException();

		if (!$model->HasFKRef($id))
		{
			$model->Delete(array('id' => $id));
			SetRedirectMessage('Success', 'Attribute set was deleted successfully.');
		}
		else
		{
			$model->SetActive(array('id' => $id), false);
			SetRedirectMessage('Success', 'Attribute set was deactivated because other items reference it.');
		}

		RedirectToAction('AttributeSet', 'Index');
	}
}
