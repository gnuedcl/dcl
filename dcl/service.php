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

include_once('inc/config.php');
include_once(DCL_ROOT . 'inc/functions.inc.php');

$realm = 'DCL';

if (isset($_SERVER['PHP_AUTH_USER']))
{
	$_REQUEST['UID'] = $_SERVER['PHP_AUTH_USER'];
	$_REQUEST['PWD'] = $_SERVER['PHP_AUTH_PW'];
	
	$model = new AuthenticateSqlModel();
	$authInfo = array();
	if ($model->IsValidLogin($authInfo))
	{
		if (isset($_POST['menuAction']))
		{
			$menuAction = $_POST['menuAction'];
			list($class, $method) = explode(".", $menuAction);
			if ($class == 'wsSccsXref')
			{
				define('SERVICE_AUTH', 1);
				Invoke($menuAction);
				    	
				exit;
			}
		}
		
		header('HTTP/1.1 403 Forbidden');
		exit;
	}
}

header("WWW-Authenticate: Basic realm=\"$realm\"");
header('HTTP/1.1 401 Unauthorized');
echo 'Unauthorized.';

exit;
