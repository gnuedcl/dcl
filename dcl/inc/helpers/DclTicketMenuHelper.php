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

class DclTicketMenuHelper
{
	public static function GetMenu()
	{
		$menuItem = new DclMenuItem(DCL_MENU_TICKETS);
		if (HasPermission(DCL_ENTITY_TICKET, DCL_PERM_ACTION))
			$menuItem->Add(new DclMenuItem(DCL_MENU_MYTICKETS, UrlAction('htmlTickets', 'show', 'filterReportto=' . DCLID)));

		if (HasPermission(DCL_ENTITY_TICKET, DCL_PERM_ADD))
			$menuItem->Add(new DclMenuItem(DCL_MENU_NEW, UrlAction('boTickets', 'add')));

		if (HasPermission(DCL_ENTITY_TICKET, DCL_PERM_REPORT))
		{
			$menuItem->Add(new DclMenuItem(DCL_MENU_ACTIVITY, UrlAction('reportTicketActivity', 'getparameters')));
			$menuItem->Add(new DclMenuItem(DCL_MENU_GRAPH, UrlAction('boTickets', 'graph')));
		}

		if (HasPermission(DCL_ENTITY_TICKET, DCL_PERM_SEARCH))
		{
			$searchMenu = new DclMenuItem(DCL_MENU_SEARCH, UrlAction('htmlTicketSearches', 'Show'));
			$menuItem->Add($searchMenu);

			$oDB = new SavedSearchesModel();
			if ($oDB->ListByUser(DCLID, DCL_ENTITY_TICKET) !== -1)
			{
				while ($oDB->next_record())
					$searchMenu->Add(new DclMenuItem($oDB->f('name'), UrlAction('boViews', 'exec', 'viewid=' . $oDB->f('viewid'))));
			}
		}

		if (HasPermission(DCL_ENTITY_TICKET, DCL_PERM_VIEW) || HasPermission(DCL_ENTITY_TICKET, DCL_PERM_VIEWSUBMITTED) || HasPermission(DCL_ENTITY_TICKET, DCL_PERM_VIEWACCOUNT))
			$menuItem->Add(new DclMenuItem(DCL_MENU_BROWSE, UrlAction('htmlTickets', 'show')));

		return $menuItem;
	}
}