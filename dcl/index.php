<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2011 Free Software Foundation
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

include('inc/config.php');
include(DCL_ROOT . 'inc/functions.inc.php');
if (!isset($dcl_info))
{
	// If we had a session, some of the config would be in it
	// and would have been removed above.
	$oConfig = new ConfigurationModel();
	$dcl_info = array();
	$oConfig->Load();
}

$t = new SmartyHelper();

if (IsSet($_REQUEST['cd']))
{
	switch ($_REQUEST['cd'])
	{
		case 1:
			$t->assign('VAL_ERROR', 'Invalid login or password');
			break;
		case 2:
			$t->assign('VAL_ERROR', 'Session expired');
			break;
		case 3:
			$t->assign('VAL_ERROR', 'Could not connect to database');
			break;
		case 4:
			$t->assign('VAL_ERROR', 'Logout successful');
			break;
		case 5:
			$t->assign('VAL_ERROR', 'Account locked');
			break;
		default:
			$t->assign('VAL_ERROR', 'Unknown error');
	}
}

$t->assign('VAL_WELCOME', $dcl_info['DCL_LOGIN_MESSAGE']);
$t->assign('TXT_TITLE', $dcl_info['DCL_HTML_TITLE']);
$t->assign('TXT_VERSION', $dcl_info['DCL_VERSION']);
$t->assign('TXT_LOGIN', 'Please Login');
$t->assign('TXT_USER', 'User');
$t->assign('TXT_PASSWORD', 'Password');
$t->assign('BTN_LOGIN', 'Login');
$t->assign('BTN_CLEAR', 'Clear');

if (IsSet($_REQUEST['refer_to']))
	$t->assign('VAL_REFERTO', urldecode($_REQUEST['refer_to']));

$t->Render('login.tpl');
