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

class WorkOrderFormViewData
{
	const Create = 1;
	const Edit = 2;
	const CreateSequence = 3;
	
	public $Mode;
	
	public $Title;
	public $IsEdit;
	public $Action;
	public $DateFormat;
	public $FormAction;
	
	public $WorkOrderId;
	public $Sequence;
	public $TicketId;
	
	public $OrganizationId;
	public $OrganizationIdCollection;
	public $OrganizationNameCollection;
	
	public $ContactId;
	public $ContactName;
	
	public $ProjectId;
	public $ProjectName;
	public $ProjectLabel;
	
	public $MultiOrganizationEnabled;
	public $AutoDateEnabled;
	public $CanAddTask;
	public $CanAction;
	public $CanAssignWorkOrder;
	
	public $CanAttachFile;
	public $MaxUploadFileSize;
	public $IsPublicUser;
	public $NotifyDefault;
	
	public $DeadlineDate;
	public $EstStartDate;
	public $EstEndDate;
	public $EstHours;
	
	public $HideProject;
	public $ReportedVersion;
	public $Summary;
	public $Notes;
	public $Description;
	
	public $IsPublic;
	public $ResponsibleId;
	public $ResponsibleName;
	
	public $ProductId;
	public $ModuleId;
	public $ReportedVersionId;
	public $TargetedVersionId;
	public $FixedVersionId;
	
	public $TypeId;
	public $SourceId;
	public $PriorityId;
	public $SeverityId;
	
	public $AttributeSetId;
	public $Tags;
	public $Hotlists;
	
	public function __construct()
	{
		global $dcl_info, $g_oSec, $dcl_preferences;
		
		$this->DateFormat = GetJSDateFormat();
		$this->FormAction = menuLink();
		
		$this->MultiOrganizationEnabled = $dcl_info['DCL_WO_SECONDARY_ACCOUNTS_ENABLED'] == 'Y';
		$this->AutoDateEnabled = $dcl_info['DCL_AUTO_DATE'] == 'Y';
		$this->CanAddTask = $g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK);
		$this->CanAction = $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION);
		$this->CanAssignWorkOrder = $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ASSIGN);
		
		$this->MaxUploadFileSize = $dcl_info['DCL_MAX_UPLOAD_FILE_SIZE'];
		$this->CanAttachFile = $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ATTACHFILE) && $this->MaxUploadFileSize > 0;
		$this->IsPublicUser = $g_oSec->IsPublicUser();
		$this->NotifyDefault = isset($dcl_preferences['DCL_PREF_NOTIFY_DEFAULT']) ? $dcl_preferences['DCL_PREF_NOTIFY_DEFAULT'] : 'N';
		
		$this->HideProject = false;
	}

	/**
	 * Copy work order to a new instance of WorkOrderFormViewData
	 * @param WorkOrderModel $workOrder
	 * @return WorkOrderFormViewData 
	 */
	public static function CopyWorkOrder(WorkOrderModel $workOrder)
	{
		$model = new WorkOrderFormViewData();
		
		$model->WorkOrderId = $workOrder->jcn;
		$model->Sequence = $workOrder->seq;
		$model->ContactId = $workOrder->contact_id;
		
		$model->DeadlineDate = $workOrder->deadlineon;
		$model->EstStartDate = $workOrder->eststarton;
		$model->EstEndDate = $workOrder->estendon;
		$model->EstHours = $workOrder->esthours;
		
		$model->ReportedVersionId = $workOrder->reported_version_id;
		$model->Summary = $workOrder->summary;
		$model->Notes = $workOrder->notes;
		$model->Description = $workOrder->description;
		$model->IsPublic = $workOrder->is_public;
		
		$model->ProductId = $workOrder->product;
		$model->ModuleId = $workOrder->module_id;
		$model->TargetedVersionId = $workOrder->targeted_version_id;
		$model->FixedVersionId = $workOrder->fixed_version_id;
		
		$model->TypeId = $workOrder->wo_type_id;
		$model->SourceId = $workOrder->entity_source_id;
		$model->PriorityId = $workOrder->priority;
		$model->SeverityId = $workOrder->severity;
		
		$model->ResponsibleId = $workOrder->responsible;
		
		return $model;
	}
	
	/**
	 * Copy ticket to a new instance of WorkOrderFormViewData
	 * @param TicketsModel $ticket
	 * @return WorkOrderFormViewData 
	 */
	public static function CopyTicket(TicketsModel $ticket)
	{
		$model = new WorkOrderFormViewData();
		
		$model->OrganizationId = $ticket->account;
		$model->ContactId = $ticket->contact_id;
		$model->Summary = $ticket->summary;
		$model->Notes = 'Copied from ticket dcl://tickets/' . $ticket->ticketid;
		$model->Description = $ticket->issue;
		
		$model->IsPublic = $ticket->is_public;
		$model->ResponsibleId = $GLOBALS['DCLID'];
		$model->SourceId = $ticket->entity_source_id;
		$model->ProductId = $ticket->product;
		$model->ModuleId = $ticket->module_id;
		
		$tagModel = new EntityTagModel();
		$model->Tags = $tagModel->getTagsForEntity(DCL_ENTITY_TICKET, $ticket->ticketid);
		
		$hotlistModel = new EntityHotlistModel();
		$model->Hotlists = $hotlistModel->getTagsForEntity(DCL_ENTITY_TICKET, $ticket->ticketid);
		
		return $model;
	}
}
