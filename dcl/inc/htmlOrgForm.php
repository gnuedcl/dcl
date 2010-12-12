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

class htmlOrgForm
{
	var $public;

	function htmlOrgForm()
	{
		$this->public = array('add', 'modify', 'delete', 'submitAdd', 'submitModify', 'submitDelete');
	}

	function add()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_ADD))
			throw new PermissionDeniedException();
			
		$this->ShowEntryForm();
	}

	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['org_id'])) === null)
		{
			throw new InvalidDataException();
		}

		$obj = new dbOrg();
		if ($obj->Load($id) == -1)
		    return;
		    
		$this->ShowEntryForm($obj);
	}

	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['org_id'])) === null)
		{
			throw new InvalidDataException();
		}

		$obj = new dbOrg();
		$obj->Load(array('org_id' => $id));
		ShowDeleteYesNo('Organization', 'htmlOrgForm.submitDelete', $obj->org_id, $obj->name);
	}

	function submitAdd()
	{
		global $dcl_info, $g_oSec;

		// We actually have to potentially add several things here for a new org
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		CleanArray($_REQUEST);

		$obj = new boOrg();
		$oOrgID = $obj->add(array(
								'name' => $_REQUEST['name'],
								'active' => 'Y',
								'created_on' => DCL_NOW,
								'created_by' => $GLOBALS['DCLID']
							)
						);

		if ($oOrgID == -1)
		{
			// TODO: redisplay in case it can be resubmitted
			$this->ShowEntryForm();
			return;
		}
		
		$aOrgTypes = @DCL_Sanitize::ToIntArray($_REQUEST['org_type_id']);
		if ($aOrgTypes !== null)
		{
			$oOrgTypeXref = new boOrgTypeXref();
			$oOrgTypeXref->PermAdd = DCL_PERM_ADD;
			foreach ($aOrgTypes as $iTypeID)
			{
				$oOrgTypeXref->add(array('org_id' => $oOrgID, 'org_type_id' => $iTypeID));
			}
		}

		// All of these are info in other tables, but they use the permissions of the org entity
		// So, we need to temporarily set the PermAdd to DCL_PERM_ADD so these will succeed.
		if ($_REQUEST['alias'] != '')
		{
			$oOrgAlias = new boOrgAlias();
			$oOrgAlias->PermAdd = DCL_PERM_ADD;
			$oOrgAlias->add(array(
							'org_id' => $oOrgID,
							'alias' => $_POST['alias'],
							'created_on' => DCL_NOW,
							'created_by' => $GLOBALS['DCLID']
							)
						);
		}

		$addr_type_id = DCL_Sanitize::ToInt($_REQUEST['addr_type_id']);
		if ($addr_type_id > 0)
		{
			$oOrgAddr = new boOrgAddr();
			$oOrgAddr->PermAdd = DCL_PERM_ADD;
			$oOrgAddr->add(array(
							'org_id' => $oOrgID,
							'addr_type_id' => $addr_type_id,
							'add1' => $_REQUEST['add1'],
							'add2' => $_REQUEST['add2'],
							'city' => $_REQUEST['city'],
							'state' => $_REQUEST['state'],
							'zip' => $_REQUEST['zip'],
							'country' => $_REQUEST['country'],
							'preferred' => 'Y',
							'created_on' => DCL_NOW,
							'created_by' => $GLOBALS['DCLID']
							)
						);
		}

		$phone_type_id = DCL_Sanitize::ToInt($_REQUEST['phone_type_id']);
		if ($_POST['phone_type_id'] > 0 && $_POST['phone_number'] != '')
		{
			$oOrgPhone = new boOrgPhone();
			$oOrgPhone->PermAdd = DCL_PERM_ADD;
			$oOrgPhone->add(array(
								'org_id' => $oOrgID,
								'phone_type_id' => $phone_type_id,
								'phone_number' => $_REQUEST['phone_number'],
								'preferred' => 'Y',
								'created_on' => DCL_NOW,
								'created_by' => $GLOBALS['DCLID']
							)
						);
		}

		$email_type_id = DCL_Sanitize::ToInt($_REQUEST['email_type_id']);
		if ($_POST['email_type_id'] > 0 && $_POST['email_addr'] != '')
		{
			$oOrgEmail = new boOrgEmail();
			$oOrgEmail->PermAdd = DCL_PERM_ADD;
			$oOrgEmail->add(array(
								'org_id' => $oOrgID,
								'email_type_id' => $email_type_id,
								'email_addr' => $_REQUEST['email_addr'],
								'preferred' => 'Y',
								'created_on' => DCL_NOW,
								'created_by' => $GLOBALS['DCLID']
							)
						);
		}

		$url_type_id = DCL_Sanitize::ToInt($_REQUEST['url_type_id']);
		if ($_POST['url_type_id'] > 0 && $_POST['url_addr'] != '')
		{
			$oOrgUrl = new boOrgUrl();
			$oOrgUrl->PermAdd = DCL_PERM_ADD;
			$oOrgUrl->add(array(
								'org_id' => $oOrgID,
								'url_type_id' => $url_type_id,
								'url_addr' => $_REQUEST['url_addr'],
								'preferred' => 'Y',
								'created_on' => DCL_NOW,
								'created_by' => $GLOBALS['DCLID']
							)
						);
		}

		if (EvaluateReturnTo())
			return;

		$_REQUEST['org_id'] = $oOrgID;
		$oOrg = new htmlOrgDetail();
		$oOrg->show();
	}

	function submitModify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$obj = new boOrg();
		CleanArray($_REQUEST);

		$aValues = array('org_id' => DCL_Sanitize::ToInt($_REQUEST['org_id']),
						'name' => $_REQUEST['name'],
						'org_type_id' => @DCL_Sanitize::ToIntArray($_REQUEST['org_type_id']),
						'active' => 'Y'
						);
						
		if (!isset($_REQUEST['active']) || $_REQUEST['active'] != 'Y')
			$aValues['active'] = 'N';

		$obj->modify($aValues);

		$oOrg = new htmlOrgDetail();
		$oOrg->show();
	}

	function submitDelete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize_failed.');
			return;
		}
		
		$obj = new boOrg();
		CleanArray($_REQUEST);

		$aKey = array('org_id' => $id);
		$obj->delete($aKey);

		$oOrg = new htmlOrgBrowse();
		$oOrg->show();
	}

	function ShowEntryForm($obj = '')
	{
		global $dcl_info, $g_oSec;

		$isEdit = is_object($obj);
		if (!$isEdit)
		{
			if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY))
				throw new PermissionDeniedException();
		}
		else
		{
			if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_ADD))
				throw new PermissionDeniedException();
		}

		$oSmarty = new DCL_Smarty();
		
		if (isset($_REQUEST['return_to']))
		{
			$oSmarty->assign('return_to', $_REQUEST['return_to']);
			$oSmarty->assign('URL_BACK', menuLink('', $_REQUEST['return_to']));
		}
		else
			$oSmarty->assign('URL_BACK', menuLink('', 'menuAction=htmlOrgBrowse.show&filterActive=Y'));
			
		if (isset($_REQUEST['hideMenu']))
			$oSmarty->assign('hideMenu', $_REQUEST['hideMenu']);

		if ($isEdit)
		{
			$oSmarty->assign('VAL_MENUACTION', 'htmlOrgForm.submitModify');
			$oSmarty->assign('VAL_NAME', $obj->name);
			$oSmarty->assign('VAL_ORGID', $obj->org_id);
			$oSmarty->assign('VAL_ACTIVE', $obj->active);
			$oSmarty->assign('TXT_FUNCTION', 'Edit Organization');
			
			$oOrgType = new OrganizationTypeModel();
			$oSmarty->assign('orgTypes', $oOrgType->GetTypes($obj->org_id));

			$oSmarty->Render('htmlOrgForm.tpl');
		}
		else
		{
			$oSmarty->assign('TXT_FUNCTION', 'Add New Organization');
			$oSmarty->assign('VAL_MENUACTION', 'htmlOrgForm.submitAdd');

			$oAddrType = new AddressTypeHtmlHelper();
			$oSmarty->assign('CMB_ADDRTYPE', $oAddrType->Select());

			$oEmailType = new EmailTypeHtmlHelper();
			$oSmarty->assign('CMB_EMAILTYPE', $oEmailType->Select());

			$oPhoneType = new htmlPhoneType();
			$oSmarty->assign('CMB_PHONETYPE', $oPhoneType->GetCombo());

			$oUrlType = new htmlUrlType();
			$oSmarty->assign('CMB_URLTYPE', $oUrlType->GetCombo());

			$oOrgType = new OrganizationTypeModel();
			$oSmarty->assign('orgTypes', $oOrgType->GetTypes());

			$oSmarty->Render('htmlNewOrgForm.tpl');
		}
	}
}
