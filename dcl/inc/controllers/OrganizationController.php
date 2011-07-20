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

class OrganizationController
{
	public function Index()
	{
		
	}
	
	public function Create()
	{
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_ADD);
		
		$presenter = new OrganizationPresenter();
		$presenter->Create();
	}

	public function Insert()
	{
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_ADD);
		CleanArray($_POST);

		$viewModel = new OrganizationCreateViewModel();
		$viewModel->Insert($_POST);

		if (EvaluateReturnTo())
			return;

		SetRedirectMessage('Success', 'Organization added successfully.');
		RedirectToAction('Organization', 'Detail', 'org_id=' . $viewModel->OrganizationId);
	}

	public function Edit()
	{
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_MODIFY);

		$orgId = @Filter::RequireInt($_REQUEST['org_id']);
		$model = new OrganizationModel();
		if ($model->Load($orgId) == -1)
		    throw new InvalidEntityException();
		    
		$presenter = new OrganizationPresenter();
		$presenter->Edit($model);
	}

	public function Update()
	{
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_MODIFY);

		CleanArray($_POST);

		$organizationId = @Filter::RequireInt($_POST['org_id']);
		$aValues = array('org_id' => $organizationId,
						'name' => $_POST['name'],
						'org_type_id' => @Filter::ToIntArray($_POST['org_type_id']),
						'active' => @Filter::ToYN($_POST['active'])
						);
						
		$obj = new boOrg();
		$obj->modify($aValues);

		SetRedirectMessage('Success', 'Organization updated successfully.');
		RedirectToAction('Organization', 'Detail', 'org_id=' . $organizationId);
	}

	public function Delete()
	{
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_DELETE);
		
		$organizationId = @Filter::RequireInt($_REQUEST['org_id']);

		$model = new OrganizationModel();
		$model->Load(array('org_id' => $organizationId));
		
		$presenter = new OrganizationPresenter();
		$presenter->Delete($model);
	}

	public function Destroy()
	{
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_DELETE);
		
		$organizationId = @Filter::RequireInt($_POST['id']);
		
		$obj = new boOrg();
		CleanArray($_POST);

		$aKey = array('org_id' => $organizationId);
		$obj->delete($aKey);

		SetRedirectMessage('Success', 'Organization deleted successfully.');
		RedirectToAction('htmlOrgBrowse', 'show', 'filterActive=Y');
	}
	
	public function Detail()
	{
		global $g_oSec, $g_oSession;
		
		$organizationId = @Filter::RequireInt($_REQUEST['org_id']);
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_VIEW, $organizationId);
		if ($g_oSec->IsOrgUser() && !in_array($id, split(',', $g_oSession->Value('member_of_orgs'))))
			throw new PermissionDeniedException();

		$model = new OrganizationModel();
		if ($model->Load($organizationId) == -1)
			throw new InvalidEntityException();
		
		$presenter = new OrganizationPresenter();
		$presenter->Detail($model);
	}
}
