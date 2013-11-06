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

class htmlContact
{
	function show($orderBy = 'short')
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		commonHeader();

		$oView = new boView();
		$oView->startrow = 0;
		$oView->numrows = 25;

		$filterActive = '';
		if (IsSet($_REQUEST['filterActive']))
			$filterActive = Filter::ToYN($_REQUEST['filterActive']);

		$oView->table = 'dcl_contact';
		$oView->title = 'Browse Contacts';
		$oView->AddDef('columnhdrs', '', array('ID', 'Last Name', 'First Name', 'Phone', 'Email', 'Internet'));

		$oView->AddDef('columns', '', array('contact_id', 'last_name', 'first_name', 'dcl_contact_phone.phone_number', 'dcl_contact_email.email_addr', 'dcl_contact_url.url_addr'));

		$oView->AddDef('order', '', array('last_name', 'first_name'));

		if ($filterActive == 'Y' || $filterActive == 'N')
			$oView->AddDef('filter', 'active', "'$filterActive'");

		$oHtml = new htmlContactBrowse();
		$oHtml->Render($oView);
	}

	function viewWorkOrders()
	{
		commonHeader();
		
		if (($id = Filter::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$oMeta = new DisplayHelper();
		$aContact = $oMeta->GetContact($id);
		
		$objView = new boView();
		$objView->title = sprintf('%s Work Orders', $aContact['name']);
		$objView->style = 'report';
		$objView->table = 'workorders';
		$objView->AddDef('columns', '', array('jcn', 'seq', 'priorities.name', 'severities.name', 'responsible.short', 'deadlineon', 'summary'));
		$objView->AddDef('columnhdrs', '', array(
				STR_WO_STATUS,
				STR_WO_JCN,
				STR_WO_SEQ,
				STR_WO_PRIORITY,
				STR_WO_SEVERITY,
				STR_WO_RESPONSIBLE,
				STR_WO_DEADLINE,
				STR_WO_SUMMARY));

		$objView->AddDef('filter', 'contact_id', $id);
		$objView->AddDef('filter', 'dcl_status_type.dcl_status_type_id', array(1, 3));
		$objView->AddDef('order', '', array('jcn', 'seq'));
		$objView->AddDef('groups', '', array('statuses.name'));

		$presenter = new WorkOrderPresenter();
		$presenter->DisplayView($objView);
	}
	
	function viewTickets()
	{
		global $g_oSec;
		
		commonHeader();
		
		if (!$g_oSec->HasAnyPerm(array(DCL_ENTITY_TICKET => array($g_oSec->PermArray(DCL_PERM_VIEW), $g_oSec->PermArray(DCL_PERM_VIEWACCOUNT), $g_oSec->PermArray(DCL_PERM_VIEWSUBMITTED)))))
		{
			throw new PermissionDeniedException();
		}
		
		if (($id = Filter::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}

		$oMeta = new DisplayHelper();
		$aContact = $oMeta->GetContact($id);
		
		$objView = new boView();
		$objView->title = sprintf('%s Tickets', $aContact['name']);
		$objView->style = 'report';
		$objView->table = 'tickets';
		
		if ($g_oSec->IsPublicUser())
		{
			$objView->AddDef('filter', 'is_public', "'Y'");
			$objView->AddDef('columns', '', array('ticketid', 'statuses.name', 'priorities.name', 'severities.name', 'summary'));
			$objView->AddDef('columnhdrs', '', array(
					STR_TCK_STATUS,
					STR_TCK_TICKET . '#',
					STR_TCK_STATUS,
					STR_TCK_PRIORITY,
					STR_TCK_TYPE,
					STR_TCK_SUMMARY));
		}
		else
		{
			$objView->AddDef('columns', '', array('ticketid', 'statuses.name', 'priorities.name', 'severities.name', 'responsible.short', 'summary'));
			$objView->AddDef('columnhdrs', '', array(
					STR_TCK_STATUS,
					STR_TCK_TICKET . '#',
					STR_TCK_STATUS,
					STR_TCK_PRIORITY,
					STR_TCK_TYPE,
					STR_TCK_RESPONSIBLE,
					STR_TCK_SUMMARY));
		}
		
		$objView->AddDef('filter', 'contact_id', $id);
		$objView->AddDef('filter', 'dcl_status_type.dcl_status_type_id', array(1, 3));
		$objView->AddDef('order', '', array('ticketid'));
		$objView->AddDef('groups', '', array('statuses.name'));

		$objHV = new htmlTicketResults();
		$objHV->bShowPager = false;
		$objHV->Render($objView);
	}
	
	function merge()
	{
		global $g_oSec;
		
		commonHeader();
		if (($contact_id = Filter::ToIntArray($_REQUEST['contact_id'])) === null)
		{
			throw new InvalidDataException();
		}

		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$oMeta = new DisplayHelper();
		$oSmarty = new SmartyHelper();
		if (count($contact_id) > 1)
		{
			$aContacts = $contact_id;
			$iIndex = 0;
			$sID = "";
			foreach ($aContacts as $iContactID)
			{
				$aContacts[$iIndex] = $oMeta->GetContact($iContactID);
				$aContacts[$iIndex]['contact_id'] = $iContactID;
				if ($iIndex > 0)
					$sID += ",";
					
				$sID += (string)$iContactID;
				$iIndex++;
			}
			
			$oSmarty->assign('VAL_MERGECONTACTID', join(',', $contact_id));
			$oSmarty->assign('VAL_CONTACTS', $aContacts);
		}
		else
		{
			$contact_id = $contact_id[0];
			$aContact =& $oMeta->GetContact($contact_id);
			
			$oSmarty->assign('VAL_CONTACTID', $contact_id);
			$oSmarty->assign_by_ref('VAL_CONTACT', $aContact);
		}

		global $g_oSession;
		$aLastContactBrowsePage = $g_oSession->Value('LAST_CONTACT_BROWSE_PAGE');
		if ($aLastContactBrowsePage !== null && is_array($aLastContactBrowsePage))
		{
			$oSmarty->assign_by_ref('VAL_LASTPAGE', $aLastContactBrowsePage);
		}
		
		$oSmarty->Render('ContactMerge.tpl');
	}
	
	function doMerge()
	{
		global $g_oSec;
		
		commonHeader();
		if (($iContactID = Filter::ToInt($_REQUEST['contact_id'])) === null ||
			($aMergeContacts = Filter::ToIntArray($_REQUEST['merge_contact_id'])) === null
			)
		{
			throw new InvalidDataException();
		}

		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();
			
		foreach ($aMergeContacts as $key => $value)
		{
			$aMergeContacts[$key] = (int)$value;
			if ($aMergeContacts[$key] == $iContactID)
				unset($aMergeContacts[$key]);
		}
			
		if (count($aMergeContacts) > 0)
		{
			$sMergeContacts = join($aMergeContacts, ',');

			// Merge orgs
			$sSQL = 'SELECT org_id FROM dcl_org_contact WHERE contact_id IN (' . $sMergeContacts . ')';
			
			$oDB = new OrganizationContactModel();
			$oDB2 = new OrganizationContactModel();
			
			$oDB->BeginTransaction();
			if ($oDB->Query($sSQL) == -1)
			{
				$oDB->RollbackTransaction();
				return;
			}
				
			while ($oDB->next_record())
			{
				$sSQL = 'SELECT 1 FROM dcl_org_contact WHERE contact_id = ' . $iContactID . ' AND org_id = ' . $oDB->f(0);
				if ($oDB2->Query($sSQL) == -1)
				{
					$oDB->RollbackTransaction();
					return;
				}
					
				if (!$oDB2->next_record())
				{
					$oDB2->contact_id = $iContactID;
					$oDB2->org_id = $oDB->f(0);
					$oDB2->created_on = DCL_NOW;
					$oDB2->created_by = DCLID;
					$oDB2->Add();
				}
			}

			if ($oDB2->Execute('DELETE FROM dcl_org_contact WHERE contact_id IN (' . $sMergeContacts . ')') == -1)
			{
				$oDB->RollbackTransaction();
				return;
			}

			// Merge phone numbers
			if ($oDB2->Execute("UPDATE dcl_contact_phone SET contact_id = $iContactID, preferred = 'N' WHERE contact_id IN ($sMergeContacts)") == -1)
			{
				$oDB->RollbackTransaction();
				return;
			}
			
			// Merge email addresses
			if ($oDB2->Execute("UPDATE dcl_contact_email SET contact_id = $iContactID, preferred = 'N' WHERE contact_id IN ($sMergeContacts)") == -1)
			{
				$oDB->RollbackTransaction();
				return;
			}

			// Merge addresses
			if ($oDB2->Execute("UPDATE dcl_contact_addr SET contact_id = $iContactID, preferred = 'N' WHERE contact_id IN ($sMergeContacts)") == -1)
			{
				$oDB->RollbackTransaction();
				return;
			}
			
			// Merge URLs
			if ($oDB2->Execute("UPDATE dcl_contact_url SET contact_id = $iContactID, preferred = 'N' WHERE contact_id IN ($sMergeContacts)") == -1)
			{
				$oDB->RollbackTransaction();
				return;
			}

			// Merge notes
			//$oDB2->Execute('UPDATE dcl_contact_notes SET contact_id = ' . $iContactID . ' WHERE contact_id IN (' . $sMergeContacts . ')');

			// Merge work orders
			if ($oDB2->Execute('UPDATE workorders SET contact_id = ' . $iContactID . ' WHERE contact_id IN (' . $sMergeContacts . ')') == -1)
			{
				$oDB->RollbackTransaction();
				return;
			}
			
			// Merge tickets
			if ($oDB2->Execute('UPDATE tickets SET contact_id = ' . $iContactID . ' WHERE contact_id IN (' . $sMergeContacts . ')') == -1)
			{
				$oDB->RollbackTransaction();
				return;
			}

			// Merge users
			if ($oDB2->Execute('UPDATE personnel SET contact_id = ' . $iContactID . ' WHERE contact_id IN (' . $sMergeContacts . ')') == -1)
			{
				$oDB->RollbackTransaction();
				return;
			}

			if ($oDB2->Execute('DELETE FROM dcl_contact WHERE contact_id IN (' . $sMergeContacts . ')') == -1)
			{
				$oDB->RollbackTransaction();
				return;
			}
			
			$oDB->EndTransaction();
		}

		if (isset($_REQUEST['chainMenuAction']) && $_REQUEST['chainMenuAction'] == 'htmlContactBrowse.Page')
		{
			$oCD = new htmlContactBrowse();
			$oCD->Page();
		}
		else
		{
			$oDetail = new htmlContactDetail();
			$oDetail->show();
		}
	}
}
