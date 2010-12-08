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

class dbTickets extends dclDB
{
	// Pseudo-field to display hh:mm:ss
	var $hoursText;
	var $aContactOrgs;

	function dbTickets()
	{
		parent::dclDB();
		$this->TableName = 'tickets';
		LoadSchema($this->TableName);
		$this->AuditEnabled = true;
		$this->aContactOrgs = array();
		parent::Clear();
	}

	function Add()
	{
		$oStatus = new StatusModel();
		if ($oStatus->GetStatusType($this->status) == 2)
		{
		    if ($this->closedon === null)
			    $this->closedon = DCL_NOW;
		}
		else
		{
			$this->closedby = null;
			$this->closedon = null;
		}

		if ($this->createdon === null)
		    $this->createdon = DCL_NOW;

		if ($this->module_id < 1)
			$this->module_id = null;

		if ($this->entity_source_id < 1)
			$this->entity_source_id = null;

		if (parent::Add() == -1)
			return -1;

		return $this->Load($this->ticketid);
	}

	function Edit()
	{
		$oStatus = new StatusModel();
		if ($oStatus->GetStatusType($this->status) == 2)
		{
			$this->closedon = DCL_NOW;
		}
		else
		{
			$this->closedby = null;
			$this->closedon = null;
		}

		if ($this->module_id < 1)
			$this->module_id = null;

		if ($this->entity_source_id < 1)
			$this->entity_source_id = null;

		parent::Edit(array('createdon'));
		$this->hoursText = $this->GetHoursText();
	}

	function Delete()
	{
		$this->BeginTransaction();

		$query = 'DELETE FROM ticketresolutions WHERE ticketid=' . (int)$this->ticketid;
		$this->Execute($query);

		$this->Audit(array('ticketid' => $this->ticketid));
		$query = 'DELETE FROM tickets WHERE ticketid=' . (int)$this->ticketid;
		$this->Execute($query);

		return $this->EndTransaction();
	}

	function GetHoursText()
	{
		// Set hoursText
		if ($this->res > 0 && count($this->Record) > 0)
		{
			$tempHours = $this->f('seconds');
		}
		else
			$tempHours = $this->seconds;

		$hh = intval($tempHours / 3600);
		$tempHours -= ($hh * 3600);
		$mm = intval($tempHours / 60);
		$tempHours -= ($mm * 60);
		$ss = intval($tempHours);

		return sprintf('%01d:%02d:%02d', $hh, $mm, $ss);
	}

	function Load($ticketid)
	{
		global $g_oSec;
		
		$oRetVal = parent::Load(array('ticketid' => $ticketid));
		if ($oRetVal !== -1)
		{
			$bIsPublic = false;
			if (($g_oSec->IsPublicUser() || $g_oSec->IsOrgUser()) && !$this->CanView($this, $GLOBALS['DCLID'], $bIsPublic))
			{
				throw new PermissionDeniedException();
			}
		}
		
		return $oRetVal;
	}

	function LoadDatesByRange($beginDate, $endDate, $product_id = 0)
	{
		$query = 'SELECT ';
		$query .= $this->ConvertTimestamp('createdon', 'createdon');
		$query .= ', ';
		$query .= $this->ConvertTimestamp('closedon', 'closedon');
		$query .= ' FROM tickets WHERE ((createdon between ' . $this->DisplayToSQL($beginDate);
		$query .= ' AND ' . $this->DisplayToSQL($endDate);
		$query .= ') OR (closedon between ' . $this->DisplayToSQL($beginDate);
		$query .= ' AND ' . $this->DisplayToSQL($endDate) . '))';

		if ($product_id > 0)
			$query .= ' AND product = ' . $product_id;

		if (!$this->Query($query))
			return -1;
	}
	
	function IsLastResolution($iTicketID, $iResolutionID)
	{
		return ($this->ExecuteScalar("SELECT COUNT(*) FROM ticketresolutions WHERE ticketid = $iTicketID AND resid > $iResolutionID") == 0);
	}

	function CanView(&$obj, $iPersonnelID, &$bIsPublic)
	{
		global $dcl_info, $g_oSession, $g_oSec;
		
		$bCanReceive = true;
		$bIsPublic = false;
		$oUR = new dbUserRole();
		$oUR->ListPermissions($iPersonnelID, DCL_ENTITY_TICKET, 0, 0, array(DCL_PERM_PUBLICONLY, DCL_PERM_VIEWACCOUNT));
		while ($oUR->next_record() && $bCanReceive)
		{
			if ($oUR->f(0) == DCL_PERM_PUBLICONLY)
			{
				$bIsPublic = true;
				if ($bCanReceive)
					$bCanReceive = ($obj->is_public == 'Y');
					
				if ($bCanReceive)
				{
					$oDBProduct = new dbProducts();
					if ($oDBProduct->Load($obj->product) !== -1)
					{
						$bCanReceive = ($oDBProduct->is_public == 'Y');
					}
					else
					{
						$bCanReceive = false;
					}
				}
			}
			else if ($oUR->f(0) == DCL_PERM_VIEWACCOUNT)
			{
				if (!isset($obj->account) || $obj->account === null || $obj->account < 1)
					return false;
					
				$oDB = new dclDB;
				$sSQL = "SELECT OC.org_id FROM dcl_org_contact OC JOIN personnel P ON OC.contact_id = P.contact_id WHERE P.id = $iPersonnelID";
				if ($oDB->Query($sSQL) != -1)
				{
					$this->aContactOrgs[$iPersonnelID] = array();
					while ($oDB->next_record())
					{
						array_push($this->aContactOrgs[$iPersonnelID], $oDB->f(0));
					}
					
					if (count($this->aContactOrgs[$iPersonnelID]) > 0)
						$bCanReceive = in_array($obj->account, $this->aContactOrgs[$iPersonnelID]);
					else
						$bCanReceive = false;
				}
				else
					$bCanReceive = false;
			}
		}
		
		return $bCanReceive;
	}
}
