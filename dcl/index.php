<?php
/*
 * $Id$
 *
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2004 Free Software Foundation
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

function GetDomainCombo()
{
	global $dcl_domain_info;

	if (count($dcl_domain_info) > 1)
	{
		$retVal = '<select name="DOMAIN">';
		reset($dcl_domain_info);
		while (list($key, $val) = each($dcl_domain_info))
			$retVal .= '<option value="' . $key . '">' . $val['name'] . '</option>';

		$retVal .= '</select>';
	}
	else
		$retVal = '<input type="hidden" name="DOMAIN" value="default">' . $dcl_domain_info['default']['name'];

	return $retVal;
}

$t = new DCL_Smarty();

if (IsSet($GLOBALS['cd']))
{
	switch ($GLOBALS['cd'])
	{
		case 1:
			$t->assign('VAL_ERROR', 'Invalid login or password');
			break;
		case 2:
			$t->assign('VAL_ERROR', 'Could not verify session');
			break;
		case 3:
			$t->assign('VAL_ERROR', 'Could not connect to database');
			break;
		case 4:
			$t->assign('VAL_ERROR', 'Logout successful');
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
$t->assign('TXT_DOMAIN', 'Domain');
$t->assign('BTN_LOGIN', 'Login');
$t->assign('BTN_CLEAR', 'Clear');
$t->assign('CMB_DOMAIN', GetDomainCombo());

if (IsSet($GLOBALS['refer_to']))
	$t->assign('VAL_REFERTO', urldecode($GLOBALS['refer_to']));

$t->Render('login.tpl');
