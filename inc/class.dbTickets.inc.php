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

	function dbTickets()
	{
		parent::dclDB();
		$this->TableName = 'tickets';
		LoadSchema($this->TableName);
		$this->AuditEnabled = true;
		parent::Clear();
	}

	function Add()
	{
		$oStatus = CreateObject('dcl.dbStatuses');
		if ($oStatus->GetStatusType($this->status) == 2)
		{
			$this->closedon = 'now()';
		}
		else
		{
			$this->closedby = null;
			$this->closedon = null;
		}

		$this->createdon = 'now()';

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
		$oStatus = CreateObject('dcl.dbStatuses');
		if ($oStatus->GetStatusType($this->status) == 2)
		{
			$this->closedon = 'now()';
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
		return parent::Load(array('ticketid' => $ticketid));
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
}
?>
