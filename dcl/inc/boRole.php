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

class boRole extends boAdminObject
{
	function boRole()
	{
		parent::boAdminObject();
		
		$this->oDB = new RoleModel();
		$this->sKeyField = 'role_id';
		$this->Entity = DCL_ENTITY_ROLE;
	}
	
	function add($aSource)
	{
		if (parent::add($aSource) == -1)
			return;
		
		if (is_array($aSource['rolePerms']))
		{
			$oRolePerm = new RolePermissionModel();
			foreach ($aSource['rolePerms'] as $entityPerm)
			{
				list($entity_id, $perm_id) = explode('_', $entityPerm);
				$oRolePerm->InitFromArray(array('role_id' => $this->oDB->role_id, 'entity_id' => $entity_id, 'perm_id' => $perm_id));
				$oRolePerm->add();
			}
		}
	}
	
	function modify($aSource)
	{
		if (parent::modify($aSource) == -1)
			return;
		
		$oRolePerm = new RolePermissionModel();
		$oRolePerm->DeleteRole($this->oDB->role_id);
		
		if (is_array($aSource['rolePerms']))
		{
			foreach ($aSource['rolePerms'] as $entityPerm)
			{
				list($entity_id, $perm_id) = explode('_', $entityPerm);
				$oRolePerm->InitFromArray(array('role_id' => $this->oDB->role_id, 'entity_id' => (int)$entity_id, 'perm_id' => (int)$perm_id));
				if ($oRolePerm->add() == -1)
					return -1;
			}
		}
		
		return 0;
	}
	
	function delete($aSource)
	{
		global $g_oSec;
		if (!$g_oSec->HasPerm(DCL_ENTITY_ROLE, DCL_PERM_DELETE))
			throw new PermissionDeniedException();
			
		$oDB = new RolePermissionModel();
		$oDB->DeleteRole($aSource['role_id']);
		
		return parent::Delete($aSource);
	}
}
