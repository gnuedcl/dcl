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

class SecurityHelper
{
	function ValidateMenuAction()
	{
		return true;
	}

	function IsPublicUser()
	{
		return $this->HasPerm(DCL_ENTITY_GLOBAL, DCL_PERM_PUBLICONLY);
	}

	function IsOrgUser()
	{
		// Restricted to certain orgs for some functions
		return $this->HasAnyPerm(array(DCL_ENTITY_TICKET => array($this->PermArray(DCL_PERM_VIEWACCOUNT)), DCL_ENTITY_WORKORDER => array($this->PermArray(DCL_PERM_VIEWACCOUNT))));
	}
	
	function HasSpecificRole($role_id)
	{
		// Checks the current user for specific role - should only be used by custom plugins
		if (($role_id = Filter::ToInt($role_id)) === null)
		{
			throw new InvalidDataException();
		}
		
		$oDB = new UserRoleModel();
		return $oDB->HasSpecificRole(DCLID, $role_id);
	}

	function HasPerm($entity, $perm, $id1 = 0, $id2 = 0)
	{
		// Cheapness to be sure - limit public users to only a subset of entities that recognize public/private
		// checking DCL_PERM_PUBLICONLY is to prevent endless loop
		if ($perm != DCL_PERM_PUBLICONLY && $this->IsPublicUser())
		{
			if (!in_array($entity, array(DCL_ENTITY_WORKORDER, DCL_ENTITY_TIMECARD, DCL_ENTITY_TICKET, DCL_ENTITY_RESOLUTION, DCL_ENTITY_FAQ, DCL_ENTITY_PRODUCT, DCL_ENTITY_PREFS, DCL_ENTITY_ORG, DCL_ENTITY_CONTACT)))
			{
				return false;
			}
		}

		// If we're looking for public or other restriction perms, we don't want to take admin into account
		if (!($perm == DCL_PERM_PUBLICONLY || $perm == DCL_PERM_VIEWSUBMITTED || $perm == DCL_PERM_VIEWACCOUNT))
		{
			// If we have global admin, then all perms are good to go
			$aPermissions = $this->GetPerms(DCL_ENTITY_GLOBAL, 0, 0);
			if (in_array(DCL_PERM_ADMIN, $aPermissions))
				return true;
		}

		// Now check the permissions for the entity type
		$aPermissions = $this->GetPerms($entity, 0, 0);
		if (in_array($perm, $aPermissions))
			return true;
		else if ($id1 == 0) // if we're checking for global, bail out now
			return false;

		// otherwise, Get role permissions for this entity and ID
		$aPermissions = $this->GetPerms($entity, $id1, $id2);
		return in_array($perm, $aPermissions);
	}

	function PermArray($ePerm, $id1 = 0, $id2 = 0)
	{
		return array('perm' => $ePerm, 'id1' => $id1, 'id2' => $id2);
	}

	function HasAnyPerm($aRequestedPerms)
	{
		if (!is_array($aRequestedPerms))
			return false;

		$bHasAnyPerm = false;
		foreach ($aRequestedPerms as $entity => $aPermInfo)
		{
			foreach ($aPermInfo as $aPerm)
			{
				$bHasAnyPerm = $this->HasPerm($entity, $aPerm['perm'], $aPerm['id1'], $aPerm['id2']);
				if ($bHasAnyPerm)
					break;
			}

			if ($bHasAnyPerm)
				break;
		}

		return $bHasAnyPerm;
	}

	function HasAllPerm($aRequestedPerms)
	{
		if (!is_array($aRequestedPerms))
			return false;

		$bHasAnyPerm = true;
		foreach ($aRequestedPerms as $entity => $aPermInfo)
		{
			foreach ($aPermInfo as $aPerm)
			{
				$bHasAnyPerm = $this->HasPerm($entity, $aPerm['perm'], $aPerm['id1'], $aPerm['id2']);
				if (!$bHasAnyPerm)
					break;
			}

			if (!$bHasAnyPerm)
				break;
		}

		return $bHasAnyPerm;
	}

	function &GetPerms($entity, $id1 = 0, $id2 = 0)
	{
		global $g_oSession;

		$sKey = $entity . '_' . $id1 . '_' . $id2;
		$sGlobalKey = $entity . '_0_0';
		$g_oSession->ValueRef('Permissions', $aPermissions);
		$oDB = new UserRoleModel();

		if (!is_array($aPermissions) || !isset($aPermissions[$sGlobalKey]))
		{
			if (!is_array($aPermissions))
				$aPermissions = array();

			if (!isset($aPermissions[$sGlobalKey]))
			{
				// Loading a specific ID, so let's get role permissions for this entity type
				$aPermissions[$sGlobalKey] = array();
				$oDB->ListPermissions(DCLID, $entity, 0, 0);
				while ($oDB->next_record())
				{
					$aPermissions[$sGlobalKey][] = $oDB->f(0);
				}

				// only cache the entity level permissions in the session since it probably won't change that often
				$g_oSession->Register('Permissions', $aPermissions);
				$g_oSession->Edit();
			}
		}

		// Asked for global entity perms
		if ($id1 == 0)
		{
			return $aPermissions[$sGlobalKey];
		}

		// otherwise, Get role permissions for this entity and object ID (not cached in session)
		$aPermissions = array();
		$oDB->ListPermissions(DCLID, $entity, $id1, $id2);
		while ($oDB->next_record())
		{
			$aPermissions[] = $oDB->f(0);
		}

		return $aPermissions;
	}
}
