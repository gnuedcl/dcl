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

class WorkOrderPresenter
{
	public function Create()
	{
		$model = new WorkOrderFormViewData();
		$model->Mode = WorkOrderFormViewData::Create;
		$this->CreateForm($model);
	}
	
	public function CreateSequence($workOrderId)
	{
		$model = new WorkOrderFormViewData();
		$model->WorkOrderId = $workOrderId;
		$model->Mode = WorkOrderFormViewData::CreateSequence;
		$this->CreateForm($model);
	}
	
	public function CreateTask($projectId)
	{
		$model = new WorkOrderFormViewData();
		$model->ProjectId = $projectId;
		$model->Mode = WorkOrderFormViewData::Create;
		$this->CreateForm($model);
	}
	
	public function CreateForContact($contactId)
	{
		$model = new WorkOrderFormViewData();
		$model->ContactId = $contactId;
		$model->Mode = WorkOrderFormViewData::Create;
		$this->CreateForm($model);
	}
	
	public function CreateForOrg($organizationId)
	{
		$model = new WorkOrderFormViewData();
		$model->OrganizationId = $organizationId;
		$model->Mode = WorkOrderFormViewData::Create;
		$this->CreateForm($model);
	}
	
	public function Copy(WorkOrderModel $workOrder)
	{
		$model = WorkOrderFormViewData::CopyWorkOrder($workOrder);
		$model->Mode = WorkOrderFormViewData::Create;
		$this->CreateForm($model);
	}
	
	public function CopyAsSequence(WorkOrderModel $workOrder)
	{
		$model = WorkOrderFormViewData::CopyWorkOrder($workOrder);
		$model->Mode = WorkOrderFormViewData::CreateSequence;
		$this->CreateForm($model);
	}
	
	public function CopyFromTicket(TicketsModel $ticket)
	{
		$model = WorkOrderFormViewData::CopyTicket($ticket);
		$model->Mode = WorkOrderFormViewData::Create;
		$this->CreateForm($model);
	}
	
	public function Edit(WorkOrderModel $workOrder)
	{
		$model = WorkOrderFormViewData::CopyWorkOrder($workOrder);
		$model->Mode = WorkOrderFormViewData::Edit;
		$this->EditForm($model);
	}
	
	public function Delete(WorkOrderModel $workOrder)
	{
		commonHeader();
		
		ShowDeleteYesNo('Delete Work Order [' . $workOrder->jcn . '-' . $workOrder->seq . ']', 'Workorder.Destroy', $workOrder->jcn, $workOrder->summary, false, 'jcn', $workOrder->seq, 'seq');
	}
	
	public function GraphCriteria()
	{
		global $dcl_info;
		
		commonHeader();
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_REPORT);

		// GD is required, so short-circuit if not installed
		if (!extension_loaded('gd'))
			throw new Exception(STR_BO_GRAPHNEEDSGD);

		$smarty = new SmartyHelper();

		$smarty->assign('CMB_DAYS', '<select id="days" name="days"><option value="7">7 ' . STR_WO_DAYS . '</option><option value="14">14 ' . STR_WO_DAYS . '</option></select>');
		$smarty->assign('VAL_TODAY', date($dcl_info['DCL_DATE_FORMAT']));

		$productHtmlHelper = new ProductHtmlHelper();
		$smarty->assign('CMB_PRODUCTS', $productHtmlHelper->Select(0, 'product', 'name', 0, 0, false));
		
