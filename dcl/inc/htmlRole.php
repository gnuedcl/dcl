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

class htmlRole
{
	function GetCombo($default = 0, $cbName = 'role_id', $size = 0, $activeOnly = true)
	{
		$oDB = new RoleModel();
		$oDB->cacheEnabled = false;

		$query = "SELECT role_id, role_desc FROM dcl_role ";
		if ($activeOnly)
			$query .= "WHERE active='Y' ";

		$query .= "ORDER BY role_desc";

		$oDB->Query($query);

		$oSelect = new SelectHtmlHelper();
		$oSelect->DefaultValue = $default;
		$oSelect->Id = $cbName;
		$oSelect->Size = $size;
		$oSelect->FirstOption = STR_CMMN_SELECTONE;
		$oSelect->CastToInt = true;

		while ($oDB->next_record())
			$oSelect->AddOption($oDB->f(0), $oDB->f(1));

		return $oSelect->GetHTML();
	}

	function show($orderBy = 'short')
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ROLE, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$oView = new boView();
		$oView->startrow = 0;
		$oView->numrows = 25;

		$filterActive = '';
		if (IsSet($_REQUEST['filterActive']))
			$filterActive = $_REQUEST['filterActive'];

		$oView->table = 'dcl_role';
		$oView->title = 'Browse Roles';
		$oView->AddDef('columnhdrs', '', array('ID', 'Active', 'Role'));

		$oView->AddDef('columns', '', array('role_id', 'active', 'role_desc'));

		$oView->AddDef('order', '', array('role_desc'));

		if ($filterActive == 'Y' || $filterActive == 'N')
			$oView->AddDef('filter', 'active', "'$filterActive'");

		$oHtml = new htmlRoleBrowse();
		$oHtml->Render($oView);
	}
}
