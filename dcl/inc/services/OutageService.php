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

class OutageService
{
	public function GetData()
	{
		global $g_oSession;

		RequirePermission(DCL_ENTITY_OUTAGE, DCL_PERM_VIEW);

		$limit = @Filter::ToInt($_REQUEST['rows']);
		if ($limit === null)
			$limit = 1;

		if ($_REQUEST['dir'] == 'next')
			$dir = 'next';
		else
			$dir = 'previous';

		$lastId = @Filter::ToInt($_REQUEST['lastid']);
		$firstId = @Filter::ToInt($_REQUEST['firstid']);

		$model = new OutageModel();
		$retVal = new stdClass();

		$sql = 'SELECT O.outage_id, O.outage_start, O.outage_end, O.outage_sched_start, O.outage_sched_end, O.outage_title, T.outage_type_name, ';
		$sql .= '(SELECT COUNT(*) FROM dcl_outage_org WHERE outage_id = O.outage_id) AS org_count, ';
		$sql .= '(SELECT COUNT(*) FROM dcl_outage_environment WHERE outage_id = O.outage_id) AS environment_count, ';
		$sql .= '(SELECT COUNT(*) FROM dcl_outage_wo WHERE outage_id = O.outage_id) AS wo_count, ';
		$sql .= 'S.status_name, O.sev_level';
		$sql .= ' FROM dcl_outage O ' . $model->JoinKeyword . ' dcl_outage_type T ON O.outage_type_id = T.outage_type_id ';
		$sql .= $model->JoinKeyword . ' dcl_outage_status S ON O.outage_status_id = S.outage_status_id ';

		$whereSql = '';
		$orderBy = 'outage_id DESC';
		$orgFilter = '';

		if (IsOrgUser())
		{
			$memberOfOrganizations = $g_oSession->Value('member_of_orgs');
			if ($memberOfOrganizations == '')
				$memberOfOrganizations = '-1';

			$orgFilter = 'EXISTS (SELECT 1 FROM dcl_outage_org WHERE outage_id = O.outage_id AND org_id IN (' . $memberOfOrganizations . '))';
			$whereSql .= ' WHERE ' . $orgFilter;
		}

		if ($lastId > 0 || $firstId > 0)
		{
			if ($whereSql == '')
				$whereSql = ' WHERE ';
			else
				$whereSql .= ' AND ';

			if ($dir == 'next')
			{
				$whereSql .= 'outage_id < ' . $lastId;
			}
			else
			{
				$whereSql .= 'outage_id > ' . $firstId;
				$orderBy = 'outage_id';
			}
		}

		$sql .= "$whereSql ORDER BY $orderBy";

		$countSql = 'SELECT MIN(O.outage_id), MAX(O.outage_id), COUNT(*) FROM dcl_outage O';
		if ($orgFilter != '')
			$countSql .= ' WHERE ' . $orgFilter;

		if ($model->Query($countSql) != -1)
		{
			if ($model->next_record())
			{
				$retVal->min = $model->f(0);
				$retVal->max = $model->f(1);
				$retVal->records = $model->f(2);
			}
		}

		$retVal->page = 1;
		$retVal->total = ceil($retVal->records / $limit);

		$model->LimitQuery($sql, 0, $limit);

		if ($dir == 'next')
			$allRecs = $model->FetchAllRows();
		else
			$allRecs = array_reverse($model->FetchAllRows());

		$retVal->rows = array();
		if ($retVal->records > 0)
		{
			foreach ($allRecs as $record)
			{
				$retVal->rows[] = (object)array(
					'id' => (int)$record[0],
					'start' => DclSmallDateTime::ToDisplay($record[1]),
					'end' => DclSmallDateTime::ToDisplay($record[2]),
					'schedstart' => DclSmallDateTime::ToDisplay($record[3]),
					'schedend' => DclSmallDateTime::ToDisplay($record[4]),
					'title' => $record[5],
					'type' => $record[6],
					'orgs' => (int)$record[7],
					'env' => (int)$record[8],
					'wo' => (int)$record[9],
					'status' => $record[10],
					'sev' => $record[11]
				);
			}
		}

		header('Content-Type: application/json');
		echo json_encode($retVal);
		exit;
	}

	public function GetUpcomingPlannedOutages()
	{
		global $g_oSession;

		RequirePermission(DCL_ENTITY_OUTAGE, DCL_PERM_VIEW);

		$model = new OutageModel();

		$sql = 'SELECT O.outage_id, O.outage_sched_start, O.outage_sched_end, O.outage_title, T.is_down, T.is_planned, S.status_name FROM dcl_outage O ' . $model->JoinKeyword . ' dcl_outage_type T ON O.outage_type_id = T.outage_type_id ' . $model->JoinKeyword . ' dcl_outage_status S ON O.outage_status_id = S.outage_status_id';
		$sql .= ' WHERE O.outage_sched_start > ' . $model->GetDateSQL() . " AND O.outage_start IS NULL AND T.is_planned = 'Y'";

		if (IsOrgUser())
		{
			$sOrgs = $g_oSession->Value('member_of_orgs');
			if ($sOrgs == '')
				$sOrgs = '-1';

			$sql .= ' AND EXISTS (SELECT 1 FROM dcl_outage_org WHERE outage_id = O.outage_id AND org_id IN (' . $sOrgs . '))';
		}

		$sql .= ' ORDER BY O.outage_sched_start';

		$model->LimitQuery($sql, 0, 5);
		$allRecs = $model->FetchAllRows();

		$retVal = new stdClass();
		$retVal->outages = array();
		if (count($allRecs) > 0)
		{
			foreach ($allRecs as $record)
			{
				$retVal->outages[] = (object)array(
					'id' => (int)$record[0],
					'schedstart' => DclSmallDateTime::ToDisplay($record[1]),
					'schedend' => DclSmallDateTime::ToDisplay($record[2]),
					'title' => $record[3],
					'down' => $record[4],
					'planned' => $record[5],
					'status' => $record[6]
				);
			}
		}

		header('Content-Type: application/json');
		echo json_encode($retVal);
		exit;
	}

