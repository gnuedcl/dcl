<?php
/*
 * $Id: class.htmlOrgEmail.inc.php,v 1.1.1.1 2006/11/27 05:30:45 mdean Exp $
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

class htmlOrgEmail
{
	var $public;

	function htmlOrgEmail()
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

		if (($id = DCL_Sanitize::ToInt($_REQUEST['org_email_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		$obj = CreateObject('dcl.dbOrgEmail');
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
		    ($email_type_id = DCL_Sanitize::ToInt($_REQUEST['email_type_id'])) === null
			)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		CleanArray($_REQUEST);

		$oOrgAlias =& CreateObject('dcl.boOrgEmail');
		$oOrgAlias->add(array(
						'org_id' => $id,
						'email_type_id' => $email_type_id,
						'email_addr' => $_REQUEST['email_addr'],
						'preferred' => isset($_REQUEST['preferred']) ? 'Y' : 'N',
						'created_on' => 'now()',
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

		$obj =& CreateObject('dcl.boOrgEmail');
		$obj->modify($_REQUEST);

		$this->ShowOrgDetail();
	}

	function submitDelete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['org_email_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		CleanArray($_REQUEST);

		$aKey = array('org_email_id' => $id);

		$obj =& CreateObject('dcl.boOrgEmail');
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
		$oEmailType =& CreateObject('dcl.htmlEmailType');

		$oSmarty->assign('URL_BACK', menuLink('', 'menuAction=htmlOrgDetail.show&org_id=' . $id));

		$oOrg = CreateObject('dcl.dbOrg');
		$oOrg->Load($_REQUEST['org_id']);
		$oSmarty->assign('VAL_ORGNAME', $oOrg->name);
		$oSmarty->assign('VAL_ORGID', $oOrg->org_id);

		if ($isEdit)
		{
			$oSmarty->assign('VAL_MENUACTION', 'htmlOrgEmail.submitModify');
			$oSmarty->assign('VAL_ORGEMAILID', $obj->org_email_id);
			$oSmarty->assign('VAL_EMAILADDR', $obj->email_addr);
			$oSmarty->assign('CMB_EMAILTYPE', $oEmailType->GetCombo($obj->email_type_id));
			$oSmarty->assign('TXT_FUNCTION', 'Edit Organization E-Mail');
		}
		else
		{
			$oSmarty->assign('TXT_FUNCTION', 'Add New Organization E-Mail');
			$oSmarty->assign('CMB_EMAILTYPE', $oEmailType->GetCombo());
			$oSmarty->assign('VAL_MENUACTION', 'htmlOrgEmail.submitAdd');
		}

		SmartyDisplay($oSmarty, 'htmlEmailForm.tpl');
	}
}
?>
