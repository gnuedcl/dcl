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

function smarty_function_dcl_hotlist_link($params, &$smarty)
{
	global $g_oMetaData, $g_oSec, $g_oSession;
	
	if (!isset($params['value']))
	{
		$smarty->trigger_error('dcl_hotlist_link: missing parameter value');
		return;
	}
	
	$sValue = trim($params['value']);
	if ($params['value'] == '')
		return;
		
	if (!isset($params['browse']))
		$params['browse'] = 'N';
		
	if (!isset($params['selected']))
		$params['selected'] = '';
	
	$sSelected = trim($params['selected']);
	$aSelected = split(',', $sSelected);
	foreach ($aSelected as $iIndex => $sSelectedHotlist)
		$aSelected[$iIndex] = trim($sSelectedHotlist);

	$aHotlists = split(',', $sValue);
	$bFirst = true;
	foreach ($aHotlists as $sHotlist)
	{
		$sHotlist = trim($sHotlist);
		if (!$bFirst)
			echo ', ';
		else
			$bFirst = false;
			
		echo '<a href="' . DCL_WWW_ROOT . 'main.php?menuAction=htmlHotlists.browse&tag=' . urlencode($sHotlist) . '">' . htmlspecialchars($sHotlist, ENT_QUOTES) . '</a>';
		if ($params['browse'] == 'Y')
		{
			if (in_array($sHotlist, $aSelected))
			{
				if (count($aSelected) == 1)
					echo ' <a href="' . DCL_WWW_ROOT . 'main.php?menuAction=htmlHotlists.browse"> [-]</a>';
				else
				{
					$sUpHotlist = '';
					foreach ($aSelected as $sSelectedHotlist)
					{
						if ($sSelectedHotlist != $sHotlist)
						{
							if ($sUpHotlist != '')
								$sUpHotlist .= ',';
								
							$sUpHotlist .= $sSelectedHotlist;
						}
					}
					
					echo ' <a href="' . DCL_WWW_ROOT . 'main.php?menuAction=htmlHotlists.browse&tag=' . urlencode($sUpHotlist) . '"> [-]</a>';
				}
			}
			else
				echo ' <a href="' . DCL_WWW_ROOT . 'main.php?menuAction=htmlHotlists.browse&tag=' . urlencode($sSelected . ',' . $sHotlist) . '"> [+]</a>';
		}
	}
}
?>