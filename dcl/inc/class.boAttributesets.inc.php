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

class boAttributesets
{
	function showall()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, DCL_PERM_VIEW))
			return PrintPermissionDenied();

		$obj = new htmlAttributesets();
		$obj->PrintAll();
	}

	function showmapping()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, DCL_PERM_VIEW))
			return PrintPermissionDenied();
		
		if (($iSetID = @DCL_Sanitize::ToInt($_REQUEST['setid'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		if (($iTypeID = @DCL_Sanitize::ToInt($_REQUEST['typeid'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		$obj = new htmlAttributesetmapping();
		$obj->Show($iSetID, $iTypeID);
	}

	function dbmap()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		if (($iSetID = @DCL_Sanitize::ToInt($_REQUEST['setid'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		if (($iTypeID = @DCL_Sanitize::ToInt($_REQUEST['typeid'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		$obj = new dbAttributesetsmap();
		$obj->setid = $iSetID;
		$obj->typeid = $iTypeID;
		
		$obj->BeginTransaction();
		$obj->DeleteBySetType($iSetID, $iTypeID);

		if (($aKeyID = @DCL_Sanitize::ToIntArray($_REQUEST['keyidset'])) !== null)
		{
			$i = 1;
			foreach ($aKeyID as $id)
			{
				$obj->weight = $i;
				$obj->keyid = $id;
				$obj->Add();
				$i++;
			}
		}

		$obj->EndTransaction();

		$objA = new dbAttributesets();
		$objA->Load($obj->setid);

		$objH = new htmlAttributesetdetail();
		$objH->Show($objA);
	}

	function view()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, DCL_PERM_VIEW))
			return PrintPermissionDenied();
			
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		$objA = new dbAttributesets();
		if ($objA->Load($iID) == -1)
			return;

		$obj = new htmlAttributesetdetail();
		$obj->Show($objA);
	}

	function add()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, DCL_PERM_ADD))
			return PrintPermissionDenied();

		$obj = new htmlAttributesets();
		$obj->ShowEntryForm();
	}

	function dbadd()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, DCL_PERM_ADD))
			return PrintPermissionDenied();

		$obj = new dbAttributesets();
		$obj->InitFromGlobals();
		$obj->Add();

		$objHTML = new htmlAttributesets();
		$objHTML->PrintAll();
	}

	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, DCL_PERM_MODIFY))
			return PrintPermissionDenied();
		
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		$obj = new dbAttributesets();
		if ($obj->Load($iID) == -1)
			return;
			
		$objHTML = new htmlAttributesets();
		$objHTML->ShowEntryForm($obj);
	}

	function dbmodify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		$obj = new dbAttributesets();
		$obj->InitFromGlobals();
		$obj->Edit();
		
		$objHTML = new htmlAttributesets();
		$objHTML->PrintAll();
	}
	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, DCL_PERM_DELETE))
			return PrintPermissionDenied();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$obj = new dbAttributesets();
		if ($obj->Load($iID) == -1)
			return;
			
		ShowDeleteYesNo('Attribute Set', 'boAttributesets.dbdelete', $obj->id, $obj->name);
	}

	function dbdelete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, DCL_PERM_DELETE))
			return PrintPermissionDenied();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$obj = new dbAttributesets();
		if ($obj->Load($iID) == -1)
			return;

		if (!$obj->HasFKRef($iID))
		{
			$obj->Delete();
			print(STR_BO_DELETED);
		}
		else
		{
			$obj->SetActive(array($obj->sKeyField => $iID), false);
			print(STR_BO_DEACTIVATED);
		}

		$objHTML = new htmlAttributesets();
		$objHTML->PrintAll();
	}
}
