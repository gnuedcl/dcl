<?php
/*
 * $Id: class.boPriorities.inc.php,v 1.1.1.1 2006/11/27 05:30:51 mdean Exp $
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
import('boAdminObject');
class boPriorities extends boAdminObject
{
	function boPriorities()
	{
		$this->oDB =& CreateObject('dcl.dbPriorities');
		$this->sKeyField = 'id';
		$this->sDescField = 'name';
		$this->Entity = DCL_ENTITY_PRIORITY;
	}

	function showall()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRIORITY, DCL_PERM_VIEW))
			return PrintPermissionDenied();

		$obj =& CreateObject('dcl.htmlPriorities');
		$obj->PrintAll();
	}

	function add()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRIORITY, DCL_PERM_ADD))
			return PrintPermissionDenied();

		$obj =& CreateObject('dcl.htmlPriorities');
		$obj->ShowEntryForm();
	}

	function dbadd()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRIORITY, DCL_PERM_ADD))
			return PrintPermissionDenied();

		$this->oDB->InitFromGlobals();
		$this->oDB->Add();

		$objHTML =& CreateObject('dcl.htmlPriorities');
		$objHTML->PrintAll();
	}

	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRIORITY, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if ($this->oDB->Load($iID) == -1)
			return;
			
		$objHTML =& CreateObject('dcl.htmlPriorities');
		$objHTML->ShowEntryForm($this->oDB);
	}

	function dbmodify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRIORITY, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		$this->oDB->InitFromGlobals();
		$this->oDB->Edit();

		$objHTML =& CreateObject('dcl.htmlPriorities');
		$objHTML->PrintAll();
	}
	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRIORITY, DCL_PERM_DELETE))
			return PrintPermissionDenied();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if ($this->oDB->Load($iID) == -1)
			return;
			
		ShowDeleteYesNo('Priority', 'boPriorities.dbdelete', $this->oDB->id, $this->oDB->name);
	}

	function dbdelete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRIORITY, DCL_PERM_DELETE))
			return PrintPermissionDenied();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		parent::delete(array('id' => $iID));

		$objHTML =& CreateObject('dcl.html' . $classSubName);
		$objHTML->PrintAll();
	}
}
?>
