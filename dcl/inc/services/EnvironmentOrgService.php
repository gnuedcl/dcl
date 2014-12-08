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

class EnvironmentOrgService
{
	public function GetData()
	{
		global $g_oSession;

		RequirePermission(DCL_ENTITY_ENVIRONMENT, DCL_PERM_VIEW);
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_VIEW);

		$sql = 'SELECT environment_id, org_id FROM dcl_environment_org';
		if (IsOrgUser())
		{
			$sql .= ' WHERE org_id IN (';
			$memberOfOrgs = $g_oSession->Value('member_of_orgs');
			if ($memberOfOrgs != '')
				$sql .= $memberOfOrgs;
			else
				$sql .= '-1';

			$sql .= ')';
		}

		$sql .= ' ORDER BY environment_id, org_id';

		$retVal = array();
		$model = new EnvironmentOrgModel();

		if ($model->Query($sql) != -1)
		{
			while ($model->next_record())
			{
				$environmentId = $model->f(0);
				$orgId = $model->f(1);

				if (!isset($retVal[$environmentId]))
					$retVal[$environmentId] = array();

				$retVal[$environmentId][] = $orgId;
			}
		}

		header('Content-Type: application/json');
		echo json_encode($retVal);
		exit;
	}
}