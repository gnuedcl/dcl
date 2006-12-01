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

LoadStringResource('ver');

class htmlVersion
{
	function displayversioninfo()
	{
		global $dcl_info;

		if (!empty($_SERVER))
			extract($_SERVER);
		else if (!empty($HTTP_SERVER_VARS))
			extract($HTTP_SERVER_VARS);

		commonHeader();

		$Template = CreateTemplate(array('hForm' => 'htmlVersion.tpl'));

		$Template->set_var('TXT_TITLE', STR_VER_TITLE);
		$Template->set_var('TXT_YOURVER', STR_VER_YOURVER);

		$Template->set_var('TXT_DCL', STR_VER_DCL);
		$Template->set_var('TXT_SERVEROS', STR_VER_SERVEROS);
		$Template->set_var('TXT_SERVERNAME', STR_VER_SERVERNAME);
		$Template->set_var('TXT_WEBSERVER', STR_VER_WEBSERVER);
		$Template->set_var('TXT_PHPVER', STR_VER_PHPVER);
		$Template->set_var('TXT_YOURIP', STR_VER_YOURIP);
		$Template->set_var('TXT_YOURBROWSER', STR_VER_YOURBROWSER);

		$Template->set_var('VAL_DCLVERSION', $dcl_info['DCL_VERSION']);
		$Template->set_var('VAL_SERVERNAME', $SERVER_NAME . '(' . $HTTP_HOST . ')');
		$Template->set_var('VAL_SERVERSOFTWARE', $SERVER_SOFTWARE);
		$Template->set_var('VAL_PHPVERSION', phpversion());
		$Template->set_var('VAL_REMOTEADDR', $REMOTE_ADDR);
		$Template->set_var('VAL_HTTPUSERAGENT', $HTTP_USER_AGENT);

		if (IsSet($OSTYPE) && IsSet($HOSTTYPE))
			$Template->set_var('VAL_SERVEROS', $OSTYPE . '-' . $HOSTTYPE);
		elseif (IsSet($OSTYPE))
			$Template->set_var('VAL_SERVEROS', $OSTYPE);
		elseif (IsSet($HOSTTYPE))
			$Template->set_var('VAL_SERVEROS', $HOSTTYPE);
		else
			$Template->set_var('VAL_SERVEROS', '');

		$Template->pparse('out', 'hForm');
	}
}
?>
