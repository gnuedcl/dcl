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
		RequirePost();
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
		RequirePost();
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_MODIFY);

		CleanArray($_POST);

		$organizationId = @Filter::RequireInt($_POST['org_id']);

		$model = new OrganizationModel();
		$model->Load(array('org_id' => $organizationId));
		
		$name = $_POST['name'];
		$active = @Filter::ToYN($_POST['active']);
		
		if ($name != $model->name || $active != $model->active)
		{
			$model->name = $name;
			$model->active = $active;
			
			$model->Edit(array('created_on', 'created_by'));
		}
		
		$orgTypes = @Filter::ToIntArray($_POST['org_type_id']);
		
		$organizationTypeXrefModel = new OrganizationTypeXrefModel();
		$organizationTypeXrefModel->Edit($organizationId, $orgTypes);
						
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
		RequirePost();
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_DELETE);
		
		$organizationId = @Filter::RequireInt($_POST['id']);
		
		$organizationModel = new OrganizationModel();
		$organizationModel->Delete($organizationId);

		SetRedirectMessage('Success', 'Organization deleted successfully.');
		RedirectToAction('htmlOrgBrowse', 'show', 'filterActive=Y');
	}
	
	public function Detail()
	{
		global $g_oSec, $g_oSession;
		
		$organizationId = @Filter::RequireInt($_REQUEST['org_id']);
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_VIEW, $organizationId);
		if ($g_oSec->IsOrgUser() && !in_array($organizationId, explode(',', $g_oSession->Value('member_of_orgs'))))
			throw new PermissionDeniedException();

		$model = new OrganizationModel();
		if ($model->Load($organizationId) == -1)
			throw new InvalidEntityException();
		
		$presenter = new OrganizationPresenter();
		$presenter->Detail($model);
	}
}
