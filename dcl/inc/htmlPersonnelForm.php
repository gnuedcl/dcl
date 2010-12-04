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

LoadStringResource('usr');

class htmlPersonnelForm
{
	function add()
	{
		commonHeader();
		$this->ShowEntryForm();
	}

	function modify()
	{
		commonHeader();
		
		if (($id = DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$oPersonnel = new dbPersonnel();
		if ($oPersonnel->Load($id) != -1)
			$this->ShowEntryForm($oPersonnel);
	}

	function ShowEntryForm($obj = '')
	{
		global $dcl_info, $g_oSec;

		$isEdit = is_object($obj);
		if (!$g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, $isEdit ? DCL_PERM_MODIFY : DCL_PERM_ADD))
			return PrintPermissionDenied();

		$Template = new DCL_Smarty();
		$Template->assign('IS_EDIT', $isEdit);

		$oUserRole = new dbUserRole();
		$oDept = new htmlDepartments();
		if ($isEdit)
		{
			$Template->assign('VAL_PERSONNELID', $obj->id);
			$Template->assign('VAL_ACTIVE', $obj->active);
			$Template->assign('VAL_SHORT', $obj->short);
			$Template->assign('VAL_REPORTTO', $obj->reportto);
			$Template->assign('VAL_DEPARTMENT', $obj->department);
			$Template->assign('Roles', $oUserRole->GetGlobalRoles($obj->id));
			
			$oMeta = new DCL_MetadataDisplay();
			$aContact =& $oMeta->GetContact($obj->contact_id);
			
			$Template->assign('VAL_CONTACTID', $obj->contact_id);
			$Template->assign('VAL_CONTACTNAME', $aContact['name']);
		}
		else
		{
			$Template->assign('VAL_ACTIVE', 'Y');
			$Template->assign('VAL_REPORTTO', $GLOBALS['DCLID']);
			$Template->assign('VAL_DEPARTMENT', 0);
			$Template->assign('VAL_SHORT', '');
			$Template->assign('Roles', $oUserRole->GetGlobalRoles());
		}

		$Template->Render('htmlPersonnelForm.tpl');
	}
}
