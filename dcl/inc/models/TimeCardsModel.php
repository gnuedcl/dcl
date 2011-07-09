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

class TimeCardsModel extends DbProvider
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'timecards';
		LoadSchema($this->TableName);

		parent::Clear();
	}

	public function Add($targeted_version_id = 0, $fixed_version_id = 0)
	{
		global $dcl_info, $g_oSec;

		if (($fEtcHours = Filter::ToDecimal($_REQUEST['etchours'])) === null)
		{
			if ($_REQUEST['menuAction'] !== 'boTimecards.dbbatchadd')
			{
				throw new InvalidDataException();
			}
		}

		$objWO = new WorkOrderModel();
		if ($objWO->Load($this->jcn, $this->seq) == -1)
		{
			trigger_error(printf(STR_DB_WORKORDERLOADERR, $this->jcn, $this->seq));
			return;
		}

		$currstatus = $objWO->status;
		$justStarted = $objWO->IsFieldNull('starton');
		$idSQL = $this->GetNewIDSQLForTable('timecards');

		$query  = 'INSERT INTO timecards (';
		if ($idSQL != '')
			$query .= 'id,';
		$query .= 'jcn, seq, actionon, inputon, actionby, status, action, hours, summary, description, is_public, reassign_from_id, reassign_to_id';
		$query .= ') VALUES (';
		if ($idSQL != '')
			$query .= $idSQL . ',';
		$query .= $this->jcn . ',' . $this->seq . ',';
		$query .= $this->DisplayToSQL($this->actionon) . ',';
		$query .= $this->GetDateSQL() . ',' . $this->actionby . ',';
		$query .= $this->status . ',' . $this->action . ',' . $this->hours;
		$query .= ',' . $this->Quote($this->summary);
		$query .= ',' . $this->Quote($this->description);
		$query .= ',' . $this->Quote($this->is_public);

		// Reassign if selected and able
		if ($this->reassign_to_id > 0 && $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ASSIGN) && $objWO->responsible != $this->reassign_to_id)
		{
			$query .= ',' . $objWO->responsible;
			$query .= ',' . $this->reassign_to_id;
			$objWO->responsible = $this->reassign_to_id;
		}
		else
			$query .= ', NULL, NULL';

		$query .= ')';
		
		$objWO->lastactionon = date($dcl_info['DCL_TIMESTAMP_FORMAT']);
		$objWO->totalhours = $objWO->totalhours + $this->hours;
		$objWO->etchours = $fEtcHours !== null ? $fEtcHours : $objWO->etchours;
		if ($currstatus != $this->status)
		{
			$objWO->status = $this->status;
			$objWO->statuson = date($dcl_info['DCL_TIMESTAMP_FORMAT']);
			$oStatus = new StatusModel();
			if ($oStatus->GetStatusType($this->status) == 2 && $oStatus->GetStatusType($currstatus) != 2)
			{
				$objWO->closedby = $this->actionby;
				$objWO->closedon = $this->actionon;
			}
			else if ($oStatus->GetStatusType($currstatus) == 2)
			{
				$objWO->closedby = 0;
				$objWO->closedon = '';
			}
		}
		
		// Check for version updates
		if ((int)$targeted_version_id > 0)
			$objWO->targeted_version_id = $targeted_version_id;
			
		if ((int)$fixed_version_id > 0)
			$objWO->fixed_version_id = $fixed_version_id;
			
		// ensure the etc hours do not get anything but zero when closed
		$oStatus = new StatusModel();
		if ($oStatus->GetStatusType($objWO->status) == 2)
			$objWO->etchours = 0.0;
		if ($justStarted == 1)
			$objWO->starton = $this->actionon;

		$this->BeginTransaction();
		$this->Insert($query);
		$objWO->Edit();
		$this->EndTransaction();
	}

	public function Edit()
	{
		// Does not update reassign information - that's historical!
		$query = 'UPDATE timecards SET actionon=' . $this->DisplayToSQL($this->actionon);
		$query .= ',status=' . $this->status . ',action=';
		$query .= $this->action . ',hours=' . $this->hours . ',summary=' . $this->Quote($this->summary);
		$query .= ',description=' . $this->Quote($this->description) . ',is_public=' . $this->Quote($this->is_public) . ' ';
		$query .= ' WHERE id=' . $this->id;

		$this->Execute($query);
	}

	public function Delete()
	{
		return parent::Delete(array('id' => $this->id));
	}

	public function Load($id)
	{
		return parent::Load(array('id' => $id));
	}
	
	public function LoadLast($jcn, $seq, $bIsPublic)
	{
		$id = $this->ExecuteScalar("select max(id) from timecards where jcn = $jcn and seq = $seq" . ($bIsPublic ? " and is_public = 'Y'" : ''));
		
		if ($id > 0)
			return $this->Load($id);
			
		return -1;
	}

	public function GetTimeCards($jcn, $seq, $bIsPublic = false)
	{
		global $dcl_info, $g_oSec;

		$this->Clear();

		$sPublicSQL = '';
		if ($g_oSec->IsPublicUser() || $bIsPublic)
			$sPublicSQL = "AND is_public = 'Y'";

		$sql = 'SELECT timecards.id, jcn, seq, ';
		$sql .= $this->ConvertDate('actionon', 'actionon');
		$sql .= ', ' . $this->ConvertTimestamp('inputon', 'inputon');
		$sql .= ', actionby, status, action, hours, summary, description, reassign_from_id, reassign_to_id, is_public, s.dcl_status_type';
		$sql .= " FROM timecards, statuses s WHERE jcn=$jcn and seq=$seq and status = s.id $sPublicSQL ORDER BY id " . $dcl_info['DCL_TIME_CARD_ORDER'];
		if (!$this->Query($sql))
			return -1;

		return 1;
	}

	public function GetTimeCardsArray($jcn, $seq, $bIsPublic = false)
	{
		if ($this->GetTimeCards($jcn, $seq, $bIsPublic) == -1)
			return -1;

		$aRetVal = $this->ResultToArray();

		if (count($aRetVal) > 0)
		{
			$oMeta = new DisplayHelper();
			for ($i = 0; $i < count($aRetVal); $i++)
			{
				$aRetVal[$i]['actionon'] = $this->FormatDateForDisplay($aRetVal[$i]['actionon']);
				$aRetVal[$i]['actionby_id'] = $aRetVal[$i]['actionby'];
				$aRetVal[$i]['actionby'] = $oMeta->GetPersonnel($aRetVal[$i]['actionby']);
				$aRetVal[$i]['reassign_from_id_int'] = $aRetVal[$i]['reassign_from_id'];
				$aRetVal[$i]['reassign_from_id'] = $oMeta->GetPersonnel($aRetVal[$i]['reassign_from_id']);
				$aRetVal[$i]['reassign_to_id_int'] = $aRetVal[$i]['reassign_to_id'];
				$aRetVal[$i]['reassign_to_id'] = $oMeta->GetPersonnel($aRetVal[$i]['reassign_to_id']);
				$aRetVal[$i]['status_id'] = $aRetVal[$i]['status'];
				$aRetVal[$i]['status'] = $oMeta->GetStatus($aRetVal[$i]['status']);
				$aRetVal[$i]['action_id'] = $aRetVal[$i]['action'];
				$aRetVal[$i]['action'] = $oMeta->GetAction($aRetVal[$i]['action']);
			}
		}

		return $aRetVal;
	}
	
	public function IsLastTimeCard($iID, $iSeq, $iTCID)
	{
		return ($this->ExecuteScalar("SELECT COUNT(*) FROM timecards WHERE id > $iTCID AND jcn = $iID AND seq = $iSeq") == 0);
	}
	
	public function GetNextTimeCardID($iID, $iSeq, $iTCID)
	{
		return $this->ExecuteScalar("SELECT MIN(id) FROM timecards WHERE id > $iTCID AND jcn = $iID AND seq = $iSeq");
	}
	
	public function GetPrevTimeCardID($iID, $iSeq, $iTCID)
	{
		return $this->ExecuteScalar("SELECT MAX(id) FROM timecards WHERE id < $iTCID AND jcn = $iID AND seq = $iSeq");
	}
}