	public function GetCurrentOutages()
	{
		global $g_oSession;

		RequirePermission(DCL_ENTITY_OUTAGE, DCL_PERM_VIEW);

		$model = new OutageModel();

		$sql = 'SELECT O.outage_id, O.outage_start, O.outage_end, O.outage_sched_start, O.outage_sched_end, O.outage_title, T.is_down, T.is_planned, S.status_name, O.sev_level FROM dcl_outage O ' . $model->JoinKeyword . ' dcl_outage_type T ON O.outage_type_id = T.outage_type_id ' . $model->JoinKeyword . ' dcl_outage_status S ON O.outage_status_id = S.outage_status_id';
		$sql .= ' WHERE O.outage_start <= ' . $model->GetDateSQL() . " AND O.outage_end IS NULL";

		if (IsOrgUser())
		{
			$sOrgs = $g_oSession->Value('member_of_orgs');
			if ($sOrgs == '')
				$sOrgs = '-1';

			$sql .= ' AND EXISTS (SELECT 1 FROM dcl_outage_org WHERE outage_id = O.outage_id AND org_id IN (' . $sOrgs . '))';
		}

		$sql .= ' ORDER BY O.outage_start';

		$model->Query($sql);
		$allRecs = $model->FetchAllRows();

		$retVal = new stdClass();
		$retVal->outages = array();
		if (count($allRecs) > 0)
		{
			foreach ($allRecs as $record)
			{
				$retVal->outages[] = (object)array(
					'id' => (int)$record[0],
					'start' => DclSmallDateTime::ToDisplay($record[1]),
					'end' => DclSmallDateTime::ToDisplay($record[2]),
					'schedstart' => DclSmallDateTime::ToDisplay($record[3]),
					'schedend' => DclSmallDateTime::ToDisplay($record[4]),
					'title' => $record[5],
					'down' => $record[6],
					'planned' => $record[7],
					'status' => $record[8],
					'sev' => $record[9] != null ? 'SEV' . $record[9] : null
				);
			}
		}

		header('Content-Type: application/json');
		echo json_encode($retVal);
		exit;
	}

	public function GetRecentOutages()
	{
		global $g_oSession, $dcl_info;

		RequirePermission(DCL_ENTITY_OUTAGE, DCL_PERM_VIEW);

		$model = new OutageModel();
		$cutoffDt = new DateTime();
		$cutoffDt->modify('-7 days');

		$sql = 'SELECT O.outage_id, O.outage_start, O.outage_end, O.outage_sched_start, O.outage_sched_end, O.outage_title, T.is_down, T.is_planned, S.status_name, O.sev_level FROM dcl_outage O ' . $model->JoinKeyword . ' dcl_outage_type T ON O.outage_type_id = T.outage_type_id ' . $model->JoinKeyword . ' dcl_outage_status S ON O.outage_status_id = S.outage_status_id';
		$sql .= ' WHERE O.outage_end >= ' . $model->DisplayToSQL($cutoffDt->format($dcl_info['DCL_DATE_FORMAT']));

		if (IsOrgUser())
		{
			$sOrgs = $g_oSession->Value('member_of_orgs');
			if ($sOrgs == '')
				$sOrgs = '-1';

			$sql .= ' AND EXISTS (SELECT 1 FROM dcl_outage_org WHERE outage_id = O.outage_id AND org_id IN (' . $sOrgs . '))';
		}

		$sql .= ' ORDER BY O.outage_end DESC';

		$model->Query($sql);
		$allRecs = $model->FetchAllRows();

		$retVal = new stdClass();
		$retVal->outages = array();
		if (count($allRecs) > 0)
		{
			foreach ($allRecs as $record)
			{
				$retVal->outages[] = (object)array(
					'id' => (int)$record[0],
					'start' => DclSmallDateTime::ToDisplay($record[1]),
					'end' => DclSmallDateTime::ToDisplay($record[2]),
					'schedstart' => DclSmallDateTime::ToDisplay($record[3]),
					'schedend' => DclSmallDateTime::ToDisplay($record[4]),
					'title' => $record[5],
					'down' => $record[6],
					'planned' => $record[7],
					'status' => $record[8],
					'sev' => $record[9] != null ? 'SEV' . $record[9] : null
				);
			}
		}

		header('Content-Type: application/json');
		echo json_encode($retVal);
		exit;
	}
}