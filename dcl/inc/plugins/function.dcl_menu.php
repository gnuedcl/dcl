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
		$myRetVal = '<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="javascript:;">';
		$myRetVal .= htmlspecialchars($menuItem->Title) . ' <b class="caret"></b></a>';

		if ($menuItem->HasItems())
		{
			$myRetVal .= '<ul class="dropdown-menu">';

			foreach ($menuItem->GetItems() as $menuSubItem)
			{
				$myRetVal .= '<li><a href="';
				if ($menuSubItem->Url == '')
					$myRetVal .= 'javascript:;';
				else
					$myRetVal .= htmlspecialchars($menuSubItem->Url);

				$myRetVal .= '"';

				if ($menuSubItem->Target != '')
					$myRetVal .= ' target="' . htmlspecialchars($menuSubItem->Target) . '"';

				$myRetVal .= '>';
				$myRetVal .= htmlspecialchars($menuSubItem->Title);
				$myRetVal .= '</a></li>';
			}

			$myRetVal .= '</ul>';
		}

		$myRetVal .= '</li>';

		return $myRetVal;
	}

	$retVal = '<ul class="nav navbar-nav">';

	foreach ($menu->GetItems() as $menuItem)
		$retVal .= renderMenuItem($menuItem);

	$retVal .= '</ul>';

	return $retVal;
}