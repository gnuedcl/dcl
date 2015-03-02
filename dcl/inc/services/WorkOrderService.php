<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2015 Free Software Foundation
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

	public function GetRubric()
	{
		$woId = Filter::RequireInt(@$_REQUEST['wo_id']);
		$seq = Filter::RequireInt(@$_REQUEST['seq']);

		$woModel = new WorkOrderModel();
		$woModel->LoadByIdSeq($woId, $seq);

		$productRubricModel = new ProductRubricModel();
		$productRubricModel->Load(array('product_id' => $woModel->product, 'wo_type_id' => $woModel->wo_type_id));

		$retVal = new stdClass();

		$model = new RubricModel();
		$model->Load($productRubricModel->rubric_id);

		$retVal->id = $model->rubric_id;
		$retVal->name = $model->rubric_name;
		$retVal->criteria = array();

		$criteriaModel = new RubricCriteriaModel();
		$criteriaModel->ListCriteriaForRubric($model->rubric_id);

		$woRubricModel = new WoRubricScoreModel();
		$selectedItems = $woRubricModel->ListByWorkOrder($woModel);

		while ($criteriaModel->next_record())
		{
			$record = new stdClass();
			$record->id = $criteriaModel->f('rubric_criteria_id');
			$record->name = $criteriaModel->f('criteria_name');
			$record->level1 = $criteriaModel->f('level1_descriptor');
			$record->level2 = $criteriaModel->f('level2_descriptor');
			$record->level3 = $criteriaModel->f('level3_descriptor');
			$record->level4 = $criteriaModel->f('level4_descriptor');

			if (array_key_exists($record->id, $selectedItems))
				$record->score = $selectedItems[$record->id];
			else
				$record->score = null;

			$retVal->criteria[] = $record;
		}

		header('Content-Type: application/json');
		echo json_encode($retVal);
		exit;
	}
}
