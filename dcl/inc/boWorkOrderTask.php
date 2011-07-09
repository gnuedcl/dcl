<?php
/*
 * $Id$
 *
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

class boWorkOrderTask extends boAdminObject
{
	function boWorkOrderTask()
	{
		parent::boAdminObject();

		$this->oDB = new WorkOrderTaskModel();
		$this->sKeyField = 'wo_task_id';
		$this->Entity = DCL_ENTITY_WORKORDER;
		$this->PermAdd = DCL_PERM_ACTION;
		$this->PermDelete = DCL_PERM_ACTION;

		$this->sCreatedDateField = 'task_create_dt';
		$this->sCreatedByField = 'task_create_by';
		
		$this->aIgnoreFieldsOnUpdate = array('wo_id', 'seq', 'task_create_by', 'task_create_dt');
	}
	
	function add($aSource)
	{
		$aSource['task_complete'] = 'N';
		
		if (parent::add($aSource) != -1)
			$this->attachFile($aSource);
	}

	function modify($aSource)
	{
		if (($wo_task_id = Filter::ToInt($aSource['wo_task_id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if ($this->oDB->Load($wo_task_id) == -1)
			return;
		
		if (!isset($aSource['task_complete']) || $aSource['task_complete'] != 'Y')
			$aSource['task_complete'] = 'N';
			
		if ($aSource['task_complete'] == 'Y' && $this->oDB->task_complete != 'Y')
		{
			$aSource['task_complete_by'] = $GLOBALS['DCLID'];
			$aSource['task_complete_dt'] = DCL_NOW;
		}
		else if ($aSource['task_complete'] == 'N')
		{
			$aSource['task_complete_by'] = null;
			$aSource['task_complete_dt'] = null;
		}
		else
		{
			$aSource['task_complete_by'] = $this->oDB->task_complete_by;
			$aSource['task_complete_dt'] = $this->oDB->task_complete_dt;
		}

		parent::modify($aSource);
	}
	
	function attachFile($aSource, $iIndex = -1)
	{
		if (($wo_task_id = Filter::ToInt($aSource['wo_task_id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if ($this->oDB->Load($wo_task_id) == -1)
			return;
			
		if (($sFileName = Filter::ToFileName('userfile')) !== null)
		{
			$o = new boFile();
			$o->iType = DCL_ENTITY_WORKORDER_TASK;
			$o->iKey1 = $wo_task_id;
			$o->sFileName = Filter::ToActualFileName('userfile');
			$o->sTempFileName = $sFileName;
			$o->sRoot = $dcl_info['DCL_FILE_PATH'] . '/attachments';
			$o->Upload();
		}
	}
	
	function toggleComplete($aSource)
	{
		if (($wo_task_id = Filter::ToInt($aSource['wo_task_id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if ($this->oDB->Load($wo_task_id) == -1)
			return;
			
		$aSource['task_summary'] = $this->oDB->task_summary;
		$this->modify($aSource);
	}
}
