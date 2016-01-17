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

function smarty_function_dcl_hotlist_link($params, &$smarty)
{
	global $g_oMetaData, $g_oSec, $g_oSession;
	
	if (!isset($params['value']))
	{
		trigger_error('dcl_hotlist_link: missing parameter value');
		return;
	}

	$aHotlists = array();
	if (is_array($params['value']))
	{
		if (count($params['value']) == 0)
			return;

		$aHotlists = $params['value'];
	}
	else
	{
		$sValue = trim($params['value']);
		if ($params['value'] == '')
			return;

		$aHotlists = explode(',', $sValue);
	}
		
	if (!isset($params['browse']))
		$params['browse'] = 'N';
		
	if (!isset($params['selected']))
		$params['selected'] = '';

	if (is_array($params['selected']))
	{
		$aSelected = array();
		foreach ($params['selected'] as $sSelectedHotlist)
			$aSelected[] = $sSelectedHotlist['hotlist'];

		$sSelected = join(',', $aSelected);
	}
	else
	{
		$sSelected = trim($params['selected']);
		$aSelected = explode(',', $sSelected);
		foreach ($aSelected as $iIndex => $sSelectedHotlist)
			$aSelected[$iIndex] = trim($sSelectedHotlist);
	}

	$bFirst = true;
	foreach ($aHotlists as $item)
	{
		$sHotlist = '';
		$priority = '';
		$hotlistId = -1;

		if (is_array($item))
		{
			$sHotlist = trim($item['hotlist']);
			$priority = $item['priority'];
			$hotlistId = $item['id'];
		}
		else
		{
			$sHotlist = trim($item);

		}
		if (!$bFirst)
			echo '';
		else
			$bFirst = false;

		if ($params['browse'] == 'Y')
		{
			echo '<span class="dcl-hotlist">';
			echo '<a href="' . DCL_WWW_ROOT . 'main.php?menuAction=Hotlist.Browse&tag=' . urlencode($sHotlist) . '">' . htmlspecialchars($sHotlist, ENT_QUOTES, 'UTF-8') . ($priority == '' ? '' : ' #' . $priority) . '</a>';
			echo '<span class="dcl-hotlist-actions">';
			if (in_array($sHotlist, $aSelected))
			{
				if (count($aSelected) == 1)
				{
					echo ' <a href="' . DCL_WWW_ROOT . 'main.php?menuAction=Hotlist.Browse" title="Remove Filter"> <span class="glyphicon glyphicon-trash"></span></a>';
				}
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
					
					echo ' <a href="' . DCL_WWW_ROOT . 'main.php?menuAction=Hotlist.Browse&tag=' . urlencode($sUpHotlist) . '" title="Remove Filter"> <span class="glyphicon glyphicon-trash"></span></a>';
				}
			}
			else
			{
				echo ' <a href="' . DCL_WWW_ROOT . 'main.php?menuAction=Hotlist.Browse&tag=' . urlencode($sSelected . ',' . $sHotlist) . '" title="Add to Filter"> <span class="glyphicon glyphicon-filter"></span></a>';
			}

			echo ' <a href="' . DCL_WWW_ROOT . 'main.php?menuAction=htmlHotlistProject.View&id=' . $hotlistId . '" title="View as Project"> <span class="glyphicon glyphicon-tasks"></span></a>';
			echo ' <a href="' . DCL_WWW_ROOT . 'main.php?menuAction=Hotlist.Prioritize&hotlist_id=' . $hotlistId . '" title="Prioritize"> <span class="glyphicon glyphicon-sort-by-attributes"></span></a>';

			echo '</span></span>';
		}
		else
		{
			echo '<a class="dcl-hotlist" href="' . DCL_WWW_ROOT . 'main.php?menuAction=Hotlist.Browse&tag=' . urlencode($sHotlist) . '">' . htmlspecialchars($sHotlist, ENT_QUOTES, 'UTF-8') . ($priority == '' ? '' : ' #' . $priority) . '</a>';
		}
	}
}
