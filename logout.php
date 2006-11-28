<?php
    /*
     * $Id: logout.php,v 1.1.1.1 2006/11/27 05:30:35 mdean Exp $
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

if (!defined('__DCL_CONFIG_INCLUDED__'))
	include_once('inc/config.php');

include_once(DCL_ROOT . 'inc/functions.inc.php');

$g_oSession = CreateObject('dcl.dbSession');
list($dcl_session_id, $DOMAIN) = explode('/', $HTTP_COOKIE_VARS['DCLINFO']);
if (strlen($dcl_session_id) == 32)
{
	$g_oSession->Connect();
	if (!$g_oSession->conn)
		Refresh(DCL_WWW_ROOT . 'index.php?cd=3');

	if ($g_oSession->Load($dcl_session_id) == false)
		Refresh(DCL_WWW_ROOT . 'index.php?cd=2');

	if ($g_oSession->IsValidSession())
	{
		$g_oSession->Delete($g_oSession->dcl_session_id);
		$g_oSession->Clear();
	}
}

if (isset($_REQUEST['cd']) && ($_REQUEST['cd'] == '1' || $_REQUEST['cd'] == '2' || $_REQUEST['cd'] == '3' || $_REQUEST['cd'] == '4'))
	Refresh(DCL_WWW_ROOT . 'index.php?cd=' . $_REQUEST['cd']);
else
	Refresh(DCL_WWW_ROOT . 'index.php?cd=4');

function Refresh($toHere = 'index.php', $session_id = '', $domain = 'default')
{
	$oSmarty =& CreateSmarty();

	if (!(isset($_REQUEST['cd']) && ($_REQUEST['cd'] == '1' || $_REQUEST['cd'] == '2' || $_REQUEST['cd'] == '3' || $_REQUEST['cd'] == '4')))
	{
		if (IsSet($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != '')
			$toHere .= sprintf('%srefer_to=%s', strpos($toHere, '?') > 0 ? '&' : '?', urlencode($_SERVER['QUERY_STRING']));
	}
	
	$oSmarty->assign('URL', $toHere);
	SmartyDisplay($oSmarty, 'logout.tpl');
	
	exit;
}
?>
