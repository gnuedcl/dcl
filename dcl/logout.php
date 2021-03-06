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

require_once('inc/config.php');
require_once(DCL_ROOT . 'inc/functions.inc.php');

if (isset($_COOKIE['DCLINFO']))
{
	$g_oSession = new SessionModel();
	$dcl_session_id = $_COOKIE['DCLINFO'];
	if (mb_strlen($dcl_session_id) == 32)
	{
		$g_oSession->Connect();
		if (!$g_oSession->conn)
			Refresh(DCL_WWW_ROOT . 'index.php?cd=3');

		if ($g_oSession->Load($dcl_session_id) == false)
			Refresh(DCL_WWW_ROOT . 'index.php?cd=2');

		if ($g_oSession->IsValidSession())
		{
			if (isset($GLOBALS['dcl_info']) && isset($GLOBALS['dcl_info']['DCL_SEC_AUDIT_ENABLED']) && $GLOBALS['dcl_info']['DCL_SEC_AUDIT_ENABLED']=='Y')
			{
				$oSecAuditDB = new SecurityAuditModel();
				$oSecAuditDB->id = DCLID;
				$oSecAuditDB->actionon = DCL_NOW;
				$oSecAuditDB->actiontxt = 'logout';
				$oSecAuditDB->actionparam = '';
				$oSecAuditDB->Add();
			}

			$g_oSession->Delete(array('dcl_session_id' => $g_oSession->dcl_session_id));
			$g_oSession->Clear();
		}
	}
}

if (isset($_REQUEST['cd']) && ($_REQUEST['cd'] == '1' || $_REQUEST['cd'] == '2' || $_REQUEST['cd'] == '3' || $_REQUEST['cd'] == '4' || $_REQUEST['cd'] == '5'))
	Refresh(DCL_WWW_ROOT . 'index.php?cd=' . $_REQUEST['cd']);
else
	Refresh(DCL_WWW_ROOT . 'index.php?cd=4');

function Refresh($toHere = 'index.php')
{
	global $dcl_info;

	$httpDomain = '';
	if (preg_match('/^[0-9]{2,3}\.[0-9]{2,3}\.[0-9]{2,3}\.[0-9]{2,3}$/', $_SERVER['HTTP_HOST']))
	{
		$httpDomain = $_SERVER['HTTP_HOST'];
	}
	else if (preg_match('/.*\..*$/', $_SERVER['HTTP_HOST']))
	{
		$httpDomain = preg_replace('/^www\./i', '', $_SERVER['HTTP_HOST']);
		$httpDomain = '.' . $httpDomain;
	}

	if (isset($dcl_info))
	{
		$forceSecureCookie = $dcl_info['DCL_FORCE_SECURE_COOKIE'] == 'Y';
	}
	else
	{
		$db = new DbProvider();
		if ($db->Query('SELECT dcl_config_varchar FROM dcl_config WHERE dcl_config_name = ' . $db->Quote('DCL_FORCE_SECURE_COOKIE')) != -1)
		{
			if ($db->next_record())
				$forceSecureCookie = $db->f(0) == 'Y';
		}
	}

	if (($p = mb_strpos($httpDomain, ':')) !== false)
		$httpDomain = mb_substr($httpDomain, 0, $p);

	setcookie('DCLINFO', null, -1, '/', $httpDomain, UseHttps() || $forceSecureCookie, true);

	if (isset($_REQUEST['refer_to']) && $_REQUEST['refer_to'] != '')
	{
		$toHere .= sprintf('%srefer_to=%s', mb_strpos($toHere, '?') > 0 ? '&' : '?', urlencode(urldecode($_REQUEST['refer_to'])));
	}

	header("Location: $toHere");

	exit;
}
