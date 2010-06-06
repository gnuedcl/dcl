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

class htmlContactLicenses
{
	var $public;

	function htmlContactLicenses()
	{
		$this->public = array('add', 'modify', 'delete', 'submitAdd', 'submitModify', 'submitDelete');
	}

	function add()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		$this->ShowEntryForm();
	}

	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			return PrintPermissionDenied();
			
		if (($id = DCL_Sanitize::ToInt($_REQUEST['contact_license_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		$obj = CreateObject('dcl.dbContactLicense');
		if ($obj->Load($id) == -1)
		    return;
		    
		$this->ShowEntryForm($obj);
	}

	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			return PrintPermissionDenied();
	}

	function submitAdd()
	{
		global $g_oSec;

		commonHeader();
		
		if (($contact_id = DCL_Sanitize::ToInt($_REQUEST['contact_id'])) === null ||
			($product_id = DCL_Sanitize::ToInt($_REQUEST['product_id'])) === null ||
			($registered_on = DCL_Sanitize::ToDate($_REQUEST['registered_on'])) === null || 
			($expires_on = DCL_Sanitize::ToDate($_REQUEST['expires_on'])) === null 
			)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		CleanArray($_REQUEST);

		$oContactEmail =& CreateObject('dcl.boContactLicense');
		$oContactEmail->add(array(
						'contact_id' => $contact_id,
						'product_id' => $product_id,
						'product_version' => $_REQUEST['product_version'],
		                'license_id' => $_REQUEST['license_id'],
		                'registered_on' => $registered_on,
		                'expires_on' => $expires_on,
						'license_notes' => $_REQUEST['license_notes'],
						'created_on' => DCL_NOW,
						'created_by' => $GLOBALS['DCLID']
						)
					);

		$this->ShowContactDetail();
	}

	function submitModify()
	{
		global $g_oSec;
		
		commonHeader();
		if (($id = DCL_Sanitize::ToInt($_REQUEST['contact_license_id'])) === null ||
		    ($contact_id = DCL_Sanitize::ToInt($_REQUEST['contact_id'])) === null ||
			($product_id = DCL_Sanitize::ToInt($_REQUEST['product_id'])) === null ||
			($registered_on = DCL_Sanitize::ToDate($_REQUEST['registered_on'])) === null  || 
			($expires_on = DCL_Sanitize::ToDate($_REQUEST['expires_on'])) === null  
			)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		CleanArray($_REQUEST);

		$obj =& CreateObject('dcl.boContactLicense');
		$obj->modify(array(
		                'contact_license_id' => $id,
						'contact_id' => $contact_id,
						'product_id' => $product_id,
						'product_version' => $_REQUEST['product_version'],
		                'license_id' => $_REQUEST['license_id'],
		                'registered_on' => $registered_on,
		                'expires_on' => $expires_on,
						'license_notes' => $_REQUEST['license_notes'],
						'modified_on' => DCL_NOW,
						'modified_by' => $GLOBALS['DCLID']
						)
					);

		$this->ShowContactDetail();
	}

	function submitDelete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			return PrintPermissionDenied();
			
		if (($id = DCL_Sanitize::ToInt($_REQUEST['contact_license_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		CleanArray($_REQUEST);

		$aKey = array('contact_license_id' => $id);

		$obj =& CreateObject('dcl.boContactLicense');
		$obj->delete($aKey);

		$this->ShowContactDetail();
	}

	function ShowContactDetail()
	{
		$oContact =& CreateObject('dcl.htmlContactDetail');
		$oContact->show();
	}

	function ShowEntryForm($obj = '')
	{
		global $g_oSec;

		$isEdit = is_object($obj);
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			return PrintPermissionDenied();
			
		if (($id = DCL_Sanitize::ToInt($_REQUEST['contact_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		$oSmarty =& CreateSmarty();
		$oEmailType =& CreateObject('dcl.htmlEmailType');

		$oSmarty->assign('URL_BACK', menuLink('', 'menuAction=htmlContactDetail.show&contact_id=' . $id));

		$oContact = CreateObject('dcl.dbContact');
		if ($oContact->Load($id) == -1)
		    return;
		    
		$oSmarty->assign('VAL_FIRSTNAME', $oContact->first_name);
		$oSmarty->assign('VAL_LASTNAME', $oContact->last_name);
		$oSmarty->assign('VAL_CONTACTID', $oContact->contact_id);

		if ($isEdit)
		{
			$oSmarty->assign('VAL_MENUACTION', 'htmlContactLicenses.submitModify');
			$oSmarty->assign('VAL_CONTACTLICENSEID', $obj->contact_license_id);
			$oSmarty->assign('VAL_PRODUCTID', $obj->product_id);
			$oSmarty->assign('VAL_VERSION', $obj->product_version);
			$oSmarty->assign('VAL_LICENSEID', $obj->license_id);
			$oSmarty->assign('VAL_REGISTEREDON', $obj->registered_on);
			$oSmarty->assign('VAL_NOTES', $obj->license_notes);
			$oSmarty->assign('VAL_EXPIRESON', $obj->expires_on);
			$oSmarty->assign('TXT_FUNCTION', 'Edit Product License');
		}
		else
		{
			$oSmarty->assign('TXT_FUNCTION', 'Add New Product License');
			$oSmarty->assign('VAL_MENUACTION', 'htmlContactLicenses.submitAdd');
		}

		SmartyDisplay($oSmarty, 'htmlLicenseForm.tpl');
	}
}
?>