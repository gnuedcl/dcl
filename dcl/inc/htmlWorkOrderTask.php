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

LoadStringResource('wo');

class htmlWorkOrderTask
{
	var $public;

	function __construct()
	{
		$this->public = array('add', 'modify', 'delete', 'submitAdd', 'submitModify', 'submitDelete');
	}
	
	function add()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION))
			throw new PermissionDeniedException();

		if (($wo_id = Filter::ToInt($_REQUEST['jcn'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (($seq = Filter::ToInt($_REQUEST['seq'])) === null)
		{
			throw new InvalidDataException();
		}

		$this->ShowEntryForm();
	}

	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		if (($wo_task_id = Filter::ToInt($_REQUEST['wo_task_id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION))
			throw new PermissionDeniedException();

		$obj = new WorkOrderTaskModel();
		if ($obj->Load($wo_task_id) == -1)
			return;
			
		$this->ShowEntryForm($obj);
	}

	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (($wo_task_id = Filter::ToInt($_REQUEST['wo_task_id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION))
			throw new PermissionDeniedException();

		$obj = new WorkOrderTaskModel();
		if ($obj->Load($wo_task_id) == -1)
			return;
			
		ShowDeleteYesNo('Work Order Task', 'htmlWorkOrderTask.submitDelete', $obj->wo_task_id, $obj->task_summary);
	}
	
	function reorder()
	{
		global $g_oSec;
		
		commonHeader();
		if (($wo_id = Filter::ToInt($_REQUEST['jcn'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (($seq = Filter::ToInt($_REQUEST['seq'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION))
			throw new PermissionDeniedException();

		$oSmarty = new SmartyHelper();
		$oTasks = new WorkOrderTaskModel();
		$oSmarty->assign('VAL_TASKS', $oTasks->GetTasksForWorkOrder($wo_id, $seq, false));
		$oSmarty->assign('VAL_JCN', $wo_id);
		$oSmarty->assign('VAL_SEQ', $seq);
		
		$oSmarty->Render('WorkOrderTaskReorder.tpl');
	}

	function submitAdd()
	{
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION);

		CleanArray($_REQUEST);
		$wo_id = Filter::RequireInt($_REQUEST['wo_id']);
		$seq = Filter::RequireInt($_REQUEST['seq']);
		
		$workOrderModel = new WorkOrderModel();
		if ($workOrderModel->LoadByIdSeq($wo_id, $seq) == -1)
			throw new InvalidEntityException();
		
		$obj = new boWorkOrderTask();
		$iOrder = $obj->oDB->ExecuteScalar("SELECT MAX(task_order) FROM dcl_wo_task WHERE wo_id = $wo_id AND seq = $seq");
		if ($iOrder === null)
			$iOrder = 0;

		$aSource = array();
		$aSource['wo_id'] = $wo_id;
		$aSource['seq'] = $seq;
		foreach ($_REQUEST['task_summary'] as $iKey => $sSummary)
		{
			$sSummary = mb_substr(trim($sSummary), 0, 255);
			if ($sSummary != '')
			{
				$aSource['task_order'] = ++$iOrder;
				$aSource['task_summary'] = $sSummary;
				$obj->add($aSource, $iKey);
			}
		}

		RedirectToAction('WorkOrder', 'Detail', "jcn=$wo_id&seq=$seq#tasks");
	}

	function submitModify()
	{
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION);
		
		$wo_task_id = Filter::RequireInt($_REQUEST['wo_task_id']);

		$obj = new boWorkOrderTask();
		CleanArray($_REQUEST);
		$obj->modify($_REQUEST);
		
		$workOrderModel = new WorkOrderModel();
		if ($workOrderModel->LoadByIdSeq($obj->oDB->wo_id, $obj->oDB->seq) == -1)
			throw new InvalidEntityException();

		RedirectToAction('WorkOrder', 'Detail', "jcn=$wo_id&seq=$seq#tasks");
	}
	
	function submitToggle()
	{
		global $g_oSec;
		
		commonHeader();
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION);
		
		$wo_task_id = Filter::RequireInt($_REQUEST['wo_task_id']);
		$task_complete = @Filter::ToYN($_REQUEST['task_complete']);

		$obj = new boWorkOrderTask();
		$aSource = array('wo_task_id' => $wo_task_id, 'task_complete' => $task_complete);
		$obj->toggleComplete($aSource);

		$workOrderModel = new WorkOrderModel();
		if ($workOrderModel->LoadByIdSeq($obj->oDB->wo_id, $obj->oDB->seq) == -1)
			throw new InvalidEntityException();
		
		$workOrderPresenter = new WorkOrderPresenter();
		$workOrderPresenter->Detail($workOrderModel);
	}

	function submitDelete()
	{
		global $g_oSec;
		
		commonHeader();
		RequirePermission(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION);

		CleanArray($_REQUEST);
		$wo_task_id = Filter::RequireInt($_REQUEST['id']);
		
		$obj = new boWorkOrderTask();
		if ($obj->oDB->Load($wo_task_id) != -1)
		{
			$obj->delete(array('wo_task_id' => $wo_task_id));
	
			$workOrderModel = new WorkOrderModel();
			if ($workOrderModel->LoadByIdSeq($obj->oDB->wo_id, $obj->oDB->seq) == -1)
				throw new InvalidEntityException();

			$workOrderPresenter = new WorkOrderPresenter();
			$workOrderPresenter->Detail($workOrderModel);
		}
	}
	
	function submitReorder()
	{
		global $g_oSec;

		// this is done as a XMLHTTP request
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION))
			throw new PermissionDeniedException();

		if (($wo_id = Filter::ToInt($_REQUEST['wo_id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (($seq = Filter::ToInt($_REQUEST['seq'])) === null)
		{
			throw new InvalidDataException();
		}

		$aTaskList = @Filter::ToIntArray($_REQUEST['task']);
		$oDB = new WorkOrderTaskModel();
		$iOrder = 1;
		for ($i = 0; $i < count($aTaskList); $i++)
		{
			$iID = $aTaskList[$i];
			$oDB->Execute("UPDATE dcl_wo_task SET task_order = $iOrder WHERE wo_task_id = $iID AND wo_id = $wo_id AND seq = $seq");
			$iOrder++;
		}

		exit;
	}

	function ShowEntryForm($obj = '')
	{
		global $dcl_info, $g_oSec;

		$isEdit = is_object($obj);
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION))
			throw new PermissionDeniedException();
			
		$t = new SmartyHelper();
		$t->assign('IS_EDIT', $isEdit);

		// Data
		if ($isEdit)
		{
			$t->assign('VAL_WO_TASK_ID', $obj->wo_task_id);
			$t->assign('URL_BACK', menuLink('', 'menuAction=WorkOrder.Detail&jcn=' . $obj->wo_id . '&seq=' . $obj->seq));
			$t->assign('VAL_COMPLETE', $obj->task_complete);
			$t->assign('VAL_SUMMARY', $obj->task_summary);
		}
		else
		{
			if (($wo_id = Filter::ToInt($_REQUEST['jcn'])) === null)
			{
				throw new InvalidDataException();
			}
			
			if (($seq = Filter::ToInt($_REQUEST['seq'])) === null)
			{
				throw new InvalidDataException();
			}

			$t->assign('URL_BACK', menuLink('', 'menuAction=WorkOrder.Detail&jcn=' . $wo_id . '&seq=' . $seq));
			$t->assign('VAL_WO_ID', $wo_id);
			$t->assign('VAL_SEQ', $seq);
			$t->assign('VAL_SUMMARY', '');
		}

		$t->Render('WorkOrderTaskForm.tpl');
	}
}
