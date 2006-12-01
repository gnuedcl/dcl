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

LoadStringResource('vw');
class htmlTicketResults
{
	function htmlTicketResults()
	{
	}

	function Render(&$oView)
	{
		global $dcl_info, $g_oSec;

		if (!is_object($oView))
		{
			trigger_error('[htmlTicketResults::Render] ' . STR_VW_VIEWOBJECTNOTPASSED);
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_SEARCH))
			return PrintPermissionDenied();

		$oTable = CreateObject('dcl.htmlTable');
		
		$oTable->assign('VAL_VIEWSETTINGS', $oView->GetForm());
		$aProducts = IsSet($_REQUEST['product']) ? DCL_Sanitize::ToIntArray($_REQUEST['product']) : array();
		if ($aProducts !== null && count($aProducts) > 0)
			$oTable->assign('HID_PRODUCT', join(',', $aProducts));
		else
			$oTable->assign('HID_PRODUCT', '');

		for ($iColumn = 0; $iColumn < count($oView->groups); $iColumn++)
		{
			$oTable->addGroup($iColumn);
			$oTable->addColumn('', 'string');
		}

		$iColumn = 0;
		foreach ($oView->columnhdrs as $sColumn)
		{
			if ($iColumn++ < count($oView->groups))
				continue;
				
			$oTable->addColumn($sColumn, 'string');
		}
		
		$aOptions = array(
			STR_CMMN_SAVE => array('menuAction' => 'boViews.add', 'hasPermission' => $g_oSec->HasPerm(DCL_ENTITY_SAVEDSEARCH, DCL_PERM_ADD)),
			'Refine' => array('menuAction' => 'htmlTicketSearches.ShowRequest', 'hasPermission' => $g_oSec->HasPerm(DCL_ENTITY_SAVEDSEARCH, DCL_PERM_VIEW)),
			'Export' => array('menuAction' => 'boViews.export', 'hasPermission' => true)
			);

		foreach ($aOptions as $sDisplay => $aOption)
		{
			if ($aOption['hasPermission'])
			{
				$oTable->addToolbar($aOption['menuAction'], $sDisplay);
			}
		}

		$oDB = new dclDB;

		$sSQL = $oView->GetSQL();
		if ($oDB->Query($sSQL) == -1)
			return;

		for ($iColumn = count($oView->groups); $iColumn < count($oView->columns) + count($oView->groups); $iColumn++)
		{
			if ($oDB->GetFieldName($iColumn) == 'ticketid')
			{
				$oTable->assign('ticket_id_ordinal', $iColumn);
				break;
			}
		}
		
		$oTable->setData($oDB->FetchAllRows());
		$oDB->FreeResult();

		$oTable->assign('VAL_VIEWSETTINGS', $oView->GetForm());

		$oTable->setCaption($oView->title);
		$oTable->setShowChecks(false);
		$oTable->sTemplate = 'htmlTableTicketResults.tpl';
		$oTable->render();
	}
}
?>