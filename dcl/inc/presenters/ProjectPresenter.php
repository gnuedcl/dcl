<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2012 Free Software Foundation
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

LoadStringResource('bo');
LoadStringResource('prj');
LoadStringResource('pm');

class ProjectPresenter
{
	public function Index()
	{
		global $dcl_info;

		commonHeader();

		$oView = new ProjectSqlQueryHelper();
		$oView->numrows = 25;

		$filterStatus = @Filter::ToSignedInt($_REQUEST['filterStatus']);
		$filterReportto = @Filter::ToInt($_REQUEST['filterReportto']);

		$oView->title = 'Projects';
		$oView->AddDef('columnhdrs', '', array(STR_CMMN_ID, STR_PRJ_LEAD, STR_PRJ_STATUS, STR_PRJ_NAME));
		$oView->AddDef('columns', '', array('projectid', 'reportto.short', 'statuses.name', 'dcl_projects.name'));
		if ($dcl_info['DCL_PROJECT_BROWSE_PARENTS_ONLY'] == 'Y')
			$oView->AddDef('filter', 'parentprojectid', 0);

		$oView->AddDef('order', '', array('dcl_projects.name'));

		if ($filterStatus !== null)
		{
			if ($filterStatus > 0)
				$oView->AddDef('filter', 'dcl_projects.status', $filterStatus);
			else if ($filterStatus == -1)
				$oView->AddDef('filter', 'statuses.dcl_status_type', '2');
			else
				$oView->AddDef('filternot', 'statuses.dcl_status_type', '2');
		}
		else
		{
			$oView->AddDef('filternot', 'statuses.dcl_status_type', '2');
		}

		if ($filterReportto !== null)
			$oView->AddDef('filter', 'dcl_projects.reportto', $filterReportto);

		if (isset($_REQUEST['filterName']) && trim($_REQUEST['filterName']) != '')
			$oView->AddDef('filterlike', 'name', GPCStripSlashes($_REQUEST['filterName']));

		$oHtml = new htmlProjectsBrowse();
		$oHtml->Render($oView);
	}

	public function Dashboard($id, array $children = null)
	{
		commonHeader();

		$model = new ProjectsModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException('Could not find a project with an id of ' . $id);

		$smarty = new SmartyHelper();
		$smarty->assign('VAL_PROJECTID', $id);
		$smarty->assign('VAL_NAME', $model->name);

		if (isset($children) && is_array($children))
		{
			$smarty->assign('VAL_PROJECTCHILDREN', join(',', $children));

			$projectSqlHelper = new ProjectSqlQueryHelper();
			$projectSqlHelper->columns = array('projectid', 'name');
			$projectSqlHelper->AddDef('filter', 'projectid', $children);

			$includedProjects = array();
			if ($model->Query($projectSqlHelper->GetSQL()))
			{
				while ($model->next_record())
				{
					$includedProjects[] = array('id' => $model->f(0), 'name' => $model->f(1));
				}
			}

			$smarty->assign('VAL_INCLUDEDPROJECTS', $includedProjects);
		}

		$smarty->Render('ProjectDashboard.tpl');
	}

	public function Create(ProjectsModel $model = null)
	{
		global $dcl_info;

		commonHeader();
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_ADD);

		$t = new SmartyHelper();

		$viewData = new ProjectFormViewData();
		if ($model == null)
		{
			$viewData->StatusId = 1;
			$viewData->ResponsibleId = DCLID;
		}
		else
		{
			$viewData->Name = $model->name;
			$viewData->ResponsibleId = $model->reportto;
			$viewData->StatusId = $model->status;
			$viewData->ParentId = $model->parentprojectid;
			$viewData->Deadline = $model->projectdeadline;
			$viewData->Description = $model->description;

			if ($model->parentprojectid > 0)
			{
				$metadata = new DisplayHelper();
				$viewData->ParentName = $metadata->GetProject($model->parentprojectid);
			}
		}

		$t->assignByRef('ViewData', $viewData);

		$t->assign('IS_EDIT', false);

		if ($dcl_info['DCL_PROJECT_XML_TEMPLATES'] == 'Y')
		{
		}

