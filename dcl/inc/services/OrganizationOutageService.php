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

class OrganizationOutageService
{
	public function GetData()
	{
		global $g_oSession, $dcl_info;

		RequirePermission(DCL_ENTITY_OUTAGE, DCL_PERM_VIEW);

		$organizationId = Filter::RequireInt($_REQUEST['org_id']);
		if (IsOrgUser() && !in_array($organizationId, explode(',', $g_oSession->Value('member_of_orgs'))))
			throw new PermissionDeniedException();

		$model = new OrganizationModel();
		if ($model->Load($organizationId) == -1)
			throw new InvalidEntityException();

		$beginDt = Filter::RequireDate($_REQUEST['begin']);
		$endDt = Filter::RequireDate($_REQUEST['end']);

		$queryEndDt = new DateTime($endDt);
		$queryEndDt->modify('+1 day');

		$outageModel = new OutageModel();
		$sql = sprintf("SELECT O.outage_id, O.outage_title, O.outage_start, O.outage_end, O.outage_type_id, OT.outage_type_name, OT.is_down, OT.is_planned, OS.status_name FROM dcl_outage_org OO JOIN dcl_outage O ON OO.outage_id = O.outage_id JOIN dcl_outage_type OT ON O.outage_type_id = OT.outage_type_id JOIN dcl_outage_status OS ON O.outage_status_id = OS.outage_status_id WHERE OO.org_id = $organizationId AND outage_start <= %s AND (outage_end IS NULL OR outage_end >= %s) ORDER BY O.outage_start",
			$model->DisplayToSQL($queryEndDt->format($dcl_info['DCL_DATE_FORMAT'])),
			$model->DisplayToSQL($beginDt));

		if ($outageModel->Query($sql) == -1)
			throw new InvalidDataException();

		$retVal = new stdClass();

		$retVal->orgName = $model->name;
		$retVal->periodName = $beginDt . ' to ' . $endDt;
		$retVal->outages = array();
		$retVal->slaThreshold = 0.0;
		$retVal->slaWarnThreshold = 0.0;

		$orgOutageModel = new OrganizationOutageSlaModel();
		if ($orgOutageModel->Load($organizationId, false) !== -1)
		{
			if ($orgOutageModel->outage_sla > 0)
			{
				$retVal->slaThreshold = (float)$orgOutageModel->outage_sla;
				if ($orgOutageModel->outage_sla_warn > 0)
					$retVal->slaWarnThreshold = (float)$orgOutageModel->outage_sla_warn;
			}
		}

		while ($outageModel->next_record())
		{
			$outage = new stdClass();
			$outage->id = (int)$outageModel->f(0);
			$outage->title = $outageModel->f(1);

			$outageStartDt = new DateTime($outageModel->f(2));
			$outage->start = $outageStartDt->format("c");

			$outageEndDtValue = $outageModel->f(3);
			if ($outageEndDtValue == null)
			{
				$outage->end = null;
			}
			else
			{
				$outageEndDt = new DateTime($outageEndDtValue);
				$outage->end = $outageEndDt->format("c");
			}

			$outage->typeId = (int)$outageModel->f(4);
			$outage->typeName = $outageModel->f(5);
			$outage->isDown = $outageModel->f(6);
			$outage->isPlanned = $outageModel->f(7);
			$outage->statusName = $outageModel->f(8);

			$retVal->outages[] = $outage;
		}

		header('Content-Type: application/json');
		echo json_encode($retVal);
		exit;
	}
}
