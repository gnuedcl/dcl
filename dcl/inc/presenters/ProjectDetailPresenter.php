<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2004 Free Software Foundation
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

LoadStringResource('prj');
LoadStringResource('wo');

class ProjectDetailPresenter
{
	private $projectMapModel;
	private $smartyHelper;
	private $project;

	public function __construct()
	{
		$this->projectMapModel = new ProjectMapModel();
		$this->smartyHelper = new SmartyHelper();
		$this->project = null;
	}

	public function Show($projectId, $woStatus, $woResponsible, $woGroupBy = 'none')
	{
		global $g_oSec;

		commonHeader();

		$bIsGrouping = (@$woGroupBy != 'none');

		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_VIEW, $projectId);

		$this->project = new ProjectsModel();
		if ($this->project->Load($projectId) == -1)
			throw new InvalidEntityException();

		$displayHelper = new DisplayHelper();

		$this->smartyHelper->assign('VAL_PROJECTID', $this->project->projectid);
		$this->smartyHelper->assign('VAL_REPORTTO', $displayHelper->GetPersonnel($this->project->reportto));
		$this->smartyHelper->assign('VAL_WATCHTYPE', '2');
		$this->smartyHelper->assign('VAL_NAME', $this->project->name);
		$this->smartyHelper->assign('VAL_PROJECTDEADLINE', $this->project->projectdeadline);
		$this->smartyHelper->assign('VAL_CREATEDON', $this->project->createdon);
		$this->smartyHelper->assign('VAL_LASTACTIVITY', $this->project->lastactivity);
		$this->smartyHelper->assign('VAL_FINALCLOSE', $this->project->finalclose);
		$this->smartyHelper->assign('VAL_DESCRIPTION', $this->project->description);
		$this->smartyHelper->assign('VAL_WIKITYPE', 1); // 1 = DCL_WIKI_PROJECT
		$this->smartyHelper->assign('VAL_CREATEDBY', $displayHelper->GetPersonnel($this->project->createdby));
		$this->smartyHelper->assign('VAL_STATUS', $displayHelper->GetStatus($this->project->status));
		$this->smartyHelper->assign('VAL_FILTERSTATUS', $woStatus);
		$this->smartyHelper->assign('VAL_FILTERRESPONSIBLE', $woResponsible);
		$this->smartyHelper->assign('VAL_FILTERGROUPBY', $woGroupBy);
		$this->smartyHelper->assign('OPT_GROUPBY', array('none' => STR_CMMN_SELECTONE, '3' => STR_WO_RESPONSIBLE, '7' => STR_WO_STATUS, '4' => STR_WO_PRODUCT, '5' => STR_CMMN_MODULE, '2' => STR_WO_TYPE));

