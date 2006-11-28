<?php
/*
 * $Id: class.dbUserRole.inc.php,v 1.1.1.1 2006/11/27 05:30:47 mdean Exp $
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

LoadStringResource('db');
class dbUserRole extends dclDB
{
	function dbUserRole()
	{
		parent::dclDB();
		$this->TableName = 'dcl_user_role';
		LoadSchema($this->TableName);

		parent::Clear();
	}

	function ListPermissions($personnel_id, $entity_type_id, $entity_id1 = 0, $entity_id2 = 0)
	{
		$sSQL = "SELECT DISTINCT rp.perm_id FROM dcl_user_role ur, dcl_role_perm rp WHERE ur.role_id = rp.role_id AND personnel_id = $personnel_id AND ((ur.entity_type_id = rp.entity_id AND ur.entity_type_id = $entity_type_id AND ur.entity_id1 = $entity_id1 AND ur.entity_id2 = $entity_id1) OR (rp.entity_id = $entity_type_id AND ur.entity_type_id = 0 AND ur.entity_id1 = 0 AND ur.entity_id2 = 0))";

		return $this->Query($sSQL);
	}

	function &GetGlobalRoles($personnel_id = -1)
	{
		$sSQL = "SELECT r.role_id, r.role_desc, ur.role_id FROM dcl_role r LEFT JOIN dcl_user_role ur ON r.role_id = ur.role_id AND ur.personnel_id = $personnel_id AND ur.entity_type_id = " . DCL_ENTITY_GLOBAL . ' ORDER BY r.role_desc';
		if ($this->Query($sSQL) == -1)
			return null;

		$aRetVal = array();
		while ($this->next_record())
		{
			$iRoleID = (int)$this->f(0);
			$sRole = $this->f(1);
			$sHasRole = $this->f(2) != '' ? 'true' : 'false';

			$aRetVal[$sRole] = array('role_id' => $iRoleID, 'selected' => $sHasRole);
		}

		return $aRetVal;
	}

	function DeleteGlobalRolesNotIn($personnel_id, $sRoleList = '')
	{
		if ($sRoleList == '')
			$sRoleList = '-1';

		return $this->Execute("DELETE FROM dcl_user_role WHERE personnel_id = $personnel_id AND entity_type_id = " . DCL_ENTITY_GLOBAL . " AND role_id NOT IN ($sRoleList)");
	}
}
?>