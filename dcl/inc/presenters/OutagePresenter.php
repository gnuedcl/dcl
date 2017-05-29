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

	public function Report()
	{
		commonHeader();

		$smartyHelper = new SmartyHelper();
		$smartyHelper->assignByRef('ViewData', $this->GetReportViewModel());

		$smartyHelper->Render('OutageReportForm.tpl');
	}

	public function ReportResults($startDate, $endDate, array $organizations)
	{
		commonHeader();

		$db = new OutageModel();
		if ($db->ListUnplannedOutages($startDate, $endDate, $organizations) == -1)
			return;

		$aRecords = $db->FetchAllRows();
		for ($i = 0; $i < count($aRecords); $i++)
		{
			if ($aRecords[$i][2] !== null)
				$aRecords[$i][2] = 'SEV-' . $aRecords[$i][2];

			$aRecords[$i][3] = DclSmallDateTime::ToDisplay($aRecords[$i][3]);
			$aRecords[$i][4] = DclSmallDateTime::ToDisplay($aRecords[$i][4]);
			$aRecords[$i][6] = $aRecords[$i][6] == 'Y' ? STR_CMMN_YES : STR_CMMN_NO;
			$aRecords[$i][8] = $aRecords[$i][8] == 'Y' ? STR_CMMN_YES : STR_CMMN_NO;
		}

		$oTable = new TableHtmlHelper();
		$oTable->addColumn('ID', 'numeric');
		$oTable->addColumn('Title', 'string');
		$oTable->addColumn('Severity', 'string');
		$oTable->addColumn('Start', 'string');
		$oTable->addColumn('End', 'string');
		$oTable->addColumn('Type', 'string');
		$oTable->addColumn('Down?', 'string');
		$oTable->addColumn('Status', 'string');
		$oTable->addColumn('Resolved?', 'string');
		$oTable->addColumn('# Orgs', 'numeric');
		$oTable->addColumn('Description', 'string');
		$oTable->setData($aRecords);
		$oTable->setShowRownum(false);
		$oTable->setCaption('Unplanned Outages ' . $startDate . ' to ' . $endDate);
		$oTable->sTemplate = 'TableView.tpl';

		$oTable->render();
	}

	private function &GetViewModel(OutageModel $model = null, array $environments = null, array $organizations = null)
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
			$viewData->SeverityLevel = $model->sev_level;

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

	private function GetReportViewModel()
	{
		global $dcl_info;

		$viewData = new stdClass();

		$endDateTime = new DateTime();
		$viewData->End = $endDateTime->format($dcl_info['DCL_DATE_FORMAT']);

		$startDateTime = new DateTime($viewData->End);
		$startDateTime->modify('-30 days');
		$viewData->Start = $startDateTime->format($dcl_info['DCL_DATE_FORMAT']);

		$viewData->Orgs = '';

		return $viewData;
	}
}