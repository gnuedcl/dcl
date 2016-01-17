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

class DclWorkOrderMenuHelper
{
	public static function GetMenu()
	{
		$menuItem = new DclMenuItem(DCL_MENU_WORKORDERS);
		if (HasPermission(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION))
			$menuItem->Add(new DclMenuItem(DCL_MENU_MYWOS, UrlAction('WorkOrder', 'SearchMy')));

		if (HasPermission(DCL_ENTITY_WORKORDER, DCL_PERM_ADD))
			$menuItem->Add(new DclMenuItem(DCL_MENU_NEW, UrlAction('WorkOrder', 'Create')));

		if (HasPermission(DCL_ENTITY_WORKORDER, DCL_PERM_IMPORT))
			$menuItem->Add(new DclMenuItem(DCL_MENU_IMPORT, UrlAction('WorkOrder', 'Import')));

		if (HasPermission(DCL_ENTITY_WORKORDER, DCL_PERM_REPORT))
		{
			$menuItem->Add(new DclMenuItem(DCL_MENU_ACTIVITY, UrlAction('reportPersonnelActivity', 'getparameters')));
			$menuItem->Add(new DclMenuItem(DCL_MENU_GRAPH, UrlAction('WorkOrder', 'GraphCriteria')));
		}

		if (HasPermission(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH))
		{
			$searchMenu = new DclMenuItem(DCL_MENU_SEARCH, UrlAction('WorkOrder', 'Criteria'));
			$menuItem->Add($searchMenu);

			$oDB = new SavedSearchesModel();
			if ($oDB->ListByUser(DCLID, DCL_ENTITY_WORKORDER) !== -1)
			{
				while ($oDB->next_record())
					$searchMenu->Add(new DclMenuItem($oDB->f('name'), UrlAction('boViews', 'exec', 'viewid=' . $oDB->f('viewid'))));
			}
		}

		if (HasPermission(DCL_ENTITY_WORKORDER, DCL_PERM_VIEW) || HasPermission(DCL_ENTITY_WORKORDER, DCL_PERM_VIEWSUBMITTED) || HasPermission(DCL_ENTITY_WORKORDER, DCL_PERM_VIEWACCOUNT))
			$menuItem->Add(new DclMenuItem('Browse', UrlAction('WorkOrder', 'Browse')));

		if (HasPermission(DCL_ENTITY_CHANGELOG, DCL_PERM_VIEW))
			$menuItem->Add(new DclMenuItem('ChangeLog', UrlAction('htmlMetrics', 'show')));

		return $menuItem;
	}
}