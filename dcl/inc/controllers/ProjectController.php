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

LoadStringResource('prj');

class ProjectController
{
	public function Index()
	{
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_VIEW);

		$presenter = new ProjectPresenter();
		$presenter->Index();
	}

	public function Dashboard()
	{
		$id = @Filter::RequireInt($_REQUEST['id']);
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_VIEW, $id);
		$children = @Filter::ToIntArray($_REQUEST['children']);

		$presenter = new ProjectPresenter();
		$presenter->Dashboard($id, $children);
	}

	public function Create()
	{
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_ADD);

		$presenter = new ProjectPresenter();
		$presenter->Create();
	}

	public function Insert()
	{
		global $dcl_info;

		RequirePost();
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_ADD);

		$model = new ProjectsModel();
		$model->InitFrom_POST();

		if ($model->Exists($model->name))
		{
			ShowError(sprintf(STR_PRJ_ALREADYEXISTS, $model->name));

			$presenter = new ProjectPresenter();
			$presenter->Create($model);
			return;
		}

		if (@Filter::ToInt($model->parentprojectid) === null)
			$model->parentprojectid = 0;

		$model->createdby = DCLID;
		$model->status = $dcl_info['DCL_DEFAULT_PROJECT_STATUS'];
		$model->Add();

		SetRedirectMessage('Success', 'Project created');
		RedirectToAction('Project', 'Detail', 'id=' . $model->projectid);
	}

	public function Edit()
	{
		$id = @Filter::RequireInt($_REQUEST['id']);

		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_MODIFY, $id);

		$model = new ProjectsModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		$presenter = new ProjectPresenter();
		$presenter->Edit($model);
	}

	public function Update()
	{
		global $dcl_info;

		RequirePost();
		$id = @Filter::RequireInt($_POST['projectid']);

		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_MODIFY, $id);

		$model = new ProjectsModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		$status = @Filter::RequireInt($_POST['status']);
		$parentProjectId = @Filter::ToInt($_POST['parentprojectid']);
		if ($parentProjectId === null)
			$parentProjectId = 0;

		$statusModel = new StatusModel();
		if ($statusModel->GetStatusType($status) == 2 && $statusModel->GetStatusType($model->status) != 2)
		{
			// moving to closed
			$model->finalclose = date($dcl_info['DCL_DATE_FORMAT']);
		}
		else if ($statusModel->GetStatusType($status) != 2 && $statusModel->GetStatusType($model->status) == 2)
		{
			// reopened
			$model->finalclose = '';
		}

		$parentChanged = ($model->parentprojectid != $parentProjectId);
		$originalParentId = $model->parentprojectid;
		$model->InitFrom_POST();
		if ($parentChanged && $model->parentprojectid > 0)
		{
			if (!$model->ParentIsNotChild($model->projectid, $model->parentprojectid))
			{
				trigger_error(STR_BO_PARENTISCHILD);

				$model->parentprojectid = $originalParentId;

				$presenter = new ProjectPresenter();
				$presenter->Edit($model);
				return;
			}
		}

		$model->Edit();

		SetRedirectMessage('Success', 'Project updated.');
		RedirectToAction('Project', 'Detail', 'id=' . $model->projectid);
	}

	public function Delete()
	{
		$id = @Filter::RequireInt($_REQUEST['id']);

		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_DELETE, $id);

		$model = new ProjectsModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		$presenter = new ProjectPresenter();
		$presenter->Delete($model);
	}

	public function Destroy()
	{
		RequirePost();
		$id = @Filter::RequireInt($_POST['id']);
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_DELETE, $id);

		$model = new ProjectsModel();
		$model->projectid = $id;
		$model->Delete();

		$watchesModel = new WatchesModel();
		$watchesModel->DeleteByObjectID(2, $id);

		SetRedirectMessage('Success', 'Project deleted.');
		RedirectToAction('Project', 'Index');
	}

	public function Detail()
	{
		$id = @Filter::RequireInt($_REQUEST['id']);

		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_VIEW, $id);

		$model = new ProjectsModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException();

		$statusFilter = @Filter::ToInt($_REQUEST['wostatus'], 0);
		$responsibleFilter = @Filter::ToInt($_REQUEST['woresponsible'], 0);
		$groupBy = @Filter::ToInt($_REQUEST['wogroupby'], 'none');

		$presenter = new ProjectDetailPresenter();
		$presenter->Show($id, $statusFilter, $responsibleFilter, $groupBy);
	}

	public function Tree()
	{
		$project = @Filter::RequireInt($_REQUEST['project']);

		if ($project > 0)
		{
			RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_VIEW, $project);

			$presenter = new ProjectDetailPresenter();
			$woStatus = @Filter::ToInt($_REQUEST['wostatus'], 0);
			$woResponsible = @Filter::ToInt($_REQUEST['woresponsible'], 0);

			$presenter->ShowTree($project, $woStatus, $woResponsible);
		}
	}

	public function AddTask()
	{
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK);

		$jcn = @Filter::RequireInt($_REQUEST['jcn']);
		$seq = @Filter::RequireInt($_REQUEST['seq']);

		$projectMapModel = new ProjectMapModel();
		if ($projectMapModel->LoadByWO($jcn, $seq) != -1)
		{
			// Mapped implicitly (seq = 0) or explicitly (seq > 0)
			$projectMapModel->GetRow();
			$presenter = new ProjectDetailPresenter();
			$presenter->Show($projectMapModel->projectid, 0, 0);
		}
		else
		{
			$presenter = new ProjectPresenter();
			$presenter->ChooseProject($jcn, $seq);
		}
	}

	public function InsertTask()
	{
		RequirePost();
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK);

		$objPM = new ProjectMapModel();
		$objPM->InitFrom_POST();
		if (IsSet($_REQUEST['addall']) && $_REQUEST['addall'] == '1')
		{
			$objPM->seq = 0;
			// Be sure all other entries for this JCN are deleted so they move to this project
			$objPM->Delete();
		}

		$objPM->Add();

		$presenter = new ProjectDetailPresenter();
		$presenter->Show($objPM->projectid, 0, 0);
	}

	public function RemoveTask()
	{
		RequirePost();
		$jcn = @Filter::RequireInt($_POST['jcn']);
		$seq = @Filter::RequireInt($_POST['seq']);

		$projectMapModel = new ProjectMapModel();
		if ($projectMapModel->LoadByWO($jcn, $seq) == -1)
			throw new InvalidEntityException();

		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_REMOVETASK, $projectMapModel->projectid);

		ProjectsModel::RemoveTask($jcn, $seq);

		SetRedirectMessage('Success', 'Task removed.');
		RedirectToAction('Project', 'Detail', 'id=' . $projectMapModel->projectid);
	}

	public function Download()
	{
		global $dcl_info;

		$id = @Filter::RequireInt($_REQUEST['projectid']);
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_VIEW, $id);

		if (!@Filter::IsValidFileName($_REQUEST['filename']))
			throw new InvalidDataException();

		$fileHelper = new FileHelper();
		$fileHelper->iType = DCL_ENTITY_PROJECT;
		$fileHelper->iKey1 = $id;
		$fileHelper->sFileName = $_REQUEST['filename'];
		$fileHelper->sRoot = $dcl_info['DCL_FILE_PATH'] . '/attachments';
		$fileHelper->Download();
	}

	public function BatchMove()
	{
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK);

		if (IsSet($_REQUEST['selected']) && is_array($_REQUEST['selected']) && count($_REQUEST['selected']) > 0)
		{
			$presenter = new ProjectPresenter();
			$presenter->AddToProject($_REQUEST['selected'], null, 'Project.PostBatchMove', 'Batch Move Work Orders to Another Project');

			$timeCardPresenter = new htmlTimeCards();
			$timeCardPresenter->ShowBatchWO();
		}
		else
			throw new PermissionDeniedException();
	}

	public function PostBatchMove()
	{
		RequirePost();

		$projectId = @Filter::RequireInt($_REQUEST['projectid']);
		$woStatus = @Filter::ToInt($_REQUEST['wostatus'], 0);
		$woResponsible = @Filter::ToInt($_REQUEST['woresponsible'], 0);

		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK, $projectId);

		$model = new ProjectsModel();
		$model->BatchMove($_POST);

		SetRedirectMessage('Success', 'Batch move successful.');
		RedirectToAction('Project', 'Detail', "id=$projectId&wostatus=$woStatus&woresponsible=$woResponsible");
	}

	public function Upload()
	{
		$projectId = @Filter::ToInt($_REQUEST['projectid']);
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_ATTACHFILE, $projectId);

		$presenter = new ProjectPresenter();
		$presenter->ShowUploadFileForm($projectId);
	}

	public function UploadAttachment()
	{
		global $dcl_info;

		RequirePost();
		$projectId = @Filter::RequireInt($_REQUEST['projectid']);
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_ATTACHFILE, $projectId);

		if (($sFileName = Filter::ToFileName('userfile')) === null)
			throw new InvalidDataException();

		$fileHelper = new FileHelper();
		$fileHelper->iType = DCL_ENTITY_PROJECT;
		$fileHelper->iKey1 = $projectId;
		$fileHelper->sFileName = Filter::ToActualFileName('userfile');
		$fileHelper->sTempFileName = $sFileName;
		$fileHelper->sRoot = $dcl_info['DCL_FILE_PATH'] . '/attachments';
		$fileHelper->Upload();

		SetRedirectMessage('Success', 'Attachment uploaded.');
		RedirectToAction('Project', 'Detail', "id=$projectId");
	}

	public function DeleteAttachment()
	{
		$projectId = @Filter::RequireInt($_REQUEST['projectid']);
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_REMOVEFILE, $projectId);

		if (!@Filter::IsValidFileName($_REQUEST['filename']))
			throw new InvalidDataException();

		$presenter = new ProjectPresenter();
		$presenter->DeleteAttachment($projectId, $_REQUEST['filename']);
	}

	public function DestroyAttachment()
	{
		global $dcl_info;

		RequirePost();
		$projectId = @Filter::RequireInt($_REQUEST['projectid']);
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_REMOVEFILE, $projectId);

		if (!@Filter::IsValidFileName($_REQUEST['filename']))
			throw new InvalidDataException();

		$attachPath = $dcl_info['DCL_FILE_PATH'] . '/attachments/prj/' . substr($projectId, -1) . '/' . $projectId . '/';
		if (is_file($attachPath . $_REQUEST['filename']) && is_readable($attachPath . $_REQUEST['filename']))
			unlink($attachPath . $_REQUEST['filename']);

		SetRedirectMessage('Success', 'Attachment deleted.');
		RedirectToAction('Project', 'Detail', "id=$projectId");
	}
}
