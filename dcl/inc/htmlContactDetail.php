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
LoadStringResource('wo');
class htmlContactDetail
{
	var $public;

	function htmlContactDetail()
	{
		$this->public = array('show');
	}

	function show()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		
		if (($id = DCL_Sanitize::ToInt($_REQUEST['contact_id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_VIEW, $id))
			throw new PermissionDeniedException();

		$oContact = new dbContact();
		if ($oContact->Load((int)$_REQUEST['contact_id']) == -1)
		{
			trigger_error('Could not load contact ID [' . $id . ']', E_USER_ERROR);
			return;
		}

		$t = new DCL_Smarty();
		$t->register_object('Contact', $oContact);

		$t->assign('PERM_MODIFY', $g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY));
		$t->assign('PERM_DELETE', $g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_DELETE));
		$t->assign('PERM_VIEW', $g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_VIEW));
		$t->assign('VAL_TODAY', mktime(0, 0, 0, date('m'), date('j'), date('Y')));

		// Get types for this contact
		$oContactType = new ContactTypeModel();
		$oContactType->ListByContact($oContact->contact_id);
		$aTypes = array();
		while ($oContactType->next_record())
		{
			$aTypes[] = $oContactType->Record;
		}

		$t->assign_by_ref('ContactType', $aTypes);
		$oContactType->FreeResult();

		// Get addresses
		$oContactAddress = new dbContactAddr();
		$oContactAddress->ListByContact($oContact->contact_id);
		$aAddresses = array();
		while ($oContactAddress->next_record())
		{
			$aAddresses[] = $oContactAddress->Record;
		}

		$t->assign_by_ref('ContactAddress', $aAddresses);
		$oContactAddress->FreeResult();

		// Get phone numbers
		$oContactPhone = new dbContactPhone();
		$oContactPhone->ListByContact($oContact->contact_id);
		$aPhoneNumbers = array();
		while ($oContactPhone->next_record())
		{
			$aPhoneNumbers[] = $oContactPhone->Record;
		}

		$t->assign_by_ref('ContactPhone', $aPhoneNumbers);
		$oContactPhone->FreeResult();

		// Get e-mail addresses
		$oContactEmail = new dbContactEmail();
		$oContactEmail->ListByContact($oContact->contact_id);
		$aEmails = array();
		while ($oContactEmail->next_record())
		{
			$aEmails[] = $oContactEmail->Record;
		}

		$t->assign_by_ref('ContactEmail', $aEmails);
		$oContactEmail->FreeResult();

		// Get e-mail addresses
		$oContactLicenses = new dbContactLicense();
		$oContactLicenses->ListByContact($oContact->contact_id);
		$aLicenses = array();
		while ($oContactLicenses->next_record())
		{
		    $oContactLicenses->objDate->SetFromDB($oContactLicenses->Record['expires_on']);
		    $oContactLicenses->Record['val_expires_on'] = $oContactLicenses->objDate->time;
			$aLicenses[] = $oContactLicenses->Record;
		}

		$t->assign_by_ref('ContactLicense', $aLicenses);
		$oContactLicenses->FreeResult();

		// Get URLs
		$oContactURL = new dbContactUrl();
		$oContactURL->ListByContact($oContact->contact_id);
		$aURL = array();
		while ($oContactURL->next_record())
		{
			$aURL[] = $oContactURL->Record;
		}

		$t->assign_by_ref('ContactURL', $aURL);
		$oContactURL->FreeResult();
		
		// Get orgs for this contact
		$oViewOrg = new boView();
		$oViewOrg->table = 'dcl_org';
		$oViewOrg->AddDef('columnhdrs', '', array(STR_CMMN_ID, STR_CMMN_NAME, 'Phone', 'Email', 'Internet'));
		$oViewOrg->AddDef('columns', '', array('org_id', 'name', 'dcl_org_phone.phone_number', 'dcl_org_email.email_addr', 'dcl_org_url.url_addr'));
		$oViewOrg->AddDef('order', '', array('name'));
		$oViewOrg->AddDef('filter', 'dcl_org_contact.contact_id', $oContact->contact_id);
		//$oViewContact->AddDef('filter', 'active', "'Y'");

		$oOrgs = new dbOrg();
		if ($oOrgs->Query($oViewOrg->GetSQL()) != -1)
		{
			$aOrgs = array();
			while ($oOrgs->next_record())
			{
				$aData = array();
				for ($i = 0; $i < count($oViewOrg->columns); $i++)
					array_push($aData, $oOrgs->f($i));
					
				$aData['org_id'] = $oOrgs->f('org_id');
				array_push($aOrgs, $aData);
			}

			$oOrgs->FreeResult();

			$t->assign_by_ref('ViewOrg', $oViewOrg);
			$t->assign_by_ref('Orgs', $aOrgs);
		}

		// Get last 10 tickets
		$oViewTicket = new boView();
		$oViewTicket->title = 'Last 10 Tickets';
		$oViewTicket->style = 'report';
		$oViewTicket->table = 'tickets';
		$oViewTicket->AddDef('columns', '', array('ticketid', 'products.name', 'statuses.name', 'priorities.name', 'severities.name', 'responsible.short', 'summary'));
		$oViewTicket->AddDef('columnhdrs', '', array(
				STR_TCK_TICKET . '#',
				STR_TCK_PRODUCT,
				STR_TCK_STATUS,
				STR_TCK_PRIORITY,
				STR_TCK_TYPE,
				STR_TCK_RESPONSIBLE,
				STR_TCK_SUMMARY));

		$oViewTicket->AddDef('filter', 'contact_id', $oContact->contact_id);
		$oViewTicket->AddDef('order', '', array('createdon DESC'));
		$oViewTicket->numrows = 10;
		
		$oTickets = new dbTickets();
		$oTickets->LimitQuery($oViewTicket->GetSQL(), 0, $oViewTicket->numrows);
		$aTickets = array();
		while ($oTickets->next_record())
		{
			$aData = array();
			for ($i = 0; $i < count($oViewTicket->columns); $i++)
				array_push($aData, $oTickets->f($i));
				
			$aData['ticketid'] = $oTickets->f('ticketid');
			array_push($aTickets, $aData);
		}

		$t->assign_by_ref('ViewTicket', $oViewTicket);
		$t->assign_by_ref('Tickets', $aTickets);
		$oTickets->FreeResult();

		// Get last 10 work orders
		$oViewWO = new boView();
		$oViewWO->title = 'Last 10 Work Orders';
		$oViewWO->style = 'report';
		$oViewWO->table = 'workorders';
		$oViewWO->AddDef('columns', '', array('jcn', 'seq', 'products.name', 'statuses.name', 'priorities.name', 'severities.name', 'responsible.short', 'deadlineon', 'summary'));
		$oViewWO->AddDef('columnhdrs', '', array(
				STR_WO_JCN,
				STR_WO_SEQ,
				STR_WO_PRODUCT,
				STR_WO_STATUS,
				STR_WO_PRIORITY,
				STR_WO_SEVERITY,
				STR_WO_RESPONSIBLE,
				STR_WO_DEADLINE,
				STR_WO_SUMMARY));

				
		$oViewWO->AddDef('filter', 'contact_id', $oContact->contact_id);
		$oViewWO->AddDef('order', '', array('createdon DESC'));
		$oViewWO->numrows = 10;
		
		$oWO = new dbWorkorders();
		$oWO->LimitQuery($oViewWO->GetSQL(), 0, $oViewWO->numrows);
		$aWO = array();
		while ($oWO->next_record())
		{
			$aData = array();
			for ($i = 0; $i < count($oViewWO->columns); $i++)
				array_push($aData, $oWO->f($i));
				
			$aData['jcn'] = $oWO->f('jcn');
			$aData['seq'] = $oWO->f('seq');

			array_push($aWO, $aData);
		}

		$t->assign_by_ref('ViewWorkOrder', $oViewWO);
		$t->assign_by_ref('WorkOrders', $aWO);
		$oWO->FreeResult();

		$t->Render('htmlContactDetail.tpl');
	}
}
