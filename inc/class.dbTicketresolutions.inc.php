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

LoadStringResource('db');

class dbTicketresolutions extends dclDB
{
	function dbTicketresolutions()
	{
		parent::dclDB();
		$this->TableName = 'ticketresolutions';
		LoadSchema($this->TableName);

		parent::Clear();
	}

	function Delete()
	{
		return parent::Delete(array('resid' => $this->resid));
	}

	function GetHoursText()
	{
		$start = new DCLTimestamp;
		$start->SetFromDB($this->f('startedon'));

		$end = new DCLTimestamp;
		$end->SetFromDB($this->f('loggedon'));

		$tempHours = ($end->time - $start->time);
		$hh = intval($tempHours / 3600);
		$tempHours -= ($hh * 3600);
		$mm = intval($tempHours / 60);
		$tempHours -= ($mm * 60);
		$ss = intval($tempHours);

		return sprintf('%02d:%02d:%02d', $hh, $mm, $ss);
	}

	function Load($resid)
	{
		return parent::Load(array('resid' => $resid));
	}

	function GetResolutions($ticketid)
	{
		global $dcl_info, $g_oSec;

		$this->Clear();

		$sPublicSQL = '';
		if ($g_oSec->IsPublicUser())
			$sPublicSQL = "AND is_public = 'Y'";

		$sql = 'SELECT resid, ticketid, loggedby, ';
		$sql .= $this->ConvertTimestamp('loggedon', 'loggedon');
		$sql .= ', status, resolution, ';
		$sql .= $this->ConvertTimestamp('startedon', 'startedon');
		$sql .= ", is_public FROM ticketresolutions WHERE ticketid=$ticketid $sPublicSQL ORDER BY resid " . $dcl_info['DCL_TIME_CARD_ORDER'];
		if (!$this->Query($sql))
			return -1;

		return 1;
	}

	function GetResolutionsArray($ticketid)
	{
		if ($this->GetResolutions($ticketid) == -1)
			return -1;

		$aRetVal = $this->ResultToArray();

		if (count($aRetVal) > 0)
		{
			$oMeta =& CreateObject('dcl.DCL_MetadataDisplay');
			for ($i = 0; $i < count($aRetVal); $i++)
			{
				$aRetVal[$i]['loggedon'] = $this->FormatDateForDisplay($aRetVal[$i]['loggedon']);
				$aRetVal[$i]['loggedby_id'] = $aRetVal[$i]['loggedby'];
				$aRetVal[$i]['loggedby'] = $oMeta->GetPersonnel($aRetVal[$i]['loggedby']);
				$aRetVal[$i]['status_id'] = $aRetVal[$i]['status'];
				$aRetVal[$i]['status'] = $oMeta->GetStatus($aRetVal[$i]['status']);
				$aRetVal[$i]['startedon'] = $this->FormatDateForDisplay($aRetVal[$i]['startedon']);
			}
		}

		return $aRetVal;
	}
	
	function IsLastResolution($iTicketID, $iResID)
	{
		return ($this->ExecuteScalar("SELECT COUNT(*) FROM ticketresolutions WHERE resid > $iResID AND ticketid = $iTicketID") == 0);
	}
	
	function GetNextResolutionID($iResID, $iTicketID)
	{
		return $this->ExecuteScalar("SELECT MIN(resid) FROM ticketresolutions WHERE resid > $iResID AND ticketid = $iTicketID");
	}
	
	function GetPrevResolutionID($iResID, $iTicketID)
	{
		return $this->ExecuteScalar("SELECT MAX(resid) FROM ticketresolutions WHERE resid < $iResID AND ticketid = $iTicketID");
	}
}
?>
