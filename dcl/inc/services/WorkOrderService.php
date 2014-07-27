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

class WorkOrderService
{
	public function GetData()
	{
		$sqlQueryHelper = new WorkOrderSqlQueryHelper();
		$sqlQueryHelper->SetFromURL();
		
		$sqlQueryHelper->numrows = Filter::RequireInt($_REQUEST['rows']);
		$sqlQueryHelper->startrow = (Filter::RequireInt($_REQUEST['page']) - 1) * $sqlQueryHelper->numrows;
		
		$countSql = $sqlQueryHelper->GetSQL(true);
		$querySql = $sqlQueryHelper->GetSQL();
		
		$retVal = new stdClass();
		$db = new DbProvider();

		$retVal->total = $db->ExecuteScalar($countSql);
		
		if ($retVal->total > 0)
		{
			$db->LimitQuery($querySql, $sqlQueryHelper->startrow, $sqlQueryHelper->numrows);
			$retVal->records = $db->FetchAllRows();
			$retVal->count = count($retVal->records);
		}
		else
		{
			$retVal->records = array();
			$retVal->count = 0;
		}
		
		header('Content-Type: application/json');
		echo json_encode($retVal);
		exit;
	}

	public function ListOrgs()
	{
		$model = new WorkOrderOrganizationModel();
		$retVal = new stdClass();
		$retVal->rows = array();
		$retVal->count = 0;

		$woId = Filter::RequireInt(@$_REQUEST['wo_id']);
		$seq = Filter::RequireInt(@$_REQUEST['seq']);

		if ($model->LoadWithPermissionFilter($woId, $seq) != -1)
		{
			while ($model->next_record())
			{
				$org = new stdClass();
				$org->id = $model->f(2);
				$org->name = $model->f(3);

				$retVal->rows[] = $org;
				$retVal->count++;
			}
		}

		header('Content-Type: application/json');
		echo json_encode($retVal);
		exit;
	}
}
