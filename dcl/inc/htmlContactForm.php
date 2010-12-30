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

LoadStringResource('wo');
LoadStringResource('cfg');

class htmlContactForm
{
	var $public;

	function htmlContactForm()
	{
		$this->public = array('add', 'modify', 'delete', 'submitAdd', 'submitModify', 'submitDelete');
	}

	function add()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$this->ShowEntryForm();
	}

	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		
		if (($id = DCL_Sanitize::ToInt($_REQUEST['contact_id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY, $id))
			throw new PermissionDeniedException();

		$obj = new dbContact();
		if ($obj->Load($id) == -1)
		    return;
		    
		$this->ShowEntryForm($obj);
	}

	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (($id = DCL_Sanitize::ToInt($_REQUEST['contact_id'])) === null)
		{
			throw new InvalidDataException();
		}

		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_DELETE, $id))
			throw new PermissionDeniedException();

		$obj = new dbContact();
		if ($obj->Load($id) == -1)
		    return;
		    
		ShowDeleteYesNo('Contact', 'htmlContactForm.submitDelete', $obj->contact_id, $obj->last_name . ', ' . $obj->first_name);
	}

	function submitAdd()
	{
		global $dcl_info, $g_oSec;

		// We actually have to potentially add several things here for a new contact
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		CleanArray($_REQUEST);

		$obj = new boContact();
		$iContactID = $obj->add(array(
								'first_name' => $_REQUEST['first_name'],
								'middle_name' => $_REQUEST['middle_name'],
								'last_name' => $_REQUEST['last_name'],
								'active' => 'Y',
								'created_on' => DCL_NOW,
								'created_by' => $GLOBALS['DCLID']
							)
						);

		if ($iContactID == -1)
		{
			// TODO: redisplay in case it can be resubmitted
			$this->ShowEntryForm();
			return;
		}

		$aContactTypes = @DCL_Sanitize::ToIntArray($_REQUEST['contact_type_id']);
		if ($aContactTypes !== null)
		{
			$oContactTypeXref = new boContactTypeXref();
			$oContactTypeXref->PermAdd = DCL_PERM_ADD;
			foreach ($aContactTypes as $iTypeID)
			{
				$oContactTypeXref->add(array('contact_id' => $iContactID, 'contact_type_id' => $iTypeID));
			}
		}

		$org_id = DCL_Sanitize::ToInt($_REQUEST['org_id']);
		if ($org_id > 0)
		{
			$oOrgContact = new boOrgContact();
			$oOrgContact->add(array(
								'org_id' => $org_id,
								'contact_id' => $iContactID,
								'created_on' => DCL_NOW,
								'created_by' => $GLOBALS['DCLID']
							)
						);
		}

		$addr_type_id = DCL_Sanitize::ToInt($_REQUEST['addr_type_id']);
		if ($addr_type_id > 0)
		{
			$contactAddressModel = new ContactAddressModel();
			$contactAddressModel->contact_id = $iContactID;
			$contactAddressModel->addr_type_id = $addr_type_id;
			$contactAddressModel->add1 = $_REQUEST['add1'];
			$contactAddressModel->add2 = $_REQUEST['add2'];
			$contactAddressModel->city = $_REQUEST['city'];
			$contactAddressModel->state = $_REQUEST['state'];
			$contactAddressModel->zip = $_REQUEST['zip'];
			$contactAddressModel->country = $_REQUEST['country'];
			$contactAddressModel->preferred = 'Y';
			$contactAddressModel->created_on = DCL_NOW;
			$contactAddressModel->created_by = $GLOBALS['DCLID'];
			$contactAddressModel->Add();
		}

		$phone_type_id = DCL_Sanitize::ToInt($_REQUEST['phone_type_id']);
		if ($phone_type_id > 0 && $_REQUEST['phone_number'] != '')
		{
			$oContactPhone = new boContactPhone();
			$oContactPhone->add(array(
								'contact_id' => $iContactID,
								'phone_type_id' => $phone_type_id,
								'phone_number' => $_REQUEST['phone_number'],
								'preferred' => 'Y',
								'created_on' => DCL_NOW,
								'created_by' => $GLOBALS['DCLID']
							)
						);
		}

		$email_type_id = DCL_Sanitize::ToInt($_REQUEST['email_type_id']);
		if ($email_type_id > 0 && $_REQUEST['email_addr'] != '')
		{
			$oContactEmail = new boContactEmail();
			$oContactEmail->add(array(
								'contact_id' => $iContactID,
								'email_type_id' => $email_type_id,
								'email_addr' => $_REQUEST['email_addr'],
								'preferred' => 'Y',
								'created_on' => DCL_NOW,
								'created_by' => $GLOBALS['DCLID']
							)
						);
		}

		$url_type_id = DCL_Sanitize::ToInt($_REQUEST['url_type_id']);
		if ($_POST['url_type_id'] > 0 && $_REQUEST['url_addr'] != '')
		{
			$oContactUrl = new boContactUrl();
			$oContactUrl->add(array(
								'contact_id' => $iContactID,
								'url_type_id' => $url_type_id,
								'url_addr' => $_REQUEST['url_addr'],
								'preferred' => 'Y',
								'created_on' => DCL_NOW,
								'created_by' => $GLOBALS['DCLID']
							)
						);
		}

		if (isset($_REQUEST['fromBrowse']) && $_REQUEST['fromBrowse'] == 'true')
		{
			$_REQUEST['return_to'] = 'menuAction=htmlContactSelector.showBrowseFrame&filterActive=S&filterID=' . $iContactID . '&updateTop=true';
		}

		if (EvaluateReturnTo())
			return;
			
		$this->ShowEntryForm();
	}

	function submitModify()
	{
		global $g_oSec;
		
		commonHeader();
		
		if (($id = DCL_Sanitize::ToInt($_REQUEST['contact_id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY, $id))
			throw new PermissionDeniedException();

		$obj = new boContact();
		CleanArray($_REQUEST);

		$aValues = array('contact_id' => DCL_Sanitize::ToInt($_REQUEST['contact_id']),
						'first_name' => $_REQUEST['first_name'],
						'middle_name' => $_REQUEST['middle_name'],
						'last_name' => $_REQUEST['last_name'],
						'contact_type_id' => DCL_Sanitize::ToIntArray($_REQUEST['contact_type_id']),
						'active' => 'Y'
						);
						
		if (!isset($_REQUEST['active']) || $_REQUEST['active'] != 'Y')
			$aValues['active'] = 'N';

		$obj->modify($aValues);

		$oContact = new htmlContactDetail();
		$oContact->show();
	}

	function submitDelete()
	{
		global $g_oSec;
		
		commonHeader();
		if (($id = DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}

		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_DELETE, $id))
			throw new PermissionDeniedException();

		$obj = new boContact();
		CleanArray($_REQUEST);

		$aKey = array('contact_id' => $id);
		$obj->delete($aKey);

		$oContact = new htmlContactBrowse();
		$oContact->show();
	}

	function ShowEntryForm($obj = '')
	{
		global $dcl_info, $g_oSec;

		$isEdit = is_object($obj);
		$oSmarty = new DCL_Smarty();
		if (isset($_REQUEST['return_to']))
		{
			$oSmarty->assign('return_to', $_REQUEST['return_to']);
			$oSmarty->assign('URL_BACK', menuLink('', $_REQUEST['return_to']));
		}

		if (isset($_REQUEST['fromBrowse']))
		{
			$oSmarty->assign('fromBrowse', $_REQUEST['fromBrowse']);
		}
		
		if (!$isEdit)
		{
			if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_ADD))
				throw new PermissionDeniedException();

			if (!isset($_REQUEST['return_to']))
				$oSmarty->assign('URL_BACK', menuLink('', 'menuAction=htmlContactBrowse.show&filterActive=Y'));
				
			if (isset($_REQUEST['hideMenu']))
				$oSmarty->assign('hideMenu', $_REQUEST['hideMenu']);

			$oContactType = new ContactTypeModel();
			$oSmarty->assign('contactTypes', $oContactType->GetTypes());

			$oSmarty->Render('htmlNewContactForm.tpl');

			return;
		}

		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY, $obj->contact_id))
			throw new PermissionDeniedException();
			
		if (!isset($_REQUEST['return_to']))
			$oSmarty->assign('URL_BACK', menuLink('', 'menuAction=htmlContactDetail.show&contact_id=' . $obj->contact_id));
			
		$oSmarty->assign('TXT_FUNCTION', 'Edit Contact');
		$oSmarty->assign('VAL_MENUACTION', 'htmlContactForm.submitModify');
		$oSmarty->assign('VAL_CONTACTID', $obj->contact_id);
		$oSmarty->assign('VAL_ACTIVE', $obj->active);
		$oSmarty->assign('VAL_FIRSTNAME', $obj->first_name);
		$oSmarty->assign('VAL_MIDDLENAME', $obj->middle_name);
		$oSmarty->assign('VAL_LASTNAME', $obj->last_name);

		$oContactType = new ContactTypeModel();
		$oSmarty->assign('contactTypes', $oContactType->GetTypes($obj->contact_id));

		$oSmarty->Render('htmlContactForm.tpl');
	}
}
