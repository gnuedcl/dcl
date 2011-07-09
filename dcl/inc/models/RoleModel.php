<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2011 Free Software Foundation
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
class RoleModel extends DbProvider
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'dcl_role';
		LoadSchema($this->TableName);

		parent::Clear();
	}

	public function &GetPermissions($role_id = -1)
	{
		$sSQL = 'SELECT e.entity_id, e.entity_desc, p.perm_id, p.perm_desc, rp.perm_id FROM dcl_entity e ';
		$sSQL .= $this->JoinKeyword . ' dcl_entity_perm ep ON e.entity_id = ep.entity_id ';
		$sSQL .= $this->JoinKeyword . ' dcl_perm p ON p.perm_id = ep.perm_id ';
		$sSQL .= "LEFT JOIN dcl_role_perm rp ON rp.entity_id = ep.entity_id AND rp.perm_id = ep.perm_id AND rp.role_id = $role_id ";
		$sSQL .= 'ORDER BY e.entity_desc, p.perm_desc';

		if ($this->Query($sSQL) == -1)
			return null;

		$aRetVal = array();
		while ($this->next_record())
		{
			$iEntityID = (int)$this->f(0);
			$sEntity = $this->f(1);
			$iPermID = (int)$this->f(2);
			$sPerm = $this->f(3);
			$sHasPerm = $this->f(4) != '' ? 'true' : 'false';
			if (!isset($aRetVal[$sEntity]))
				$aRetVal[$sEntity] = array();

			$aRetVal[$sEntity][$iEntityID . '_' . $iPermID] = array('desc' => $sPerm, 'selected' => $sHasPerm);
		}

		return $aRetVal;
	}
}
