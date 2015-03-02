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

class RubricController
{
	public function Index()
	{
		RequirePermission(DCL_ENTITY_RUBRIC, DCL_PERM_VIEW);

		$presenter = new RubricPresenter();
		$presenter->Index();
	}

	public function Create()
	{
		RequirePermission(DCL_ENTITY_RUBRIC, DCL_PERM_ADD);

		$presenter = new RubricPresenter();
		$presenter->Create();
	}

	public function Insert()
	{
		RequirePost();
		RequirePermission(DCL_ENTITY_RUBRIC, DCL_PERM_ADD);

		$json = file_get_contents('php://input');
		$requestData = json_decode($json);

		$rubricModel = new RubricModel();
		$rubricModel->rubric_name = $requestData->name;
		$rubricModel->create_dt = DCL_NOW;
		$rubricModel->create_by = DCLID;
		$rubricModel->update_dt = DCL_NOW;
		$rubricModel->update_by = DCLID;
		$rubricModel->Add();

		if ($rubricModel->rubric_id != -1)
		{
			$order = 0;
			foreach ($requestData->criteria as $criteriaRequestData)
			{
				$rubricCriteriaModel = new RubricCriteriaModel();
				$rubricCriteriaModel->rubrid_id = $rubricModel->rubric_id;
				$rubricCriteriaModel->criteria_order = $order++;
				$rubricCriteriaModel->criteria_name = $criteriaRequestData->name;
				$rubricCriteriaModel->level1_descriptor = $criteriaRequestData->level1;
				$rubricCriteriaModel->level2_descriptor = $criteriaRequestData->level2;
				$rubricCriteriaModel->level3_descriptor = $criteriaRequestData->level3;
				$rubricCriteriaModel->level4_descriptor = $criteriaRequestData->level4;
				$rubricCriteriaModel->create_dt = DCL_NOW;
				$rubricCriteriaModel->create_by = DCLID;
				$rubricCriteriaModel->update_dt = DCL_NOW;
				$rubricCriteriaModel->update_by = DCLID;

				$rubricCriteriaModel->Add();
			}
		}
	}

	public function Edit()
	{
		RequirePermission(DCL_ENTITY_RUBRIC, DCL_PERM_MODIFY);

		$id = Filter::RequireInt($_REQUEST['id']);
		$model = new RubricModel();
		$model->Load($id);

		$presenter = new RubricPresenter();
		$presenter->Edit($model);
	}

	public function Update()
	{
		RequirePost();
		RequirePermission(DCL_ENTITY_RUBRIC, DCL_PERM_MODIFY);

		$json = file_get_contents('php://input');
		$requestData = json_decode($json);

		$id = Filter::RequireInt($requestData->id);

		$rubricModel = new RubricModel();
		$rubricModel->Load($id);

		if ($rubricModel->rubric_name != $requestData->name)
		{
			$rubricModel->rubric_name = $requestData->name;
			$rubricModel->update_dt = DCL_NOW;
			$rubricModel->update_by = DCLID;
			$rubricModel->Edit();
		}

		$insertedOrUpdatedIds = array();
		$originalCriteria = array();
		$originalCriteriaModel = new RubricCriteriaModel();
		$originalCriteriaModel->ListCriteriaForRubric($id);

		while ($originalCriteriaModel->next_record())
			$originalCriteria[] = $originalCriteriaModel->Record;

		$order = 0;
		foreach ($requestData->criteria as $criteriaRequestData)
		{
			$rubricCriteriaModel = new RubricCriteriaModel();

			if ($criteriaRequestData->id > 0)
			{
				if ($rubricCriteriaModel->Load($criteriaRequestData->id) != -1)
				{
					$rubricCriteriaModel->criteria_order = $order++;
					$rubricCriteriaModel->criteria_name = $criteriaRequestData->name;
					$rubricCriteriaModel->level1_descriptor = $criteriaRequestData->level1;
					$rubricCriteriaModel->level2_descriptor = $criteriaRequestData->level2;
					$rubricCriteriaModel->level3_descriptor = $criteriaRequestData->level3;
					$rubricCriteriaModel->level4_descriptor = $criteriaRequestData->level4;
					$rubricCriteriaModel->update_dt = DCL_NOW;
					$rubricCriteriaModel->update_by = DCLID;

					$rubricCriteriaModel->Edit();

					$order++;
					$insertedOrUpdatedIds[] = $rubricCriteriaModel->rubric_criteria_id;
					continue;
				}
			}

			$rubricCriteriaModel->rubric_id = $rubricModel->rubric_id;
			$rubricCriteriaModel->criteria_order = $order++;
			$rubricCriteriaModel->criteria_name = $criteriaRequestData->name;
			$rubricCriteriaModel->level1_descriptor = $criteriaRequestData->level1;
			$rubricCriteriaModel->level2_descriptor = $criteriaRequestData->level2;
			$rubricCriteriaModel->level3_descriptor = $criteriaRequestData->level3;
			$rubricCriteriaModel->level4_descriptor = $criteriaRequestData->level4;
			$rubricCriteriaModel->create_dt = DCL_NOW;
			$rubricCriteriaModel->create_by = DCLID;
			$rubricCriteriaModel->update_dt = DCL_NOW;
			$rubricCriteriaModel->update_by = DCLID;

			$rubricCriteriaModel->Add();

			$insertedOrUpdatedIds[] = $rubricCriteriaModel->rubric_criteria_id;
		}

		$deletedIds = array();
		foreach ($originalCriteria as $criteria)
		{
			if (!in_array($criteria['rubric_criteria_id'], $insertedOrUpdatedIds))
				$deletedIds[] = $criteria['rubric_criteria_id'];
		}

		if (count($deletedIds) > 0)
		{
			$rubricCriteriaModel = new RubricCriteriaModel();
			$rubricCriteriaModel->DeleteCollection($deletedIds);
		}
	}

	public function Destroy()
	{
		RequirePost();
		RequirePermission(DCL_ENTITY_RUBRIC, DCL_PERM_DELETE);

		$id = Filter::RequireInt($_REQUEST['id']);

		$rubricModel = new RubricModel();
		$rubricModel->Delete(array('rubric_id' => $id));
	}
}