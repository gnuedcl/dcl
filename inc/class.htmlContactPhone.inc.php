<?php
/*
 * $Id: class.htmlContactPhone.inc.php,v 1.1.1.1 2006/11/27 05:30:48 mdean Exp $
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

class htmlContactPhone
{
	var $public;

	function htmlContactPhone()
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

		if (($id = DCL_Sanitize::ToInt($_REQUEST['contact_phone_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$obj = CreateObject('dcl.dbContactPhone');
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
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['contact_id'])) === null ||
		    ($phone_type_id = DCL_Sanitize::ToInt($_REQUEST['phone_type_id'])) === null
		    )
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		CleanArray($_REQUEST);

		$oContactPhone =& CreateObject('dcl.boContactPhone');
		$oContactPhone->add(array(
						'contact_id' => $id,
						'phone_type_id' => $phone_type_id,
						'phone_number' => $_REQUEST['phone_number'],
						'preferred' => isset($_REQUEST['preferred']) ? 'Y' : 'N',
						'created_on' => 'now()',
						'created_by' => $GLOBALS['DCLID']
						)
					);

		$this->ShowContactDetail();
	}

	function submitModify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		CleanArray($_REQUEST);

		$obj =& CreateObject('dcl.boContactPhone');
		$obj->modify($_REQUEST);

		$this->ShowContactDetail();
	}

	function submitDelete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['contact_phone_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		CleanArray($_REQUEST);

		$aKey = array('contact_phone_id' => $id);

		$obj =& CreateObject('dcl.boContactPhone');
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
		global $dcl_info, $g_oSec;

		$isEdit = is_object($obj);
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['contact_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		$oSmarty =& CreateSmarty();
		$oPhoneType =& CreateObject('dcl.htmlPhoneType');

		$oSmarty->assign('URL_BACK', menuLink('', 'menuAction=htmlContactDetail.show&contact_id=' . $id));

		$oContact = CreateObject('dcl.dbContact');
		$oContact->Load($_REQUEST['contact_id']);
		$oSmarty->assign('VAL_FIRSTNAME', $oContact->first_name);
		$oSmarty->assign('VAL_LASTNAME', $oContact->last_name);
		$oSmarty->assign('VAL_CONTACTID', $oContact->contact_id);

		if ($isEdit)
		{
			$oSmarty->assign('VAL_MENUACTION', 'htmlContactPhone.submitModify');
			$oSmarty->assign('VAL_CONTACTPHONEID', $obj->contact_phone_id);
			$oSmarty->assign('VAL_PHONENUMBER', $obj->phone_number);
			$oSmarty->assign('VAL_PREFERRED', $obj->preferred);
			$oSmarty->assign('CMB_PHONETYPE', $oPhoneType->GetCombo($obj->phone_type_id));
			$oSmarty->assign('TXT_FUNCTION', 'Edit Contact Phone Number');
		}
		else
		{
			$oSmarty->assign('TXT_FUNCTION', 'Add New Contact Phone Number');
			$oSmarty->assign('CMB_PHONETYPE', $oPhoneType->GetCombo());
			$oSmarty->assign('VAL_MENUACTION', 'htmlContactPhone.submitAdd');
		}

		SmartyDisplay($oSmarty, 'htmlPhoneForm.tpl');
	}
}
?>
