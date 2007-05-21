<?php
/*
 * $Id$
 *
 * Double Choco Latte - Source Configuration Management System
 * Copyright (C) 1999-2004 Free Software Foundation
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
	global $dcl_info, $g_oSec;

	$sTemplateSet = GetDefaultTemplateSet();
	
	include(DCL_ROOT . 'templates/' . $sTemplateSet . '/navbar.php');

	$t =& CreateSmarty();

	$t->assign('DIR_IMAGES', 'templates/' . $sTemplateSet . '/img');
	$t->assign('DIR_CSS', 'templates/' . $sTemplateSet . '/css');
	$t->assign('DIR_JS', 'js');
	$t->assign('LNK_LOGOFF', menuLink('logout.php'));

	if ($g_oSec->IsPublicUser())
		$t->assign('LNK_HOME', menuLink('', 'menuAction=htmlPublicMyDCL.show'));
	else
		$t->assign('LNK_HOME', menuLink('', 'menuAction=htmlMyDCL.show'));

	$t->assign('LNK_PREFERENCES', menuLink('', 'menuAction=htmlPreferences.modify'));
	$t->assign('TXT_WORKORDERS', DCL_MENU_WORKORDERS);
	$t->assign('TXT_TICKETS', DCL_MENU_TICKETS);
	$t->assign('TXT_PROJECTS', DCL_MENU_PROJECTS);
	$t->assign('TXT_HOME', DCL_MENU_HOME);
	$t->assign('TXT_PREFERENCES', DCL_MENU_PREFERENCES);
	$t->assign('TXT_LOGOFF', DCL_MENU_LOGOFF);
	
	$t->assign('PERM_WORKORDERSEARCH', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH) || $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEW));
	$t->assign('PERM_TICKETSEARCH', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_SEARCH) || $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_VIEW));
	$t->assign('PERM_PROJECTSEARCH', $g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_SEARCH) || $g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEW));
	$t->assign('PERM_PREFS', $g_oSec->HasPerm(DCL_ENTITY_PREFS, DCL_PERM_MODIFY));

	$aMenu = &getMenuJS();
	$t->assign('JS_INIT_DCL_MENU', $aMenu[0]);
	$t->assign('VAL_DCL_MENU', $aMenu[1]);
	
	$oNav = new DCLNavBar;
	$t->assign('NAV_BOXEN', $oNav->getHtml());

	SmartyDisplay($t, 'menu.tpl');
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
		if ($menuname == DCL_MENU_HOME || $menuname == DCL_MENU_LOGOFF)
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
