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

function smarty_function_dcl_menu($params, &$smarty)
{
	if (!isset($params['menu']))
	{
		$smarty->trigger_error('dcl_menu: missing parameter menu');
		return;
	}

	$menu = $params['menu'];
	if (!is_a($menu, 'DclMenu'))
	{
		$smarty->trigger_error('dcl_menu: incorrect parameter type menu');
		return;
	}

	function renderMenuItem(DclMenuItem $menuItem)
	{
		$myRetVal = '<li><a href="';
		if ($menuItem->Url == '')
			$myRetVal .= 'javascript:;';
		else
			$myRetVal .= htmlspecialchars($menuItem->Url);

		$myRetVal .= '"';

		if ($menuItem->Target != '')
			$myRetVal .= ' target="' . htmlspecialchars($menuItem->Target) . '"';

		$myRetVal .= '>' . htmlspecialchars($menuItem->Title) . '</a>';

		if ($menuItem->HasItems())
		{
			$myRetVal .= '<ul>';

			foreach ($menuItem->GetItems() as $menuSubItem)
				$myRetVal .= renderMenuItem($menuSubItem);

			$myRetVal .= '</ul>';
		}

		$myRetVal .= '</li>';

		return $myRetVal;
	}

	$retVal = '<div class="sf-menu-container"><ul class="sf-menu">';

	foreach ($menu->GetItems() as $menuItem)
		$retVal .= renderMenuItem($menuItem);

	$retVal .= '</ul></div>';

	return $retVal;
}