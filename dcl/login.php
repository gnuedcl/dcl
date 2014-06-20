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

// This should only be called from main.php or such.  Template files need to include config.php to get info before login
include_once('inc/config.php');

$bNoHeader = (IsSet($_REQUEST['menuAction']) && ($_REQUEST['menuAction'] == 'htmlTicketDetail.Download' || $_REQUEST['menuAction'] == 'WorkOrder.DownloadAttachment' || $_REQUEST['menuAction'] == 'LineGraphImageHelper.Show'));

include_once(DCL_ROOT . 'inc/functions.inc.php');

$g_oSec = new SecurityHelper();

function Refresh($toHere = 'logout.php', $session_id = '', $domain = 'default')
{
    $bIsLogin = (substr($toHere, 0, 10) == 'logout.php');

    if ($bIsLogin)
    {
        $theCookie = '';
        if (IsSet($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != '')
            $toHere .= sprintf('%srefer_to=%s', strpos($toHere, '?') > 0 ? '&' : '?', urlencode($_SERVER['QUERY_STRING']));
    }
    else
        $theCookie = $session_id . '/' . $domain;

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

	if (($p = strpos($httpDomain, ':')) !== false)
		$httpDomain = substr($httpDomain, 0, $p);

	setcookie('DCLINFO', $theCookie, 0, '/', $httpDomain, UseHttps(), true);
	header("Location: $toHere\n\n");

	exit;
}

if (IsSet($_COOKIE['DCLINFO']) && !IsSet($_POST['UID']))
{
    $g_oSession = new SessionModel();
    list($dcl_session_id, $DOMAIN) = explode('/', $_COOKIE['DCLINFO']);
    if (strlen($dcl_session_id) != 32)
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
    $authenticateModel = new AuthenticateSqlModel();
    $authInfo = array();
    if ($authenticateModel->IsValidLogin($authInfo))
    {
        $oConfig = new ConfigurationModel();
        $dcl_info = array();
        $oConfig->Load();

        $g_oSession = new SessionModel();
        if (!$g_oSession->conn)
            Refresh('logout.php?cd=3');

        $g_oSession->personnel_id = $authInfo['id'];
        $g_oSession->Add();

        $oPreferences = new PreferencesModel();
        $oPreferences->Load($authInfo['id']);

        $g_oSession->Register('DCLID', $authInfo['id']);
        define('DCLID', $authInfo['id']);

        $g_oSession->Register('DCLNAME', trim($authInfo['short']));
        $g_oSession->Register('USEREMAIL', $authInfo['email']);
        $g_oSession->Register('contact_id', $authInfo['contact_id']);
        $g_oSession->Register('dcl_info', $dcl_info);
        $g_oSession->Register('dcl_preferences', $oPreferences->preferences_data);

        // If we have org restrictions, cache the affiliated orgs for this contact record
        if ($authInfo['contact_id'] != null && $authInfo['contact_id'] > 0)
        {
            if ($g_oSec->IsOrgUser())
            {
                $oContact = new ContactModel();
                $aOrgs = $oContact->GetOrgArray($authInfo['contact_id']);
                $g_oSession->Register('member_of_orgs', join(',', $aOrgs));

                // Also grab the filtered product list for the orgs
                $oOrg = new OrganizationModel();
                $aProducts = $oOrg->GetProductArray($aOrgs);
                if (count($aProducts) == 0)
                    $aProducts = array('-1');

                $g_oSession->Register('org_products', join(',', $aProducts));
            }
        }

        $g_oSession->Edit();

        if ($GLOBALS['dcl_info']['DCL_SEC_AUDIT_ENABLED']=='Y')
        {
            $oSecAuditDB = new SecurityAuditModel();
            $oSecAuditDB->id = DCLID;
            $oSecAuditDB->actionon = DCL_NOW;
            $oSecAuditDB->actiontxt = 'login';
            $oSecAuditDB->actionparam = '';
            $oSecAuditDB->Add();
        }

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
