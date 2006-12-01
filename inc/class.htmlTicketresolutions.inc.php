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

LoadStringResource('tck');

class htmlTicketresolutions
{
	var $public;

	function htmlTicketresolutions()
	{
		$this->public = array('modify', 'delete', 'submitModify', 'submitDelete');
	}

	function modify()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (($id = DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_RESOLUTION, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		$oResolution = CreateObject('dcl.dbTicketresolutions');
		if ($oResolution->Load($id) == -1)
			return;
			
		$oTicket =& CreateObject('dcl.dbTickets');
		if ($oTicket->Load($oResolution->ticketid) == -1)
		{
			return -1;
		}
		
		$obj =& CreateObject('dcl.htmlTicketDetail');
		$obj->Show($oTicket, $id, false);
	}

	function submitModify()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_RESOLUTION, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		$oBO = CreateObject('dcl.boTicketresolutions');
		CleanArray($_REQUEST);
		$oBO->modify($_REQUEST);

		$obj = CreateObject('dcl.dbTickets');
		$obj->Load($oBO->oDB->ticketid);

		$objH = CreateObject('dcl.htmlTicketDetail');
		$objH->Show($obj);
	}

	function delete()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (($id = DCL_Sanitize::ToInt($_REQUEST['resid'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_RESOLUTION, DCL_PERM_DELETE))
			return PrintPermissionDenied();

		$oResolution = CreateObject('dcl.dbTicketresolutions');
		if ($oResolution->Load($id) == -1)
			return;
			
		$oTicket =& CreateObject('dcl.dbTickets');
		if ($oTicket->Load($oResolution->ticketid) == -1)
		{
			return -1;
		}
		
		$obj =& CreateObject('dcl.htmlTicketDetail');
		$obj->Show($oTicket, $id, true);
	}

	function submitDelete()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_RESOLUTION, DCL_PERM_DELETE))
			return PrintPermissionDenied();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['resid'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$oResolution = CreateObject('dcl.dbTicketresolutions');
		if ($oResolution->Load($id) == -1)
			return;
			
		$iTicketID = $oResolution->ticketid;
		
		$oBO = CreateObject('dcl.boTicketresolutions');
		$aKey = array('resid' => $id, 'ticketid' => $iTicketID);
		$oBO->delete($aKey);

		if (EvaluateReturnTo())
			return;

		$oTicket =& CreateObject('dcl.dbTickets');
		if ($oTicket->Load($iTicketID) == -1)
		{
			return -1;
		}
		
		$objH = CreateObject('dcl.htmlTicketDetail');
		$objH->Show($oTicket);
	}

	function DisplayForm($ticketid, $obj = '')
	{
		global $dcl_info, $g_oSec;

		$isEdit = is_object($obj);
		if ($isEdit)
		{
			if (!$g_oSec->HasPerm(DCL_ENTITY_RESOLUTION, DCL_PERM_MODIFY, (int)$obj->resid))
				return PrintPermissionDenied();
		}
		else
		{
			if (!$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ACTION, (int)$ticketid))
				return PrintPermissionDenied();
		}

		$objT = CreateObject('dcl.dbTickets');
		$objProduct = CreateObject('dcl.dbProducts');
		$objStat = CreateObject('dcl.htmlStatuses');

		if ($objT->Load((int)$ticketid) == -1)
			return;
			
		$objProduct->Query('SELECT tcksetid FROM products WHERE id=' . $objT->product);
		$objProduct->next_record();
		$setid = $objProduct->f(0);

		$t = CreateSmarty();
		$t->assign('IS_EDIT', $isEdit);

		if ($isEdit)
		{
			$t->assign('TXT_TITLE', sprintf(STR_TCK_EDITRESOLUTION, $obj->ticketid));
			$t->assign('CMB_STATUS', $objStat->GetCombo($obj->status, 'status', 'name', 0, false, $setid));
			$t->assign('VAL_ISPUBLIC', $obj->is_public);
			$t->assign('VAL_RESOLUTION', $obj->resolution);
			$t->assign('menuAction', 'htmlTicketresolutions.submitModify');
			$t->assign('startedon', $obj->startedon);
			$t->assign('resid', $obj->resid);
		}
		else
		{
			$t->assign('TXT_TITLE', sprintf(STR_TCK_ADDRESOLUTION, $ticketid));

			$t->assign('CMB_STATUS', $objStat->GetCombo($objT->status, 'status', 'name', 0, false, $setid));
			$t->assign('VAL_ISPUBLIC', 'Y');
			$t->assign('VAL_RESOLUTION', '');
			
			$t->assign('menuAction', 'boTicketresolutions.dbadd');
			$t->assign('startedon', date($dcl_info['DCL_TIMESTAMP_FORMAT']));

			// Allow agents to escalate to ticket leads
			$t->assign('PERM_ASSIGN',$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ASSIGN));
		}

		$t->assign('ticketid', $ticketid);

		SmartyDisplay($t, 'htmlTicketresolutionsForm.tpl');
	}

	function GetResolutions($ticketid, $editResID = 0, $forDelete = false)
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_RESOLUTION, DCL_PERM_VIEW, (int)$ticketid))
			return '';

		$retVal = '';

		$obj = CreateObject('dcl.dbTicketresolutions');
		if ($obj->GetResolutions((int)$ticketid) != -1)
		{
			if ($obj->next_record())
			{
				$oSmarty =& CreateSmarty();
				$oMeta =& CreateObject('dcl.DCL_MetadataDisplay');

				do
				{
					$obj->GetRow();

					$oSmarty->assign('IS_DELETE', $forDelete);
					$oSmarty->assign('VAL_RESOLUTIONID', $obj->resid);
					$oSmarty->assign('VAL_STATUS', $oMeta->GetStatus($obj->status));
					$oSmarty->assign('VAL_LOGGEDBY', $oMeta->GetPersonnel($obj->loggedby));
					$oSmarty->assign('VAL_LOGGEDON', $obj->loggedon);
					$oSmarty->assign('VAL_HOURSTEXT', $obj->GetHoursText());
					$oSmarty->assign('VAL_RESOLUTION', $obj->resolution);
					$oSmarty->assign('VAL_PUBLIC', $obj->is_public == 'Y' ? STR_CMMN_YES : STR_CMMN_NO);

					$retVal .= SmartyFetch($oSmarty, 'htmlTicketresolutions.tpl');
				}
				while ($obj->next_record());
			}
		}

		return $retVal;
	}
}
?>
