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

LoadStringResource('db');
class WorkOrderTaskModel extends dclDB
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'dcl_wo_task';
		LoadSchema($this->TableName);

		parent::Clear();
	}
	
	public function DeleteByWorkOrder($wo_id, $seq)
	{
		if (($wo_id = Filter::ToInt($wo_id)) === null || ($seq = Filter::ToInt($seq)) === null)
		{
			throw new InvalidDataException();
		}
		
		return $this->Execute("DELETE FROM dcl_wo_task WHERE wo_id = $wo_id AND seq = $seq");
	}
	
	public function GetTasksForWorkOrder($wo_id, $seq, $bForDisplay = true)
	{
		if (($wo_id = Filter::ToInt($wo_id)) === null || ($seq = Filter::ToInt($seq)) === null)
		{
			throw new InvalidDataException();
		}
		
		if ($bForDisplay)
			$sOrder = 'task_complete, task_order, wo_task_id';
		else
			$sOrder= 'task_order, wo_task_id';
			
		$this->Query('SELECT ' . $this->SelectAllColumns() . " FROM dcl_wo_task WHERE wo_id = $wo_id AND seq = $seq ORDER BY $sOrder");

		$aRetVal = $this->ResultToArray();
		for ($i = 0; $i < count($aRetVal); $i++)
		{
			$aRetVal[$i]['task_create_dt'] = $this->FormatTimeStampForDisplay($aRetVal[$i]['task_create_dt']);
			if ($aRetVal[$i]['task_complete_dt'] !== null)
				$aRetVal[$i]['task_complete_dt'] = $this->FormatTimeStampForDisplay($aRetVal[$i]['task_complete_dt']);
		}
		
		return $aRetVal;
	}
	
	public function GetCountIncompleteTasksForWorkOrder($wo_id, $seq)
	{
		if (($wo_id = Filter::ToInt($wo_id)) === null || ($seq = Filter::ToInt($seq)) === null)
		{
			throw new InvalidDataException();
		}
		
		return $this->ExecuteScalar("SELECT COUNT(*) FROM dcl_wo_task WHERE wo_id = $wo_id AND seq = $seq AND task_complete = 'N'");
	}
	
	public function CloseAllIncompleteTasksForWorkOrder($wo_id, $seq)
	{
		global $DCLID;
		
		if (($wo_id = Filter::ToInt($wo_id)) === null || ($seq = Filter::ToInt($seq)) === null)
		{
			throw new InvalidDataException();
		}
		
		if ($this->GetCountIncompleteTasksForWorkOrder($wo_id, $seq) > 0)
		{
			$this->Execute("UPDATE dcl_wo_task SET task_complete = 'Y', task_complete_by = $DCLID, task_complete_dt = " . $this->GetDateSQL() . " WHERE wo_id = $wo_id AND seq = $seq AND task_complete = 'N'");
			return true;
		}
		
		return false;
	}
}
