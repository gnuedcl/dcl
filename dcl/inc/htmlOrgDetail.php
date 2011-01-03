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
class htmlOrgDetail
{
	var $public;

	function htmlOrgDetail()
	{
		$this->public = array('show');
	}

	function show()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (($id = DCL_Sanitize::ToInt($_REQUEST['org_id'])) === null)
		{
			throw new InvalidDataException();
		}

		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_VIEW, $id))
			throw new PermissionDeniedException();

		$oOrg = new dbOrg();
		if ($oOrg->Load($id) == -1)
		{
			trigger_error('Could not load organization ID [' . $id . ']', E_USER_ERROR);
			return;
		}

		$t = new DCL_Smarty();
		$t->register_object('Org', $oOrg);

		$t->assign('PERM_MODIFY', $g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY));
		$t->assign('PERM_DELETE', $g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_DELETE));
		$t->assign('PERM_VIEW', $g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_VIEW));

		// Get aliases for this org
		$oOrgAlias = new OrganizationAliasModel();
		$oOrgAlias->ListByOrg($oOrg->org_id);
		$aAliases = array();
		while ($oOrgAlias->next_record())
		{
			$aAliases[] = $oOrgAlias->Record;
		}

		$t->assign_by_ref('OrgAlias', $aAliases);
		$oOrgAlias->FreeResult();

		// Get types for this org
		$oOrgType = new OrganizationTypeModel();
		$oOrgType->ListByOrg($oOrg->org_id);
		$aTypes = array();
		while ($oOrgType->next_record())
		{
			$aTypes[] = $oOrgType->Record;
		}

		$t->assign_by_ref('OrgType', $aTypes);
		$oOrgType->FreeResult();

		// Get products for this org
		$oOrgProduct = new dbOrgProduct();
		$oOrgProduct->ListByOrg($oOrg->org_id);
		$aProducts = array();
		while ($oOrgProduct->next_record())
		{
			$aProducts[] = $oOrgProduct->Record;
		}

		$t->assign_by_ref('OrgProduct', $aProducts);
		$oOrgProduct->FreeResult();

		// Get addresses
		$oOrgAddress = new OrganizationAddressModel();
		$oOrgAddress->ListByOrg($oOrg->org_id);
		$aAddresses = array();
		while ($oOrgAddress->next_record())
		{
			$aAddresses[] = $oOrgAddress->Record;
		}

		$t->assign_by_ref('OrgAddress', $aAddresses);
		$oOrgAddress->FreeResult();

		// Get phone numbers
		$oOrgPhone = new dbOrgPhone();
		$oOrgPhone->ListByOrg($oOrg->org_id);
		$aPhoneNumbers = array();
		while ($oOrgPhone->next_record())
		{
			$aPhoneNumbers[] = $oOrgPhone->Record;
		}

		$t->assign_by_ref('OrgPhone', $aPhoneNumbers);
		$oOrgPhone->FreeResult();

		// Get e-mail addresses
		$oOrgEmail = new OrganizationEmailModel();
		$oOrgEmail->ListByOrg($oOrg->org_id);
		$aEmails = array();
		while ($oOrgEmail->next_record())
		{
			$aEmails[] = $oOrgEmail->Record;
		}

		$t->assign_by_ref('OrgEmail', $aEmails);
		$oOrgEmail->FreeResult();

		// Get URLs
		$oOrgURL = new dbOrgUrl();
		$oOrgURL->ListByOrg($oOrg->org_id);
		$aURL = array();
		while ($oOrgURL->next_record())
		{
			$aURL[] = $oOrgURL->Record;
		}

		$t->assign_by_ref('OrgURL', $aURL);
		$oOrgURL->FreeResult();
		
		// Get main contacts
		$oOrgContacts = new dbOrg();
		$oOrgContacts->ListMainContacts($oOrg->org_id);
		$aContacts = array();
		$oMetadata = new DCL_MetadataDisplay();
		$oContactType = new ContactTypeModel();
		while ($oOrgContacts->next_record())
		{
			$aContact = $oMetadata->GetContact($oOrgContacts->f('contact_id'));
			$aRow = array('contact_id' => $oOrgContacts->f('contact_id'), 'name' => $aContact['name'], 'phone' => $aContact['phone'], 'email' => $aContact['email'], 'url' => $aContact['url']);
			
			$oContactType->ListByContact($oOrgContacts->f('contact_id'));
			$aContactTypes = array();
			while ($oContactType->next_record())
			{
				$aContactTypes[] = $oContactType->f('contact_type_name');
			}

			$aRow['type'] = join(', ', $aContactTypes);
			$oContactType->FreeResult();
			
			$aContacts[] = $aRow;
		}
		
		$t->assign_by_ref('OrgContacts', $aContacts);
		$oOrgContacts->FreeResult();

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

		$oViewTicket->AddDef('filter', 'account', $oOrg->org_id);
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

				
		$oViewWO->AddDef('filter', 'dcl_wo_account.account_id', $oOrg->org_id);
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

		$t->Render('htmlOrgDetail.tpl');
	}
}
