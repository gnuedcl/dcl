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

function smarty_function_dcl_get_entity_tags($params, &$smarty)
{
	global $g_oMetaData, $g_oSec, $g_oSession;
	
	if (!isset($params['entity']))
	{
		$smarty->trigger_error('dcl_get_entity_tags: missing parameter entity');
		return;
	}

	if (!isset($params['key_id']))
	{
		$smarty->trigger_error('dcl_get_entity_tags: missing parameter key_id');
		return;
	}
	
	if ($params['entity'] == DCL_ENTITY_WORKORDER && !isset($params['key_id2']))
	{
		$smarty->trigger_error('dcl_get_entity_tags: missing parameter key_id2 is required for entity ' . $params['entity']);
		return;
	}
	
	$oEntityTag = CreateObject('dcl.dbEntityTag');
	$sValue = $oEntityTag->getTagsForEntity($params['entity'], $params['key_id'], $params['key_id2']);
	if ($sValue == '')
		return;

	$aTags = split(',', $sValue);
	$bFirst = true;
	foreach ($aTags as $sTag)
	{
		$sTag = trim($sTag);
		if (!$bFirst)
			echo ', ';
		else
			$bFirst = false;
			
		if (isset($params['link']) && $params['link'] == 'Y')
			echo '<a href="' . DCL_WWW_ROOT . 'main.php?menuAction=htmlTags.browse&tag=' . urlencode($sTag) . '">' . htmlspecialchars($sTag, ENT_QUOTES) . '</a>';
		else
			echo htmlspecialchars($sTag, ENT_QUOTES);
	}
}
?>