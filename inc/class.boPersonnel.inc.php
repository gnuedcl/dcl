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

LoadStringResource('bo');

class boPersonnel
{
	function add()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_ADD))
			return PrintPermissionDenied();

		$obj =& CreateObject('dcl.htmlPersonnel');
		$obj->ShowEntryForm();
	}

	function dbadd()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_ADD))
			return PrintPermissionDenied();

		$obj =& CreateObject('dcl.dbPersonnel');
		$obj->InitFromGlobals();
		if (isset($_REQUEST['active']))
			$obj->active = 'Y';
		else
			$obj->active = 'N';

		$obj->Encrypt();
		$obj->Add();

		$aRoles = @DCL_Sanitize::ToIntArray($_REQUEST['roles']);
		if (count($aRoles) > 0)
		{
			// Set up global user roles
			$oUserRole =& CreateObject('dcl.dbUserRole');
			$oUserRole->personnel_id = $obj->id;
			$oUserRole->entity_type_id = DCL_ENTITY_GLOBAL;
			$oUserRole->entity_id1 = 0;
			$oUserRole->entity_id2 = 0;

			foreach ($aRoles as $oUserRole->role_id)
				$oUserRole->add();
		}

		$oBrowse =& CreateObject('dcl.htmlPersonnelBrowse');
		$oBrowse->show();
	}

	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$obj =& CreateObject('dcl.dbPersonnel');
		if ($obj->Load($iID) == -1)
			return;

		$objHTML =& CreateObject('dcl.htmlPersonnelForm');
		$objHTML->ShowEntryForm($obj);
	}

	function dbmodify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		$obj =& CreateObject('dcl.dbPersonnel');
		$obj->InitFromGlobals();
		if (isset($_REQUEST['active']))
			$obj->active = 'Y';
		else
			$obj->active = 'N';

		$obj->Edit();

		$oUserRole =& CreateObject('dcl.dbUserRole');
		$oUserRole->DeleteGlobalRolesNotIn($obj->id);
		
		$aRoles = @DCL_Sanitize::ToIntArray($_REQUEST['roles']);
		if (count($aRoles) > 0)
		{
			// Set up global user roles
			$oUserRole->personnel_id = $obj->id;
			$oUserRole->entity_type_id = DCL_ENTITY_GLOBAL;
			$oUserRole->entity_id1 = 0;
			$oUserRole->entity_id2 = 0;

			foreach ($aRoles as $oUserRole->role_id)
				$oUserRole->add();
		}

		$oBrowse =& CreateObject('dcl.htmlPersonnelBrowse');
		$oBrowse->show();
	}

	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_DELETE))
			return PrintPermissionDenied();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$obj =& CreateObject('dcl.dbPersonnel');
		if ($obj->Load($iID) == -1)
			return;
			
		ShowDeleteYesNo('User', 'boPersonnel.dbdelete', $obj->id, $obj->short);
	}

	function dbdelete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_DELETE))
			return PrintPermissionDenied();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$obj =& CreateObject('dcl.dbPersonnel');
		if ($obj->Load($iID) == -1)
			return;

		if (!$obj->HasFKRef($iID))
		{
			$obj->Delete();
		}
		else
		{
			$obj->SetActive(array($obj->sKeyField => $iID), false);
		}

		$oBrowse =& CreateObject('dcl.htmlPersonnelBrowse');
		$oBrowse->show();
	}

	function passwd()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PREFS, DCL_PERM_PASSWORD))
			return PrintPermissionDenied();

		$objPersonnel =& CreateObject('dcl.htmlPersonnel');
		$objPersonnel->DisplayPasswdForm();
	}

	function dbpasswd()
	{
		global $g_oSec;
		
		commonHeader();
		$iID = $GLOBALS['DCLID'];
		if (isset($_REQUEST['userid']))
		{
			if (($iID = @DCL_Sanitize::ToInt($_REQUEST['userid'])) === null)
			{
				trigger_error('Data sanitize failed.');
				return;
			}
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PREFS, DCL_PERM_PASSWORD) || (!$g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_PASSWORD) && $GLOBALS['DCLID'] != $iID))
			return PrintPermissionDenied();

		if ($_REQUEST['confirm'] != $_REQUEST['new'] || $_REQUEST['new'] == '')
		{
			trigger_error(STR_BO_PASSWORDERR);
			$objPersonnel =& CreateObject('dcl.htmlPersonnel');
			$objPersonnel->DisplayPasswdForm();
		}
		else
		{
			$objDBPersonnel =& CreateObject('dcl.dbPersonnel');
			$sOriginal = '';
			if (isset($_REQUEST['original']))
				$sOriginal = $_REQUEST['original'];

			$objDBPersonnel->ChangePassword($iID, $sOriginal, $_REQUEST['new'], $_REQUEST['confirm']);
		}
	}

	function showall()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_VIEW))
			return PrintPermissionDenied();

		$obj =& CreateObject('dcl.htmlPersonnelBrowse');
		$obj->show();
	}
}
?>
