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

LoadStringResource('bo');

class boTimecards
{
	function add()
	{
		global $g_oSec;
		
		commonHeader();
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION);

		$id = Filter::RequireInt($_REQUEST['jcn']);
		$seq = Filter::RequireInt($_REQUEST['seq']);
		
		$workOrderModel = new WorkOrderModel();
		if ($workOrderModel->LoadByIdSeq($id, $seq) == -1)
			throw new InvalidEntityException();
		
		$obj = new htmlTimeCardForm();
		$obj->Show($id, $seq);

		$workOrderPresenter = new WorkOrderPresenter();
		$workOrderPresenter->Detail($workOrderModel);
	}
	
	function closeIncompleteTasks($wo_id, $seq)
	{
		$oTasks = new WorkOrderTaskModel();
		if ($oTasks->CloseAllIncompleteTasksForWorkOrder($wo_id, $seq))
		{
			ShowInfo('Remaining incomplete tasks have been marked as closed by you.');
		}
	}

	function dbadd()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION))
			throw new PermissionDeniedException();

		AntiCsrf::ValidateToken();

		$timeCardModel = new TimeCardsModel();
		$workOrderModel = new WorkOrderModel();
		$statusModel = new StatusModel();

		$timeCardModel->InitFrom_POST();
		$timeCardModel->actionby = DCLID;
		if ($g_oSec->IsPublicUser())
			$timeCardModel->is_public = 'Y';
		else
			$timeCardModel->is_public = @Filter::ToYN($_REQUEST['is_public']);

		$timeCardModel->inputon = DCL_NOW;
		if ($workOrderModel->LoadByIdSeq($timeCardModel->jcn, $timeCardModel->seq) == -1)
		    return;
		
		$originalWorkOrder = clone $workOrderModel;
		    
		if (($targeted_version_id = @Filter::ToInt($_REQUEST['targeted_version_id'])) === null)
			$targeted_version_id = 0;
		
		if (($fixed_version_id = @Filter::ToInt($_REQUEST['fixed_version_id'])) === null)
			$fixed_version_id = 0;
		    
		$status = $workOrderModel->status;
		$oldStatusType = $statusModel->GetStatusType($status);
		$newStatusType = $statusModel->GetStatusType($timeCardModel->status);

		if ($oldStatusType != $newStatusType && $newStatusType == 2)
		{
			// Check if re-open is allowed
		}

		$timeCardModel->Add($targeted_version_id, $fixed_version_id);
		$notify = '4';
		if ($status != $timeCardModel->status)
		{
			$notify .= ',3';
			if ($newStatusType == 2)
			{
				$notify .= ',2';
				
				// also need to close all incomplete tasks and warn user if it happens
				$this->closeIncompleteTasks($timeCardModel->jcn, $timeCardModel->seq);
			}
			elseif ($newStatusType == 1 && $oldStatusType != 1)
				$notify .= ',1';
		}
		
		// See if we modified some work order items
		// * Tags
		if (isset($_REQUEST['tags']) && $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_MODIFY))
		{
			$oTag = new EntityTagModel();
			$oTag->serialize(DCL_ENTITY_WORKORDER, $workOrderModel->jcn, $workOrderModel->seq, $_REQUEST['tags']);
		}

		// * Hotlists
		if (isset($_REQUEST['hotlist']) && $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_MODIFY))
		{
			$oTag = new EntityHotlistModel();
			$oTag->serialize(DCL_ENTITY_WORKORDER, $workOrderModel->jcn, $workOrderModel->seq, $_REQUEST['hotlist']);
		}

		// * Organizations - only if multiple are allowed to improve workflow
		if ($g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_MODIFY) && $dcl_info['DCL_WO_SECONDARY_ACCOUNTS_ENABLED'] == 'Y')
		{
			$oWOA = new WorkOrderOrganizationModel();
			if (IsSet($_REQUEST['secaccounts']))
			{
				$aAccounts = @Filter::ToIntArray($_REQUEST['secaccounts']);
				if ($aAccounts === null)
					$aAccounts = array();
					
				$oWOA->DeleteByWorkOrder($workOrderModel->jcn, $workOrderModel->seq, join(',', $aAccounts));
				
				// Add the new ones
				if (count($aAccounts) > 0)
				{
					$oWOA->wo_id = $workOrderModel->jcn;
					$oWOA->seq = $workOrderModel->seq;
	
					for ($i = 0; $i < count($aAccounts); $i++)
					{
						if ($aAccounts[$i] > 0)
						{
							$oWOA->account_id = $aAccounts[$i];
							$oWOA->Add();
						}
					}
				}
			}
			else
				$oWOA->DeleteByWorkOrder($workOrderModel->jcn, $workOrderModel->seq);
		}

		// * Project
		if ($g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK))
		{
			if (($iProjID = @Filter::ToInt($_REQUEST['projectid'])) !== null && $iProjID > 0)
			{
				$projectMapModel = new ProjectMapModel();
				if ($projectMapModel->LoadByWO($workOrderModel->jcn, $workOrderModel->seq) == -1 || $projectMapModel->projectid != $iProjID)
				{
					$projectModel = new ProjectsModel();
					$aSource = array();
					$aSource['selected'] = array($workOrderModel->jcn . '.' . $workOrderModel->seq);
					$aSource['projectid'] = $iProjID;
					
					$projectModel->BatchMove($aSource);
				}
			}
		}
		
		// * File attachment
		if (($sFileName = Filter::ToFileName('userfile')) !== null && $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ATTACHFILE))
		{
			$o = new FileHelper();
			$o->iType = DCL_ENTITY_WORKORDER;
			$o->iKey1 = $workOrderModel->jcn;
			$o->iKey2 = $workOrderModel->seq;
			$o->sFileName = Filter::ToActualFileName('userfile');
			$o->sTempFileName = $sFileName;
			$o->sRoot = $dcl_info['DCL_FILE_PATH'] . '/attachments';
			$o->Upload();
		}

		$workOrderModel->LoadByIdSeq($timeCardModel->jcn, $timeCardModel->seq);
		
		PubSub::Publish('TimeCard.Inserted', $originalWorkOrder, $workOrderModel);

		$objWtch = new boWatches();
		$objWtch->sendNotification($workOrderModel, $notify);

		$workOrderPresenter = new WorkOrderPresenter();
		$workOrderPresenter->Detail($workOrderModel);
	}

	function batchadd()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION))
			throw new PermissionDeniedException();

		if (IsSet($_REQUEST['selected']) && is_array($_REQUEST['selected']) && count($_REQUEST['selected']) > 0)
		{
			$aSelected = array();
			foreach ($_REQUEST['selected'] as $key => $val)
			{
				list($jcn, $seq) = explode('.', $val);
				
				$jcn = Filter::ToInt($jcn);
				$seq = Filter::ToInt($seq);
				if ($jcn === null || $seq === null)
					continue;

				$aSelected[] = $val;
			}
		}

		if (count($aSelected) > 0)
		{
			$objTC = new htmlTimeCardForm();
			$objTC->Show(-1, -1, '', $aSelected);

			$obj = new htmlTimeCards();
			$_REQUEST['selected'] = $aSelected;
			$obj->ShowBatchWO();

			return;
		}
			
		if (EvaluateReturnTo())
			return;

		$objView = new WorkOrderSqlQueryHelper();
		$objView->SetFromURL();
		
		$presenter = new WorkOrderPresenter();
		$presenter->DisplayView($objView);
	}

	function dbbatchadd()
	{
   		global $g_oSec;
   		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION))
		{
			throw new PermissionDeniedException();
		}

		AntiCsrf::ValidateToken();

		$objTimecard = new TimeCardsModel();
		$objTimecard->InitFrom_POST();
		$objTimecard->actionby = DCLID;
		if ($g_oSec->IsPublicUser())
			$objTimecard->is_public = 'Y';
		else
			$objTimecard->is_public = @Filter::ToYN($_REQUEST['is_public']);
			
		if (($targeted_version_id = @Filter::ToInt($_REQUEST['targeted_version_id'])) === null)
			$targeted_version_id = 0;
		
		if (($fixed_version_id = @Filter::ToInt($_REQUEST['fixed_version_id'])) === null)
			$fixed_version_id = 0;

		if (($batchStatus = @Filter::ToInt($_REQUEST['status'])) === null)
			$batchStatus = 0;

		$batchEtc = @Filter::ToDecimal($_REQUEST['etchours']);
		
		$workOrderModel = new WorkOrderModel();
		$objWtch = new boWatches();
		if (IsSet($_REQUEST['selected']) && is_array($_REQUEST['selected']) && count($_REQUEST['selected']) > 0)
		{
    		$bProcessTags = (isset($_REQUEST['tags']) && trim($_REQUEST['tags']) != '' && $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_MODIFY));
        	$oTag = new EntityTagModel();
    		$bProcessHotlist = (isset($_REQUEST['hotlist']) && trim($_REQUEST['hotlist']) != '' && $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_MODIFY));
        	$oHotlist = new EntityHotlistModel();
        	foreach ($_REQUEST['selected'] as $key => $val)
			{
				list($objTimecard->jcn, $objTimecard->seq) = explode('.', $val);
				
				$objTimecard->jcn = Filter::ToInt($objTimecard->jcn);
				$objTimecard->seq = Filter::ToInt($objTimecard->seq);
				if ($objTimecard->jcn === null || $objTimecard->seq === null)
					continue;
					
				if ($workOrderModel->LoadByIdSeq($objTimecard->jcn, $objTimecard->seq) == -1)
				    continue;
				
				$originalWorkOrder = clone $workOrderModel;
				    
				$status = $workOrderModel->status;
				if ($batchStatus == 0)
					$objTimecard->status = $status;
				
				$objTimecard->Add($targeted_version_id, $fixed_version_id);
				
    			// * Tags
    			if ($bProcessTags)
        		{
        			$oTag->serialize(DCL_ENTITY_WORKORDER, $objTimecard->jcn, $objTimecard->seq, $_REQUEST['tags'], true);
        		}
				
    			// * Hotlists
    			if ($bProcessHotlist)
        		{
        			$oHotlist->serialize(DCL_ENTITY_WORKORDER, $objTimecard->jcn, $objTimecard->seq, $_REQUEST['hotlist'], true);
        		}
				
        		$notify = '4';
				if ($status != $objTimecard->status)
				{
					$notify .= ',3';
					$oStatus = new StatusModel();
					if ($oStatus->GetStatusType($objTimecard->status) == 2)
					{
						$notify .= ',2';
				
						// also need to close all incomplete tasks and warn user if it happens
						$this->closeIncompleteTasks($objTimecard->jcn, $objTimecard->seq);
					}
					else if ($oStatus->GetStatusType($objTimecard->status) == 1)
						$notify .= ',1';
				}

				// Reload before sending since time card modifies the work order
				if ($workOrderModel->LoadByIdSeq($objTimecard->jcn, $objTimecard->seq) != -1)
				{
					PubSub::Publish('TimeCard.Inserted', $originalWorkOrder, $workOrderModel);
					$objWtch->sendNotification($workOrderModel, $notify, false);
				}
			}
		}

		if (EvaluateReturnTo())
			return;

		$sqlQueryHelper = new WorkOrderSqlQueryHelper();
		$sqlQueryHelper->SetFromURL();
		
		$presenter = new WorkOrderPresenter();
		$presenter->DisplayView($sqlQueryHelper);
	}

	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		RequirePermission(DCL_ENTITY_TIMECARD, DCL_PERM_MODIFY);

		$id = Filter::RequireInt($_REQUEST['id']);
		
		$objTC = new TimeCardsModel();
		if ($objTC->Load($id) == -1)
			throw new InvalidEntityException();
		
		$workOrderModel = new WorkOrderModel();
		if ($workOrderModel->LoadByIdSeq($objTC->jcn, $objTC->seq) == -1)
			throw new InvalidEntityException();

		$workOrderPresenter = new WorkOrderPresenter();
		$workOrderPresenter->Detail($workOrderModel, $objTC->id);
	}

	function dbmodify()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_TIMECARD, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		AntiCsrf::ValidateToken();

		$objTC = new TimeCardsModel();
		$objOldTC = new TimeCardsModel();
		$objTC->InitFrom_POST();

		if ($g_oSec->IsPublicUser())
			$objTC->is_public = 'Y';
		else
			$objTC->is_public = @Filter::ToYN($_REQUEST['is_public']);

		if ($objOldTC->Load($objTC->id) == -1)
			return;

		if ($g_oSec->IsPublicUser() && $objOldTC->is_public == 'N')
			throw new PermissionDeniedException();

		// If the hours change, we'll need to adjust the workorder
		$hoursDiff = $objTC->hours - $objOldTC->hours;

		$objWO = new WorkOrderModel();
		if ($objWO->LoadByIdSeq($objTC->jcn, $objTC->seq) == -1)
			return;
		
		$woChanged = false;
		$notify = '4';

		// See if any time cards were issued after this one.  If not, assume
		// that this time card was the last one entered and affected the work order
		// status when input.  In other words, adjust as needed.
		if ($objTC->status != $objOldTC->status)
		{
			$notify .= ',3';

			$objQueryTC = new TimeCardsModel();
			if ($objQueryTC->IsLastTimeCard($objTC->id, $objTC->jcn, $objTC->seq))
			{
				// We're the last one!  This does (of course) assume that time cards
				// are entered sequentially in correct chronological order.
				if ($objTC->status != $objWO->status)
				{
					$objWO->status = $objTC->status;
					$objWO->statuson = date($dcl_info['DCL_TIMESTAMP_FORMAT']);
					$woChanged = true;
					
					$oStatus = new StatusModel();
					if ($oStatus->GetStatusType($objTC->status) == 2)
					{
						$objWO->closedby = $objTC->actionby;
						$objWO->closedon = $objTC->actionon;
						$objWO->etchours = 0.0;

						$notify .= ',2';
				
						// also need to close all incomplete tasks and warn user if it happens
						$this->closeIncompleteTasks($objTC->jcn, $objTC->seq);
					}
				}
			}
			else
			{
				// Don't allow status change if more are left.  Last time card controls status of WO
				$objTC->status = $objOldTC->status;
			}
		}

		if ($hoursDiff != 0)
		{
			$objWO->totalhours += $hoursDiff;
			$woChanged = true;
		}

		if ($woChanged)
			$objTC->BeginTransaction();

		$objTC->Edit();

		if ($woChanged)
		{
			$objWO->edit();
			$objTC->EndTransaction();
		}

		$objWtch = new boWatches();
		$objWtch->sendNotification($objWO, $notify);

		$workOrderPresenter = new WorkOrderPresenter();
		$workOrderPresenter->Detail($objWO);
	}

	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		RequirePermission(DCL_ENTITY_TIMECARD, DCL_PERM_DELETE);
		
		$id = Filter::RequireInt($_REQUEST['id']);
		
		$objTC = new TimeCardsModel();
		if ($objTC->Load($id) == -1)
			throw new InvalidEntityException();
		
		$workOrderModel = new WorkOrderModel();
		if ($workOrderModel->LoadByIdSeq($objTC->jcn, $objTC->seq) == -1)
			throw new InvalidEntityException();

		$workOrderPresenter = new WorkOrderPresenter();
		$workOrderPresenter->Detail($workOrderModel, $objTC->id, true);
	}

	function dbdelete()
	{
		global $dcl_info, $g_oSec;

		commonHeader();

		if (($iID = @Filter::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$objTC = new TimeCardsModel();
		if ($objTC->Load($iID) == -1)
			return;

		if (!$g_oSec->HasPerm(DCL_ENTITY_TIMECARD, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		$objWO = new WorkOrderModel();
		if ($objWO->LoadByIdSeq($objTC->jcn, $objTC->seq) == -1)
			return;

		// Get the next time card issued after this one.  If not, assume
		// that this time card was the last one entered and affected the work order
		// status when input.
		$objQueryTC = new TimeCardsModel();
		if (($iNextID = $objQueryTC->GetNextTimeCardID($objTC->id, $objTC->jcn, $objTC->seq)) === null)
		{
			// OK, we're the last time card input, therefore we control status.
			// See if any time cards were input before this one.  If so,
			// try to revert to the previous time card status.  Otherwise, open it.
			if (($iPrevID = $objQueryTC->GetPrevTimeCardID($objTC->id, $objTC->jcn, $objTC->seq)) !== null)
			{
				$objQueryTC->Load($iPrevID);
				if ($objQueryTC->status != $objWO->status)
				{
					$objWO->statuson = date($dcl_info['DCL_TIMESTAMP_FORMAT']);
					$oStatus = new StatusModel();
					if ($oStatus->GetStatusType($objQueryTC->status) == 2 && $oStatus->GetStatusType($objWO->status) != 2)
					{
						$objWO->closedby = $objQueryTC->actionby;
						$objWO->closedon = $objQueryTC->actionon;
						$objWO->etchours = 0.0;
				
						// also need to close all incomplete tasks and warn user if it happens
						$this->closeIncompleteTasks($objTC->jcn, $objTC->seq);
					}
					else if ($oStatus->GetStatusType($objWO->status) == 2)
					{
						$objWO->closedby = 0;
						$objWO->closedon = '';
					}

					$objWO->status = $objQueryTC->status;
				}
			}
			else
			{
				// No other time cards, so default the status
				$objWO->status = $dcl_info['DCL_DEF_STATUS_ASSIGN_WO']; // Open it
				$objWO->statuson = date($dcl_info['DCL_TIMESTAMP_FORMAT']);
				$objWO->closedby = 0;
				$objWO->closedon = '';
				$objWO->lastactionon = '';
				$objWO->etchours = $objWO->esthours;
			}
		}
		else
		{
			$objQueryTC->Load($iNextID);
			$objWO->starton = $objQueryTC->actionon;
		}

		$objWO->totalhours -= $objTC->hours;

		$objTC->BeginTransaction();
		
		try
		{
			$objTC->Delete();
			$objWO->Edit();
			$objTC->EndTransaction();
		}
		catch (Exception $ex)
		{
			$objTC->RollbackTransaction();
		}

		ShowInfo(sprintf(STR_BO_TIMECARDDELETED, $objTC->id, $objWO->jcn, $objWO->seq));

		$workOrderPresenter = new WorkOrderPresenter();
		$workOrderPresenter->Detail($objWO);
	}
}
