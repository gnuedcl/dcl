<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2014 Free Software Foundation
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

class OutageController
{
	public function Index()
	{
		RequirePermission(DCL_ENTITY_OUTAGE, DCL_PERM_VIEW);

		$presenter = new OutagePresenter();
		$presenter->Index();
	}

	public function Create()
	{
		RequirePermission(DCL_ENTITY_OUTAGE, DCL_PERM_ADD);

		$presenter = new OutagePresenter();
		$presenter->Create();
	}

	public function Insert()
	{
		RequirePost();
		RequirePermission(DCL_ENTITY_OUTAGE, DCL_PERM_ADD);

		$model = new OutageModel();
		$model->InitFrom_POST();
		$environments = @Filter::ToIntArray($_POST['environment_id']);
		$organizations = @Filter::ToIntArray($_POST['outage_orgs']);

		$validator = new OutageModelValidator($model);
		if (!$validator->Validate())
		{
			$presenter = new OutagePresenter();
			$presenter->Create($model, $environments, $organizations, $validator->Errors());
			return;
		}

		$outageTypeModel = new OutageTypeModel();
		$outageTypeModel->Load($model->outage_type_id);

		$outageStatusModel = new OutageStatusModel();
		$model->outage_status_id = $outageStatusModel->GetInitialStatusByOutagePlanned($outageTypeModel->is_planned == 'Y');

		$model->create_dt = DCL_NOW;
		$model->update_dt = DCL_NOW;
		$model->create_by = DCLID;
		$model->update_by = DCLID;
		$model->Add();

		// Add any referenced environments
		if ($environments != null && count($environments) > 0)
		{
			$envOutage = new EnvironmentOutageModel();
			$envOutage->outage_id = $model->outage_id;
			$envOutage->create_dt = DCL_NOW;
			$envOutage->create_by = DCLID;

			foreach ($environments as $environmentId)
			{
				$envOutage->environment_id = $environmentId;
				$envOutage->Add();
			}

			// If the outage has started, we need to capture all the orgs we know exist on the environment at that time
			if ($model->outage_start != '')
			{
				$envOrgModel = new EnvironmentOrgModel();
				$envOrgs = $envOrgModel->GetOrganizationIdsByEnvironments($model->outage_start, $environments);

				if ($envOrgs != null)
				{
					if ($organizations == null)
						$organizations = array();

					// Merge the organizations with any that have already been selected
					$organizations = array_unique(array_merge($organizations, $envOrgs));
				}
			}
		}

		// Add referenced organizations
		if ($organizations != null && count($organizations) > 0)
		{
			$orgOutage = new OutageOrgModel();
			$orgOutage->outage_id = $model->outage_id;
			$orgOutage->create_dt = DCL_NOW;
			$orgOutage->create_by = DCLID;

			foreach ($organizations as $orgId)
			{
				$orgOutage->org_id = $orgId;
				$orgOutage->Add();
			}
		}

		PubSub::Publish('Outage.Inserted', $model);

		SetRedirectMessage('Success', 'Outage added successfully.');
		RedirectToAction('Outage', 'Index');
	}

	public function Edit()
	{
		RequirePermission(DCL_ENTITY_OUTAGE, DCL_PERM_MODIFY);

		$id = @Filter::RequireInt($_REQUEST['id']);
		$model = new OutageModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		$envOutageModel = new EnvironmentOutageModel();
		$environments = $envOutageModel->LoadIdByOutageId($model->outage_id);

		$orgOutageModel = new OutageOrgModel();
		$organizations = $orgOutageModel->LoadIdByOutageId($model->outage_id);

		$presenter = new OutagePresenter();
		$presenter->Edit($model, $environments, $organizations);
	}

	public function Update()
	{
		RequirePost();
		RequirePermission(DCL_ENTITY_OUTAGE, DCL_PERM_MODIFY);

		$id = @Filter::RequireInt($_POST['id']);
		$environments = @Filter::ToIntArray($_POST['environment_id']);
		if ($environments == null)
			$environments = array();

		$organizations = @Filter::ToIntArray($_POST['outage_orgs']);
		if ($organizations == null)
			$organizations = array();

		$model = new OutageModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		$originalStart = $model->outage_start;

		$model->InitFrom_POST();

		$validator = new OutageModelValidator($model);
		if (!$validator->Validate())
		{
			$presenter = new OutagePresenter();
			$presenter->Edit($model, $environments, $organizations, $validator->Errors());
			return;
		}

		$model->update_dt = DCL_NOW;
		$model->update_by = DCLID;
		$model->Edit(array('create_dt', 'create_by'));

		$envOutage = new EnvironmentOutageModel();
		$originalEnvironments = $envOutage->LoadIdByOutageId($model->outage_id);

		$newEnvironments = array_values(array_diff($environments, $originalEnvironments));
		$removedEnvironments = array_values(array_diff($originalEnvironments, $environments));

		// Add any new environments
		if (count($newEnvironments) > 0)
		{
			$envOutage->outage_id = $model->outage_id;
			$envOutage->create_dt = DCL_NOW;
			$envOutage->create_by = DCLID;

			foreach ($newEnvironments as $environmentId)
			{
				$envOutage->environment_id = $environmentId;
				$envOutage->Add();
			}
		}

		// Remove environments no longer selected
		$envOutage->DeleteEnvironmentsForOutage($model->outage_id, $removedEnvironments);

		// If the outage has started, we need to make sure any orgs on selected environments are in sync
		if ($model->outage_start != '' && ($originalStart == '' || count($newEnvironments) > 0 || count($removedEnvironments) > 0))
		{
			$envOrgModel = new EnvironmentOrgModel();
			$envOrgs = $envOrgModel->GetOrganizationIdsByEnvironments($model->outage_start, $environments);
			$removedEnvOrgs = $envOrgModel->GetOrganizationIdsByEnvironments($model->outage_start, $removedEnvironments);

			if ($envOrgs != null)
			{
				if ($organizations == null)
					$organizations = array();

				// Merge the organizations with any that have already been selected
				$organizations = array_unique(array_merge($organizations, $envOrgs));
			}

			if ($removedEnvOrgs != null)
			{
				if ($organizations != null)
				{
					$organizations = array_values(array_diff($organizations, $removedEnvOrgs));
				}
			}
		}

		$orgOutage = new OutageOrgModel();
		$originalOrgs = $orgOutage->LoadIdByOutageId($model->outage_id);

		$newOrgs = array_values(array_diff($organizations, $originalOrgs));
		$removedOrgs = array_values(array_diff($originalOrgs, $organizations));

		// Add new organizations
		if (count($newOrgs) > 0)
		{
			$orgOutage->outage_id = $model->outage_id;
			$orgOutage->create_dt = DCL_NOW;
			$orgOutage->create_by = DCLID;

			foreach ($newOrgs as $orgId)
			{
				$orgOutage->org_id = $orgId;
				$orgOutage->Add();
			}
		}

		// Remove orgs no longer selected
		$orgOutage->DeleteOrgsForOutage($model->outage_id, $removedOrgs);

		PubSub::Publish('Outage.Updated', $model);

		SetRedirectMessage('Success', 'Outage updated successfully.');
		RedirectToAction('Outage', 'Index');
	}

	public function Delete()
	{

	}

	public function Destroy()
	{

	}
}