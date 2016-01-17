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

// This should only be called from main.php or such.  Template files need to include config.php to get info before login
require_once('inc/config.php');

$bNoHeader = (IsSet($_REQUEST['menuAction']) && ($_REQUEST['menuAction'] == 'htmlTicketDetail.Download' || $_REQUEST['menuAction'] == 'WorkOrder.DownloadAttachment' || $_REQUEST['menuAction'] == 'LineGraphImageHelper.Show'));

require_once(DCL_ROOT . 'inc/functions.inc.php');

$g_oSec = new SecurityHelper();

function Refresh($toHere = 'logout.php', $session_id = '')
{
	global $dcl_info;

    $bIsLogin = (mb_substr($toHere, 0, 10) == 'logout.php');

    if ($bIsLogin)
    {
        $theCookie = '';
        if (IsSet($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != '')
            $toHere .= sprintf('%srefer_to=%s', mb_strpos($toHere, '?') > 0 ? '&' : '?', urlencode($_SERVER['QUERY_STRING']));
    }
    else
        $theCookie = $session_id;

	$httpDomain = '';
	if (preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}(:[0-9]+)*$/', $_SERVER['HTTP_HOST']))
	{
		$httpDomain = $_SERVER['HTTP_HOST'];
	}
	else if (preg_match('/.*\..*$/', $_SERVER['HTTP_HOST']))
	{
		$httpDomain = preg_replace('/^www\./i', '', $_SERVER['HTTP_HOST']);
		$httpDomain = '.' . $httpDomain;
	}

	if (($p = mb_strpos($httpDomain, ':')) !== false)
		$httpDomain = mb_substr($httpDomain, 0, $p);

	setcookie('DCLINFO', $theCookie, 0, '/', $httpDomain, UseHttps() || $dcl_info['DCL_FORCE_SECURE_COOKIE'] == 'Y', true);
	header("Location: $toHere");

	exit;
}

if (IsSet($_COOKIE['DCLINFO']) && !IsSet($_POST['UID']))
{
    $g_oSession = new SessionModel();
	$slashIdx = mb_strpos($_COOKIE['DCLINFO'], '/');
	if ($slashIdx > 0)
		$dcl_session_id = mb_substr($_COOKIE['DCLINFO'], 0, $slashIdx);
	else
		$dcl_session_id = $_COOKIE['DCLINFO'];

    if (mb_strlen($dcl_session_id) != 32)
        Refresh(DCL_WWW_ROOT . 'logout.php?cd=2');

    if (!$g_oSession->conn)
        Refresh(DCL_WWW_ROOT . 'logout.php?cd=3');

    if ($g_oSession->Load($dcl_session_id) == false)
        Refresh(DCL_WWW_ROOT . 'logout.php?cd=2');

    if (!$g_oSession->IsValidSession())
        Refresh(DCL_WWW_ROOT . 'logout.php?cd=2');

    LoadStringResource('cmmn');
}
else
{
	$oConfig = new ConfigurationModel();
	$dcl_info = array();
	$oConfig->Load();

	$authenticateModel = new AuthenticateSqlModel();
    $authInfo = array();
    if ($authenticateModel->IsValidLogin($authInfo))
    {
		if ($authInfo['locked'])
		{
			Refresh('logout.php?cd=5');
		}
		else
		{
			SessionHelper::Start($authInfo['id'], $authInfo['email'], $authInfo['short'], $authInfo['contact_id']);
			SecurityAuditModel::AddAudit(DCLID, 'login');
			PersonnelModel::SetLastLoginDate(DCLID);

			$forcePwdChange = $authInfo['forcepwdchange'];
			if (!$forcePwdChange && $authInfo['lastpwdchange'] != null && $dcl_info['DCL_PASSWORD_MAX_AGE'] > 0)
			{
				$nextChgDt = new DateTime($authInfo['lastpwdchange']);
				$nextChgDt->modify('+' . $dcl_info['DCL_PASSWORD_MAX_AGE'] . ' days');
				$nowDt = new DateTime();

				$forcePwdChange = $nowDt >= $nextChgDt;
			}

			if ($forcePwdChange)
			{
				$g_oSession->Register('ForcePasswordChange', '1');
				$g_oSession->Edit();
			}

			$menuAction = 'menuAction=HomePage.Index';

			if (IsSet($_POST['refer_to']) && $_POST['refer_to'] != '')
				$menuAction = urldecode($_POST['refer_to']);

			Refresh('main.php?' . $menuAction, $g_oSession->dcl_session_id);
		}
    }
    else
	{
		Refresh('logout.php?cd=1');
	}
}
