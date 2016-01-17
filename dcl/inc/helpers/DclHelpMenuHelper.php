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

class DclHelpMenuHelper
{
	public static function GetMenu()
	{
		$menuItem = new DclMenuItem(DCL_MENU_HELP);

		if (HasPermission(DCL_ENTITY_FAQ, DCL_PERM_VIEW))
			$menuItem->Add(new DclMenuItem(DCL_MENU_FAQS, UrlAction('Faq', 'Index')));

		$menuItem->Add(new DclMenuItem(DCL_MENU_DCLHOMEPAGE, 'https://github.com/gnuedcl/dcl', '_blank'));
		$menuItem->Add(new DclMenuItem('GNU Enterprise', 'http://www.gnuenterprise.org/index.php', '_blank'));
		$menuItem->Add(new DclMenuItem(DCL_MENU_LICENSEINFO, UrlAction('License', 'Index')));
		$menuItem->Add(new DclMenuItem(DCL_MENU_VERSIONINFO, UrlAction('About', 'Detail')));

		return $menuItem;
	}
}