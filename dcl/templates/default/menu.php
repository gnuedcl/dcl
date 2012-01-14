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
	global $dcl_info, $g_oSec, $g_oSession;

	$sTemplateSet = GetDefaultTemplateSet();
	
	include(DCL_ROOT . 'templates/' . $sTemplateSet . '/navbar.php');

	$t = new SmartyHelper();

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
	$t->assign('PERM_WORKSPACE', $g_oSec->HasPerm(DCL_ENTITY_WORKSPACE, DCL_PERM_VIEW));
	$t->assign('PERM_HOTLISTVIEW', $g_oSec->HasPerm(DCL_ENTITY_HOTLIST, DCL_PERM_VIEW));
	$t->assign('VAL_WORKSPACE', $g_oSession->Value('workspace'));

	$t->assign('VAL_DCL_MENU', $GLOBALS['DCL_MENU']);
	
	$oNav = new DCLNavBar;
	$t->assign('NAV_BOXEN', $oNav->getHtml());

	$t->Render('menu.tpl');
}
