<?php
/*
 * $Id$
 *
 * Double Choco Latte - Source Configuration Management System
 * Copyright (C) 1999  Michael L. Dean & Tim R. Norman
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
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

function renderDCLMenu()
{
	global $dcl_info;

	if (!empty($_SERVER))
		extract($_SERVER);

	$t = CreateTemplate(array('hForm' => 'menu.tpl'));

	$t->set_var('DIR_IMAGES', 'templates/' . GetDefaultTemplateSet() . '/img');
	$t->set_var('DIR_CSS', 'templates/' . GetDefaultTemplateSet() . '/css');
	$t->set_var('DIR_JS', 'js');
	$t->set_var('LNK_LOGOFF', menuLink('logout.php'));
	$t->set_var('LNK_HOME', menuLink('', 'menuAction=htmlMyDCL.show'));
	$t->set_var('LNK_PREFERENCES', menuLink('', 'menuAction=htmlPreferences.modify'));
	$t->set_var('TXT_WORKORDERS', DCL_MENU_WORKORDERS);
	$t->set_var('TXT_TICKETS', DCL_MENU_TICKETS);
	$t->set_var('TXT_PROJECTS', DCL_MENU_PROJECTS);
	$t->set_var('TXT_HOME', DCL_MENU_HOME);
	$t->set_var('TXT_PREFERENCES', DCL_MENU_PREFERENCES);
	$t->set_var('TXT_LOGOFF', DCL_MENU_LOGOFF);

	$aMenu = getMenuJS();
	$t->set_var('JS_INIT_DCL_MENU', $aMenu[0]);
	$t->set_var('VAL_DCL_MENU', $aMenu[1]);

	$t->pparse('out', 'hForm');
}

import('LayersMenu');
function getMenuJS()
{
	$mid = new LayersMenu(3, 8, 1, 1);
	$mid->setMenuStructureString(getMenuString());
	$mid->parseStructureForMenu('hormenu1');
	$mid->newHorizontalMenu('hormenu1');

	return array($mid->makeHeader(), $mid->getMenu('hormenu1') . "\n" . $mid->makeFooter());
}

function getMenuLink($link)
{
	if (!ereg('html$', $link) && !ereg('\.php$', $link))
		return menuLink('', 'menuAction=' . $link);
	elseif (substr($link, 0, 7) != 'http://')
		return menuLink(DCL_WWW_ROOT . $link);

	return $link;
}

function getMenuString()
{
	$sRetVal = '';

	foreach ($GLOBALS['DCL_MENU'] as $menuname => $themenu)
	{
		if (count($themenu) < 3)
			continue;

		$sSubMenu = '';
		foreach ($themenu as $name => $item)
		{
			reset($item);
			list($link, $bHasPerm) = $item;
			if ($bHasPerm)
			{
				$sSubMenu .= '..|' . $name . '|' . getMenuLink($link) . "\n";
			}
		}

		if ($sSubMenu != '')
			$sRetVal .= '.|' . $menuname . "\n" . $sSubMenu;
	}

	return $sRetVal;
}
?>
