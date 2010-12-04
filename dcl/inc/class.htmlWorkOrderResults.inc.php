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
LoadStringResource('prj');
LoadStringResource('bm');

class htmlWorkOrderResults
{
	function htmlWorkOrderResults()
	{
	}

	function Render(&$oView)
	{
		global $dcl_info, $g_oSec, $g_oSession;

		if (!is_object($oView))
		{
			trigger_error('[htmlWorkOrderResults::Render] ' . STR_VW_VIEWOBJECTNOTPASSED);
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH))
			return PrintPermissionDenied();

		$oTable = new htmlTable();
		
		$bIsExplicitView = is_a($oView, 'boExplicitView');
		if (!$bIsExplicitView)
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
			STR_CMMN_SAVE => array('menuAction' => 'boViews.add', 'hasPermission' => !$bIsExplicitView && $g_oSec->HasPerm(DCL_ENTITY_SAVEDSEARCH, DCL_PERM_ADD)),
			'Refine' => array('menuAction' => 'htmlWOSearches.ShowRequest', 'hasPermission' => !$bIsExplicitView && $g_oSec->HasPerm(DCL_ENTITY_SAVEDSEARCH, DCL_PERM_VIEW)),
			'Export' => array('menuAction' => 'boViews.export', 'hasPermission' => true),
			'Detail' => array('menuAction' => 'boWorkorders.batchdetail', 'hasPermission' => $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ADD)),
			'Time Card' => array('menuAction' => 'boTimecards.batchadd', 'hasPermission' => $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION)),
			'Assign' => array('menuAction' => 'boWorkorders.batchassign', 'hasPermission' => $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ASSIGN)),
			'Project' => array('menuAction' => 'htmlProjectmap.batchmove', 'hasPermission' => $g_oSec->HasAllPerm(array(DCL_ENTITY_PROJECT => array($g_oSec->PermArray(DCL_PERM_ADDTASK), $g_oSec->PermArray(DCL_PERM_REMOVETASK)))))
			);

		$showBM = $g_oSession->Value('showBM');
		if (IsSet($showBM) && (int)$showBM == 1)
		{
			$aOptions_BM = array('Version' => array('menuAction' => 'boBuildManager.SubmitWO', 'hasPermission' => $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION)));
			$aOptions = array_merge($aOptions, $aOptions_BM);
			$g_oSession->Unregister('showBM');
			$g_oSession->Edit();
		}

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

		$iOffset = 0;
		for ($iColumn = count($oView->groups); $iColumn < $oDB->NumFields(); $iColumn++)
		{
			$sFieldName = $oDB->GetFieldName($iColumn);
			if ($sFieldName == 'jcn')
				$oTable->assign('wo_id_ordinal', $iColumn);
			else if ($sFieldName == 'seq')
				$oTable->assign('seq_ordinal', $iColumn);
			else if ($sFieldName == '_num_accounts_')
			{
				$iOffset--;
				$oTable->assign('num_accounts_ordinal', $iColumn);
			}
			else if ($sFieldName == '_num_tags_')
			{
				$iOffset--;
				$oTable->assign('num_tags_ordinal', $iColumn);
			}
			else if ($sFieldName == 'tag_desc')
			{
				$oTable->assign('tag_ordinal', $iColumn);
			}
			else if ($sFieldName == '_num_hotlist_')
			{
				$iOffset--;
				$oTable->assign('num_hotlist_ordinal', $iColumn);
			}
			else if ($sFieldName == 'hotlist_tag')
			{
				$oTable->assign('hotlist_ordinal', $iColumn);
			}
			else if ($oView->columns[$iColumn - count($oView->groups)] == 'dcl_org.name')
			{
				$oTable->assign('org_ordinal', $iColumn);
			}
		}
		
		$oTable->setData($oDB->FetchAllRows());

		$oTable->assign('VAL_ENDOFFSET', $iOffset);
		
		if (!$bIsExplicitView)
			$oTable->assign('VAL_VIEWSETTINGS', $oView->GetForm());

		$oTable->setCaption($oView->title);
		$oTable->setShowChecks(true);
		$oDB->FreeResult();

		$oTable->sTemplate = 'htmlTableWorkOrderResults.tpl';
		$oTable->render();
	}
}
