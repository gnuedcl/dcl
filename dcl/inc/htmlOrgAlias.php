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

class htmlOrgAlias
{
	var $public;

	function htmlOrgAlias()
	{
		$this->public = array('add', 'modify', 'delete', 'submitAdd', 'submitModify', 'submitDelete');
	}

	function add()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$this->ShowEntryForm();
	}

	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['org_alias_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		$obj = new dbOrgAlias();
		if ($obj->Load($id) == -1)
		    return;
		    
		$this->ShowEntryForm($obj);
	}

	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();
	}

	function submitAdd()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['org_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		CleanArray($_REQUEST);

		$oOrgAlias = new boOrgAlias();
		$oOrgAlias->add(array(
						'org_id' => $id,
						'alias' => $_REQUEST['alias'],
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
			throw new PermissionDeniedException();

		CleanArray($_REQUEST);

		$obj = new boOrgAlias();
		$obj->modify($_REQUEST);

		$this->ShowOrgDetail();
	}

	function submitDelete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['org_alias_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		$aKey = array('org_alias_id' => $id);

		$obj = new boOrgAlias();
		$obj->delete($aKey);

		$this->ShowOrgDetail();
	}

	function ShowOrgDetail()
	{
		$oOrg = new htmlOrgDetail();
		$oOrg->show();
	}

	function ShowEntryForm($obj = '')
	{
		global $dcl_info, $g_oSec;

		$isEdit = is_object($obj);
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['org_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		$oSmarty = new DCL_Smarty();
		$oSmarty->assign('URL_BACK', menuLink('', 'menuAction=htmlOrgDetail.show&org_id=' . $id));

		$oOrg = new dbOrg();
		$oOrg->Load($_REQUEST['org_id']);
		$oSmarty->assign('VAL_ORGNAME', $oOrg->name);
		$oSmarty->assign('VAL_ORGID', $oOrg->org_id);

		if ($isEdit)
		{
			$oSmarty->assign('VAL_MENUACTION', 'htmlOrgAlias.submitModify');
			$oSmarty->assign('VAL_ORGALIASID', $obj->org_alias_id);
			$oSmarty->assign('VAL_ALIAS', $obj->alias);
			$oSmarty->assign('TXT_FUNCTION', 'Edit Organization Alias');
		}
		else
		{
			$oSmarty->assign('TXT_FUNCTION', 'Add New Organization Alias');
			$oSmarty->assign('VAL_MENUACTION', 'htmlOrgAlias.submitAdd');
		}

		$oSmarty->Render('htmlOrgAlias.tpl');
	}
}
