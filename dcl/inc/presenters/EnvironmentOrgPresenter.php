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

class EnvironmentOrgPresenter
{
	public function Create(EnvironmentOrgModel $model = null, array $errors = null)
	{
		commonHeader();

		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('menuAction', 'EnvironmentOrg.Insert');

		$viewData = new stdClass();

		if ($model == null)
		{
			$viewData->OrganizationId = Filter::RequireInt($_REQUEST['org_id']);
		}
		else
		{
			$viewData->OrganizationId = $model->org_id;
			$viewData->EnvironmentId = $model->environment_id;
			$viewData->BeginDt = $model->begin_dt;
			$viewData->EndDt = $model->end_dt;
		}

		$orgModel = new OrganizationModel();
		if ($orgModel->Load($viewData->OrganizationId) == -1)
		{
			ShowError("Could not load organization id " . $viewData->OrganizationId);
			return;
		}

		$smartyHelper->assignByRef('ViewData', $viewData);
		$smartyHelper->assign('TXT_FUNCTION', 'Add Environment for ' . $orgModel->name);
		$smartyHelper->assign('ERRORS', $errors);

		$smartyHelper->Render('EnvironmentOrgForm.tpl');
	}

	public function Edit(EnvironmentOrgModel $model, array $errors = null)
	{
		commonHeader();

		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('menuAction', 'EnvironmentOrg.Update');

		$viewData = new stdClass();

		$viewData->EnvironmentOrgId = $model->environment_org_id;
		$viewData->OrganizationId = $model->org_id;
		$viewData->EnvironmentId = $model->environment_id;
		$viewData->BeginDt = DclSmallDateTime::ToDisplay($model->begin_dt);
		$viewData->EndDt = DclSmallDateTime::ToDisplay($model->end_dt);

		$orgModel = new OrganizationModel();
		if ($orgModel->Load($viewData->OrganizationId) == -1)
		{
			ShowError("Could not load organization id " . $viewData->OrganizationId);
			return;
		}

		$smartyHelper->assignByRef('ViewData', $viewData);
		$smartyHelper->assign('TXT_FUNCTION', 'Update Environment for ' . $orgModel->name);
		$smartyHelper->assign('ERRORS', $errors);

		$smartyHelper->Render('EnvironmentOrgForm.tpl');
	}

	public function Delete(EnvironmentOrgModel $model)
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_MODIFY);

		$envModel = new EnvironmentModel();
		if ($envModel->Load($model->environment_id) == -1)
		{
			ShowError('Environment not found.');
			return;
		}

		ShowDeleteYesNo('Organization Environment', 'EnvironmentOrg.Destroy', $model->environment_org_id, $model->environment_name);
	}
}