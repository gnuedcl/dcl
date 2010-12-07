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
class boPriorities extends boAdminObject
{
	function boPriorities()
	{
		$this->oDB = new dbPriorities();
		$this->sKeyField = 'id';
		$this->sDescField = 'name';
		$this->Entity = DCL_ENTITY_PRIORITY;
	}

	function showall()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRIORITY, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$obj = new htmlPriorities();
		$obj->PrintAll();
	}

	function add()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRIORITY, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$obj = new htmlPriorities();
		$obj->ShowEntryForm();
	}

	function dbadd()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRIORITY, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$this->oDB->InitFromGlobals();
		$this->oDB->Add();

		$objHTML = new htmlPriorities();
		$objHTML->PrintAll();
	}

	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRIORITY, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if ($this->oDB->Load($iID) == -1)
			return;
			
		$objHTML = new htmlPriorities();
		$objHTML->ShowEntryForm($this->oDB);
	}

	function dbmodify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRIORITY, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$this->oDB->InitFromGlobals();
		$this->oDB->Edit();

		$objHTML = new htmlPriorities();
		$objHTML->PrintAll();
	}
	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRIORITY, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

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
			throw new PermissionDeniedException();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		parent::delete(array('id' => $iID));

		$objHTML = new htmlPriorities();
		$objHTML->PrintAll();
	}
}
