<?php
/*
 * $Id$
 *
 * Double Choco Latte - Source Configuration Management System
 * Copyright (C) 1999  Michael L. Dean & Tim R. Norman
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
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

class htmlOrgBrowse
{
	var $sPagingMenuAction;
	var $oView;
	var $oDB;

	function htmlOrgBrowse()
	{
		$this->sPagingMenuAction = 'htmlOrgBrowse.Page';
		
		$this->oView = null;
		$this->oDB = new OrganizationModel();
	}

	function Render(&$oView)
	{
		global $dcl_info, $g_oSec;

		if (!is_object($oView))
		{
			trigger_error('[htmlOrgBrowse::Render] ' . STR_VW_VIEWOBJECTNOTPASSED);
			return;
		}

		if (!$g_oSec->HasAnyPerm(array(DCL_ENTITY_ORG => array($g_oSec->PermArray(DCL_PERM_VIEW), $g_oSec->PermArray(DCL_PERM_VIEWACCOUNT)))))
			throw new PermissionDeniedException();

		$this->oView = &$oView;
		
		// Reset start row if filter changes
		if (isset($_REQUEST['filter']) && $_REQUEST['filter'] == 'Filter')
			$oView->startrow = 0;

		if (!$this->_Execute())
			return;

		$oTable = new TableHtmlHelper();
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
		
		$aOptions = array(STR_CMMN_NEW => array('menuAction' => 'htmlOrgForm.add', 'hasPermission' => $g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_ADD)));

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

		$oTable->assign('VAL_LETTERS', array_merge(array('All'), range('A', 'Z')));
		$oTable->assign('VAL_FILTERMENUACTION', $this->sPagingMenuAction);
		$oTable->assign('VAL_FILTERSTARTROW', $this->oView->startrow);
		$oTable->assign('VAL_FILTERNUMROWS', $this->oView->numrows);
		$oTable->assign('VAL_VIEWSETTINGS', $this->oView->GetForm());

		$filterActive = @DCL_Sanitize::ToYN($_REQUEST['filterActive']);
		if ($filterActive != 'Y' && $filterActive != 'N')
			$filterActive = '';

		$filterStartsWith = '';
		if (IsSet($_REQUEST['filterStartsWith']))
			$filterStartsWith = $_REQUEST['filterStartsWith'];

		$filterSearch = '';
		if (IsSet($_REQUEST['filterSearch']))
			$filterSearch = $_REQUEST['filterSearch'];

		$oTable->assign('VAL_FILTERACTIVE', $filterActive);
		$oTable->assign('VAL_FILTERSTART', $filterStartsWith);
		$oTable->assign('VAL_FILTERSEARCH', $filterSearch);

		$oTable->setCaption('Browse Organizations');
		$oTable->setShowChecks(false);
		$oTable->sTemplate = 'htmlTableOrganization.tpl';
		$oTable->render();
	}

	function Page()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasAnyPerm(array(DCL_ENTITY_ORG => array($g_oSec->PermArray(DCL_PERM_VIEW), $g_oSec->PermArray(DCL_PERM_VIEWACCOUNT)))))
			throw new PermissionDeniedException();

		$oView = new OrganizationSqlQueryHelper();
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

		// Reset search params based on request
		$oView->RemoveDef('filter', 'active');
		$oView->RemoveDef('filterlike', 'name');
		$oView->RemoveDef('filterstart', 'name');
		
		$filterActive = @DCL_Sanitize::ToYN($_REQUEST['filterActive']);
		if ($filterActive == 'Y' || $filterActive == 'N')
			$oView->AddDef('filter', 'active', "'$filterActive'");

		$filterSearch = isset($_REQUEST['filterSearch']) ? $_REQUEST['filterSearch'] : '';
		if ($filterSearch != '')
			$oView->AddDef('filterlike', 'name', $filterSearch);

		$filterStartsWith = isset($_REQUEST['filterStartsWith']) ? $_REQUEST['filterStartsWith'] : '';
		if ($filterStartsWith != '')
			$oView->AddDef('filterstart', 'name', $filterStartsWith);

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
	
	function show()
	{
		global $dcl_info, $g_oSec;

		commonHeader();

		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$oView = new OrganizationSqlQueryHelper();
		$oView->AddDef('columnhdrs', '', array(STR_CMMN_ID, STR_CMMN_ACTIVE, STR_CMMN_NAME, 'Phone', 'Email', 'Internet'));
		$oView->AddDef('columns', '', array('org_id', 'active', 'name', 'dcl_org_phone.phone_number', 'dcl_org_email.email_addr', 'dcl_org_url.url_addr'));
		$oView->AddDef('order', '', array('name'));
		
		$oView->numrows = 25;

		$filterActive = @DCL_Sanitize::ToYN($_REQUEST['filterActive']);
		if ($filterActive == 'Y' || $filterActive == 'N')
			$oView->AddDef('filter', 'active', "'$filterActive'");

		$filterSearch = isset($_REQUEST['filterSearch']) ? $_REQUEST['filterSearch'] : '';
		if ($filterSearch != '')
			$oView->AddDef('filterlike', 'name', $filterSearch);

		$filterStartsWith = isset($_REQUEST['filterStartsWith']) ? $_REQUEST['filterStartsWith'] : '';
		if ($filterStartsWith != '')
			$oView->AddDef('filterstart', 'name', $filterStartsWith);

		$this->sColumnTitle = STR_CMMN_OPTIONS;
		$this->bShowPager = true;
		$this->Render($oView);
	}
}
