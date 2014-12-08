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

class OutagePresenter
{
	public function Index()
	{
		commonHeader();

		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('PERM_ADD', HasPermission(DCL_ENTITY_OUTAGE, DCL_PERM_ADD));
		$smartyHelper->assign('PERM_MODIFY', HasPermission(DCL_ENTITY_OUTAGE, DCL_PERM_MODIFY));
		$smartyHelper->assign('PERM_DELETE', HasPermission(DCL_ENTITY_OUTAGE, DCL_PERM_DELETE));

		$smartyHelper->Render('OutageIndex.tpl');
	}

	public function Create(OutageModel $model = null, array $environments = null, array $organizations = null, array $errors = null)
	{
		commonHeader();

		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('TXT_FUNCTION', 'Add New Outage');
		$smartyHelper->assign('menuAction', 'Outage.Insert');
		$smartyHelper->assign('ERRORS', $errors);

		if ($model != null)
			$smartyHelper->assignByRef('ViewData', $this->GetViewModel($model, $environments, $organizations));

		$smartyHelper->Render('OutageForm.tpl');
	}

	public function Edit(OutageModel $model, array $environments = null, array $organizations = null, array $errors = null)
	{
		commonHeader();

		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('TXT_FUNCTION', 'Modify Outage');
		$smartyHelper->assign('menuAction', 'Outage.Update');
		$smartyHelper->assign('ERRORS', $errors);
		$smartyHelper->assign('IS_EDIT', true);

		$smartyHelper->assignByRef('ViewData', $this->GetViewModel($model, $environments, $organizations));

		$smartyHelper->Render('OutageForm.tpl');
	}

	private function GetViewModel(OutageModel $model = null, array $environments = null, array $organizations = null)
	{
		$viewData = new stdClass();

		if ($model != null)
		{
			$viewData->OutageId = $model->outage_id;
			$viewData->OutageType = $model->outage_type_id;
			$viewData->Title = $model->outage_title;
			$viewData->SchedStart = DclSmallDateTime::ToDisplay($model->outage_sched_start);
			$viewData->SchedEnd = DclSmallDateTime::ToDisplay($model->outage_sched_end);
			$viewData->Start = DclSmallDateTime::ToDisplay($model->outage_start);
			$viewData->End = DclSmallDateTime::ToDisplay($model->outage_end);
			$viewData->Description = $model->outage_description;
			$viewData->Status = $model->outage_status_id;

			if ($model->outage_type_id > 0)
			{
				$outageTypeModel = new OutageTypeModel();
				if ($outageTypeModel->Load($model->outage_type_id) != -1)
				{
					$viewData->IsPlanned = $outageTypeModel->is_planned == 'Y';
				}
			}
		}

		if ($environments != null)
			$viewData->Environments = $environments;

		if ($organizations != null)
			$viewData->Orgs = join(',', $organizations);

		return $viewData;
	}
}