		$smarty->Render('WorkOrderGraph.tpl');
	}
	
	public function Graph($days, $dateFrom, $product)
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_REPORT);

		// GD is required, so short-circuit if not installed
		if (!extension_loaded('gd'))
			throw new Exception(STR_BO_GRAPHNEEDSGD);

		$model = new WorkOrderModel();
		$graph = $model->Graph($days, $dateFrom, $product);
		
		print('<center>');
		echo '<img border="0" src="', menuLink('', 'menuAction=LineGraphImageHelper.Show&' . $graph->ToURL()), '">';
		print('</center>');
	}
	
	public function Reassign(WorkOrderModel $workOrder, $returnTo = null, $projectId = null)
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ASSIGN, $workOrder->jcn, $workOrder->seq);
		
		$productModel = new ProductModel();
		$personnelHtmlHelper = new PersonnelHtmlHelper();
		$priorityHtmlHelper = new PriorityHtmlHelper();
		$severityHtmlHelper = new SeverityHtmlHelper();

		$smartyHelper = new SmartyHelper();
		
		$setid = $productModel->GetWorkOrderAttributeSet($workOrder->product);

		$smartyHelper->assign('TXT_TITLE', STR_WO_REASSIGNTITLE . ' ' . $workOrder->jcn . '-' . $workOrder->seq);

		$smartyHelper->assign('menuAction', 'WorkOrder.ReassignWorkOrder');
		$smartyHelper->assign('jcn', $workOrder->jcn);
		$smartyHelper->assign('seq', $workOrder->seq);

		$smartyHelper->assign('VAL_DEADLINEON', $workOrder->deadlineon);
		$smartyHelper->assign('VAL_ESTSTARTON', $workOrder->eststarton);
		$smartyHelper->assign('VAL_ESTENDON', $workOrder->estendon);
		$smartyHelper->assign('VAL_ESTHOURS', $workOrder->esthours);
		$smartyHelper->assign('VAL_ETCHOURS', $workOrder->etchours);

		$smartyHelper->assign('CMB_RESPONSIBLE', $personnelHtmlHelper->Select($workOrder->responsible, 'responsible', 'lastfirst', 0, true, DCL_ENTITY_WORKORDER));
		$smartyHelper->assign('CMB_PRIORITY', $priorityHtmlHelper->Select($workOrder->priority, 'priority', 'name', 0, false, $setid));
		$smartyHelper->assign('CMB_SEVERITY', $severityHtmlHelper->Select($workOrder->severity, 'severity', 'name', 0, false, $setid));

		if ($returnTo != null)
		{
			$smartyHelper->assign('return_to', $returnTo);
			// FIXME: specific to projects
			if ($projectId != null)
				$smartyHelper->assign('project', $projectId);
		}

		$smartyHelper->Render('WorkOrderReassign.tpl');
	}
	
	public function BatchReassign($selected, $returnTo, $projectId)
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ASSIGN);
		
		$personnelHtmlHelper = new PersonnelHtmlHelper();
		$priorityHtmlHelper = new PriorityHtmlHelper();
		$severityHtmlHelper = new SeverityHtmlHelper();

		$smartyHelper = new SmartyHelper();
		
		$smartyHelper->assign('menuAction', 'WorkOrder.BatchReassignWorkOrders');
		$smartyHelper->assign('selected', $selected);

		$smartyHelper->assign('TXT_TITLE', STR_CMMN_REASSIGN);

		$smartyHelper->assign('CMB_RESPONSIBLE', $personnelHtmlHelper->Select(DCLID, 'responsible', 'lastfirst', 0, true, DCL_ENTITY_WORKORDER));
		$smartyHelper->assign('CMB_PRIORITY', $priorityHtmlHelper->Select(0, 'priority', 'name', 0, false));
		$smartyHelper->assign('CMB_SEVERITY', $severityHtmlHelper->Select(0, 'severity', 'name', 0, false));

		$sqlQueryHelper = new WorkOrderSqlQueryHelper();
		$sqlQueryHelper->SetFromURL();
		$smartyHelper->assign('VAL_VIEW', $sqlQueryHelper->GetForm());

		if ($returnTo != null)
		{
			$smartyHelper->assign('return_to', $returnTo);
			// FIXME: specific to projects
			if ($projectId != null)
				$smartyHelper->assign('project', $projectId);
		}

		$smartyHelper->Render('WorkOrderBatchAssign.tpl');
	}
	
	public function BatchDetail($selected)
	{
		global $g_oSec;
		
		commonHeader();

		$workOrderModel = new WorkOrderModel();
		$bNeedBreak = false;

		foreach ($selected as $val)
		{
			if ($bNeedBreak)
				print('<p style="page-break-after: always;"></p>');

			list($id, $seq) = explode('.', $val);
			$id = Filter::RequireInt($id);
			$seq = Filter::RequireInt($seq);

			if ($g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEW, $id, $seq))
			{
				if ($workOrderModel->Load($id, $seq) == -1)
					continue;

				$this->Detail($workOrderModel);
				$bNeedBreak = true;
			}
		}
	}
	
	public function Detail(WorkOrderModel $workOrder, $editTimeCardID = 0, $forDelete = false)
	{
		global $dcl_info, $g_oSec, $g_oSession;
		
		commonHeader();

		if (!$forDelete)
			RequireAnyPermission(DCL_ENTITY_WORKORDER, array(DCL_PERM_VIEW, DCL_PERM_VIEWACCOUNT, DCL_PERM_VIEWSUBMITTED), $workOrder->jcn, $workOrder->seq);
		else
			RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_DELETE, $workOrder->jcn, $workOrder->seq);

		$oMeta = new DisplayHelper();
		$smarty = new SmartyHelper();
		
		$smarty->assign_by_ref('WorkOrder', $workOrder);
		$smarty->assign('IS_PUBLIC', $g_oSec->IsPublicUser());

		$smarty->assign('VAL_FORMACTION', menuLink());
		$smarty->assign('VAL_SUMMARY', $workOrder->summary);
		$smarty->assign('VAL_JCN', $workOrder->jcn);
		$smarty->assign('VAL_SEQ', $workOrder->seq);
		$smarty->assign('VAL_PUBLIC', $workOrder->is_public == 'Y' ? STR_CMMN_YES : STR_CMMN_NO);
		$smarty->assign('VAL_DEADLINEON', $workOrder->deadlineon);

		$smarty->assign('VAL_REPORTED_VERSION', $oMeta->GetProductVersion($workOrder->reported_version_id));
		$smarty->assign('VAL_TARGETED_VERSION', $oMeta->GetProductVersion($workOrder->targeted_version_id));
		$smarty->assign('VAL_FIXED_VERSION', $oMeta->GetProductVersion($workOrder->fixed_version_id));
		$smarty->assign('VAL_ESTSTARTON', $workOrder->eststarton);
		$smarty->assign('VAL_STARTON', $workOrder->starton);
		$smarty->assign('VAL_ESTENDON', $workOrder->estendon);
		$smarty->assign('VAL_ESTHOURS', $workOrder->esthours);
		$smarty->assign('VAL_TOTALHOURS', $workOrder->totalhours);
		$smarty->assign('VAL_ETCHOURS', $workOrder->etchours);
		$smarty->assign('VAL_CREATEDON', $workOrder->createdon);
		$smarty->assign('VAL_STATUSON', $workOrder->statuson);
		$smarty->assign('VAL_LASTACTIONON', $workOrder->lastactionon);
		$smarty->assign('VAL_NOTES', $workOrder->notes);
		$smarty->assign('VAL_DESCRIPTION', $workOrder->description);

		$smarty->assign('VAL_CREATEBY', $oMeta->GetPersonnel($workOrder->createby));
		$smarty->assign('VAL_STATUS', $oMeta->GetStatus($workOrder->status));
		$smarty->assign('VAL_PRODUCT', $oMeta->GetProduct($workOrder->product));
		$smarty->assign('VAL_SETID', $oMeta->oProduct->wosetid);
		$smarty->assign('VAL_TYPE', $oMeta->GetWorkOrderType($workOrder->wo_type_id));
		$smarty->assign('VAL_MODULE', $oMeta->GetModule($workOrder->module_id));
		$smarty->assign('VAL_SOURCE', $oMeta->GetSource($workOrder->entity_source_id));
		$smarty->assign('VAL_RESPONSIBLEID', $workOrder->responsible);
		$smarty->assign('VAL_RESPONSIBLE', $oMeta->GetPersonnel($workOrder->responsible));
		$smarty->assign('VAL_PRIORITY', $oMeta->GetPriority($workOrder->priority));
		$smarty->assign('VAL_SEVERITY', $oMeta->GetSeverity($workOrder->severity));
		$smarty->assign('VAL_TAGS', str_replace(',', ', ', $oMeta->GetTags(DCL_ENTITY_WORKORDER, $workOrder->jcn, $workOrder->seq)));
		$smarty->assign('VAL_HOTLIST', $oMeta->GetHotlistWithPriority(DCL_ENTITY_WORKORDER, $workOrder->jcn, $workOrder->seq));
		
		$iStatusType = $oMeta->oStatus->GetStatusType($workOrder->status);
		$smarty->assign('VAL_STATUS_TYPE', $iStatusType);
		if ($iStatusType == 2)
		{
			$smarty->assign('VAL_CLOSEDBY', $oMeta->GetPersonnel($workOrder->closedby));
			$smarty->assign('VAL_CLOSEDON', $workOrder->closedon);
		}

		$aContact = $oMeta->GetContact($workOrder->contact_id);
		$smarty->assign('VAL_CONTACTID', $workOrder->contact_id);
		$smarty->assign('VAL_CONTACT', $aContact['name']);
		$smarty->assign('VAL_CONTACTPHONETYPE', $aContact['phonetype']);
		$smarty->assign('VAL_CONTACTPHONE', $aContact['phone']);
		$smarty->assign('VAL_CONTACTEMAILTYPE', $aContact['emailtype']);
		$smarty->assign('VAL_CONTACTEMAIL', $aContact['email']);
		$smarty->assign('VAL_WATCHTYPE', '3');

		if ($forDelete && $editTimeCardID == 0)
			$smarty->assign('IS_DELETE', true);

		$oTC = new TimeCardsModel();
		$smarty->assign('VAL_TIMECARDS', $oTC->GetTimeCardsArray($workOrder->jcn, $workOrder->seq));
		$smarty->assign('VAL_EDITTCID', $editTimeCardID);
		$smarty->assign('VAL_FORDELETE', $forDelete);
		
		$oTasks = new WorkOrderTaskModel();
		$smarty->assign('VAL_TASKS', $oTasks->GetTasksForWorkOrder($workOrder->jcn, $workOrder->seq));

		if ($g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEWFILE))
		{
			$oAttachments = new FileHelper();
			$smarty->assign('VAL_ATTACHMENTS', $oAttachments->GetAttachments(DCL_ENTITY_WORKORDER, $workOrder->jcn, $workOrder->seq));
		}

		if ($g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEW))
		{
			$smarty->assign('VAL_PROJECTS', ProjectsModel::GetProjectPath($workOrder->jcn, $workOrder->seq));
		}

		$oAcct = new WorkOrderOrganizationModel();
		if ($oAcct->Load($workOrder->jcn, $workOrder->seq) != -1)
		{
			$aOrgs = array();
			$bHasPerm = $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEWACCOUNT) || $g_oSec->IsOrgUser();
			$bViewAll = !$g_oSec->IsOrgUser();
			if ($bHasPerm)
				$aOrgs = explode(',', $g_oSession->Value('member_of_orgs'));

			$aOrgNames = array();
			$iOrgIndex = 0;
			while ($oAcct->next_record())
			{
				$oAcct->GetRow();
				if ($bViewAll || ($bHasPerm && in_array($oAcct->account_id, $aOrgs)))
				{
					$aOrgNames[$iOrgIndex]['org_id'] = $oAcct->account_id;
					$aOrgNames[$iOrgIndex]['org_name'] = $oAcct->account_name;
					$iOrgIndex++;
				}
			}

			$smarty->assign('VAL_ORGS', $aOrgNames);
		}

		$smarty->assign('PERM_ACTION', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION));
		$smarty->assign('PERM_ASSIGN', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ASSIGN));
		$smarty->assign('PERM_ADDTASK', $g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK));
		$smarty->assign('PERM_REMOVETASK', $g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_REMOVETASK));
		$smarty->assign('PERM_ADD', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ADD));
		$smarty->assign('PERM_COPYTOWO', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_COPYTOWO));
		$smarty->assign('PERM_MODIFY', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_MODIFY));
		$smarty->assign('PERM_DELETE', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_DELETE));
		$smarty->assign('PERM_ATTACHFILE', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ATTACHFILE));
		$smarty->assign('PERM_REMOVEFILE', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_REMOVEFILE));
		$smarty->assign('PERM_VIEW', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEW));
		$smarty->assign('PERM_VIEWWIKI', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEWWIKI));
		$smarty->assign('PERM_VIEWCHANGELOG', $g_oSec->HasPerm(DCL_ENTITY_CHANGELOG, DCL_PERM_VIEW));
		$smarty->assign('PERM_VIEWORG', $g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_VIEW));
		$smarty->assign('PERM_VIEWCONTACT', $g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_VIEW));
		$smarty->assign('PERM_AUDIT', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_AUDIT));
		$smarty->assign('PERM_MODIFY_TC', $g_oSec->HasPerm(DCL_ENTITY_TIMECARD, DCL_PERM_MODIFY));
		$smarty->assign('PERM_DELETE_TC', $g_oSec->HasPerm(DCL_ENTITY_TIMECARD, DCL_PERM_DELETE));
		$smarty->assign('PERM_ISPUBLICUSER', $g_oSec->IsPublicUser());

		if ($g_oSec->IsPublicUser())
			$smarty->Render('WorkordersDetailPublic.tpl');
		else
			$smarty->Render('WorkordersDetail.tpl');
	}
	
	public function ListSelected($selected)
	{
		global $dcl_info;

		commonHeader();
		
		$workOrderModel = new WorkOrderModel();
		if ($workOrderModel->ListSelected($selected) != -1)
		{
			$oTable = new TableHtmlHelper();
			$oTable->sCaption = 'Selected Work Orders';
			$oTable->addColumn(STR_WO_JCN, 'numeric');
			$oTable->addColumn(STR_WO_SEQ, 'numeric');
			$oTable->addColumn(STR_WO_RESPONSIBLE, 'string');
			$oTable->addColumn(STR_WO_STATUS, 'string');
			$oTable->addColumn(STR_WO_PROJECT, 'string');
			$oTable->addColumn(STR_WO_SUMMARY, 'string');
			$oTable->setShowRownum(true);
			$oTable->setData($workOrderModel->FetchAllRows());
			$oTable->render();
		}
	}
	
	public function Attachment(WorkOrderModel $workOrderModel)
	{
		global $dcl_info;
		
		commonHeader();
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ATTACHFILE, $workOrderModel->jcn, $workOrderModel->seq);

		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('VAL_MAXUPLOADFILESIZE', $dcl_info['DCL_MAX_UPLOAD_FILE_SIZE']);
		$smartyHelper->assign('VAL_JCN', $workOrderModel->jcn);
		$smartyHelper->assign('VAL_SEQ', $workOrderModel->seq);

		$smartyHelper->Render('WorkOrderAddAttachment.tpl');
	}
	
	public function DeleteAttachment(WorkOrderModel $workOrderModel, $fileName)
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_REMOVEFILE, $workOrderModel->jcn, $workOrderModel->seq);

		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('TXT_TITLE', STR_WO_DELETEATTACHMENTTITLE);
		$smartyHelper->assign('VAL_JCN', $workOrderModel->jcn);
		$smartyHelper->assign('VAL_SEQ', $workOrderModel->seq);
		$smartyHelper->assign('VAL_FILENAME', htmlspecialchars($fileName));
		$smartyHelper->assign('VAL_FORMACTION', menuLink());
		$smartyHelper->assign('TXT_DELATTCONFIRM', sprintf(STR_WO_DELATTCONFIRM, htmlspecialchars($fileName)));
		$smartyHelper->assign('BTN_YES', STR_CMMN_YES);
		$smartyHelper->assign('BTN_NO', STR_CMMN_NO);

		$smartyHelper->Render('WorkOrderDelAttachment.tpl');
	}
	
	public function Import()
	{
		global $dcl_info;

		commonHeader();
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_IMPORT);

		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('VAL_MAXUPLOADFILESIZE', $dcl_info['DCL_MAX_UPLOAD_FILE_SIZE']);
		$smartyHelper->Render('WorkOrderCSVUpload.tpl');
	}
	
	public function ChangeLog($id, $seq)
	{
		commonHeader();

		$sccsXrefModel = new SccsXrefModel();
		if ($sccsXrefModel->ListChangeLog(DCL_ENTITY_WORKORDER, $id, $seq) != -1)
		{
			$allRecs = array();
			while ($sccsXrefModel->next_record())
			{
				$allRecs[] = array($sccsXrefModel->f(0) . ': ' . $sccsXrefModel->f(2), 
					$sccsXrefModel->f(1), 
					$sccsXrefModel->f(3), 
					$sccsXrefModel->f(4), 
					$sccsXrefModel->f(5), 
					$sccsXrefModel->FormatTimestampForDisplay($sccsXrefModel->f(6)));
			}

			$tableHtmlHelper = new TableHtmlHelper();
			$tableHtmlHelper->setCaption("ChangeLog for Work Order $id-$seq");
			$tableHtmlHelper->addColumn('Project', 'string');
			$tableHtmlHelper->addColumn('Changed By', 'string');
			$tableHtmlHelper->addColumn('File', 'string');
			$tableHtmlHelper->addColumn('Version', 'string');
			$tableHtmlHelper->addColumn('Comments', 'string');
			$tableHtmlHelper->addColumn('Date', 'string');
	
			$tableHtmlHelper->addToolbar(menuLink('', "menuAction=WorkOrder.Detail&jcn=$id&seq=$seq"), 'Back');
			$tableHtmlHelper->addGroup(0);
			$tableHtmlHelper->setData($allRecs);
			$tableHtmlHelper->setShowRownum(true);
			$tableHtmlHelper->render();
		}
	}
	
	public function Criteria(WorkOrderSqlQueryHelper $sqlQueryHelper = null)
	{
		global $dcl_info, $g_oSec, $g_oSession;

		commonHeader();
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH);

		$haveCriteria = $sqlQueryHelper != null;
		
		$aProtectedFields = array('notes', 'dcl_hotlist.hotlist_tag', 'is_public', 'timecards.actionby', 'timecards.summary');

		$attributeSetJsHelper = new AttributeSetJsHelper();
		$attributeSetJsHelper->bModules = true;
		$attributeSetJsHelper->bStatusTypes = true;
		$attributeSetJsHelper->bDepartments = !$g_oSec->IsPublicUser();
		$attributeSetJsHelper->DisplayAttributeScript();

		$statusHtmlHelper = new StatusHtmlHelper();
		$projectHtmlHelper = new ProjectHtmlHelper();

		$personnelModel = new PersonnelModel();
		$personnelModel->Load(DCLID);
		
		$smartyHelper = new SmartyHelper();
		
		if ($haveCriteria)
			$smartyHelper->assign('VAL_REPORTTITLE', $sqlQueryHelper->title);
		else
			$smartyHelper->assign('VAL_REPORTTITLE', '');

		$smartyHelper->assign('VAL_DEPARTMENT', $personnelModel->department);
		$smartyHelper->assign('VAL_ID', DCLID);

		$aDefault = array();
		$aDefault['product'] = array();
		$aDefault['priority'] = array();
		$aDefault['severity'] = array();
		$aDefault['dcl_wo_account.account_id'] = array();
		$aDefault['status'] = array();
		$aDefault['project'] = array();
		$aDefault['wo_type_id'] = array();
		$aDefault['statuses.dcl_status_type'] = array();
		$aDefault['department'] = array();
		$aDefault['personnel'] = array();
		$aDefault['is_public'] = array();
		$aDefault['entity_source_id'] = array();
		$aDefault['module_id'] = array();
		$sPersonnelKey = '';
		$sStatusKey = '';
		$sModuleKey = '';
		
		if ($haveCriteria)
		{
			$filter = $sqlQueryHelper->GetFilter();
			$filterLike = $sqlQueryHelper->GetFilterLike();
			$filterDate = $sqlQueryHelper->GetFilterDate();
		}

		if ($haveCriteria)
		{
			$filter = $sqlQueryHelper->GetFilter();
			foreach ($filter as $field => $values)
			{
				if (substr($field, 1) == '.department')
				{
					if ($field[0] == 'a')
						$smartyHelper->assign('CHK_RESPONSIBLE', ' checked');
					else if ($field[0] == 'b')
						$smartyHelper->assign('CHK_CREATEBY', ' checked');
					else if ($field[0] == 'c')
						$smartyHelper->assign('CHK_CLOSEDBY', ' checked');

					$field = 'department';
					$sPersonnelKey = '';
				}
				else if ($field == 'responsible' || $field == 'createby' || $field == 'closedby')
				{
					$smartyHelper->assign('CHK_' . strtoupper($field), ' checked');
					$field = 'personnel';
					$sPersonnelKey = '';
				}
				else if ($field == 'account')
				{
					$field = 'dcl_wo_account.account_id';
				}
				else if ($field == 'dcl_projects.projectid')
				{
					$field = 'project';
				}

				if (array_key_exists($field, $aDefault))
					$aDefault[$field] = $values;
			}

			if (isset($aDefault['personnel']) && is_array($aDefault['personnel']) && count($aDefault['personnel']) > 0)
			{
				$sPersonnel = implode(',', $aDefault['personnel']);
				$personnelModel->Query("select department, id from personnel where id in ($sPersonnel)");
				while ($personnelModel->next_record())
				{
					if (!in_array($personnelModel->f(0), $aDefault['department']))
						$aDefault['department'][] = $personnelModel->f(0);

					if ($sPersonnelKey != '')
						$sPersonnelKey .= ':';

					$sPersonnelKey .= sprintf('%d,%d', $personnelModel->f(0), $personnelModel->f(1));
				}
			}

			if (isset($aDefault['status']) && is_array($aDefault['status']) && count($aDefault['status']) > 0)
			{
				$sStatus = implode(',', $aDefault['status']);
				$personnelModel->Query("select dcl_status_type, id from statuses where id in ($sStatus)");
				while ($personnelModel->next_record())
				{
					if (!in_array($personnelModel->f(0), $aDefault['statuses.dcl_status_type']))
						$aDefault['statuses.dcl_status_type'][] = $personnelModel->f(0);

					if ($sStatusKey != '')
						$sStatusKey .= ':';

					$sStatusKey .= sprintf('%d,%d', $personnelModel->f(0), $personnelModel->f(1));
				}
			}

			if (isset($aDefault['module_id']) && is_array($aDefault['module_id']) && count($aDefault['module_id']) > 0)
			{
				$sModule = implode(',', $aDefault['module_id']);
				$personnelModel->Query("select product_id, product_module_id from dcl_product_module where product_module_id in ($sModule)");
				while ($personnelModel->next_record())
				{
					if (!in_array($personnelModel->f(0), $aDefault['product']))
						$aDefault['product'][] = $personnelModel->f(0);

					if ($sModuleKey != '')
						$sModuleKey .= ':';

					$sModuleKey .= sprintf('%d,%d', $personnelModel->f(0), $personnelModel->f(1));
				}
			}
		}
		else
		{
			$aDefault['product'] = 0;
			$aDefault['priority'] = 0;
			$aDefault['severity'] = 0;
			$aDefault['dcl_wo_account.account_id'] = 0;
			$aDefault['status'] = 0;
			$aDefault['project'] = 0;
			$aDefault['wo_type_id'] = 0;
			$aDefault['entity_source_id'] = 0;
			$aDefault['is_public'] = 0;
			$aDefault['statuses.dcl_status_type'] = 1;
			$aDefault['department'] = array($personnelModel->department);
			$aDefault['personnel'] = DCLID;
			$sPersonnelKey = sprintf('%d,%d', $personnelModel->department, DCLID);
			$sStatusKey = '';
			$sModuleKey = '';

			if ($GLOBALS['g_oSec']->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION))
			{
				$smartyHelper->assign('CHK_RESPONSIBLE', ' checked');
				$smartyHelper->assign('CHK_CREATEBY', '');
				$smartyHelper->assign('CHK_CLOSEDBY', '');
			}
			else
			{
				$smartyHelper->assign('CHK_CREATEBY', ' checked');
				$smartyHelper->assign('CHK_RESPONSIBLE', '');
				$smartyHelper->assign('CHK_CLOSEDBY', '');
			}
		}
		
		$smartyHelper->assign('VAL_DEPARTMENTS', $aDefault['department']);
		$smartyHelper->assign('VAL_PERSONNEL', $aDefault['personnel']);
		$smartyHelper->assign('VAL_WO_TYPE', $aDefault['wo_type_id']);
		$smartyHelper->assign('VAL_PRODUCT', $aDefault['product']);
		$smartyHelper->assign('VAL_MODULE', $aDefault['module_id']);
		$smartyHelper->assign('VAL_PRIORITY', $aDefault['priority']);
		$smartyHelper->assign('VAL_SEVERITY', $aDefault['severity']);

		$smartyHelper->assign('VAL_SELECTPERSONNELKEY', $sPersonnelKey);
		$smartyHelper->assign('VAL_SELECTSTATUSKEY', $sStatusKey);
		$smartyHelper->assign('VAL_SELECTMODULEKEY', $sModuleKey);

		$smartyHelper->assign('CMB_STATUSES', $statusHtmlHelper->Select($aDefault['status'], 'status', 'name', 8));

		$smartyHelper->assign('IS_PUBLIC', $g_oSec->IsPublicUser());
		if (!$g_oSec->IsPublicUser())
		{
			$smartyHelper->assign('CMB_PROJECTS', $projectHtmlHelper->GetCombo($aDefault['project'], 'project', 'name', 8));
			$smartyHelper->assign('CMB_PUBLIC', GetYesNoCombo($aDefault['is_public'], 'is_public', 2, false));
		}

		$oSelect = new SelectHtmlHelper();

		if ($g_oSec->IsOrgUser())
			$oSelect->SetOptionsFromDb('dcl_org', 'org_id', 'name', 'org_id IN (' . $g_oSession->Value('member_of_orgs') . ')', 'name');
		else
			$oSelect->SetOptionsFromDb('dcl_org', 'org_id', 'name', '', 'name');
		
		$oSelect->Size = 8;
		$oSelect->DefaultValue = $aDefault['dcl_wo_account.account_id'];
		$oSelect->Id = 'account';
		$smartyHelper->assign('CMB_ACCOUNTS', $oSelect->GetHTML());

		$oSource = new EntitySourceHtmlHelper();
		$smartyHelper->assign('CMB_SOURCE', $oSource->Select($aDefault['entity_source_id'], 'entity_source_id', 8, false));

		// Empty status is for selecting status type, then filtering status if desired
		$oSelect->Id = 'status';
		$oSelect->Size = 8;
		$smartyHelper->assign('CMB_STATUSESEMPTY', $oSelect->GetHTML());

		// Status Types
		$oSelect->Id = 'dcl_status_type';
		$oSelect->Size = 8;
		$oSelect->DefaultValue = $aDefault['statuses.dcl_status_type'];
		$oSelect->SetOptionsFromDb('dcl_status_type', 'dcl_status_type_id', 'dcl_status_type_name', '', 'dcl_status_type_id');
		$smartyHelper->assign('CMB_STATUSTYPES', $oSelect->GetHTML());

		$smartyHelper->assign('CHK_SUMMARY', '');
		$smartyHelper->assign('CHK_NOTES', '');
		$smartyHelper->assign('CHK_DESCRIPTION', '');
		$smartyHelper->assign('VAL_SEARCHTEXT', '');
		if ($haveCriteria && count($filterLike) > 0)
		{
			$searchText = '';
			foreach ($filterLike as $field => $values)
			{
				if ($field == 'summary' || $field == 'notes' || $field == 'description')
				{
					$smartyHelper->assign('CHK_' . strtoupper($field), ' CHECKED');
					$searchText = $values[0];
				}
			}

			$smartyHelper->assign('VAL_SEARCHTEXT', $searchText);
		}
		
		if (isset($filter['dcl_tag.tag_desc']) && is_array($filter['dcl_tag.tag_desc']) && count($filter['dcl_tag.tag_desc']) > 0)
			$smartyHelper->assign('VAL_TAGS', join(',', $filter['dcl_tag.tag_desc']));

		if (isset($filter['dcl_hotlist.hotlist_tag']) && is_array($filter['dcl_hotlist.hotlist_tag']) && count($filter['dcl_hotlist.hotlist_tag']) > 0)
			$smartyHelper->assign('VAL_HOTLISTS', join(',', $filter['dcl_hotlist.hotlist_tag']));

		$aDateChecks = array('createdon', 'closedon', 'statuson', 'lastactionon',
							'deadlineon', 'eststarton', 'estendon', 'starton');

		for ($i = 0; $i < count($aDateChecks); $i++)
			$smartyHelper->assign('CHK_' . strtoupper($aDateChecks[$i]), '');

		if ($haveCriteria)
		{
			$smartyHelper->assign('VAL_DATEFROM', '');
			$smartyHelper->assign('VAL_DATETO', '');
			if (count($filterDate) > 0)
			{
				$fromDate = '';
				$toDate = '';

				foreach ($filterDate as $field => $values)
				{
					$smartyHelper->assign('CHK_' . strtoupper($field), ' CHECKED');
					$fromDate = $values[0];
					$toDate = $values[1];
				}

				$smartyHelper->assign('VAL_DATEFROM', $fromDate);
				$smartyHelper->assign('VAL_DATETO', $toDate);
			}
		}
		else
		{
			$aFewDaysAgo = mktime(0, 0, 0, date('m'), date('d') - 3, date('Y'));
			$smartyHelper->assign('VAL_DATEFROM', date($dcl_info['DCL_DATE_FORMAT'], $aFewDaysAgo));
			$smartyHelper->assign('VAL_DATETO', date($dcl_info['DCL_DATE_FORMAT']));
		}

		$aCols = array(
				'jcn' => STR_WO_JCN,
				'seq' => STR_WO_SEQ,
				'dcl_wo_type.type_name' => STR_WO_TYPE,
				'responsible.short' => STR_WO_RESPONSIBLE,
				'products.name' => STR_WO_PRODUCT,
				'statuses.name' => STR_WO_STATUS,
				'summary' => STR_WO_SUMMARY,
				'dcl_product_module.module_name' => STR_CMMN_MODULE,
				'dcl_projects.name' => STR_WO_PROJECT,
				'dcl_org.name' => STR_WO_ACCOUNT,
				'count(*):dcl_org' => '# ' . STR_WO_ACCOUNT,
				'dcl_tag.tag_desc' => STR_CMMN_TAGS,
				'dcl_hotlist.hotlist_tag' => 'Hotlists',
				'createby.short' => STR_WO_OPENBY,
				'createdon' => STR_WO_OPENEDON,
				'closedby.short' => STR_WO_CLOSEBY,
				'closedon' => STR_WO_CLOSEDON,
				'statuson' => STR_WO_STATUSON,
				'lastactionon' => STR_WO_LASTACTION,
				'deadlineon' => STR_WO_DEADLINE,
				'eststarton' => STR_WO_ESTSTART,
				'estendon' => STR_WO_ESTEND,
				'starton' => STR_WO_START,
				'esthours' => STR_WO_ESTHOURS,
				'etchours' => STR_WO_ETCHOURS,
				'totalhours' => STR_WO_ACTHOURS,
				'priorities.name' => STR_WO_PRIORITY,
				'severities.name' => STR_WO_SEVERITY,
				'reported_version_id.product_version_text' => STR_WO_REVISION,
				'targeted_version_id.product_version_text' => 'Target Version',
				'fixed_version_id.product_version_text' => 'Fixed Version',
				'dcl_contact.last_name' => 'Contact Last Name',
				'dcl_contact.first_name' => 'Contact First Name',
				'dcl_contact_phone.phone_number' => STR_WO_CONTACTPHONE,
				'notes' => STR_WO_NOTES,
				'description' => STR_WO_DESCRIPTION,
				'dcl_status_type.dcl_status_type_name' => STR_CMMN_STATUSTYPE,
				'dcl_entity_source.entity_source_name' => STR_CMMN_SOURCE,
				'is_public' => STR_CMMN_PUBLIC,
				'actionby.short' => 'Last Time Card By',
				'timecards.summary' => 'Last Time Card Summary'
			);
			
		if ($g_oSec->IsPublicUser())
		{
			foreach ($aProtectedFields as $sField)
				unset($aCols[$sField]);
		}

		if ($haveCriteria)
		{
			$aShow = array();
			$aGroup = array();

			foreach ($sqlQueryHelper->columns as $colName)
			{
				if ($colName == 'a.short')
					$colName = 'responsible.short';
				else if ($colName == 'b.short')
					$colName = 'closedby.short';
				else if ($colName == 'c.short')
					$colName = 'createby.short';
				else if ($colName == 'g.short')
					$colName = 'actionby.short';
					
				$aShow[$colName] = $aCols[$colName];
			}

			foreach ($sqlQueryHelper->groups as $colName)
			{
				if ($colName == 'a.short')
					$colName = 'responsible.short';
				else if ($colName == 'b.short')
					$colName = 'closedby.short';
				else if ($colName == 'c.short')
					$colName = 'createby.short';
				else if ($colName == 'g.short')
					$colName = 'actionby.short';
					
				$aGroup[$colName] = $aCols[$colName];
			}
			
			if ($g_oSec->IsPublicUser())
			{
				foreach ($aProtectedFields as $sField)
				{
					if (isset($aShow[$sField]))
						unset($aShow[$sField]);
						
					if (isset($aGroup[$sField]))
						unset($aGroup[$sField]);
				}
			}
		}
		else
		{
			$aShow = array(
					'jcn' => STR_WO_JCN,
					'seq' => STR_WO_SEQ,
					'dcl_wo_type.type_name' => STR_WO_TYPE,
					'responsible.short' => STR_WO_RESPONSIBLE,
					'products.name' => STR_WO_PRODUCT,
					'statuses.name' => STR_WO_STATUS,
					'summary' => STR_WO_SUMMARY
				);

			$aGroup = array();
		}

		array_remove_keys($aCols, $aShow);
		array_remove_keys($aCols, $aGroup);

		$smartyHelper->assign('VAL_COLS', $aCols);
		$smartyHelper->assign('VAL_SHOW', $aShow);
		$smartyHelper->assign('VAL_GROUP', $aGroup);

		if ($haveCriteria)
		{
			$aOrder = array();
			
			$order = $sqlQueryHelper->GetOrder();
			foreach ($order as $val)
			{
				if ($val == 'priorities.weight')
					$sText = isset($aShow['priorities.name']) ? $aShow['priorities.name'] : $aCols['priorities.name'];
				else if ($val == 'severities.weight')
					$sText = isset($aShow['severities.name']) ? $aShow['severities.name'] : $aCols['severities.name'];
				else if (IsSet($aShow[$val]))
					$sText = $aShow[$val];
				else if (isset($aCols[$val]))
					$sText = $aCols[$val];

				array_push($aOrder, array($val => $sText));
			}
			
			$smartyHelper->assign('VAL_SORT');
		}

		$smartyHelper->Render('WorkOrderSearch.tpl');
	}
	
	private function CreateForm(WorkOrderFormViewData $viewData)
	{
		global $dcl_info, $g_oSec, $dcl_preferences, $g_oSession;
		
		commonHeader();
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ADD);
		
		$viewData->ProjectId = Filter::ToInt($viewData->ProjectId, 0);
		$viewData->ContactId = Filter::ToInt($viewData->ContactId, 0);
		$viewData->OrganizationId = Filter::ToInt($viewData->OrganizationId, 0);

		$attributeSetJsHelper = new AttributeSetJsHelper();
		$attributeSetJsHelper->bActiveOnly = true;
		$attributeSetJsHelper->bPriorities = true;
		$attributeSetJsHelper->bSeverities = true;
		$attributeSetJsHelper->bModules = true;
		$attributeSetJsHelper->DisplayAttributeScript();

		if ($viewData->Mode == WorkOrderFormViewData::CreateSequence)
			$viewData->Title = sprintf(STR_WO_ADDSEQJCN, $viewData->WorkOrderId);
		else
			$viewData->Title = STR_WO_ADDWO;
		
		$viewData->IsEdit = false;
		$viewData->Action = 'WorkOrder.Insert';
		$viewData->CanAddTask = $g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK, $viewData->ProjectId);

		$viewData->ResponsibleId = DCLID;
		$viewData->ResponsibleName = $GLOBALS['DCLNAME'];
		
		if ($dcl_info['DCL_AUTO_DATE'] == 'Y')
		{
			$viewData->DeadlineDate = date($dcl_info['DCL_DATE_FORMAT']);
			$viewData->EstStartDate = date($dcl_info['DCL_DATE_FORMAT']);
			$viewData->EstEndDate = date($dcl_info['DCL_DATE_FORMAT']);
		}

		// If not editing, display project options (if any)
		$objPM = new ProjectMapModel();
		if ($g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK) && $viewData->ProjectId > 0)
		{
			$projectModel = new ProjectsModel();
			$projectModel->Load($viewData->ProjectId);

			$viewData->ProjectName = $projectModel->name;
			$viewData->ProjectLabel = sprintf(STR_WO_WILLBEPARTOFPROJECT, $projectModel->name);
		}

		$oMeta = new DisplayHelper();
		if ($viewData->ContactId > 0)
		{
			$aContact =& $oMeta->GetContact($viewData->ContactId);
			if (is_array($aContact) && count($aContact) > 1)
				$viewData->ContactName = $aContact['name'];
			else
				$viewData->ContactId = '';
		}

		$aOrgID = array();
		$aOrgName = array();
		if ($viewData->OrganizationId == 0 && $g_oSession->Value('member_of_orgs') != '')
			$viewData->OrganizationId = array_shift(explode(',', $g_oSession->Value('member_of_orgs')));

		if ($viewData->OrganizationId > 0)
		{
			$aOrg =& $oMeta->GetOrganization($viewData->OrganizationId);
			if (is_array($aOrg) && count($aOrg) > 0)
			{
				$aOrgID[] = $viewData->OrganizationId;
				$aOrgName[] = $aOrg['name'];
			}
		}

		if (count($aOrgID) > 0)
		{
			$viewData->OrganizationIdCollection = $aOrgID;
			$viewData->OrganizationNameCollection = $aOrgName;
		}
		
		$viewData->Sequence = 0;
		if ($viewData->Mode == WorkOrderFormViewData::Create)
			$viewData->WorkOrderId = 0;

		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign_by_ref('ViewData', $viewData);
		$smartyHelper->Render('WorkOrderForm.tpl');
	}
	
	private function EditForm(WorkOrderFormViewData $viewData)
	{
		global $g_oSec, $dcl_preferences, $g_oSession;
		
		commonHeader();
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_MODIFY, $viewData->WorkOrderId, $viewData->Sequence);
		
		$attributeSetJsHelper = new AttributeSetJsHelper();
		$attributeSetJsHelper->bActiveOnly = false;
		$attributeSetJsHelper->bPriorities = true;
		$attributeSetJsHelper->bSeverities = true;
		$attributeSetJsHelper->bModules = true;
		$attributeSetJsHelper->DisplayAttributeScript();

		$viewData->Title = sprintf(STR_WO_EDITWO, $viewData->WorkOrderId, $viewData->Sequence);
		$viewData->IsEdit = true;
		$viewData->HideProject = true;
		$viewData->CanAttachFile = false;
		$viewData->Action = 'WorkOrder.Update';
		
		$metaData = new DisplayHelper();
		$viewData->ResponsibleName = $metaData->GetPersonnel($viewData->ResponsibleId);

		if ($viewData->ContactId > 0)
		{
			$aContact =& $metaData->GetContact($viewData->ContactId);
			if (is_array($aContact) && count($aContact) > 1)
				$viewData->ContactName = $aContact['name'];
			else
				$viewData->ContactId = '';
		}
		
		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign_by_ref('ViewData', $viewData);
		$smartyHelper->Render('WorkOrderForm.tpl');
	}
	
	public function Search(WorkOrderCriteriaModel $workOrderCriteriaModel)
	{
		commonHeader();
		
		if ($workOrderCriteriaModel->Title == '')
			$workOrderCriteriaModel->Title = STR_WO_RESULTSTITLE;
		
		$sqlQueryHelper = $workOrderCriteriaModel->GetSqlQueryHelper();
		
		$this->DisplayView($sqlQueryHelper);
	}
	
	public function DisplayView(WorkOrderSqlQueryHelper $sqlQueryHelper)
	{
		global $g_oSec, $g_oSession;

		commonHeader();
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH);

		$oTable = new TableHtmlHelper();
		$oTable->assign('VAL_VIEWSETTINGS', $sqlQueryHelper->GetForm());
			
		for ($iColumn = 0; $iColumn < count($sqlQueryHelper->groups); $iColumn++)
		{
			$oTable->addGroup($iColumn);
			$oTable->addColumn('', 'string');
		}

		$iColumn = 0;
		foreach ($sqlQueryHelper->columnhdrs as $sColumn)
		{
			if ($iColumn++ < count($sqlQueryHelper->groups))
				continue;
				
			$oTable->addColumn($sColumn, 'string');
		}
		
		$aOptions = array(
			STR_CMMN_SAVE => array('menuAction' => 'boViews.add', 'hasPermission' => $g_oSec->HasPerm(DCL_ENTITY_SAVEDSEARCH, DCL_PERM_ADD)),
			'Refine' => array('menuAction' => 'WorkOrder.RefineCriteria', 'hasPermission' => $g_oSec->HasPerm(DCL_ENTITY_SAVEDSEARCH, DCL_PERM_VIEW)),
			'Export' => array('menuAction' => 'boViews.export', 'hasPermission' => true),
			'Detail' => array('menuAction' => 'WorkOrder.BatchDetail', 'hasPermission' => $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ADD)),
			'Time Card' => array('menuAction' => 'boTimecards.batchadd', 'hasPermission' => $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION)),
			'Assign' => array('menuAction' => 'WorkOrder.BatchReassign', 'hasPermission' => $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ASSIGN)),
			'Project' => array('menuAction' => 'Project.BatchMove', 'hasPermission' => $g_oSec->HasAllPerm(array(DCL_ENTITY_PROJECT => array($g_oSec->PermArray(DCL_PERM_ADDTASK), $g_oSec->PermArray(DCL_PERM_REMOVETASK)))))
			);

		foreach ($aOptions as $sDisplay => $aOption)
		{
			if ($aOption['hasPermission'])
			{
				$oTable->addToolbar($aOption['menuAction'], $sDisplay);
			}
		}

		$oDB = new DbProvider;

		$sSQL = $sqlQueryHelper->GetSQL();
		if ($oDB->Query($sSQL) == -1)
			return;

		$iOffset = 0;
		for ($iColumn = count($sqlQueryHelper->groups); $iColumn < $oDB->NumFields(); $iColumn++)
		{
			$sFieldName = $oDB->GetFieldName($iColumn);
			if ($sFieldName == 'jcn')
				$oTable->assign('wo_id_ordinal', $iColumn);
			else if ($sFieldName == 'seq')
				$oTable->assign('seq_ordinal', $iColumn);
			else if ($sFieldName == '_num_accounts_')
			{
				$iOffset--;
				$oTable->assign('num_accounts_ordinal', $iColumn);
			}
			else if ($sFieldName == '_num_tags_')
			{
				$iOffset--;
				$oTable->assign('num_tags_ordinal', $iColumn);
			}
			else if ($sFieldName == 'tag_desc')
			{
				$oTable->assign('tag_ordinal', $iColumn);
			}
			else if ($sFieldName == '_num_hotlist_')
			{
				$iOffset--;
				$oTable->assign('num_hotlist_ordinal', $iColumn);
			}
			else if ($sFieldName == 'hotlist_tag')
			{
				$oTable->assign('hotlist_ordinal', $iColumn);
			}
			else if ($sqlQueryHelper->columns[$iColumn - count($sqlQueryHelper->groups)] == 'dcl_org.name')
			{
				$oTable->assign('org_ordinal', $iColumn);
			}
		}
		
		$oTable->setData($oDB->FetchAllRows());

		$oTable->assign('VAL_ENDOFFSET', $iOffset);
		$oTable->assign('VAL_VIEWSETTINGS', $sqlQueryHelper->GetForm());

		$oTable->setCaption($sqlQueryHelper->title);
		$oTable->setShowChecks(true);
		$oDB->FreeResult();

		$oTable->sTemplate = 'TableWorkOrderResults.tpl';
		$oTable->render();
	}
}