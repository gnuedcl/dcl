<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2012 Free Software Foundation
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

function smarty_function_dcl_personnel_link($params, &$smarty)
{
	foreach ($smarty->getPluginsDir() as $pluginDir)
	{
		$pluginPath = $pluginDir . '/shared.escape_special_chars.php';
		if (file_exists($pluginPath))
		{
			require_once($pluginPath);
			break;
		}
	}

	if (!isset($params['text']))
	{
		return '';
	}
	
	if (!HasPermission(DCL_ENTITY_PERSONNEL, DCL_PERM_VIEW))
	{
		return smarty_function_escape_special_chars($params['text']);
	}
	
	if (!isset($params['id']))
	{
		$smarty->trigger_error('dcl_personnel_link: missing parameter id');
		return;
	}
	
	return '<a href="' . UrlAction('Personnel', 'Detail', 'id=' . $params['id']) . '">' . smarty_function_escape_special_chars($params['text']) . '</a>';
}