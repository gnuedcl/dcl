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

if (!IsSet($GLOBALS['LOGIN_PHP_INCLUDED']))
{
	$GLOBALS['LOGIN_PHP_INCLUDED'] = 1;

	// This should only be called from main.php or such.  Template files need to include config.php to get info before login
	if (!defined('__DCL_CONFIG_INCLUDED__'))
		include_once('inc/config.php');

	$bNoHeader = (IsSet($menuAction) && ($menuAction == 'htmlTicketDetail.Download' || $menuAction == 'htmlWorkOrderDetail.Download' || $menuAction == 'boGraph.Show'));

	if (!defined('DCL_ENTITY_GLOBAL'))
		include_once(DCL_ROOT . 'inc/functions.inc.php');

	$g_oSec = CreateObject('dcl.boSecurity');

	function Refresh($toHere = 'logout.php', $session_id = '', $domain = 'default')
	{
		global $DCLINFO, $DCLUI;
		if (!empty($_SERVER))
			extract($_SERVER);

		$bIsLogin = (substr($toHere, 0, 10) == 'logout.php');

		if ($bIsLogin)
		{
			$theCookie = '';
			if (IsSet($QUERY_STRING) && $QUERY_STRING != '')
				$toHere .= sprintf('%srefer_to=%s', strpos($toHere, '?') > 0 ? '&' : '?', urlencode($QUERY_STRING));
		}
		else
			$theCookie = $session_id . '/' . $domain;

		if (DCL_COOKIE_METHOD == 'header')
		{
			$hdr = '';
			if (DCL_REDIR_METHOD == 'php')
				$hdr = "Location: $toHere\n";

			$hdr .= "Set-Cookie: DCLINFO=$theCookie\n";
			$hdr .= "\n";

			Header($hdr);
			if ($bIsLogin)
				exit;
		}

		if (DCL_COOKIE_METHOD == 'php')
		{
			$httpDomain = '';
			if (ereg('^[0-9]{2,3}\.[0-9]{2,3}\.[0-9]{2,3}\.[0-9]{2,3}$', $HTTP_HOST))
			{
				$httpDomain = $HTTP_HOST;
			}
			else if (ereg('.*\..*$', $HTTP_HOST))
			{
				$httpDomain = eregi_replace('^www\.', '', $HTTP_HOST);
				$httpDomain = '.' . $httpDomain;
			}

			if (($p = strpos($httpDomain, ':')) !== false)
				$httpDomain = substr($httpDomain, 0, $p);
			
			SetCookie('DCLINFO', $theCookie, 0, '/', $httpDomain);

			if (DCL_REDIR_METHOD == 'php')
			{
				Header("Location: $toHere\n\n");
				if ($bIsLogin)
					exit;
			}
		}

		print('<html><head>');

		if (DCL_COOKIE_METHOD == 'meta')
		{
			print("<meta http-equiv=\"Set-Cookie\" content=\"DCLINFO=$theCookie\">");
		}

		print("<meta http-equiv=\"refresh\" content=\"00;URL=$toHere\">");
		print('</head>');
		if ($bIsLogin)
		{
			print('<body bgcolor="#FFFFFF"></body></html>');
			exit;
		}
	}

	if (IsSet($_COOKIE['DCLINFO']) && !IsSet($_POST['UID']))
	{
		$g_oSession = CreateObject('dcl.dbSession');
		list($dcl_session_id, $DOMAIN) = explode('/', $_COOKIE['DCLINFO']);
		if (strlen($dcl_session_id) != 32)
			Refresh(DCL_WWW_ROOT . 'logout.php?cd=2');

		$g_oSession->Connect();
		if (!$g_oSession->conn)
			Refresh(DCL_WWW_ROOT . 'logout.php?cd=3');

		if ($g_oSession->Load($dcl_session_id) == false)
			Refresh(DCL_WWW_ROOT . 'logout.php?cd=2');

		if (!$g_oSession->conn)
			Refresh(DCL_WWW_ROOT . 'logout.php?cd=3');

		if (!$g_oSession->IsValidSession())
			Refresh(DCL_WWW_ROOT . 'logout.php?cd=2');

		LoadStringResource('cmmn');
	}
	else
	{
		$obj = GetAuthenticator();
		$aAuthInfo = array();
		if ($obj->IsValidLogin($aAuthInfo))
		{
			$oConfig = CreateObject('dcl.dbConfig');
			$dcl_info = array();
			$oConfig->Load();

			$g_oSession = CreateObject('dcl.dbSession');
			$g_oSession->Connect();
			if (!$g_oSession->conn)
				Refresh('logout.php?cd=3');

			$g_oSession->personnel_id = $aAuthInfo['id'];
			$g_oSession->Add();

			$oPreferences = CreateObject('dcl.dbPreferences');
			$oPreferences->Connect();
			$oPreferences->Load($aAuthInfo['id']);

			// Save the user ID and copy it to global space so Security object can use the info
			$g_oSession->Register('DCLID', $aAuthInfo['id']);
			$GLOBALS['DCLID'] = $aAuthInfo['id'];

			$g_oSession->Register('DCLNAME', trim($aAuthInfo['short']));
			$g_oSession->Register('USEREMAIL', $aAuthInfo['email']);
			$g_oSession->Register('contact_id', $aAuthInfo['contact_id']);
			$g_oSession->Register('dcl_info', $dcl_info);
			$g_oSession->Register('dcl_preferences', $oPreferences->preferences_data);

			// If we have org restrictions, cache the affiliated orgs for this contact record
			if ($aAuthInfo['contact_id'] != null && $aAuthInfo['contact_id'] > 0)
			{
				if ($g_oSec->IsOrgUser())
				{
					$oContact =& CreateObject('dcl.dbContact');
					$aOrgs = $oContact->GetOrgArray($aAuthInfo['contact_id']);
					$g_oSession->Register('member_of_orgs', join(',', $aOrgs));
				}
			}

			$g_oSession->Edit();

			$menuAction = 'menuAction=htmlMyDCL.show';
			if ($g_oSec->IsPublicUser())
				$menuAction = 'menuAction=htmlPublicMyDCL.show';

			if (IsSet($_POST['refer_to']) && $_POST['refer_to'] != '')
				$menuAction = urldecode($_POST['refer_to']);

			$tpl = $oPreferences->Value('DCL_PREF_TEMPLATE_SET');
			if ($tpl == '')
				$tpl = $dcl_info['DCL_DEF_TEMPLATE_SET'];

			if (file_exists('templates/' . $tpl . '/frameset.php'))
				Refresh('templates/' . $tpl . '/frameset.php?' . $menuAction, $g_oSession->dcl_session_id, $_POST['DOMAIN']);
			else
				Refresh('main.php?' . $menuAction, $g_oSession->dcl_session_id, $_POST['DOMAIN']);
		}
		else
			Refresh('logout.php?cd=1');
	}
}
?>
