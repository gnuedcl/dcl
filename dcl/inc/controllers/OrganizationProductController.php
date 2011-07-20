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

class OrganizationProductController extends AbstractController
{
	public function __construct()
	{
		parent::__construct();
		
		$this->model = new OrganizationProductModel();
		$this->Entity = DCL_ENTITY_ORG;
		$this->sKeyField = '';
		
		$this->sCreatedDateField = 'created_on';
		$this->sCreatedByField = 'created_by';
	}
	
	public function Edit()
	{
		if (($orgId = Filter::ToInt($_REQUEST['org_id'])) === null)
			throw new InvalidDataException();

		$presenter = new OrganizationProductPresenter();
		$presenter->Edit($orgId);
	}

	public function Update()
	{
		global $g_oSec;

		if (($id = Filter::ToInt($_POST['org_id'])) === null)
			throw new InvalidDataException();

		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY, $id))
			throw new PermissionDeniedException();

		CleanArray($_POST);

		$aProducts = @Filter::ToIntArray($_POST['product_id']);
		$organizationProductModel = new OrganizationProductModel();
		$organizationProductModel->UpdateProducts($id, $aProducts);

		SetRedirectMessage('Success', 'Products updated successfully.');
		RedirectToAction('Organization', 'Detail', 'org_id=' . $id);
	}
}
