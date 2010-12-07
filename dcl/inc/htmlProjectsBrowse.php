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
class htmlProjectsBrowse
{
	var $sPagingMenuAction;
	var $oView;
	var $oDB;
	
	function htmlProjectsBrowse()
	{
		$this->sPagingMenuAction = 'htmlProjectsBrowse.Page';
		
		$this->oView = null;
		$this->oDB = new dbProjects();
	}

	function Render(&$oView)
	{
		global $dcl_info, $g_oSec;

		if (!is_object($oView))
		{
			trigger_error('[htmlProjectsBrowse::Render] ' . STR_VW_VIEWOBJECTNOTPASSED);
			return;
		}

		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$this->oView = &$oView;
		
		// Reset start row if filter changes
		if (isset($_REQUEST['filter']) && $_REQUEST['filter'] == 'Filter')
			$oView->startrow = 0;

		if (!$this->_Execute())
			return;

		$oTable = new htmlTable();
		$oTable->setData($this->oDB->FetchAllRows());
		
		for ($iColumn = 0; $iColumn < count($this->oView->groups); $iColumn++)
		{
			$oTable->addGroup($iColumn);
			$oTable->addColumn('');
		}
		
		foreach ($this->oView->columnhdrs as $sColumn)
		{
			$oTable->addColumn($sColumn, 'string');
		}
		
		$oTable->addColumn(STR_CMMN_OPTIONS, 'string');
		
		//$aOptions = array('Export' => array('menuAction' => 'boViews.export', 'hasPermission' => true));
		$aOptions = array();

		foreach ($aOptions as $sDisplay => $aOption)
		{
			if ($aOption['hasPermission'])
			{
				$oTable->addToolbar($aOption['menuAction'], $sDisplay);
			}
		}

		$oDB = new dclDB;

		$sSQL = $this->oView->GetSQL(true);
		if ($oDB->Query($sSQL) == -1)
			return;

		$oDB->next_record();
		$iRecords = $oDB->f(0);
		$oDB->FreeResult();

		if ($this->oView->numrows > 0)
		{
			if ($iRecords % $this->oView->numrows == 0)
				$oTable->assign('VAL_PAGES', strval($iRecords / $this->oView->numrows));
			else
				$oTable->assign('VAL_PAGES', strval(ceil($iRecords / $this->oView->numrows)));

			$oTable->assign('VAL_PAGE', strval(($this->oView->startrow / $this->oView->numrows) + 1));
		}
		else
		{
			$oTable->assign('VAL_PAGES', '0');
			$oTable->assign('VAL_PAGE', '0');
		}

		$oTable->assign('VAL_FILTERMENUACTION', $this->sPagingMenuAction);
		$oTable->assign('VAL_FILTERSTARTROW', $this->oView->startrow);
		$oTable->assign('VAL_FILTERNUMROWS', $this->oView->numrows);
		$oTable->assign('VAL_FILTERSTATUS', isset($_REQUEST['filterStatus']) ? $_REQUEST['filterStatus'] : -1);
		$oTable->assign('VAL_FILTERREPORTTO', isset($_REQUEST['filterReportto']) ? $_REQUEST['filterReportto'] : -1);
		$oTable->assign('VAL_FILTERNAME', isset($_REQUEST['filterName']) ? $_REQUEST['filterName'] : '');
		$oTable->assign('VAL_VIEWSETTINGS', $this->oView->GetForm());
		$oTable->assign('VAL_WIKIUSED', $dcl_info['DCL_WIKI_ENABLED'] == 'Y' && $g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEWWIKI));

		$oTable->setCaption($this->oView->title);
		$oTable->setShowChecks(false);
		$oTable->sTemplate = 'htmlTableProject.tpl';
		$oTable->render();
	}

	function Page()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$oView = new boView();
		$oView->SetFromURL();

		if (IsSet($_REQUEST['jumptopage']) && IsSet($_REQUEST['startrow']) && IsSet($_REQUEST['numrows']))
		{
			$iPage = (int)$_REQUEST['jumptopage'];
			if ($iPage < 1)
				$iPage = 1;

			$oView->startrow = ($iPage - 1) * (int)$_REQUEST['numrows'];

			if ($oView->startrow < 0)
				$oView->startrow = 0;

			$oView->numrows = (int)$_REQUEST['numrows'];
		}
		else
		{
			$oView->numrows = 25;
			$oView->startrow = 0;
		}

		$filterStatus = -1;
		if (IsSet($_REQUEST['filterStatus']))
			$filterStatus =@ DCL_Sanitize::ToSignedInt($_REQUEST['filterStatus']);
			
		$filterReportto = @DCL_Sanitize::ToInt($_REQUEST['filterReportto']);

		$oView->RemoveDef('filternot', 'statuses.dcl_status_type');
		$oView->RemoveDef('filter', 'statuses.dcl_status_type');
		$oView->RemoveDef('filter', 'status');
		$oView->RemoveDef('filterlike', 'name');
		if ($filterStatus !== null && $filterStatus != 0)
		{
			if ($filterStatus == -1)
				$oView->AddDef('filternot', 'statuses.dcl_status_type', '2');
			else if ($filterStatus == -2)
				$oView->AddDef('filter', 'statuses.dcl_status_type', '2');
			else if ($filterStatus !== null)
				$oView->AddDef('filter', 'status', $filterStatus);
		}
		else
			$oView->RemoveDef('filter', 'status');

		if ($filterReportto !== null && $filterReportto > 0)
			$oView->ReplaceDef('filter', 'reportto', $filterReportto);
		else
			$oView->RemoveDef('filter', 'reportto');
			
		if (isset($_REQUEST['filterName']) && trim($_REQUEST['filterName']) != '')
			$oView->AddDef('filterlike', 'name', GPCStripSlashes($_REQUEST['filterName']));

		$this->sColumnTitle = STR_CMMN_OPTIONS;
		$this->bShowPager = true;
		$this->Render($oView);
	}
	
	function _Execute()
	{
		if ($this->oView->numrows > 0 || $this->oView->startrow > 0)
			$result = $this->oDB->LimitQuery($this->oView->GetSQL(), $this->oView->startrow, $this->oView->numrows);
		else
			$result = $this->oDB->Query($this->oView->GetSQL());

		if ($result == -1)
		{
			return false;
		}

		return true;
	}
}
