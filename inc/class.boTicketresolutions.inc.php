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

class boTicketresolutions
{
	var $oDB;
	
	function boTicketresolutions()
	{
		$this->oDB =& CreateObject('dcl.dbTicketresolutions');
	}

	function add()
	{
		global $g_oSec;
		
		commonHeader();
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['ticketid'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ACTION, $iID))
			return PrintPermissionDenied();

		$objTicket =& CreateObject('dcl.dbTickets');
		if ($objTicket->Load($iID) == -1)
			return;

		$obj =& CreateObject('dcl.htmlTicketresolutions');
		$obj->DisplayForm($iID);

		$objHT =& CreateObject('dcl.htmlTicketDetail');
		$objHT->Show($objTicket);
	}

	function dbadd()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['ticketid'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ACTION, $iID))
			return PrintPermissionDenied();

		$this->oDB->InitFromGlobals();
		$this->oDB->loggedby = $GLOBALS['DCLID'];
		$this->oDB->loggedon = date($dcl_info['DCL_TIMESTAMP_FORMAT']);
		$this->oDB->is_public = @DCL_Sanitize::ToYN($_REQUEST['is_public']);

		$obj =& CreateObject('dcl.dbTickets');
		if ($obj->Load($this->oDB->ticketid) == -1)
			return;

		$obj->lastactionon = date($dcl_info['DCL_TIMESTAMP_FORMAT']);

		$notify = '4';
		if ($this->oDB->status != $obj->status)
		{
			$oStatus =& CreateObject('dcl.dbStatuses');
			$notify .= ',3';
			$obj->statuson = date($dcl_info['DCL_TIMESTAMP_FORMAT']);
			if ($oStatus->GetStatusType($this->oDB->status) == 2)
			{
				$notify .= ',2';
				$obj->closedby = $GLOBALS['DCLID'];
				$obj->closedon = date($dcl_info['DCL_TIMESTAMP_FORMAT']);
			}
			if ($oStatus->GetStatusType($this->oDB->status) == 1)
				$notify .= ',1';

			$obj->status = $this->oDB->status;
		}

		if (IsSet($_REQUEST['escalate']) && $_REQUEST['escalate'] == '1')
		{
			$objP =& CreateObject('dcl.dbProducts');
			$objP->Load($obj->product);
			if ($obj->responsible != $objP->ticketsto)
			{
				$obj->responsible = $objP->ticketsto;
				$objDP =& CreateObject('dcl.dbPersonnel');
				$objDP->Load($obj->responsible);
				$this->oDB->resolution = '*** ' . STR_BO_ESCALATEDTO . ': ' . $objDP->short . ' ***' . phpCrLf . phpCrLf . $this->oDB->resolution;
			}
		}
		else if ($g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ASSIGN))
		{
			$iReassignTo = @DCL_Sanitize::ToInt($_REQUEST['reassign_to_id']);
			if ($iReassignTo > 0 && $obj->responsible != $iReassignTo)
			{
				$obj->responsible = $iReassignTo;
			}
		}
		
		if (isset($_REQUEST['tags']) && $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_MODIFY))
		{
			$oTag =& CreateObject('dcl.dbEntityTag');
			$oTag->serialize(DCL_ENTITY_TICKET, $iID, 0, $_REQUEST['tags']);
		}

		$this->oDB->BeginTransaction();
		$this->oDB->Add();

		$start = new DCLTimestamp;
		$start->SetFromDisplay($this->oDB->startedon);

		$end = new DCLTimestamp;
		$end->SetFromDisplay($this->oDB->loggedon);

		$obj->seconds += ($end->time - $start->time);

		$obj->Edit();
		$this->oDB->EndTransaction();

		$objWtch =& CreateObject('dcl.boWatches');
		$objWtch->sendTicketNotification($obj, $notify);

		@$this->sendCustomerResponseEmail($obj);

		$objH =& CreateObject('dcl.htmlTicketDetail');
		$objH->Show($obj);
	}

	function modify(&$aSource)
	{
		global $dcl_info, $g_oSec;
		
		commonHeader();
		
		if ($this->oDB->Load($aSource['resid']) == -1)
			return;
			
		if (!$g_oSec->HasPerm(DCL_ENTITY_RESOLUTION, DCL_PERM_MODIFY, $this->oDB->ticketid))
			return PrintPermissionDenied();

		$iOrigStatus = $this->oDB->status;
		$this->oDB->InitFromArray($aSource);
		$this->oDB->is_public = @DCL_Sanitize::ToYN($_REQUEST['is_public']);

		$oTicket =& CreateObject('dcl.dbTickets');
		$oTicket->Load($this->oDB->ticketid);
		$oTicket->lastactionon = DCL_NOW;

		$notify = '4';
		if ($oTicket->IsLastResolution($this->oDB->ticketid, $this->oDB->resid))
		{
			if ($this->oDB->status != $oTicket->status)
			{
				$oStatus =& CreateObject('dcl.dbStatuses');
				$notify .= ',3';
				$oTicket->statuson = DCL_NOW;
				if ($oStatus->GetStatusType($this->oDB->status) == 2)
				{
					$notify .= ',2';
					$oTicket->closedby = $GLOBALS['DCLID'];
					$oTicket->closedon = DCL_NOW;
				}
				else if ($oStatus->GetStatusType($this->oDB->status) == 1)
					$notify .= ',1';
	
				$oTicket->status = $this->oDB->status;
			}
			else
			{
				$this->oDB->status = $iOrigStatus;
			}
		}

		$this->oDB->BeginTransaction();
		$this->oDB->Edit();
		$oTicket->Edit();
		$this->oDB->EndTransaction();

		$objWtch =& CreateObject('dcl.boWatches');
		$objWtch->sendTicketNotification($oTicket, $notify);

		@$this->sendCustomerResponseEmail($oTicket);
	}

	function delete(&$aSource)
	{
		global $dcl_info, $g_oSec;
		
		commonHeader();
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_RESOLUTION, DCL_PERM_DELETE, $aSource['ticketid']))
			return PrintPermissionDenied();

		$this->oDB->InitFromArray($aSource);
		
		$oTicket =& CreateObject('dcl.dbTickets');
		$oTicket->Load($this->oDB->ticketid);
		$oTicket->lastactionon = DCL_NOW;

		// Get the next resolution issued after this one.  If not, assume
		// that this resolution was the last one entered and affected the ticket
		// status when input.
		$oQueryTR =& CreateObject('dcl.dbTicketresolutions');
		if ($oQueryTR->Load($aSource['resid']) == -1)
			return;
			
		if (($iNextID = $oQueryTR->GetNextResolutionID($oQueryTR->resid, $oQueryTR->ticketid)) === null)
		{
			// OK, we're the last resolution input, therefore we control status.
			// See if any resolutions were input before this one.  If so,
			// try to revert to the previous resolution status.  Otherwise, open it.
			if (($iPrevID = $oQueryTR->GetPrevResolutionID($oQueryTR->resid, $oQueryTR->ticketid)) !== null)
			{
				$oQueryTR->Load($iPrevID);
				if ($oQueryTR->status != $oTicket->status)
				{
					$oTicket->statuson = DCL_NOW;
					$oStatus =& CreateObject('dcl.dbStatuses');
					if ($oStatus->GetStatusType($oQueryTR->status) == 2 && $oStatus->GetStatusType($oTicket->status) != 2)
					{
						$oTicket->closedby = $oQueryTR->loggedby;
						$oTicket->closedon = $oQueryTR->loggedon;
					}
					else if ($oStatus->GetStatusType($oTicket->status) == 2)
					{
						$oTicket->closedby = 0;
						$oTicket->closedon = '';
					}

					$oTicket->status = $oQueryTR->status;
				}
			}
			else
			{
				$oTicket->status = $dcl_info['DCL_DEF_STATUS_ASSIGN_WO']; // Open it
				$oTicket->statuson = date($dcl_info['DCL_TIMESTAMP_FORMAT']);
				$oTicket->closedby = 0;
				$oTicket->closedon = '';
				$oTicket->lastactionon = '';
			}
		}
		else
		{
			if ($oQueryTR->Load($iNextID) != -1)
				$oTicket->starton = $oQueryTR->loggedon;
		}

		$this->oDB->BeginTransaction();
		if ($this->oDB->Delete() != -1)
			$oTicket->Edit();
			
		$this->oDB->EndTransaction();
	}

	function sendCustomerResponseEmail($oTicket)
	{
		global $dcl_info;

		if (!is_object($this->oDB) || $dcl_info['DCL_CQQ_PERCENT'] == 0)
			return;

		$oStatus =& CreateObject('dcl.dbStatuses');
		if ($oStatus->GetStatusType($this->oDB->status) != 2)
			return;

		$oMeta =& CreateObject('dcl.DCL_MetadataDisplay');
		$aContact = $oMeta->GetContact($oTicket->contact_id);
		if (!IsSet($aContact['email']) || trim($aContact['email']) == '')
			return;

		srand((double)microtime() * 1000000);
		$pct = rand(1, 100);
		if ($pct <= $dcl_info['DCL_CQQ_PERCENT'])
		{
			$t =& CreateSmarty();
			$t->assign('VAL_TICKETID', $this->oDB->ticketid);
			$t->assign('VAL_CLOSEDON', date('n/j/Y'));
			$t->assign('contact', $aContact);

			$oMail =& CreateObject('dcl.boSMTP');
			$oMail->isHtml = true;
			$oMail->to = $aContact['email'];
			$oMail->from = $dcl_info['DCL_CQQ_FROM'];
			$oMail->subject = $dcl_info['DCL_CQQ_SUBJECT'];
			
			$sProductTemplate = DCL_ROOT . 'templates/custom/cqq_' . $oTicket->product . '.tpl';
			if (file_exists($sProductTemplate))
				$oMail->body = SmartyFetch($t, $sProductTemplate, 'custom');
			else
				$oMail->body = SmartyFetch($t, $dcl_info['DCL_CQQ_TEMPLATE'], 'custom');
			
			$oMail->Send();
		}
	}
}
?>
