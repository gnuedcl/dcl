<?php
/*
 * $Id: build.php,v 1.1.1.1 2006/11/27 05:30:54 mdean Exp $
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

if (php_sapi_name() != 'cli')
{
		echo 'This script can only be run via PHP CLI.';
		exit;
}

define('DCL_ROOT', '/var/www/dcl/');
define('SMARTY_DIR', DCL_ROOT . 'inc/');

require_once(DCL_ROOT . 'inc/config.php');
require_once(DCL_ROOT . 'inc/functions.inc.php');
require_once(SMARTY_DIR . 'Smarty.class.php');

$g_oSec = new SecurityHelper();
$g_oSession = new dbSession();
$oSmarty = new Smarty();

$aCompileDir = array('default');
foreach ($aCompileDir as $sDir)
{
		$oSmarty->template_dir = DCL_ROOT . "templates/$sDir/";
		$oSmarty->compile_dir = $oSmarty->template_dir . 'templates_c';

		echo 'Compiling templates in directory ' . $oSmarty->template_dir . "...\n";
		$hDir = opendir($oSmarty->template_dir);
		while (($sFile = readdir($hDir)) !== false)
		{
				$sFullPath = $oSmarty->template_dir . $sFile;
				if (is_file($sFullPath) && substr($sFullPath, -3, 3) == 'tpl')
				{
						echo "$sFile...";
						$oSmarty->fetch($sFile);
						echo "done\n";
				}
		}
}

echo "Compile finished.\n";
