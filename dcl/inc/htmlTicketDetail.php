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

class htmlTicketDetail
{
	function Show($obj, $editResID = 0, $forDelete = false)
	{
		global $dcl_info, $g_oSec;

		if (!is_object($obj) || !is_a($obj, 'dbTickets'))
		{
			trigger_error('A dbTickets object was not passed to htmlTicketDetail::Show()!');
			return;
		}

		if (!$g_oSec->HasAnyPerm(array(DCL_ENTITY_TICKET => array($g_oSec->PermArray(DCL_PERM_VIEW, $obj->ticketid), $g_oSec->PermArray(DCL_PERM_VIEWACCOUNT, $obj->ticketid), $g_oSec->PermArray(DCL_PERM_VIEWSUBMITTED, $obj->ticketid)))))
			throw new PermissionDeniedException();

		$oSmarty = new DCL_Smarty();

		$oSmarty->assign('IS_DELETE', $forDelete);
		$oSmarty->assign('VAL_EDITRESID', $editResID);

		$oSmarty->assign('VAL_TICKETID', $obj->ticketid);
		$oSmarty->assign('VAL_WIKITYPE', DCL_ENTITY_TICKET);
		$oSmarty->assign('VAL_WATCHTYPE', '5');

		$oMeta = new DCL_MetadataDisplay();
		$oSmarty->assign('VAL_CREATEDBY', $oMeta->GetPersonnel($obj->createdby));
		$oSmarty->assign('VAL_CLOSEDBY', $oMeta->GetPersonnel($obj->closedby));
		$oSmarty->assign('VAL_RESPONSIBLE', $oMeta->GetPersonnel($obj->responsible));
		$oSmarty->assign('VAL_STATUS', $oMeta->GetStatus($obj->status));
		$oSmarty->assign('VAL_PRIORITY', $oMeta->GetPriority($obj->priority));
		$oSmarty->assign('VAL_TYPE', $oMeta->GetSeverity($obj->type));
		$oSmarty->assign('VAL_PRODUCT', $oMeta->GetProduct($obj->product));
		$oSmarty->assign('VAL_MODULE', $oMeta->GetModule($obj->module_id));

		$aOrg = $oMeta->GetOrganization($obj->account);
		$oSmarty->assign('VAL_ORGID', $obj->account);
		$oSmarty->assign('VAL_ACCOUNT', $aOrg['name']);

		$aContact = $oMeta->GetContact($obj->contact_id);
		$oSmarty->assign('VAL_CONTACTID', $obj->contact_id);
		$oSmarty->assign('VAL_CONTACT', $aContact['name']);
		$oSmarty->assign('VAL_CONTACTPHONE', $aContact['phone']);
		$oSmarty->assign('VAL_CONTACTEMAIL', $aContact['email']);

		$oSmarty->assign('VAL_VERSION', $obj->version);
		$oSmarty->assign('VAL_HOURSTEXT', $obj->GetHoursText());
		$oSmarty->assign('VAL_CREATEDON', $obj->createdon);
		$oSmarty->assign('VAL_STATUSON', $obj->statuson);
		$oSmarty->assign('VAL_LASTACTIONON', $obj->lastactionon);
		$oSmarty->assign('VAL_SUMMARY', $obj->summary);
		$oSmarty->assign('VAL_ISSUE', $obj->issue);
		$oSmarty->assign('VAL_PUBLIC', $obj->is_public == 'Y' ? STR_CMMN_YES : STR_CMMN_NO);
		$oSmarty->assign('VAL_TAGS', str_replace(',', ', ', $oMeta->GetTags(DCL_ENTITY_TICKET, $obj->ticketid)));

		if ($obj->entity_source_id != '' && $obj->entity_source_id > 0)
		{
			$oSource = new dbEntitySource();
			$oSource->Load($obj->entity_source_id);
			$oSmarty->assign('VAL_SOURCE', $oSource->entity_source_name);
		}

		if ($oMeta->oStatus->GetStatusType($obj->status) == 2)
			$oSmarty->assign('VAL_CLOSEDON', $obj->closedon);

		if ($g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_VIEWFILE))
		{
			$oAttachments = new boFile();
			$oSmarty->assign('VAL_ATTACHMENTS', $oAttachments->GetAttachments(DCL_ENTITY_TICKET, $obj->ticketid));
		}

		$oTR = new dbTicketresolutions();
		$oSmarty->assign('VAL_RESOLUTIONS', $oTR->GetResolutionsArray($obj->ticketid));

		$oSmarty->assign('PERM_ACTION', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ACTION));
		$oSmarty->assign('PERM_ASSIGN', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ASSIGN));
		$oSmarty->assign('PERM_COPYTOWO', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_COPYTOWO));
		$oSmarty->assign('PERM_MODIFY', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_MODIFY));
		$oSmarty->assign('PERM_DELETE', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_DELETE));
		$oSmarty->assign('PERM_ATTACHFILE', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ATTACHFILE));
		$oSmarty->assign('PERM_REMOVEFILE', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_REMOVEFILE));
		$oSmarty->assign('PERM_VIEW', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_VIEW));
		$oSmarty->assign('PERM_VIEWWIKI', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_VIEWWIKI));
		$oSmarty->assign('PERM_VIEWORG', $g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_VIEW));
		$oSmarty->assign('PERM_VIEWCONTACT', $g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_VIEW));
		$oSmarty->assign('PERM_AUDIT', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_AUDIT));
		$oSmarty->assign('PERM_MODIFY_TR', $g_oSec->HasPerm(DCL_ENTITY_RESOLUTION, DCL_PERM_MODIFY));
		$oSmarty->assign('PERM_DELETE_TR', $g_oSec->HasPerm(DCL_ENTITY_RESOLUTION, DCL_PERM_DELETE));
		$oSmarty->assign('PERM_PUBLICONLY', $g_oSec->IsPublicUser());

		if ($g_oSec->IsPublicUser())
			$oSmarty->Render('htmlTicketDetailPublic.tpl');
		else
			$oSmarty->Render('htmlTicketDetail.tpl');
	}

	function Download()
	{
		global $dcl_info, $g_oSec;

		if (($id = DCL_Sanitize::ToInt($_REQUEST['ticketid'])) === null ||
			!DCL_Sanitize::IsValidFileName($_REQUEST['filename'])
			)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_VIEW, $id))
			throw new PermissionDeniedException();

		$o = new boFile();
		$o->iType = DCL_ENTITY_TICKET;
		$o->iKey1 = $id;
		$o->sFileName = $_REQUEST['filename'];
		$o->sRoot = $dcl_info['DCL_FILE_PATH'] . '/attachments';
		$o->Download();
	}
}