		$t->Render('ProjectsForm.tpl');
	}

	public function Edit(ProjectsModel $model)
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_MODIFY, $model->projectid);

		$t = new SmartyHelper();

		$viewData = new ProjectFormViewData();
		$viewData->Id = $model->projectid;
		$viewData->Name = $model->name;
		$viewData->ResponsibleId = $model->reportto;
		$viewData->StatusId = $model->status;
		$viewData->ParentId = $model->parentprojectid;
		$viewData->Deadline = $model->projectdeadline;
		$viewData->Description = $model->description;

		if ($model->parentprojectid > 0)
		{
			$metadata = new DisplayHelper();
			$viewData->ParentName = $metadata->GetProject($model->parentprojectid);
		}

		$t->assignByRef('ViewData', $viewData);

		$t->assign('IS_EDIT', true);

		$t->Render('ProjectsForm.tpl');
	}

	public function Delete(ProjectsModel $model)
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_DELETE, $model->projectid);

		ShowDeleteYesNo('Project', 'Project.Destroy', $model->projectid, $model->name, false, 'id');
	}

	public function AddToProject($jcn, $seq, $menuAction, $sFunction)
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK);

		$projectHtmlHelper = new ProjectHtmlHelper();

		$smartyHelper = new SmartyHelper();

		$smartyHelper->assign('TXT_FUNCTION', $sFunction);
        $smartyHelper->assign('menuAction', $menuAction);
		$smartyHelper->assign('CMB_PROJECT', $projectHtmlHelper->GetCombo(0, 'projectid', 0, 0, -1, true));
		$smartyHelper->assign('jcn', $jcn);
		$smartyHelper->assign('seq', $seq);

		$smartyHelper->Render('ProjectmapForm.tpl');
	}

	public function ChooseProject($jcn, $seq)
	{
		$this->AddToProject($jcn, $seq, 'Project.InsertTask', STR_PM_ADDTOPRJ);
	}

	public function ShowUploadFileForm($projectId)
	{
		global $dcl_info;

		commonHeader();
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_ATTACHFILE, $projectId);

		$t = new SmartyHelper();

		$t->assign('VAL_MAXUPLOADFILESIZE', $dcl_info['DCL_MAX_UPLOAD_FILE_SIZE']);
		$t->assign('VAL_PROJECTID', $projectId);
		$t->assign('LNK_CANCEL', menuLink('', 'menuAction=Project.Detail&id=' . $projectId));

		$t->Render('ProjectsUpload.tpl');
	}

	public function DeleteAttachment($projectId, $filename)
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_REMOVEFILE, $projectId);

		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('VAL_FILENAME', $filename);
		$smartyHelper->assign('VAL_PROJECTID', $projectId);
		$smartyHelper->assign('TXT_DELCONFIRM', sprintf(STR_PRJ_DELCONFIRM, $filename));

		$smartyHelper->Render('ProjectsDeleteAttachment.tpl');
	}

	public function ChangeLog()
	{
		commonHeader();

		RequirePermission(DCL_ENTITY_CHANGELOG, DCL_PERM_VIEW);
		$id = @Filter::RequireInt($_REQUEST['projectid']);
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_VIEW, $id);

		$o = new SccsXrefModel();
		if ($o->ListChangeLog(DCL_ENTITY_PROJECT, $id) != -1)
		{
			$allRecs = array();
			while ($o->next_record())
			{
				$allRecs[] = array($o->f(0) . ': ' . $o->f(2), $o->f(1), $o->f(3), $o->f(4), $o->f(5), $o->FormatTimestampForDisplay($o->f(6)));
			}

			$oTable = new TableHtmlHelper();
			$oTable->setCaption("ChangeLog for Project $id");
			$oTable->addColumn('Project', 'string');
			$oTable->addColumn('Changed By', 'string');
			$oTable->addColumn('File', 'string');
			$oTable->addColumn('Version', 'string');
			$oTable->addColumn('Comments', 'string');
			$oTable->addColumn('Date', 'string');

			$oTable->addToolbar(menuLink('', "menuAction=Project.Detail&id=$id"), 'Back');
			$oTable->addGroup(0);
			$oTable->setData($allRecs);
			$oTable->setShowRownum(true);
			$oTable->render();
		}
	}
}
