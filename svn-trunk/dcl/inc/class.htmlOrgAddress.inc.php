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

class htmlOrgAddress
{
	var $public;

	function htmlOrgAddress()
	{
		$this->public = array('add', 'modify', 'delete', 'submitAdd', 'submitModify', 'submitDelete');
	}

	function add()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		$this->ShowEntryForm();
	}

	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['org_addr_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		$obj = CreateObject('dcl.dbOrgAddr');
		if ($obj->Load($id) == -1)
		    return;
		    
		$this->ShowEntryForm($obj);
	}

	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY))
			return PrintPermissionDenied();
	}

	function submitAdd()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['org_id'])) === null ||
		    ($addr_type_id = DCL_Sanitize::ToInt($_REQUEST['addr_type_id'])) === null
			)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		CleanArray($_REQUEST);

		$oOrgAddr = CreateObject('dcl.boOrgAddr');
		$oOrgAddr->add(array(
						'org_id' => $id,
						'addr_type_id' => $addr_type_id,
						'add1' => $_REQUEST['add1'],
						'add2' => $_REQUEST['add2'],
						'city' => $_REQUEST['city'],
						'state' => $_REQUEST['state'],
						'zip' => $_REQUEST['zip'],
						'country' => $_REQUEST['country'],
						'preferred' => isset($_REQUEST['preferred']) ? 'Y' : 'N',
						'created_on' => DCL_NOW,
						'created_by' => $GLOBALS['DCLID']
						)
					);

		$this->ShowOrgDetail();
	}

	function submitModify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		CleanArray($_REQUEST);

		$obj =& CreateObject('dcl.boOrgAddr');
		$obj->modify($_REQUEST);

		$this->ShowOrgDetail();
	}

	function submitDelete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['org_addr_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		CleanArray($_REQUEST);

		$aKey = array('org_addr_id' => $id);

		$obj =& CreateObject('dcl.boOrgAddr');
		$obj->delete($aKey);

		$this->ShowOrgDetail();
	}

	function ShowOrgDetail()
	{
		$oOrg =& CreateObject('dcl.htmlOrgDetail');
		$oOrg->show();
	}

	function ShowEntryForm($obj = '')
	{
		global $dcl_info, $g_oSec;

		$isEdit = is_object($obj);
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['org_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		$oSmarty =& CreateSmarty();
		$oAddrType =& CreateObject('dcl.htmlAddrType');

		$oSmarty->assign('URL_BACK', menuLink('', 'menuAction=htmlOrgDetail.show&org_id=' . $id));

		$oOrg = CreateObject('dcl.dbOrg');
		$oOrg->Load($_REQUEST['org_id']);
		$oSmarty->assign('VAL_ORGNAME', $oOrg->name);
		$oSmarty->assign('VAL_ORGID', $oOrg->org_id);

		if ($isEdit)
		{
			$oSmarty->assign('VAL_MENUACTION', 'htmlOrgAddress.submitModify');
			$oSmarty->assign('VAL_ORGADDRID', $obj->org_addr_id);
			$oSmarty->assign('VAL_ADD1', $obj->add1);
			$oSmarty->assign('VAL_ADD2', $obj->add2);
			$oSmarty->assign('VAL_CITY', $obj->city);
			$oSmarty->assign('VAL_STATE', $obj->state);
			$oSmarty->assign('VAL_ZIP', $obj->zip);
			$oSmarty->assign('VAL_COUNTRY', $obj->country);
			$oSmarty->assign('VAL_PREFERRED', $obj->preferred);
			$oSmarty->assign('CMB_ADDRTYPE', $oAddrType->GetCombo($obj->addr_type_id));
			$oSmarty->assign('TXT_FUNCTION', 'Edit Organization Address');
		}
		else
		{
			$oSmarty->assign('TXT_FUNCTION', 'Add New Organization Address');
			$oSmarty->assign('VAL_MENUACTION', 'htmlOrgAddress.submitAdd');
			$oSmarty->assign('CMB_ADDRTYPE', $oAddrType->GetCombo());
		}

		SmartyDisplay($oSmarty, 'htmlAddrForm.tpl');
	}
}
?>
