<?php
/*
 * $Id: class.dbTimeCards.inc.php,v 1.1.1.1 2006/11/27 05:30:50 mdean Exp $
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

class dbTimeCards extends dclDB
{
	function dbTimeCards()
	{
		parent::dclDB();
		$this->TableName = 'timecards';
		LoadSchema($this->TableName);

		parent::Clear();
	}

	function Add()
	{
		global $dcl_info, $g_oSec;

		if (($fEtcHours = DCL_Sanitize::ToDecimal($_REQUEST['etchours'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		$objWO = CreateObject('dcl.dbWorkorders');
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
		$query .= 'jcn, seq, actionon, inputon, actionby, status, action, hours, summary, description, revision, reassign_from_id, reassign_to_id';
		$query .= ') VALUES (';
		if ($idSQL != '')
			$query .= $idSQL . ',';
		$query .= $this->jcn . ',' . $this->seq . ',';
		$query .= $this->DisplayToSQL($this->actionon) . ',';
		$query .= $this->GetDateSQL() . ',' . $this->actionby . ',';
		$query .= $this->status . ',' . $this->action . ',' . $this->hours;
		$query .= ',' . $this->Quote($this->summary);
		$query .= ',' . $this->Quote($this->description);
		$query .= ',' . $this->Quote($this->revision);

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
		$objWO->etchours = $_REQUEST['etchours'];
		if ($currstatus != $this->status)
		{
			$objWO->status = $this->status;
			$objWO->statuson = date($dcl_info['DCL_TIMESTAMP_FORMAT']);
			$oStatus = CreateObject('dcl.dbStatuses');
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
		// ensure the etc hours do not get anything but zero when closed
		$oStatus = CreateObject('dcl.dbStatuses');
		if ($oStatus->GetStatusType($objWO->status) == 2)
			$objWO->etchours = 0.0;
		if ($justStarted == 1)
			$objWO->starton = $this->actionon;

		$this->BeginTransaction();
		$this->Insert($query);
		$objWO->Edit();
		$this->EndTransaction();
	}

	function Edit()
	{
		// Does not update reassign information - that's historical!
		$query = 'UPDATE timecards SET actionon=' . $this->DisplayToSQL($this->actionon);
		$query .= ',status=' . $this->status . ',action=';
		$query .= $this->action . ',hours=' . $this->hours . ',summary=\'' . $this->DBAddSlashes($this->summary);
		$query .= '\',description=\'' . $this->DBAddSlashes($this->description) . '\',revision=\'' . $this->DBAddSlashes($this->revision) . '\' ';
		$query .= ' WHERE id=' . $this->id;

		$this->Execute($query);
	}

	function Delete()
	{
		return parent::Delete(array('id' => $this->id));
	}

	function Load($id)
	{
		return parent::Load(array('id' => $id));
	}

	function GetTimeCards($jcn, $seq)
	{
		global $dcl_info, $g_oSec;

		$this->Clear();

		$sPublicSQL = '';
		if ($g_oSec->IsPublicUser())
			$sPublicSQL = "AND is_public = 'Y'";

		$sql = 'SELECT id, jcn, seq, ';
		$sql .= $this->ConvertDate('actionon', 'actionon');
		$sql .= ', ' . $this->ConvertTimestamp('inputon', 'inputon');
		$sql .= ', actionby, status, action, hours, summary, description, revision, reassign_from_id, reassign_to_id, is_public';
		$sql .= " FROM timecards WHERE jcn=$jcn and seq=$seq $sPublicSQL ORDER BY id " . $dcl_info['DCL_TIME_CARD_ORDER'];
		if (!$this->Query($sql))
			return -1;

		return 1;
	}

	function GetTimeCardsArray($jcn, $seq)
	{
		if ($this->GetTimeCards($jcn, $seq) == -1)
			return -1;

		$aRetVal = $this->ResultToArray();

		if (count($aRetVal) > 0)
		{
			$oMeta =& CreateObject('dcl.DCL_MetadataDisplay');
			for ($i = 0; $i < count($aRetVal); $i++)
			{
				$aRetVal[$i]['actionon'] = $this->FormatDateForDisplay($aRetVal[$i]['actionon']);
				$aRetVal[$i]['actionby_id'] = $aRetVal[$i]['actionby'];
				$aRetVal[$i]['actionby'] = $oMeta->GetPersonnel($aRetVal[$i]['actionby']);
				$aRetVal[$i]['reassign_from_id'] = $oMeta->GetPersonnel($aRetVal[$i]['reassign_from_id']);
				$aRetVal[$i]['reassign_to_id'] = $oMeta->GetPersonnel($aRetVal[$i]['reassign_to_id']);
				$aRetVal[$i]['status_id'] = $aRetVal[$i]['status'];
				$aRetVal[$i]['status'] = $oMeta->GetStatus($aRetVal[$i]['status']);
				$aRetVal[$i]['action_id'] = $aRetVal[$i]['action'];
				$aRetVal[$i]['action'] = $oMeta->GetAction($aRetVal[$i]['action']);
			}
		}

		return $aRetVal;
	}
	
	function IsLastTimeCard($iID, $iSeq, $iTCID)
	{
		return ($this->ExecuteScalar("SELECT COUNT(*) FROM timecards WHERE id > $iTCID AND jcn = $iID AND seq = $iSeq") == 0);
	}
	
	function GetNextTimeCardID($iID, $iSeq, $iTCID)
	{
		return $this->ExecuteScalar("SELECT MIN(id) FROM timecards WHERE id > $iTCID AND jcn = $iID AND seq = $iSeq");
	}
	
	function GetPrevTimeCardID($iID, $iSeq, $iTCID)
	{
		return $this->ExecuteScalar("SELECT MAX(id) FROM timecards WHERE id < $iTCID AND jcn = $iID AND seq = $iSeq");
	}
}
?>
