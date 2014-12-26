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

class OrganizationMeasurementService
{
	public function GetData()
	{
		global $g_oSession, $dcl_info;

		RequirePermission(DCL_ENTITY_ORGMEASUREMENT, DCL_PERM_VIEW);

		$organizationId = Filter::RequireInt($_REQUEST['org_id']);
		if (IsOrgUser() && !in_array($organizationId, explode(',', $g_oSession->Value('member_of_orgs'))))
			throw new PermissionDeniedException();

		$model = new OrganizationModel();
		if ($model->Load($organizationId) == -1)
			throw new InvalidEntityException();

		$measureTypeId = Filter::RequireInt($_REQUEST['type']);
		$beginDt = Filter::RequireDate($_REQUEST['begin']);
		$endDt = Filter::RequireDate($_REQUEST['end']);

		$queryEndDt = new DateTime($endDt);
		$queryEndDt->modify('+1 day');

		$measureTypeModel = new MeasurementTypeModel();
		if ($measureTypeModel->Load($measureTypeId) == -1)
			throw new InvalidEntityException();

		$measureUnitModel = new MeasurementUnitModel();
		$measureUnitModel->Load($measureTypeModel->measurement_unit_id);

		$retVal = new stdClass();

		$retVal->orgName = $model->name;
		$retVal->periodName = $beginDt . ' to ' . $endDt;
		$retVal->measurements = array();
		$retVal->type = $measureTypeModel->measurement_name;
		$retVal->unitName = $measureUnitModel->unit_name;
		$retVal->unitAbbr = $measureUnitModel->unit_abbr;
		$retVal->slaIsTrim = false;
		$retVal->slaTrimPct = 5;
		$retVal->schedule = array();
		$retVal->scheduleExceptions = array();

		$retVal->histogram = array(
			array('min' => 0, 'max' => 999),
			array('min' => 1000, 'max' => 1999),
			array('min' => 2000, 'max' => 2999),
			array('min' => 3000, 'max' => 3999),
			array('min' => 4000, 'max' => 4999),
			array('min' => 5000, 'max' => 5999),
			array('min' => 6000, 'max' => 6999),
			array('min' => 7000, 'max' => null)
		);

		$orgMeasurementModel = new OrganizationMeasurementSlaModel();
		if ($orgMeasurementModel->Load(array('org_id' => $organizationId, 'measurement_type_id' => $measureTypeId), false) != -1)
		{
			$retVal->minValid = NullSafeCast::ToInt($orgMeasurementModel->min_valid_value);
			$retVal->maxValid = NullSafeCast::ToInt($orgMeasurementModel->max_valid_value);
			$retVal->slaThreshold = NullSafeCast::ToInt($orgMeasurementModel->measurement_sla);
			$retVal->slaWarnThreshold = NullSafeCast::ToInt($orgMeasurementModel->measurement_sla_warn);

			$slaTrimPct = NullSafeCast::ToFloat($orgMeasurementModel->sla_trim_pct);
			if ($slaTrimPct !== null && $slaTrimPct > 0.0)
			{
				$retVal->slaTrimPct = $slaTrimPct;
				$retVal->slaIsTrim = $orgMeasurementModel->sla_is_trim_based == "Y";
			}

			if ($orgMeasurementModel->sla_schedule_id !== null)
			{
				$slaSchedule = new SlaScheduleModel();
				if ($slaSchedule->Load($orgMeasurementModel->sla_schedule_id, false) != -1)
				{
					// SLA schedule
					$retVal->schedule = array(
						array('st' => array('hr' => $slaSchedule->day0_start_hour, 'min' => $slaSchedule->day0_start_minute), 'end' => array('hr' => $slaSchedule->day0_end_hour, 'min' => $slaSchedule->day0_end_minute)),
						array('st' => array('hr' => $slaSchedule->day1_start_hour, 'min' => $slaSchedule->day1_start_minute), 'end' => array('hr' => $slaSchedule->day1_end_hour, 'min' => $slaSchedule->day1_end_minute)),
						array('st' => array('hr' => $slaSchedule->day2_start_hour, 'min' => $slaSchedule->day2_start_minute), 'end' => array('hr' => $slaSchedule->day2_end_hour, 'min' => $slaSchedule->day2_end_minute)),
						array('st' => array('hr' => $slaSchedule->day3_start_hour, 'min' => $slaSchedule->day3_start_minute), 'end' => array('hr' => $slaSchedule->day3_end_hour, 'min' => $slaSchedule->day3_end_minute)),
						array('st' => array('hr' => $slaSchedule->day4_start_hour, 'min' => $slaSchedule->day4_start_minute), 'end' => array('hr' => $slaSchedule->day4_end_hour, 'min' => $slaSchedule->day4_end_minute)),
						array('st' => array('hr' => $slaSchedule->day5_start_hour, 'min' => $slaSchedule->day5_start_minute), 'end' => array('hr' => $slaSchedule->day5_end_hour, 'min' => $slaSchedule->day5_end_minute)),
						array('st' => array('hr' => $slaSchedule->day6_start_hour, 'min' => $slaSchedule->day6_start_minute), 'end' => array('hr' => $slaSchedule->day6_end_hour, 'min' => $slaSchedule->day6_end_minute))
					);

					// Load any exceptions to this schedule for our date range
					$slaScheduleExceptions = new SlaScheduleExceptionModel();
					if ($slaScheduleExceptions->LoadByScheduleAndDate($slaSchedule->sla_schedule_id, $beginDt, $endDt) != -1)
					{
						while ($slaScheduleExceptions->next_record())
						{
							$slaScheduleExceptions->GetRow();
							$exceptionStartDt = new DateTime($slaScheduleExceptions->start_dt);
							$exceptionEndDt = new DateTime($slaScheduleExceptions->end_dt);
							$dateKey = $exceptionStartDt->format('Y-m-d');

							if ($exceptionStartDt == $exceptionEndDt)
								$retVal->scheduleExceptions[$dateKey] = array('st' => null, 'end' => null);
							else
								$retVal->scheduleExceptions[$dateKey] = array('st' => $exceptionStartDt->format('H:i'), 'end' => $exceptionEndDt->format('H:i'));
						}
					}
				}
			}
		}
		else
		{
			$retVal->minValid = null;
			$retVal->maxValid = null;
			$retVal->slaThreshold = null;
			$retVal->slaWarnThreshold = null;
		}

		// Measurements for this period
		$measureModel = new OrganizationMeasurementModel();

		$sql = sprintf("SELECT measurement_ts, measurement FROM dcl_org_measurement WHERE org_id = $organizationId AND measurement_type_id = $measureTypeId AND measurement_ts >= %s AND measurement_ts < %s ORDER BY measurement_ts",
			$model->DisplayToSQL($beginDt),
			$model->DisplayToSQL($queryEndDt->format($dcl_info['DCL_DATE_FORMAT'])));

		if ($measureModel->Query($sql) == -1)
			throw new InvalidDataException();

		while ($measureModel->next_record())
		{
			$msTimestamp = new DateTime($measureModel->f(0));

			$measurement = new stdClass();
			$measurement->ts = $msTimestamp->format('c');
			$measurement->ms = (int)$measureModel->f(1);

			$retVal->measurements[] = $measurement;
		}

		header('Content-Type: application/json');
		echo json_encode($retVal);
		exit;
	}
}