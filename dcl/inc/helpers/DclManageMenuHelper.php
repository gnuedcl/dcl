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

class DclManageMenuHelper
{
	public static function GetMenu()
	{
		global $dcl_info;

		$menuItem = new DclMenuItem(DCL_MENU_MANAGE);

		if (HasPermission(DCL_ENTITY_ORG, DCL_PERM_VIEW))
			$menuItem->Add(new DclMenuItem('Organizations', UrlAction('htmlOrgBrowse', 'show', 'filterActive=Y')));

		if (HasPermission(DCL_ENTITY_CONTACT, DCL_PERM_VIEW))
			$menuItem->Add(new DclMenuItem('Contacts', UrlAction('htmlContactBrowse', 'show', 'filterActive=Y')));

		if (HasPermission(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH) || HasPermission(DCL_ENTITY_TICKET, DCL_PERM_SEARCH))
			$menuItem->Add(new DclMenuItem(STR_CMMN_TAGS, UrlAction('htmlTags', 'browse')));

		if (HasPermission(DCL_ENTITY_FORMS, DCL_PERM_VIEW))
			$menuItem->Add(new DclMenuItem(DCL_MENU_CHECKLISTS, UrlAction('boChecklists', 'show')));

		if (HasPermission(DCL_ENTITY_WORKSPACE, DCL_PERM_VIEW))
			$menuItem->Add(new DclMenuItem('Workspaces', UrlAction('htmlWorkspaceBrowse', 'show')));

		if (HasPermission(DCL_ENTITY_HOTLIST, DCL_PERM_VIEW))
			$menuItem->Add(new DclMenuItem('Hotlists', UrlAction('htmlHotlistBrowse', 'show')));

		if (HasPermission(DCL_ENTITY_PRODUCT, DCL_PERM_VIEW))
			$menuItem->Add(new DclMenuItem(DCL_MENU_PRODUCTS, UrlAction('Product', 'Index')));

		if (HasPermission(DCL_ENTITY_SAVEDSEARCH, DCL_PERM_VIEW))
			$menuItem->Add(new DclMenuItem(DCL_MENU_VIEWS, UrlAction('htmlViews', 'PrintAll')));

		if (HasPermission(DCL_ENTITY_WORKORDER, DCL_PERM_VIEW) || HasPermission(DCL_ENTITY_WORKORDER, DCL_PERM_VIEWSUBMITTED) || HasPermission(DCL_ENTITY_WORKORDER, DCL_PERM_VIEWACCOUNT) ||
			HasPermission(DCL_ENTITY_TICKET, DCL_PERM_VIEW) || HasPermission(DCL_ENTITY_TICKET, DCL_PERM_VIEWSUBMITTED) || HasPermission(DCL_ENTITY_TICKET, DCL_PERM_VIEWACCOUNT))
			$menuItem->Add(new DclMenuItem(DCL_MENU_WATCHES, UrlAction('boWatches', 'showall')));

		if (HasPermission(DCL_ENTITY_WORKORDER, DCL_PERM_REPORT) || HasPermission(DCL_ENTITY_TICKET, DCL_PERM_REPORT))
			$menuItem->Add(new DclMenuItem('Metrics', UrlAction('htmlMetrics', 'show')));

		if (HasPermission(DCL_ENTITY_GLOBAL, DCL_PERM_VIEWWIKI) && $dcl_info['DCL_WIKI_ENABLED'] == 'Y')
			$menuItem->Add(new DclMenuItem(DCL_MENU_MAINWIKI, UrlAction('htmlWiki', 'show', 'name=FrontPage&type=0')));

		return $menuItem;
	}
}