		$this->smartyHelper->assign('PERM_AUDIT', $g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_AUDIT));
		$this->smartyHelper->assign('PERM_ATTACHFILE', $g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ATTACHFILE));
		$this->smartyHelper->assign('PERM_REMOVEFILE', $g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_REMOVEFILE));

		$this->SetStatistics();
		$this->SetChildProjects();
		$this->SetTasks($woStatus, $woResponsible, $bIsGrouping);

		if ($g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEWFILE))
		{
			$oAttachments = new FileHelper();
			$this->smartyHelper->assign('VAL_ATTACHMENTS', $oAttachments->GetAttachments(DCL_ENTITY_PROJECT, $this->project->projectid));
		}

		$this->smartyHelper->assign('VAL_PROJECTS', ProjectsModel::GetParentProjectPath($this->project->projectid));

		$this->smartyHelper->Render('ProjectsDetail.tpl');
	}

	private function SetTasks($wostatus, $woresponsible, $bIsGrouping)
	{
		global $dcl_info;

		$cols = array('a.jcn', 'a.seq', 'h.type_name', 'b.short', 'c.name', 'g.module_name', 'd.name', 'e.name', 'a.deadlineon', 'a.totalhours', 'a.etchours', 'a.esthours', '(a.totalhours + a.etchours) - a.esthours', 'a.summary');
		$sql = 'Select a.jcn, a.seq, h.type_name, b.short, c.name, g.module_name, d.name, e.name, ' . $this->projectMapModel->ConvertDate('a.deadlineon', 'deadlineon') . ', a.totalhours, a.etchours, a.esthours, (a.totalhours + a.etchours) - a.esthours, a.summary';

		if ($dcl_info['DCL_WO_SECONDARY_ACCOUNTS_ENABLED'] == 'Y')
		{
			$sql .= ', (select count(*) from dcl_wo_account where wo_id = a.jcn And seq = a.seq) As num_accounts';
		}

		$sql .= ' From workorders a ';
		$sql .= $this->projectMapModel->JoinKeyword . ' personnel b ON a.responsible = b.id ';
		$sql .= $this->projectMapModel->JoinKeyword . ' products c ON a.product = c.id';
		$sql .= ' LEFT JOIN dcl_wo_account i ON a.jcn = i.wo_id AND a.seq = i.seq';
		$sql .= ' LEFT JOIN dcl_org d ON i.account_id = d.org_id ';
		$sql .= $this->projectMapModel->JoinKeyword . ' statuses e ON a.status = e.id ';
		$sql .= $this->projectMapModel->JoinKeyword . ' projectmap f ON a.jcn = f.jcn and f.seq in (0, a.seq)';
		$sql .= ' LEFT JOIN dcl_product_module g ON a.module_id = g.product_module_id ';
		$sql .= $this->projectMapModel->JoinKeyword . ' dcl_wo_type h ON a.wo_type_id = h.wo_type_id';
		$sql .= ' Where f.projectid=' . $this->project->projectid;

		if ($wostatus > 0)
			$sql .= ' And a.status=' . $wostatus;
		else if ($wostatus == -1)
			$sql .= ' And e.dcl_status_type != 2';
		else if ($wostatus == -2)
			$sql .= ' And e.dcl_status_type = 2';

		if ($woresponsible > 0)
			$sql .= ' And a.responsible=' . $woresponsible;

		if ($dcl_info['DCL_WO_SECONDARY_ACCOUNTS_ENABLED'] == 'Y')
		{
			$sql .= ' And (i.account_id is null Or i.account_id = ';
			$sql .= '(Select min(account_id) From dcl_wo_account where wo_id = a.jcn And seq = a.seq))';
		}

		if ($bIsGrouping)
			$sql .= ' Order By ' . $cols[$_REQUEST['wogroupby']] . ', a.jcn, a.seq';
		else
			$sql .= ' Order By a.jcn, a.seq';

		if ($this->projectMapModel->Query($sql) != -1)
		{
			$allRecs = $this->projectMapModel->FetchAllRows();
			$this->projectMapModel->FreeResult();

			if (count($allRecs) > 0)
			{
				$aTasks = array();

				$objWOAcct = new WorkOrderOrganizationModel();
				$oDate = new DateHelper;

				for ($i = 0; $i < count($allRecs); $i++)
				{
					$oDate->SetFromDB($allRecs[$i][8]);
					$ouHours = -($allRecs[$i][11] - $allRecs[$i][9]);
					$diffHours = $ouHours;
					if ($diffHours < 0)
						$diffHours = -$diffHours;

					$ouPct = 0.0;
					$sign = '';
					if ($allRecs[$i][11] > 0)
					{
						$ouPct = $diffHours / $allRecs[$i][11] * 100;
						if ($allRecs[$i][11] > $allRecs[$i][9] && $allRecs[$i][11] > 0)
							$sign = '-';
						else if ($allRecs[$i][9] > $allRecs[$i][11] && $allRecs[$i][9] > 0)
							$sign = '+';
					}

					$fPctComplete = 0.0;
					if ($allRecs[$i][10] + $allRecs[$i][9] > 0)
						$fPctComplete = (($allRecs[$i][9] / ($allRecs[$i][10] + $allRecs[$i][9])) * 100);
					elseif ($allRecs[$i][10] == 0.0)
						$fPctComplete = 100.0;

					$aTasks[] = array(
							'woid' => $allRecs[$i][0],
							'seq' => $allRecs[$i][1],
							'type' => $allRecs[$i][2],
							'responsible' => $allRecs[$i][3],
							'product' => $allRecs[$i][4],
							'module' => $allRecs[$i][5],
							'org' => $allRecs[$i][6],
							'status' => $allRecs[$i][7],
							'hours' => $allRecs[$i][9],
							'etc' => $allRecs[$i][10],
							'projected' => $allRecs[$i][11],
							'summary' => $allRecs[$i][13],
							'deadline' => $oDate->ToDisplay(),
							'plusminus' => sprintf('%s%0.2f (%s%0.2f%%)', $sign, abs($ouHours), $sign, abs($ouPct)),
							'pctcomplete' => sprintf("%0.2f%%", $fPctComplete),
							'secorgs' => ($dcl_info['DCL_WO_SECONDARY_ACCOUNTS_ENABLED'] == 'Y' ? $allRecs[$i][14] : 0)
						);
				}

				if ($bIsGrouping)
				{
					$sGroupBy = null;
					switch ($_REQUEST['wogroupby'])
					{
						case '3': $sGroupBy = 'responsible'; break;
						case '7': $sGroupBy = 'status';      break;
						case '4': $sGroupBy = 'product';     break;
						case '5': $sGroupBy = 'module';      break;
						case '2': $sGroupBy = 'type';        break;
					}

					$this->smartyHelper->assign('VAL_GROUPBY', $sGroupBy);
				}

				$this->smartyHelper->assignByRef('VAL_TASKS', $aTasks);
			}
		}
	}

	private function SetChildProjects()
	{
		$oDB = new DbProvider;
		$oDB->Query('SELECT projectid FROM dcl_projects WHERE parentprojectid = ' . $this->project->projectid . ' ORDER BY name');
		if ($oDB->next_record())
		{
			$oProject = new ProjectsModel();
			$aProjects = array();
			do
			{
				if ($oProject->Load($oDB->f(0)) != -1)
				{
					$aStat = $oProject->GetProjectStatistics($oProject->projectid);
					$aProjects[] = array(
							'projectid' => $oProject->projectid,
							'name' => $oProject->name,
							'totaltasks' => $aStat['totaltasks'],
							'tasksclosed' => $aStat['tasksclosed'],
							'esthours' => $aStat['esthours'],
							'totalhours' => $aStat['totalhours'],
							'etchours' => $aStat['etchours']
						);
				}
			}
			while ($oDB->next_record());

			$this->smartyHelper->assign('VAL_CHILDPROJECTS', $aProjects);
		}
	}

	private function SetStatistics()
	{
		global $dcl_info;

		if ($this->project == null)
			return;

		$arrayStats = $this->project->GetProjectStatistics($this->project->projectid, $dcl_info['DCL_PROJECT_INCLUDE_CHILD_STATS'] == 'Y', $dcl_info['DCL_PROJECT_INCLUDE_PARENT_STATS'] == 'Y');
		$this->smartyHelper->assign('VAL_TOTALTASKS', $arrayStats['totaltasks']);
		$this->smartyHelper->assign('VAL_TASKSCLOSED', $arrayStats['tasksclosed']);
		$this->smartyHelper->assign('VAL_ESTHOURS', $arrayStats['esthours']);
		if ($arrayStats['etchours'] > 0)
		{
			$oneDay = 24 * 60 * 60; // Just in case time scale changes in the future
			$i = 0;
			$workDays = $arrayStats['etchours'] / 8.0;
			if ($arrayStats['resources'] > 1)
				$workDays /= $arrayStats['resources'];

			$endDay = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
			while ($i < $workDays)
			{
				$endDay += $oneDay;
				if (date('w', $endDay) != 0 && date('w', $endDay) != 6)
					$i++;
			}

			$this->smartyHelper->assign('VAL_ETCDATE', date($dcl_info['DCL_DATE_FORMAT'], $endDay));
		}
		else
			$this->smartyHelper->assign('VAL_ETCDATE', '');

		$this->smartyHelper->assign('VAL_RESOURCES', $arrayStats['resources']);

		$ouHours = -($arrayStats['esthours'] - $arrayStats['totalhours']);
		$diffHours = $ouHours;
		if ($diffHours < 0)
			$diffHours = -$diffHours;

		$ouPct = 0.0;
		$sign = '';
		if ($arrayStats['esthours'] > 0)
		{
			$ouPct = $diffHours / $arrayStats['esthours'] * 100;
			if ($arrayStats['esthours'] > $arrayStats['totalhours'] && $arrayStats['esthours'] > 0)
				$sign = '-';
			else if ($arrayStats['totalhours'] > $arrayStats['esthours'] && $arrayStats['totalhours'] > 0)
				$sign = '+';
		}

		$this->smartyHelper->assign('VAL_HOURSPM', sprintf('%s%0.2f (%s%0.2f%%)', $sign, abs($ouHours), $sign, abs($ouPct)));
		$this->smartyHelper->assign('VAL_TOTALHOURS', $arrayStats['totalhours']);
		$this->smartyHelper->assign('VAL_ETCHOURS', $arrayStats['etchours']);

		if ($arrayStats['totalhours'] + $arrayStats['etchours'] > 0.0)
			$this->smartyHelper->assign('VAL_PCTCOMP', sprintf('%0.2f%%', ($arrayStats['totalhours'] / ($arrayStats['totalhours'] + $arrayStats['etchours'])) * 100));
		else
			$this->smartyHelper->assign('VAL_PCTCOMP', '0.00%');
	}
}
