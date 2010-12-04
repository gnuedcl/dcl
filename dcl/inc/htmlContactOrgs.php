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

class htmlContactOrgs
{
	var $public;

	function htmlContactOrgs()
	{
		$this->public = array('modify', 'submitModify');
	}

	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		
		if (($id = DCL_Sanitize::ToInt($_REQUEST['contact_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY, $id))
			return PrintPermissionDenied();

		$oContact = new dbContact();
		if ($oContact->Load($id) == -1)
		    return;
		    
		// Get orgs for this contact
		$oViewOrg = new boView();
		$oViewOrg->table = 'dcl_org';
		$oViewOrg->AddDef('columnhdrs', '', array(STR_CMMN_ID, STR_CMMN_NAME));
		$oViewOrg->AddDef('columns', '', array('org_id', 'name'));
		$oViewOrg->AddDef('order', '', array('name'));
		$oViewOrg->AddDef('filter', 'dcl_org_contact.contact_id', $id);

		$aOrgs = array();
		$aOrgNames = array();
		
		$oOrgs = new dbOrg();
		if ($oOrgs->Query($oViewOrg->GetSQL()) != -1)
		{
			while ($oOrgs->next_record())
			{
				$aOrgs[] = $oOrgs->f(0);
				$aOrgNames[] = $oOrgs->f(1);
			}

			$oOrgs->FreeResult();
		}

		$this->ShowEntryForm($oContact, $aOrgs, $aOrgNames);
	}

	function submitModify()
	{
		global $g_oSec;
		
		commonHeader();
		
		if (($id = DCL_Sanitize::ToInt($_REQUEST['contact_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY, $id))
			return PrintPermissionDenied();

		CleanArray($_REQUEST);

		$aOrgs = @DCL_Sanitize::ToIntArray($_REQUEST['org_id']);
		$oDbContact = new dbOrgContact();
		$oDbContact->updateOrgs($id, $aOrgs);

		$oContact = new htmlContactDetail();
		$oContact->show();
	}

	function ShowEntryForm(&$oContact, &$aOrgID, &$aOrgName)
	{
		global $dcl_info, $g_oSec;

		$oSmarty = new DCL_Smarty();
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY, $oContact->contact_id))
			return PrintPermissionDenied();
			
		$oSmarty->assign('TXT_TITLE', 'Edit Contact Organizations');
		$oSmarty->assign('VAL_MENUACTION', 'htmlContactOrgs.submitModify');
		$oSmarty->assign('VAL_CONTACTID', $oContact->contact_id);
		$oSmarty->assign('URL_BACK', menuLink('', 'menuAction=htmlContactDetail.show&contact_id=' . $oContact->contact_id));			
		$oSmarty->assign('VAL_FIRSTNAME', $oContact->first_name);
		$oSmarty->assign('VAL_MIDDLENAME', $oContact->middle_name);
		$oSmarty->assign('VAL_LASTNAME', $oContact->last_name);

		$oSmarty->assign_by_ref('VAL_ORGID', $aOrgID);
		$oSmarty->assign_by_ref('VAL_ORGNAME', $aOrgName);

		$oSmarty->Render('htmlContactOrgs.tpl');
	}
}
