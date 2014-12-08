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

class OrganizationOutageSlaController
{
	public function Edit()
	{
		RequirePermission(DCL_ENTITY_OUTAGE, DCL_PERM_VIEW);

		$orgId = Filter::RequireInt($_REQUEST['org_id']);
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_MODIFY, $orgId);

		$orgOutageModel = new OrganizationOutageSlaModel();
		if ($orgOutageModel->Load($orgId, false) == -1)
			$orgOutageModel->org_id = $orgId;

		$presenter = new OrganizationOutageSlaPresenter();
		$presenter->Edit($orgOutageModel);
	}

	public function Update()
	{
		RequirePost();
		RequirePermission(DCL_ENTITY_OUTAGE, DCL_PERM_VIEW);

		$orgId = Filter::RequireInt($_POST['org_id']);
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_MODIFY, $orgId);

		$orgOutageModel = new OrganizationOutageSlaModel();
		$isUpdate = true;

		if ($orgOutageModel->Load($orgId, false) == -1)
		{
			$orgOutageModel->org_id = $orgId;
			$orgOutageModel->create_by = DCLID;
			$orgOutageModel->create_dt = DCL_NOW;

			$isUpdate = false;
		}

		$orgOutageModel->outage_sla = Filter::ToDecimal($_POST['outage_sla']);
		if ($orgOutageModel->outage_sla === null)
			$orgOutageModel->outage_sla = 0;

		$orgOutageModel->outage_sla_warn = Filter::ToDecimal($_POST['outage_sla_warn']);
		$orgOutageModel->update_by = DCLID;
		$orgOutageModel->update_dt = DCL_NOW;

		if ($isUpdate)
			$orgOutageModel->Edit();
		else
			$orgOutageModel->Add();

		SetRedirectMessage('Success', 'Organization outage SLA successfully updated.');
		RedirectToAction('Organization', 'Detail', 'org_id=' . $orgId);
	}
}