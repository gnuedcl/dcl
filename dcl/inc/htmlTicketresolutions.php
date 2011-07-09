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
		if (($id = Filter::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_RESOLUTION, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$oResolution = new TicketResolutionsModel();
		if ($oResolution->Load($id) == -1)
			return;
			
		$oTicket = new TicketsModel();
		if ($oTicket->Load($oResolution->ticketid) == -1)
		{
			return -1;
		}
		
		$obj = new htmlTicketDetail();
		$obj->Show($oTicket, $id, false);
	}

	function submitModify()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_RESOLUTION, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$oBO = new boTicketresolutions();
		CleanArray($_REQUEST);
		$oBO->modify($_REQUEST);

		$obj = new TicketsModel();
		$obj->Load($oBO->oDB->ticketid);

		$objH = new htmlTicketDetail();
		$objH->Show($obj);
	}

	function delete()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (($id = Filter::ToInt($_REQUEST['resid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_RESOLUTION, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		$oResolution = new TicketResolutionsModel();
		if ($oResolution->Load($id) == -1)
			return;
			
		$oTicket = new TicketsModel();
		if ($oTicket->Load($oResolution->ticketid) == -1)
		{
			return -1;
		}
		
		$obj = new htmlTicketDetail();
		$obj->Show($oTicket, $id, true);
	}

	function submitDelete()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_RESOLUTION, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		if (($id = Filter::ToInt($_REQUEST['resid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$oResolution = new TicketResolutionsModel();
		if ($oResolution->Load($id) == -1)
			return;
			
		$iTicketID = $oResolution->ticketid;
		
		$oBO = new boTicketresolutions();
		$aKey = array('resid' => $id, 'ticketid' => $iTicketID);
		$oBO->delete($aKey);

		if (EvaluateReturnTo())
			return;

		$oTicket = new TicketsModel();
		if ($oTicket->Load($iTicketID) == -1)
		{
			return -1;
		}
		
		$objH = new htmlTicketDetail();
		$objH->Show($oTicket);
	}

	function DisplayForm($ticketid, $obj = '')
	{
		global $dcl_info, $g_oSec, $dcl_preferences;

		$isEdit = is_object($obj);
		if ($isEdit)
		{
			if (!$g_oSec->HasPerm(DCL_ENTITY_RESOLUTION, DCL_PERM_MODIFY, (int)$obj->resid))
				throw new PermissionDeniedException();
		}
		else
		{
			if (!$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ACTION, (int)$ticketid))
				throw new PermissionDeniedException();
		}

		$objT = new TicketsModel();
		$objProduct = new ProductModel();
		$objStat = new StatusHtmlHelper();

		if ($objT->Load((int)$ticketid) == -1)
			return;
			
		$objProduct->Query('SELECT tcksetid FROM products WHERE id=' . $objT->product);
		$objProduct->next_record();
		$setid = $objProduct->f(0);

		$t = new DCL_Smarty();
		$t->assign('IS_EDIT', $isEdit);
		$t->assign('VAL_NOTIFYDEFAULT', isset($dcl_preferences['DCL_PREF_NOTIFY_DEFAULT']) ? $dcl_preferences['DCL_PREF_NOTIFY_DEFAULT'] : 'N');

		if ($isEdit)
		{
			$t->assign('TXT_TITLE', sprintf(STR_TCK_EDITRESOLUTION, $obj->ticketid));
			$t->assign('CMB_STATUS', $objStat->Select($obj->status, 'status', 'name', 0, false, $setid));
			$t->assign('VAL_ISPUBLIC', $obj->is_public);
			$t->assign('VAL_RESOLUTION', $obj->resolution);
			$t->assign('menuAction', 'htmlTicketresolutions.submitModify');
			$t->assign('startedon', $obj->startedon);
			$t->assign('resid', $obj->resid);
		}
		else
		{
			$t->assign('TXT_TITLE', sprintf(STR_TCK_ADDRESOLUTION, $ticketid));

			$t->assign('CMB_STATUS', $objStat->Select($objT->status, 'status', 'name', 0, false, $setid));
			$t->assign('VAL_ISPUBLIC', 'Y');
			$t->assign('VAL_RESOLUTION', '');
			
			$t->assign('menuAction', 'boTicketresolutions.dbadd');
			$t->assign('startedon', date($dcl_info['DCL_TIMESTAMP_FORMAT']));

			// Allow agents to escalate to ticket leads
			$t->assign('PERM_ASSIGN',$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ASSIGN));
			if ($g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ASSIGN))
			{
				$objPersonnel = new PersonnelHtmlHelper();
				$t->assign('CMB_REASSIGN', $objPersonnel->Select(0, 'reassign_to_id', 'lastfirst', 0, true, DCL_ENTITY_TICKET));
			}

			// Can modify tags right here if user can modify ticket
			$t->assign('PERM_MODIFYTICKET',$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_MODIFY));
			
			if ($g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_MODIFY))
			{
				$oTag = new EntityTagModel();
				$t->assign('VAL_TAGS', $oTag->getTagsForEntity(DCL_ENTITY_TICKET, $ticketid));
			}
		}

		$t->assign('ticketid', $ticketid);

		$t->Render('htmlTicketresolutionsForm.tpl');
	}
}
