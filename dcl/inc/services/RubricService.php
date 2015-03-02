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

class RubricService
{
	public function GetData()
	{
		$page = @Filter::ToInt($_REQUEST['page']);
		$limit = @Filter::ToInt($_REQUEST['rows']);
		$sidx = @Filter::ToSqlName($_REQUEST['sidx']);
		$sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';

		if ($sord != 'asc' && $sord != 'desc')
			$sord = 'asc';

		if ($sidx === null)
			$sidx = 'rubric_name';

		if ($page === null)
			$page = 1;

		if ($limit === null)
			$limit = 1;

		$validColumns = array('rubric_id', 'rubric_name');
		if (!in_array($sidx, $validColumns))
			$sidx = 'rubric_name';

		$idFilter = @Filter::ToInt($_REQUEST['rubric_id']);
		$nameFilter = @$_REQUEST['rubric_name'];

		$model = new RubricModel();
		$queryHelper = new RubricSqlQueryHelper();
		$queryHelper->AddDef('columns', '', array('rubric_id', 'rubric_name'));

		if ($idFilter !== null)
			$queryHelper->AddDef('filter', 'rubric_id', $idFilter);

		if (isset($nameFilter))
			$queryHelper->AddDef('filterlike', 'rubric_name', $nameFilter);

		$queryHelper->AddDef('order', '', array($sidx . ' ' . $sord));

		$retVal = new stdClass();
		$retVal->records = $model->ExecuteScalar($queryHelper->GetSQL(true));
		$retVal->page = $page;
		$retVal->total = ceil($retVal->records / $limit);

		$query = $queryHelper->GetSQL();
		if ($limit > 0)
			$model->LimitQuery($query, ($page - 1) * $limit, $limit);
		else
			$model->Query($query);

		$allRecs = $model->FetchAllRows();

		$retVal->rows = array();
		if ($retVal->records > 0)
		{
			foreach ($allRecs as $record)
			{
				$retVal->rows[] = array('id' => $record[0], 'cell' => $record);
			}
		}

		header('Content-Type: application/json');
		echo json_encode($retVal);
		exit;
	}

	public function Item()
	{
		$id = Filter::RequireInt($_REQUEST['id']);
		$retVal = new stdClass();

		$model = new RubricModel();
		$model->Load($id);

		$retVal->id = $model->rubric_id;
		$retVal->name = $model->rubric_name;
		$retVal->criteria = array();

		$criteriaModel = new RubricCriteriaModel();
		$criteriaModel->ListCriteriaForRubric($id);

		while ($criteriaModel->next_record())
		{
			$record = new stdClass();
			$record->id = $criteriaModel->f('rubric_criteria_id');
			$record->name = $criteriaModel->f('criteria_name');
			$record->level1 = $criteriaModel->f('level1_descriptor');
			$record->level2 = $criteriaModel->f('level2_descriptor');
			$record->level3 = $criteriaModel->f('level3_descriptor');
			$record->level4 = $criteriaModel->f('level4_descriptor');

			$retVal->criteria[] = $record;
		}

		header('Content-Type: application/json');
		echo json_encode($retVal);
		exit;
	}
}