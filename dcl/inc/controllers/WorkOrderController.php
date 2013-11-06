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

LoadStringResource('wo');
class WorkOrderController
{
	public function Create()
	{
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ADD);

		$presenter = new WorkOrderPresenter();
		$presenter->Create();
	}
	
	public function CreateTask()
	{
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ADD);
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK);
		
		$projectId = Filter::RequireInt($_REQUEST['projectid']);
		
		$presenter = new WorkOrderPresenter();
		$presenter->CreateTask($projectId);
	}
	
	public function CreateForContact()
	{
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ADD);
		
		$contactId = Filter::RequireInt($_REQUEST['contact_id']);
		
		$presenter = new WorkOrderPresenter();
		$presenter->CreateForContact($contactId);
	}
	
	public function CreateForOrg()
	{
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ADD);
		
		$organizationId = Filter::RequireInt($_REQUEST['org_id']);
		
		$presenter = new WorkOrderPresenter();
		$presenter->CreateForOrg($organizationId);
	}
	
	public function CreateSequence()
	{
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ADD);

		$id = @Filter::RequireInt($_REQUEST['jcn']);
		
		$presenter = new WorkOrderPresenter();
		$presenter->CreateSequence($id);
	}
	
	public function Copy()
	{
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ADD);

		$id = @Filter::RequireInt($_REQUEST['jcn']);
		$seq = @Filter::RequireInt($_REQUEST['seq']);
		
		$workOrder = new WorkOrderModel();
		if ($workOrder->Load($id, $seq) == -1)
			throw new InvalidEntityException();
		
		$presenter = new WorkOrderPresenter();
		$presenter->Copy($workOrder);
	}
	
	public function CopyAsSequence()
	{
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ADD);

		$id = @Filter::RequireInt($_REQUEST['jcn']);
		$seq = @Filter::RequireInt($_REQUEST['seq']);
		
		$workOrder = new WorkOrderModel();
		if ($workOrder->Load($id, $seq) == -1)
			throw new InvalidEntityException();
		
		$presenter = new WorkOrderPresenter();
		$presenter->CopyAsSequence($workOrder);
	}
	
	public function Insert()
	{
		RequirePost();
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ADD);

		global $dcl_info, $g_oSec;

		$workOrderModel = new WorkOrderModel();

		// If we're creating a seq, be sure the jcn exists
		$workOrderId = 0;
		if (IsSet($_REQUEST['jcn']) && $_REQUEST['jcn'] != '')
		{
			$workOrderId = Filter::RequireInt($_REQUEST['jcn']);
			
			$workOrderModel->Query('SELECT jcn FROM workorders where jcn=' . $workOrderId);
			if (!$workOrderModel->next_record())
			{
				trigger_error(sprintf(STR_BO_NOJCNFORSEQWARNING, $workOrderId));
				$workOrderId = 0;
			}
		}

		$workOrderModel->InitFrom_POST();
		$workOrderModel->etchours = $workOrderModel->esthours;
		$workOrderModel->createby = DCLID;
		$workOrderModel->is_public = ((isset($_REQUEST['is_public']) && $_REQUEST['is_public'] == 'Y') || $g_oSec->IsPublicUser() ? 'Y' : 'N');

		PubSub::Publish('WorkOrder.Inserting', $workOrderModel);
		
		$workOrderModel->Add();

		// multiple accounts?
		if ($dcl_info['DCL_WO_SECONDARY_ACCOUNTS_ENABLED'] == 'Y')
		{
			if (IsSet($_REQUEST['secaccounts']) && $_REQUEST['secaccounts'] != '')
			{
				$aAccounts = explode(',', $_REQUEST['secaccounts']);
				if (count($aAccounts) > 0)
				{
					$workOrderOrganizationModel = new WorkOrderOrganizationModel();
					$workOrderOrganizationModel->wo_id = $workOrderModel->jcn;
					$workOrderOrganizationModel->seq = $workOrderModel->seq;

					for ($i = 0; $i < count($aAccounts); $i++)
					{
						if (($organizationId = Filter::ToInt($aAccounts[$i])) !== null && $organizationId > 0)
						{
							$workOrderOrganizationModel->account_id = $organizationId;
							$workOrderOrganizationModel->Add();
						}
					}
				}
			}
		}
		else if (IsSet($_REQUEST['secaccounts']))
		{
			if (($organizationId = @Filter::ToInt($_REQUEST['secaccounts'])) !== null && $organizationId > 0)
			{
				$workOrderOrganizationModel = new WorkOrderOrganizationModel();
				$workOrderOrganizationModel->wo_id = $workOrderModel->jcn;
				$workOrderOrganizationModel->seq = $workOrderModel->seq;
				$workOrderOrganizationModel->account_id = $organizationId;
				$workOrderOrganizationModel->Add();
			}
		}
		
		if (isset($_REQUEST['tags']))
		{
			$oTag = new EntityTagModel();
			$oTag->serialize(DCL_ENTITY_WORKORDER, $workOrderModel->jcn, $workOrderModel->seq, $_REQUEST['tags']);
		}

		if (isset($_REQUEST['hotlist']))
		{
			$oHotlist = new EntityHotlistModel();
			$oHotlist->serialize(DCL_ENTITY_WORKORDER, $workOrderModel->jcn, $workOrderModel->seq, $_REQUEST['hotlist']);
		}

		// add to a project?
		if (IsSet($_REQUEST['projectid']))
		{
			if (($iProjID = @Filter::ToInt($_REQUEST['projectid'])) !== null && $iProjID > 0)
			{
				if ($g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK, $iProjID))
				{
					$objPM = new ProjectMapModel();
					$objPM->projectid = $iProjID;
					$objPM->jcn = $workOrderModel->jcn;
		
					if (IsSet($_REQUEST['addall']) && $_REQUEST['addall'] == '1')
					{
						$objPM->Execute('DELETE FROM projectmap WHERE jcn=' . $workOrderModel->jcn);
						$objPM->seq = 0;
					}
					else
						$objPM->seq = $workOrderModel->seq;
		
					$objPM->Add();
				}
			}
		}

		// upload a file attachment?
		if (($sFileName = Filter::ToFileName('userfile')) !== null)
		{
			$fileHelper = new FileHelper();
			$fileHelper->iType = DCL_ENTITY_WORKORDER;
			$fileHelper->iKey1 = $workOrderModel->jcn;
			$fileHelper->iKey2 = $workOrderModel->seq;
			$fileHelper->sFileName = Filter::ToActualFileName('userfile');
			$fileHelper->sTempFileName = $sFileName;
			$fileHelper->sRoot = $dcl_info['DCL_FILE_PATH'] . '/attachments';
			$fileHelper->Upload();
		}

		// copied from ticket?
		if (IsSet($_REQUEST['ticketid']))
		{
			if (($iTicketID = @Filter::ToInt($_REQUEST['ticketid'])) !== null && $iTicketID > 0)
			{
				$ticketResolutionsModel = new TicketResolutionsModel();
				$ticketResolutionsModel->ticketid = $iTicketID;
				$ticketResolutionsModel->loggedby = DCLID;
				$ticketResolutionsModel->loggedon = date($dcl_info['DCL_TIMESTAMP_FORMAT']);
				$ticketResolutionsModel->startedon = date($dcl_info['DCL_TIMESTAMP_FORMAT']);
				$ticketResolutionsModel->is_public = $workOrderModel->is_public;
				$ticketResolutionsModel->resolution = sprintf('Copied to dcl://workorders/%d-%d', $workOrderModel->jcn, $workOrderModel->seq);
	
				$ticketsModel = new TicketsModel();
				$ticketsModel->Load($ticketResolutionsModel->ticketid);
				$ticketsModel->lastactionon = date($dcl_info['DCL_TIMESTAMP_FORMAT']);
				$ticketResolutionsModel->status = $ticketsModel->status;
	
				$ticketResolutionsModel->BeginTransaction();
				$ticketResolutionsModel->Add();
				$ticketsModel->Edit();
				$ticketResolutionsModel->EndTransaction();
			}
		}

		// Reload work order to update fields now that we have it all stored
		$workOrderModel->Load($workOrderModel->jcn, $workOrderModel->seq);

		PubSub::Publish('WorkOrder.Inserted', $workOrderModel);

		$watches = new boWatches();
		$watches->sendNotification($workOrderModel, '4,1');
		
		if (EvaluateReturnTo())
			return;

		SetRedirectMessage('Success', 'New work order created.');
		RedirectToAction('WorkOrder', 'Detail', 'jcn=' . $workOrderModel->jcn . '&seq=' . $workOrderModel->seq);
	}

	public function Edit()
	{
		$id = Filter::RequireInt($_REQUEST['jcn']);
		$seq = Filter::RequireInt($_REQUEST['seq']);
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_MODIFY, $id, $seq);
		
		$model = new WorkOrderModel();
		if ($model->Load($id, $seq) == -1)
			throw new InvalidEntityException();

		$presenter = new WorkOrderPresenter();
		$presenter->Edit($model);
	}

	public function Update()
	{
		RequirePost();
		global $dcl_info;
		
		$id = Filter::RequireInt($_REQUEST['jcn']);
		$seq = Filter::RequireInt($_REQUEST['seq']);

		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_MODIFY, $id, $seq);

		$workOrderModel = new WorkOrderModel();
		if ($workOrderModel->Load($id, $seq) == -1)
			return;

		$updateableFields = array('product', 'module_id', 'wo_type_id', 'deadlineon', 'eststarton', 'estendon', 'esthours', 'priority', 'severity',
						'contact_id', 'summary', 'notes', 'description', 'responsible', 'reported_version_id', 'is_public', 'entity_source_id', 'targeted_version_id', 'fixed_version_id');

		$isModified = false;
		foreach ($updateableFields as $field)
		{
			if ($field == 'is_public')
			{
				$requestValue = (isset($_REQUEST['is_public']) && $_REQUEST['is_public'] == 'Y' ? 'Y' : 'N');
			}
			else
			{
				if (!IsSet($_REQUEST[$field]))
					continue;

				$requestValue = $_REQUEST[$field];
			}

			$fieldType = $GLOBALS['phpgw_baseline'][$workOrderModel->TableName]['fd'][$field]['type'];
			if ($fieldType == 'text' || $fieldType == 'varchar' || $fieldType == 'char')
				$requestValue = $workOrderModel->GPCStripSlashes($requestValue);
			else if ($fieldType == 'int')
				$requestValue = Filter::ToInt($requestValue);
			else if ($fieldType == 'float')
				$requestValue = Filter::ToDecimal($requestValue);

			if ($workOrderModel->$field != $requestValue)
			{
				$isModified = true;
				$workOrderModel->$field = $requestValue;
			}
		}

		PubSub::Publish('WorkOrder.Updating', $workOrderModel);
		
		if ($isModified)
			$workOrderModel->Edit();

		$workOrderOrgModel = new WorkOrderOrganizationModel();
		if (IsSet($_REQUEST['secaccounts']))
		{
			$organizationCollection = @Filter::ToIntArray($_REQUEST['secaccounts']);
			if ($organizationCollection === null)
				$organizationCollection = array();
				
			$workOrderOrgModel->DeleteByWorkOrder($workOrderModel->jcn, $workOrderModel->seq, join(',', $organizationCollection));
			
			// Add the new ones
			if (count($organizationCollection) > 0)
			{
				$workOrderOrgModel->wo_id = $workOrderModel->jcn;
				$workOrderOrgModel->seq = $workOrderModel->seq;

				for ($i = 0; $i < count($organizationCollection); $i++)
				{
					if ($organizationCollection[$i] > 0)
					{
						$workOrderOrgModel->account_id = $organizationCollection[$i];
						$workOrderOrgModel->Add();
						if ($dcl_info['DCL_WO_SECONDARY_ACCOUNTS_ENABLED'] != 'Y')
							break;
					}
				}
			}
		}
		else
		{
			$workOrderOrgModel->DeleteByWorkOrder($workOrderModel->jcn, $workOrderModel->seq);
		}

		if (isset($_REQUEST['tags']))
		{
			$oTag = new EntityTagModel();
			$oTag->serialize(DCL_ENTITY_WORKORDER, $workOrderModel->jcn, $workOrderModel->seq, $_REQUEST['tags']);
		}
		
		if (isset($_REQUEST['hotlist']))
		{
			$oHotlist = new EntityHotlistModel();
			$oHotlist->serialize(DCL_ENTITY_WORKORDER, $workOrderModel->jcn, $workOrderModel->seq, $_REQUEST['hotlist']);
		}
		
		PubSub::Publish('WorkOrder.Updated', $workOrderModel);

		$watches = new boWatches();
		$watches->sendNotification($workOrderModel, '4');

		if (EvaluateReturnTo())
			return;

		SetRedirectMessage('Success', 'Work order updated.');
		RedirectToAction('WorkOrder', 'Detail', 'jcn=' . $workOrderModel->jcn . '&seq=' . $workOrderModel->seq);
	}
	
	public function Delete()
	{
		$id = Filter::RequireInt($_REQUEST['jcn']);
		$seq = Filter::RequireInt($_REQUEST['seq']);
		
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_DELETE, $id, $seq);
		
		$model = new WorkOrderModel();
		if ($model->Load($id, $seq) == -1)
			throw new InvalidEntityException();

		$presenter = new WorkOrderPresenter();
		$presenter->Delete($model);
	}
	
	public function Destroy()
	{
		RequirePost();
		
		$id = Filter::RequireInt($_REQUEST['jcn']);
		$seq = Filter::RequireInt($_REQUEST['seq']);
		
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_DELETE, $id, $seq);

		$model = new WorkOrderModel();
		if ($model-Load($id, $seq) == -1)
			throw new InvalidEntityException();
		
		PubSub::Publish('WorkOrder.Deleting', $model);
		
		$model->Delete();

		PubSub::Publish('WorkOrder.Deleted', $model);

		SetRedirectMessage('Success', sprintf(STR_BO_WORKORDERDELETED, $id, $seq));
		RedirectToAction('htmlMyDCL', 'showMy');
	}
	
	public function Detail()
	{
		$id = Filter::RequireInt($_REQUEST['jcn']);
		$seq = Filter::RequireInt($_REQUEST['seq']);
		
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_VIEW, $id, $seq);

		$model = new WorkOrderModel();
		if ($model->Load($id, $seq) == -1)
			throw new InvalidEntityException();
		
		$presenter = new WorkOrderPresenter();
		$presenter->Detail($model);
	}
	
	public function BatchDetail()
	{
		$presenter = new WorkOrderPresenter();
		$presenter->BatchDetail($_REQUEST['selected']);
	}
	
	public function GraphCriteria()
	{
		$presenter = new WorkOrderPresenter();
		$presenter->GraphCriteria();
	}
	
	public function Graph()
	{
		LoadStringResource('bo');
		
		$days = Filter::RequireInt($_REQUEST['days']);
		$dateFrom = Filter::RequireDate($_REQUEST['dateFrom']);
		$product = @Filter::ToInt($_REQUEST['product']);
		
		$presenter = new WorkOrderPresenter();
		$presenter->Graph($days, $dateFrom, $product);
	}
	
	public function Reassign()
	{
		$id = Filter::RequireInt($_REQUEST['jcn']);
		$seq = Filter::RequireInt($_REQUEST['seq']);
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ASSIGN, $id, $seq);
		
		$returnTo = isset($_REQUEST['return_to']) ? $_REQUEST['return_to'] : null;
		$productId = @Filter::ToInt($_REQUEST['product']);
		
		$model = new WorkOrderModel();
		if ($model->Load($id, $seq) == -1)
			throw new InvalidEntityException();
		
		$presenter = new WorkOrderPresenter();
		$presenter->Reassign($model, $returnTo, $productId);
		$presenter->Detail($model);
	}
	
	public function ReassignWorkOrder()
	{
		global $dcl_info;

		RequirePost();
		
		$id = Filter::RequireInt($_REQUEST['jcn']);
		$seq = Filter::RequireInt($_REQUEST['seq']);
		$responsibleId = Filter::RequireInt($_REQUEST['responsible']);
		$estHours = Filter::RequireDecimal($_REQUEST['esthours']);
		$etcHours = Filter::RequireDecimal($_REQUEST['etchours']);
		$severityId = Filter::RequireInt($_REQUEST['severity']);
		$priorityId = Filter::RequireInt($_REQUEST['priority']);
		$deadlineon = Filter::RequireDate($_REQUEST['deadlineon']);
		$eststarton = Filter::RequireDate($_REQUEST['eststarton']);
		$estendon = Filter::RequireDate($_REQUEST['estendon']);

		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ASSIGN, $id, $seq);

		$workOrder = new WorkOrderModel();
		if ($workOrder->Load($id, $seq) == -1)
			throw new InvalidEntityException();
		
		if ($workOrder->responsible != $responsibleId ||
				$workOrder->deadlineon != $deadlineon ||
				$workOrder->eststarton != $eststarton ||
				$workOrder->estendon != $estendon ||
				$workOrder->esthours != $estHours ||
				$workOrder->etchours != $etcHours ||
				$workOrder->priority != $priorityId ||
				$workOrder->status == $dcl_info['DCL_DEF_STATUS_UNASSIGN_WO'] ||
				$workOrder->severity != $severityId)
		{
			$workOrder->responsible = $responsibleId;
			$workOrder->deadlineon = $deadlineon;
			$workOrder->eststarton = $eststarton;
			$workOrder->estendon = $estendon;
			$workOrder->esthours = $estHours;

			$statusModel = new StatusModel();
			if ($statusModel->GetStatusType($workOrder->status) != 2)
			{
				$workOrder->etchours = $etcHours;
				if ($workOrder->status == $dcl_info['DCL_DEF_STATUS_UNASSIGN_WO'])
				{
					$workOrder->status = $dcl_info['DCL_DEF_STATUS_ASSIGN_WO'];
					$workOrder->statuson = $workOrder->GetDateSQL();
				}
			}
			else
				$workOrder->etchours = 0.0;

			$workOrder->priority = $priorityId;
			$workOrder->severity = $severityId;
			$workOrder->Edit();

			$objWtch = new boWatches();
			$objWtch->sendNotification($workOrder, '4');
		}

		SetRedirectMessage('Success', 'Work order reassigned.');
		RedirectToAction('WorkOrder', 'Detail', 'jcn=' . $workOrder->jcn . '&seq=' . $workOrder->seq);
	}
	
	public function BatchReassign()
	{
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ASSIGN);
		
		$selected = @$_REQUEST['selected'];
		$returnTo = @$_REQUEST['return_to'];
		$projectId = @Filter::ToInt($_REQUEST['project']);

		$presenter = new WorkOrderPresenter();
		$presenter->BatchReassign($selected, $returnTo, $projectId);
		$presenter->ListSelected($selected);
	}
	
	public function BatchReassignWorkOrders()
	{
		RequirePost();
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ASSIGN);

		if (IsSet($_REQUEST['selected']) && is_array($_REQUEST['selected']) && count($_REQUEST['selected']) > 0)
		{
			$watches = new boWatches();
			$workOrderModel = new WorkOrderModel();

			$responsibleId = Filter::RequireInt($_REQUEST['responsible']);
			
			if (($priorityId = @Filter::ToInt($_REQUEST['priority'])) === null)
				$priorityId = 0;

			if (($severityId = @Filter::ToInt($_REQUEST['severity'])) === null)
				$severityId = 0;
			
			foreach ($_REQUEST['selected'] as $val)
			{
				list($jcn, $seq) = explode('.', $val);
				if (($jcn = Filter::ToInt($jcn)) === null || ($seq = Filter::ToInt($seq)) === null)
					throw new InvalidDataException();

				if ($workOrderModel->Load($jcn, $seq) == -1)
					continue;
					
				if ($workOrderModel->responsible != $responsibleId || ($priorityId > 0 && $workOrderModel->priority != $priorityId) || ($severityId > 0 && $workOrderModel->severity != $severityId))
				{
					$workOrderModel->responsible = $responsibleId;
					
					if ($priorityId > 0)
						$workOrderModel->priority = $priorityId;
						
					if ($severityId > 0)
						$workOrderModel->severity = $severityId;
						
					$workOrderModel->Edit();

					$watches->sendNotification($workOrderModel, '4,1');
				}
			}
		}

		if (EvaluateReturnTo())
			return;

		// FIXME: Redirect needed
		$sqlQueryHelper = new WorkOrderSqlQueryHelper();
		$sqlQueryHelper->SetFromURL();
		
		$presenter = new WorkOrderPresenter();
		$presenter->DisplayView($sqlQueryHelper);
	}
	
	public function Attachment()
	{
		$id = Filter::RequireInt($_REQUEST['jcn']);
		$seq = Filter::RequireInt($_REQUEST['seq']);
		
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ATTACHFILE, $id, $seq);
		
		$workOrderModel = new WorkOrderModel();
		if ($workOrderModel->Load($id, $seq) == -1)
			throw new InvalidEntityException ();
		
		$presenter = new WorkOrderPresenter();
		$presenter->Attachment($workOrderModel);
		$presenter->Detail($workOrderModel);
	}
	
	public function UploadAttachment()
	{
		global $dcl_info;
		
		RequirePost();
		
		$id = Filter::RequireInt($_REQUEST['jcn']);
		$seq = Filter::RequireInt($_REQUEST['seq']);
		
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ATTACHFILE, $id, $seq);

		$workOrderModel = new WorkOrderModel();
		if ($workOrderModel->Load($id, $seq) == -1)
			throw new InvalidEntityException();

		$fileName = Filter::RequireFileName('userfile');

		$fileHelper = new FileHelper();
		$fileHelper->iType = DCL_ENTITY_WORKORDER;
		$fileHelper->iKey1 = $id;
		$fileHelper->iKey2 = $seq;
		$fileHelper->sFileName = Filter::ToActualFileName('userfile');
		$fileHelper->sTempFileName = $fileName;
		$fileHelper->sRoot = $dcl_info['DCL_FILE_PATH'] . '/attachments';
		$fileHelper->Upload();
		
		SetRedirectMessage('Success', 'Attachment uploaded.');
		RedirectToAction('WorkOrder', 'Detail', 'jcn=' . $workOrderModel->jcn . '&seq=' . $workOrderModel->seq);
	}
	
	public function DownloadAttachment()
	{
		global $dcl_info;

		$id = Filter::RequireInt($_REQUEST['jcn']);
		$seq = Filter::RequireInt($_REQUEST['seq']);
		$fileName = $_REQUEST['filename'];
		
		if (!Filter::IsValidFileName($fileName))
			throw new InvalidArgumentException();
		
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_VIEW, $id, $seq);

		$fileHelper = new FileHelper();
		$fileHelper->iType = DCL_ENTITY_WORKORDER;
		$fileHelper->iKey1 = $id;
		$fileHelper->iKey2 = $seq;
		$fileHelper->sFileName = $fileName;
		$fileHelper->sRoot = $dcl_info['DCL_FILE_PATH'] . '/attachments';
		$fileHelper->Download();
	}
	
	public function DeleteAttachment()
	{
		$id = Filter::RequireInt($_REQUEST['jcn']);
		$seq = Filter::RequireInt($_REQUEST['seq']);

		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_REMOVEFILE, $id, $seq);
		
		$fileName = $_REQUEST['filename'];
		if (!@Filter::IsValidFileName($fileName))
			throw new InvalidDataException();
		
		$workOrderModel = new WorkOrderModel();
		if ($workOrderModel->Load($id, $seq) == -1)
			throw new InvalidEntityException();
		
		$presenter = new WorkOrderPresenter();
		$presenter->DeleteAttachment($workOrderModel, $fileName);
		$presenter->Detail($workOrderModel);
	}
	
	public function DestroyAttachment()
	{
		global $dcl_info;
		
		RequirePost();
		
		$id = Filter::RequireInt($_REQUEST['jcn']);
		$seq = Filter::RequireInt($_REQUEST['seq']);
		
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_REMOVEFILE, $id, $seq);
		
		$fileName = $_REQUEST['filename'];
		if (!@Filter::IsValidFileName($fileName))
			throw new InvalidDataException();
		
		$workOrderModel = new WorkOrderModel();
		if ($workOrderModel->Load($id, $seq) == -1)
			throw new InvalidEntityException();
		
		$attachmentPath = $dcl_info['DCL_FILE_PATH'] . '/attachments/wo/' . substr($id, -1) . '/' . $id . '/' . $seq . '/' . $fileName;
		if (is_file($attachmentPath) && is_readable($attachmentPath))
			unlink($attachmentPath);
		
		SetRedirectMessage('Success', 'Attachment deleted.');
		RedirectToAction('WorkOrder', 'Detail', 'jcn=' . $workOrderModel->jcn . '&seq=' . $workOrderModel->seq);
	}
	
	public function Import()
	{
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_IMPORT);
		
		$presenter = new WorkOrderPresenter();
		$presenter->Import();
	}
	
	public function ImportFile()
	{
		global $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_IMPORT))
			throw new PermissionDeniedException();

		if (($fileName = Filter::ToFileName('userfile')) === null)
			throw new PermissionDeniedException();
		
		$import = new WorkOrderCsvImport();
		$newIds = $import->Import($fileName);

		if (count($newIds) > 0)
		{
			// Display imported work orders
			$sqlQueryHelper = new WorkOrderSqlQueryHelper();
			$sqlQueryHelper->style = 'report';
			$sqlQueryHelper->title = 'Work Order CSV Upload Results';
			$sqlQueryHelper->AddDef('filter', 'jcn', $newIds);
			$sqlQueryHelper->AddDef('order', 'jcn');
	
			$sqlQueryHelper->AddDef('columns', '',
				array('jcn', 'seq', 'responsible.short', 'products.name', 'statuses.name', 'eststarton', 'deadlineon',
					'etchours', 'totalhours', 'summary'));
	
			$sqlQueryHelper->AddDef('columnhdrs', '',
				array(STR_WO_JCN, STR_WO_SEQ, STR_WO_RESPONSIBLE, STR_WO_PRODUCT,
					STR_WO_STATUS, STR_WO_ESTSTART, STR_WO_DEADLINE, STR_WO_ETCHOURS, STR_WO_ACTHOURS, STR_WO_SUMMARY));
	
			$presenter = new WorkOrderPresenter();
			$presenter->DisplayView($sqlQueryHelper);
		}
	}
	
	public function Criteria()
	{
		$presenter = new WorkOrderPresenter();
		$presenter->Criteria();
	}
	
	public function LoadCriteria()
	{
		$id = Filter::RequireInt($_REQUEST['id']);
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH);

		$savedSearchesModel = new SavedSearchesModel();
		if ($savedSearchesModel->Load($id) == -1)
			throw new InvalidEntityException();

		$sqlQueryHelper = new WorkOrderSqlQueryHelper();
		$sqlQueryHelper->SetFromURLString($savedSearchesModel->viewurl);
		
		$presenter = new WorkOrderPresenter();
		$presenter->Criteria($sqlQueryHelper);
	}

	public function RefineCriteria()
	{
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH);

		$sqlQueryHelper = new WorkOrderSqlQueryHelper();
		$sqlQueryHelper->SetFromURL();
		
		$presenter = new WorkOrderPresenter();
		$presenter->Criteria($sqlQueryHelper);
	}
	
	public function ChangeLog()
	{
		RequirePermission(DCL_ENTITY_CHANGELOG, DCL_PERM_VIEW);
		
		$id = Filter::RequireInt($_REQUEST['jcn']);
		$seq = Filter::RequireInt($_REQUEST['seq']);

		$presenter = new WorkOrderPresenter();
		$presenter->ChangeLog($id, $seq);
	}
	
	public function Search()
	{
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH);

		$workOrderCriteriaModel = new WorkOrderCriteriaModel();
		$workOrderCriteriaModel->Personnel = isset($_REQUEST['personnel']) && is_array($_REQUEST['personnel']) ? $_REQUEST['personnel'] : array();
		$workOrderCriteriaModel->Status = @$_REQUEST['status'];
		$workOrderCriteriaModel->IsPublic = @$_REQUEST['is_public'];
		$workOrderCriteriaModel->CreatedOn = @$_REQUEST['createdon'];
		$workOrderCriteriaModel->ClosedOn = @$_REQUEST['closedon'];
		$workOrderCriteriaModel->StatusOn = @$_REQUEST['statuson'];
		$workOrderCriteriaModel->LastActionOn = @$_REQUEST['lastactionon'];
		$workOrderCriteriaModel->DeadlineOn = @$_REQUEST['deadlineon'];
		$workOrderCriteriaModel->EstStartOn = @$_REQUEST['eststarton'];
		$workOrderCriteriaModel->EstEndOn = @$_REQUEST['estendon'];
		$workOrderCriteriaModel->StartOn = @$_REQUEST['starton'];
		$workOrderCriteriaModel->ModuleId = isset($_REQUEST['module_id']) && is_array($_REQUEST['module_id']) ? $_REQUEST['module_id'] : array();
		$workOrderCriteriaModel->SearchText = $_REQUEST['searchText'];
		$workOrderCriteriaModel->Tags = $_REQUEST['tags'] == '' ? array() : explode(',', $_REQUEST['tags']);
		$workOrderCriteriaModel->Hotlist = $_REQUEST['hotlist'] == '' ? array() :explode(',', $_REQUEST['hotlist']);
		$workOrderCriteriaModel->Columns = $_REQUEST['columns'];
		$workOrderCriteriaModel->Groups = $_REQUEST['groups'];
		$workOrderCriteriaModel->Order = $_REQUEST['order'];
		$workOrderCriteriaModel->ColumnHdrs = $_REQUEST['columnhdrs'];

		$workOrderCriteriaModel->Account = @Filter::ToIntArray($_REQUEST['account']);
		$workOrderCriteriaModel->EntitySourceId = @Filter::ToIntArray($_REQUEST['entity_source_id']);
		$workOrderCriteriaModel->Severity = @Filter::ToIntArray($_REQUEST['severity']);
		$workOrderCriteriaModel->Priority = @Filter::ToIntArray($_REQUEST['priority']);
		$workOrderCriteriaModel->DclStatusType = @Filter::ToIntArray($_REQUEST['dcl_status_type']);
		$workOrderCriteriaModel->Product = @Filter::ToIntArray($_REQUEST['product']);
		$workOrderCriteriaModel->Department = @Filter::ToIntArray($_REQUEST['department']);
		$workOrderCriteriaModel->Project = @Filter::ToIntArray($_REQUEST['project']);
		$workOrderCriteriaModel->WoTypeId = @Filter::ToIntArray($_REQUEST['wo_type_id']);

		$workOrderCriteriaModel->DateFrom = Filter::ToDate($_REQUEST['dateFrom']);
		$workOrderCriteriaModel->DateTo = Filter::ToDate($_REQUEST['dateTo']);
		
		$workOrderCriteriaModel->SearchCreateBy = isset($_REQUEST['createby']) && $_REQUEST['createby'] == '1';
		$workOrderCriteriaModel->SearchClosedBy = isset($_REQUEST['closedby']) && $_REQUEST['closedby'] == '1';
		$workOrderCriteriaModel->SearchResponsible = isset($_REQUEST['responsible']) && $_REQUEST['responsible'] == '1';
		
		$workOrderCriteriaModel->SearchNotes = isset($_REQUEST['notes']) && $_REQUEST['notes'] == '1';
		$workOrderCriteriaModel->SearchSummary = isset($_REQUEST['summary']) && $_REQUEST['summary'] == '1';
		$workOrderCriteriaModel->SearchDescription = isset($_REQUEST['description']) && $_REQUEST['description'] == '1';
		
		$workOrderCriteriaModel->SearchCreatedOn = isset($_REQUEST['createdon']) && $_REQUEST['createdon'] == '1';
		$workOrderCriteriaModel->SearchClosedOn = isset($_REQUEST['closedon']) && $_REQUEST['closedon'] == '1';
		$workOrderCriteriaModel->SearchStatusOn = isset($_REQUEST['statuson']) && $_REQUEST['statuson'] == '1';
		$workOrderCriteriaModel->SearchLastActionOn = isset($_REQUEST['lastactionon']) && $_REQUEST['lastactionon'] == '1';
		
		$workOrderCriteriaModel->SearchDeadlineOn = isset($_REQUEST['deadlineon']) && $_REQUEST['deadlineon'] == '1';
		$workOrderCriteriaModel->SearchEstStartOn = isset($_REQUEST['eststarton']) && $_REQUEST['eststarton'] == '1';
		$workOrderCriteriaModel->SearchEstEndOn = isset($_REQUEST['estendon']) && $_REQUEST['estendon'] == '1';
		$workOrderCriteriaModel->SearchStartOn = isset($_REQUEST['starton']) && $_REQUEST['starton'] == '1';
		
		$workOrderCriteriaModel->Title = GPCStripSlashes($_REQUEST['title']);

		$presenter = new WorkOrderPresenter();
		$presenter->Search($workOrderCriteriaModel);
	}
	
	public function SearchMy()
	{
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION);
		
		$workOrderCriteriaModel = new WorkOrderCriteriaModel();
		$currentUser = new PersonnelModel();
		$currentUser->Load(DCLID);
		
		$workOrderCriteriaModel->SearchResponsible = true;
		$workOrderCriteriaModel->Title = STR_WO_MYWO;
		
		$workOrderCriteriaModel->Department = Filter::ToIntArray($currentUser->department);
		$workOrderCriteriaModel->Personnel = array($currentUser->department . ',' . $currentUser->id);
		$workOrderCriteriaModel->DclStatusType = Filter::ToIntArray('1,3');
		
		if (IsPublicUser())
		{
			$workOrderCriteriaModel->Columns = 'jcn,seq,dcl_wo_type.type_name,products.name,statuses.name,priorities.name,severities.name,dcl_tag.tag_desc,summary';
			$columnHeaders = array(STR_WO_JCN, STR_WO_SEQ, STR_WO_TYPE, STR_WO_PRODUCT, STR_WO_STATUS, STR_WO_PRIORITY, STR_WO_SEVERITY, STR_CMMN_TAGS, STR_WO_SUMMARY);
		}
		else
		{
			$workOrderCriteriaModel->Columns = 'jcn,seq,dcl_wo_type.type_name,responsible.short,products.name,statuses.name,priorities.name,severities.name,dcl_hotlist.hotlist_tag,dcl_tag.tag_desc,summary';
			$columnHeaders = array(STR_WO_JCN, STR_WO_SEQ, STR_WO_TYPE, STR_WO_RESPONSIBLE, STR_WO_PRODUCT, STR_WO_STATUS, STR_WO_PRIORITY, STR_WO_SEVERITY, 'Hotlists', STR_CMMN_TAGS, STR_WO_SUMMARY);
		}
		
		$workOrderCriteriaModel->ColumnHdrs = implode(',', $columnHeaders);
		$workOrderCriteriaModel->Order = 'priorities.weight,severities.weight,jcn,seq';

		$presenter = new WorkOrderPresenter();
		$presenter->Search($workOrderCriteriaModel);
	}
	
	public function SearchMyCreated()
	{
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ADD);
		
		$workOrderCriteriaModel = new WorkOrderCriteriaModel();
		$currentUser = new PersonnelModel();
		$currentUser->Load(DCLID);
		
		$workOrderCriteriaModel->SearchCreateBy = true;
		$workOrderCriteriaModel->Title = STR_WO_MYSUBMISSIONS;
		
		$workOrderCriteriaModel->Department = Filter::ToIntArray($currentUser->department);
		$workOrderCriteriaModel->Personnel = array($currentUser->department . ',' . $currentUser->id);
		$workOrderCriteriaModel->DclStatusType = Filter::ToIntArray('1,3');
		
		if (IsPublicUser())
		{
			$workOrderCriteriaModel->Columns = 'jcn,seq,dcl_wo_type.type_name,products.name,statuses.name,priorities.name,severities.name,dcl_tag.tag_desc,summary';
			$columnHeaders = array(STR_WO_JCN, STR_WO_SEQ, STR_WO_TYPE, STR_WO_PRODUCT, STR_WO_STATUS, STR_WO_PRIORITY, STR_WO_SEVERITY, STR_CMMN_TAGS, STR_WO_SUMMARY);
		}
		else
		{
			$workOrderCriteriaModel->Columns = 'jcn,seq,dcl_wo_type.type_name,responsible.short,products.name,statuses.name,priorities.name,severities.name,dcl_hotlist.hotlist_tag,dcl_tag.tag_desc,summary';
			$columnHeaders = array(STR_WO_JCN, STR_WO_SEQ, STR_WO_TYPE, STR_WO_RESPONSIBLE, STR_WO_PRODUCT, STR_WO_STATUS, STR_WO_PRIORITY, STR_WO_SEVERITY, 'Hotlists', STR_CMMN_TAGS, STR_WO_SUMMARY);
		}
		
		$workOrderCriteriaModel->ColumnHdrs = implode(',', $columnHeaders);
		$workOrderCriteriaModel->Order = 'priorities.weight,severities.weight,jcn,seq';
		
		$presenter = new WorkOrderPresenter();
		$presenter->Search($workOrderCriteriaModel);
	}
	
	public function Browse()
	{
		RequireAnyPermission(DCL_ENTITY_WORKORDER, array(DCL_PERM_VIEW, DCL_PERM_VIEWSUBMITTED, DCL_PERM_VIEWACCOUNT));
		
		$workOrderCriteriaModel = new WorkOrderCriteriaModel();
		$currentUser = new PersonnelModel();
		$currentUser->Load(DCLID);
		
		$workOrderCriteriaModel->Title = STR_WO_BROWSEWO;
		
		$filterStatus = -1;
		$filterPriority = 0;
		$filterProduct = 0;
		
		if (IsSet($_REQUEST['filterStatus']))
			$filterStatus = Filter::RequireInt($_REQUEST['filterStatus']);
		
		if (IsSet($_REQUEST['filterPriority']))
			$filterPriority = Filter::RequireInt($_REQUEST['filterPriority']);

		if (IsSet($_REQUEST['filterProduct']))
			$filterProduct = Filter::RequireInt($_REQUEST['filterProduct']);

		if ($filterStatus != 0)
		{
			if ($filterStatus == -1)
			{
				$workOrderCriteriaModel->DclStatusType = Filter::ToIntArray('1,3');
			}
			else if ($filterStatus == -2)
			{
				$workOrderCriteriaModel->DclStatusType = Filter::ToIntArray('2');
			}
			else
			{
				$statusModel = new StatusModel();
				$statusModel->Load($filterStatus);
				$workOrderCriteriaModel->DclStatusType = Filter::ToIntArray($statusModel->dcl_status_type);
				$workOrderCriteriaModel->Status = array($statusModel->dcl_status_type . ',' . $filterStatus);
			}
		}

		if ($filterPriority != 0)
			$workOrderCriteriaModel->Priority = Filter::ToIntArray($filterPriority);
		
		if ($filterProduct != 0)
			$workOrderCriteriaModel->Product = Filter::ToIntArray($filterProduct);
		
		if (IsPublicUser())
		{
			$workOrderCriteriaModel->Columns = 'jcn,seq,dcl_wo_type.type_name,products.name,statuses.name,priorities.name,severities.name,dcl_tag.tag_desc,summary';
			$columnHeaders = array(STR_WO_JCN, STR_WO_SEQ, STR_WO_TYPE, STR_WO_PRODUCT, STR_WO_STATUS, STR_WO_PRIORITY, STR_WO_SEVERITY, STR_CMMN_TAGS, STR_WO_SUMMARY);
		}
		else
		{
			$workOrderCriteriaModel->Columns = 'jcn,seq,dcl_wo_type.type_name,responsible.short,products.name,statuses.name,priorities.name,severities.name,dcl_hotlist.hotlist_tag,dcl_tag.tag_desc,summary';
			$columnHeaders = array(STR_WO_JCN, STR_WO_SEQ, STR_WO_TYPE, STR_WO_RESPONSIBLE, STR_WO_PRODUCT, STR_WO_STATUS, STR_WO_PRIORITY, STR_WO_SEVERITY, 'Hotlists', STR_CMMN_TAGS, STR_WO_SUMMARY);
		}
		
		$workOrderCriteriaModel->ColumnHdrs = implode(',', $columnHeaders);
		$workOrderCriteriaModel->Order = 'priorities.weight,severities.weight,jcn,seq';

		$presenter = new WorkOrderPresenter();
		$presenter->Search($workOrderCriteriaModel);
	}
}
