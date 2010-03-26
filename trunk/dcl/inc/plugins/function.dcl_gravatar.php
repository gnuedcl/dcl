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

function smarty_function_dcl_gravatar($params, &$smarty)
{
	global $g_oSec, $g_oSession, $g_GravitarsByUserId;
	
	$userId = -1;
	if (!isset($params['userId']) || ($userId = DCL_Sanitize::ToInt($params['userId'])) === null)
	{
		$smarty->trigger_error('dcl_gravatar: missing or incorrect parameter userId');
		return;
	}

	$gravitarHash = '';
	if (!isset($g_GravitarsByUserId) || !isset($g_GravitarsByUserId[$userId]))
	{
		$dbContactEmail = CreateObject('dcl.dbContactEmail');
		$emailResult = $dbContactEmail->GetPrimaryEmailByUserID($params['userId']);
		if ($emailResult === -1 || $emailResult === false)
		{
			$g_GravitarsByUserId[$userId] = null;
			return;
		}
		
		$gravitarHash = md5(strtolower($dbContactEmail->f('email_addr')));
		$g_GravitarsByUserId[$userId] = $gravitarHash;
	}
	else 
	{
		$gravitarHash = $g_GravitarsByUserId[$userId];
		if ($gravitarHash == null)
			return;
	}

	$size = 32;
	if (isset($params['size']) && DCL_Sanitize::ToInt($params['size']) !== null)
		$size = $params['size'];
		
	$gravitarUrl = 'http://www.gravatar.com/avatar/';
	$gravitarUrl .= $gravitarHash;
	$gravitarUrl .= '?s=' . $size;
	$gravitarUrl .= '&d=identicon';
	
	echo '<img src="', $gravitarUrl, '"';
	if (isset($params['style']) && $params['style'] != '')
		echo ' style="', $params['style'], '"';
		
	echo ' />';
}
?>