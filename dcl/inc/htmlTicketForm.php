<?php
/*
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

class htmlTicketForm
{
	function Show($obj = '')
	{
		global $dcl_info, $g_oSec, $dcl_preferences;

		$isEdit = is_object($obj);
		if (!$g_oSec->HasPerm(DCL_ENTITY_TICKET, $isEdit ? DCL_PERM_MODIFY : DCL_PERM_ADD, $isEdit ? $obj->ticketid : 0))
			throw new PermissionDeniedException();

		$oSmarty = new SmartyHelper();
		$objJS = new AttributeSetJsHelper();

		if (!$isEdit)
			$objJS->bActiveOnly = true;

		$objJS->bPriorities = true;
		$objJS->bSeverities = true;
		$objJS->bModules = true;
		$objJS->forWhat = DCL_ENTITY_TICKET;
		$objJS->DisplayAttributeScript();

		$oSmarty->assign('VAL_FORMACTION', menuLink());
		$oSmarty->assign('VAL_JSDATEFORMAT', GetJSDateFormat());
		$oSmarty->assign('PERM_ACTION', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ACTION));
		$oSmarty->assign('PERM_ASSIGNWO', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ASSIGN));
		$oSmarty->assign('PERM_ATTACHFILE', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ATTACHFILE) && $dcl_info['DCL_MAX_UPLOAD_FILE_SIZE'] > 0 && !$isEdit);
		$oSmarty->assign('PERM_ISPUBLIC', $g_oSec->IsPublicUser());
		$oSmarty->assign('VAL_MAXUPLOADFILESIZE', $dcl_info['DCL_MAX_UPLOAD_FILE_SIZE']);
		$oSmarty->assign('VAL_ISEDIT', $isEdit);
		$oSmarty->assign('VAL_DCLID', $GLOBALS['DCLID']);
		$oSmarty->assign('VAL_NOTIFYDEFAULT', isset($dcl_preferences['DCL_PREF_NOTIFY_DEFAULT']) ? $dcl_preferences['DCL_PREF_NOTIFY_DEFAULT'] : 'N');

		$oMeta = new DisplayHelper();
		if ($isEdit)
		{
			$oProduct = new ProductModel();
			$oProduct->Query('SELECT tcksetid FROM products WHERE id=' . $obj->product);
			if ($oProduct->next_record())
				$oSmarty->assign('VAL_SETID', $oProduct->f(0));

			$oSmarty->assign('TXT_TITLE', sprintf(STR_TCK_EDITTITLE, $obj->ticketid));

			$oSmarty->assign('VAL_ISPUBLIC', $obj->is_public);
			$oSmarty->assign('VAL_SOURCE', $obj->entity_source_id);
			$oSmarty->assign('VAL_PRODUCT', $obj->product);
			$oSmarty->assign('VAL_MODULE', $obj->module_id);
			$oSmarty->assign('VAL_PRIORITY', $obj->priority);
			$oSmarty->assign('VAL_TYPE', $obj->type);

			$oSmarty->assign('VAL_VERSION', $obj->version);
			$oSmarty->assign('VAL_SUMMARY', $obj->summary);
			$oSmarty->assign('VAL_ISSUE', $obj->issue);

			$oSmarty->assign('VAL_CONTACTID', $obj->contact_id);
			if ($obj->contact_id > 0)
			{
				$oContact = new ContactModel();
				if ($oContact->Load(array('contact_id' => $obj->contact_id)) != -1)
					$oSmarty->assign('VAL_CONTACTNAME', sprintf('%s %s', $oContact->first_name, $oContact->last_name));
			}

			$oSmarty->assign('VAL_RESPONSIBLE', $obj->responsible);
			if ($obj->responsible == $GLOBALS['DCLID'])
			{
				$oSmarty->assign('VAL_RESPONSIBLENAME', $GLOBALS['DCLNAME']);
			}
			else
			{
				$oPersonnel = new PersonnelModel();
				if ($oPersonnel->Load($obj->responsible) != -1)
					$oSmarty->assign('VAL_RESPONSIBLENAME', $oPersonnel->short);
				else
					$oSmarty->assign('VAL_RESPONSIBLENAME', 'Unknown');
			}

			$organizationModel = new OrganizationModel();
			$organizationModel->ListSelectedByTicket($obj->ticketid);
			if ($organizationModel->next_record())
			{
				$oSmarty->assign('VAL_ORGID', $organizationModel->f('org_id'));
				$oSmarty->assign('VAL_ORGNAME', $organizationModel->f('name'));
			}

			$oSmarty->assign('VAL_MENUACTION', 'boTickets.dbmodify');
			$oSmarty->assign('VAL_TICKETID', $obj->ticketid);
			$oSmarty->assign('VAL_STATUS', $obj->status);
			
			$oTag = new EntityTagModel();
			$oSmarty->assign('VAL_TAGS', $oTag->getTagsForEntity(DCL_ENTITY_TICKET, $obj->ticketid));
		}
		else
		{
			$oSmarty->assign('TXT_TITLE', STR_TCK_ADDTITLE);
			$oSmarty->assign('VAL_ISPUBLIC', 'Y');
			$oSmarty->assign('VAL_RESPONSIBLE', $GLOBALS['DCLID']);
			$oSmarty->assign('VAL_RESPONSIBLENAME', $GLOBALS['DCLNAME']);

			$oSmarty->assign('VAL_MENUACTION', 'boTickets.dbadd');
			$oSmarty->assign('VAL_STARTEDON', date($dcl_info['DCL_TIMESTAMP_FORMAT']));
			$oSmarty->assign('VAL_STATUS', $dcl_info['DCL_DEFAULT_TICKET_STATUS']);
			
			if (($iOrgID = @Filter::ToInt($_REQUEST['org_id'])) !== null && $iOrgID > 0)
			{
				$aOrg =& $oMeta->GetOrganization($iOrgID);
				if (is_array($aOrg) && count($aOrg) > 0)
				{
					$oSmarty->assign('VAL_ORGID', $iOrgID);
					$oSmarty->assign('VAL_ORGNAME', $aOrg['name']);
				}
			}
			
			if (($iContactID = @Filter::ToInt($_REQUEST['contact_id'])) !== null && $iContactID > 0)
			{
				$aContact =& $oMeta->GetContact($iContactID);
				if (is_array($aContact) && count($aContact) > 1)
				{
					$oSmarty->assign('VAL_CONTACTID', $iContactID);
					$oSmarty->assign('VAL_CONTACTNAME', $aContact['name']);
				}
			}
		}

		$oSmarty->Render('TicketForm.tpl');
	}
}
