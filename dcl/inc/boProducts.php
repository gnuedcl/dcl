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
class boProducts extends boAdminObject
{
	function boProducts()
	{
		$this->oDB = new dbProducts();
		$this->sKeyField = 'id';
		$this->sDescField = 'name';
		$this->sPublicField = 'is_public';
		$this->Entity = DCL_ENTITY_PRODUCT;
	}

	function add($aSource)
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$obj = new dbProducts();
		$obj->InitFromArray($aSource);
		$obj->Add();
	}

	function modify($aSource)
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_MODIFY, (int)$aSource['id']))
			throw new PermissionDeniedException();

		$obj = new dbProducts();
		$obj->InitFromArray($aSource);
		$obj->Edit();
	}

	function delete($id)
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_DELETE, (int)$id))
			throw new PermissionDeniedException();

		if (($id = @DCL_Sanitize::ToInt($id)) === null)
		{
			throw new InvalidDataException();
		}
		
		$oDB = new dbProducts();
		if (!$oDB->HasFKRef($id))
		{
			$oDB->id = $id;
			$oDB->Delete();
			trigger_error(STR_BO_DELETED, E_USER_NOTICE);
		}
		else
		{
			$oDB->SetActive(array($obj->sKeyField => $iID), false);
			trigger_error(STR_BO_DEACTIVATED, E_USER_WARNING);
		}
	}
	
	function view()
	{
		global $g_oSec;
		
		commonHeader();
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_VIEW, $iID))
			throw new PermissionDeniedException();

		$which = isset($_REQUEST['which']) ? $_REQUEST['which'] : '';
		if ($which != 'workorders' && $which != 'tickets' && $which != 'modules' && $which != 'release' && $which != 'build')
			$which = null;
		
		$versionid = null;
		if (IsSet($_REQUEST['versionid']))
			$versionid = @DCL_Sanitize::ToInt($_REQUEST['versionid']);
		
		$obj = new htmlProductDetail();
		if ($which !== null)
		{
			if ($versionid !== null)
				$obj->Show($iID, $which, $versionid);
			else
				$obj->Show($iID, $which);
		}
		else
			$obj->Show($iID);
	}

	function viewWO()
	{
		$_REQUEST['which'] = 'workorders';
		$this->view();
	}

	function viewTck()
	{
		$_REQUEST['which'] = 'tickets';
		$this->view();
	}

	function viewModules()
	{
		$_REQUEST['which'] = 'modules';
		$this->view();
	}

	function viewRelease()
	{
		$_REQUEST['which'] = 'release';
		$this->view();
	}

	function viewBuild()
	{
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['product_id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (($iVerID = @DCL_Sanitize::ToInt($_REQUEST['product_version_id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$_REQUEST['which'] = 'build';
		$_REQUEST['id'] = $iID;
		$_REQUEST['versionid'] = $iVerID;

		$this->view();
	}
}
