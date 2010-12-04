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

class htmlContactUrl
{
	var $public;

	function htmlContactUrl()
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
			
		if (($id = DCL_Sanitize::ToInt($_REQUEST['contact_url_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		$obj = new dbContactUrl();
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
		    ($url_type_id = DCL_Sanitize::ToInt($_REQUEST['url_type_id'])) === null
		    )
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		CleanArray($_REQUEST);

		$oContactPhone = new boContactUrl();
		$oContactPhone->add(array(
						'contact_id' => $id,
						'url_type_id' => $url_type_id,
						'url_addr' => $_REQUEST['url_addr'],
						'preferred' => isset($_REQUEST['preferred']) ? 'Y' : 'N',
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
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		CleanArray($_REQUEST);

		$obj = new boContactUrl();
		$obj->modify($_REQUEST);

		$this->ShowContactDetail();
	}

	function submitDelete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['contact_url_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		CleanArray($_REQUEST);

		$aKey = array('contact_url_id' => $id);

		$obj = new boContactUrl();
		$obj->delete($aKey);

		$this->ShowContactDetail();
	}

	function ShowContactDetail()
	{
		$oContact = new htmlContactDetail();
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

		$oSmarty = new DCL_Smarty();
		$oUrlType = new htmlUrlType();

		$oSmarty->assign('URL_BACK', menuLink('', 'menuAction=htmlContactDetail.show&contact_id=' . $id));

		$oContact = new dbContact();
		if ($oContact->Load($id) == -1)
		    return;
		    
		$oSmarty->assign('VAL_FIRSTNAME', $oContact->first_name);
		$oSmarty->assign('VAL_LASTNAME', $oContact->last_name);
		$oSmarty->assign('VAL_CONTACTID', $oContact->contact_id);

		if ($isEdit)
		{
			$oSmarty->assign('VAL_MENUACTION', 'htmlContactUrl.submitModify');
			$oSmarty->assign('VAL_CONTACTURLID', $obj->contact_url_id);
			$oSmarty->assign('VAL_URLADDR', $obj->url_addr);
			$oSmarty->assign('VAL_PREFERRED', $obj->preferred);
			$oSmarty->assign('CMB_URLTYPE', $oUrlType->GetCombo($obj->url_type_id));
			$oSmarty->assign('TXT_FUNCTION', 'Edit Contact URL');
		}
		else
		{
			$oSmarty->assign('TXT_FUNCTION', 'Add New Contact URL');
			$oSmarty->assign('CMB_URLTYPE', $oUrlType->GetCombo());
			$oSmarty->assign('VAL_MENUACTION', 'htmlContactUrl.submitAdd');
		}

		$oSmarty->Render('htmlUrlForm.tpl');
	}
}
