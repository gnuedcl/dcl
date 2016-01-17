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

function smarty_function_dcl_menu($params, Smarty_Internal_Template $smarty)
{
	$menuList = '<ul class="nav navbar-nav">';

	$menu = DclMainMenuHelper::GetMenu();
	foreach ($menu->GetItems() as $menuItem)
	{
		$menuList .= '<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="javascript:;">';
		$menuList .= htmlspecialchars($menuItem->Title, ENT_QUOTES, 'UTF-8') . ' <b class="caret"></b></a>';

		if ($menuItem->HasItems())
		{
			$menuList .= '<ul class="dropdown-menu">';

			foreach ($menuItem->GetItems() as $menuSubItem)
			{
				$menuList .= '<li><a href="';
				if ($menuSubItem->Url == '')
					$menuList .= 'javascript:;';
				else
					$menuList .= htmlspecialchars($menuSubItem->Url, ENT_QUOTES, 'UTF-8');

				$menuList .= '"';

				if ($menuSubItem->Target != '')
					$menuList .= ' target="' . htmlspecialchars($menuSubItem->Target, ENT_QUOTES, 'UTF-8') . '"';

				$menuList .= '>';
				$menuList .= htmlspecialchars($menuSubItem->Title);
				$menuList .= '</a></li>';
			}

			$menuList .= '</ul>';
		}

		$menuList .= '</li>';
	}

	$menuList .= '</ul>';

	$menuModel = new MenuModel();
	$smarty->assignByRef('Menu', $menuModel);
	$smarty->assign('MenuList', $menuList);
}