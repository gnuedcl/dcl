<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2014 Free Software Foundation
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

class DclAdminMenuHelper
{
	public static function GetMenu()
	{
		$menuItem = new DclMenuItem(DCL_MENU_ADMIN);

		if (HasPermission(DCL_ENTITY_PREFS, DCL_PERM_PASSWORD))
			$menuItem->Add(new DclMenuItem(DCL_MENU_CHANGEPASSWORD, UrlAction('Personnel', 'EditPassword')));

		if (HasPermission(DCL_ENTITY_PREFS, DCL_PERM_MODIFY))
			$menuItem->Add(new DclMenuItem(DCL_MENU_PREFERENCES, UrlAction('htmlPreferences', 'modify')));

		if (HasPermission(DCL_ENTITY_ADMIN, DCL_PERM_VIEW))
			$menuItem->Add(new DclMenuItem(DCL_MENU_SYSTEMSETUP, UrlAction('SystemSetup', 'Index')));

		if (HasPermission(DCL_ENTITY_SESSION, DCL_PERM_VIEW))
			$menuItem->Add(new DclMenuItem(DCL_MENU_SESSIONS, UrlAction('Session', 'Index')));

		if (HasPermission(DCL_ENTITY_ADMIN, DCL_PERM_MODIFY))
			$menuItem->Add(new DclMenuItem(DCL_MENU_SEC_AUDITING, UrlAction('boSecAudit', 'Show')));

		return $menuItem;
	}
}