<?php
/*
 * $Id: class.htmlContactBrowse.inc.php,v 1.1.1.1 2006/11/27 05:30:50 mdean Exp $
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

class htmlContactBrowse
{
	var $sPagingMenuAction;
	var $oView;
	var $oDB;

	function htmlContactBrowse()
	{
		$this->sPagingMenuAction = 'htmlContactBrowse.Page';
		
		$this->oView = null;
		$this->oDB = CreateObject('dcl.dbContact');
	}

	function Render(&$oView)
	{
		global $dcl_info, $g_oSec;

		if (!is_object($oView))
		{
			trigger_error('[htmlContactBrowse::Render] ' . STR_VW_VIEWOBJECTNOTPASSED);
			return;
		}

		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_VIEW))
			return PrintPermissionDenied();

		$this->oView = &$oView;
		
		// Reset start row if filter changes
		if (isset($_REQUEST['filter']) && $_REQUEST['filter'] == 'Filter')
			$oView->startrow = 0;

		if (!$this->_Execute())
			return;

		$oTable = CreateObject('dcl.htmlTable');
		
		$oMetadata = CreateObject('dcl.DCL_MetadataDisplay');
		$aContacts = array();
		while ($this->oDB->next_record())
		{
			$aContact = $oMetadata->GetContact($this->oDB->f('contact_id'));
			$aRow = array($this->oDB->f('contact_id'), $this->oDB->f('active'), $this->oDB->f('last_name'), $this->oDB->f('first_name'), $aContact['org_name'],
							$aContact['phone'], $aContact['email'], 'url_addr' => $aContact['url']);
						
			$aContacts[] = $aRow;
		}

		$oTable->setData($aContacts);
		
		for ($iColumn = 0; $iColumn < count($this->oView->groups); $iColumn++)
		{
			$oTable->addGroup($iColumn);
			$oTable->addColumn('');
		}
		
		foreach ($this->oView->columnhdrs as $sColumn)
		{
			$oTable->addColumn($sColumn, 'string');
		}

		$aOptions = array(STR_CMMN_NEW => array('menuAction' => 'htmlContactForm.add', 'hasPermission' => $g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_ADD)),
							'Merge' => array('menuAction' => 'javascript:merge();', 'hasPermission' => $g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY)));

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

			$iPage = ($this->oView->startrow / $this->oView->numrows) + 1;
		}
		else
		{
			$oTable->assign('VAL_PAGES', '0');
			$iPage = 0;
		}

		$oTable->setCaption('Browse Contacts');
		$oTable->assign('VAL_PAGE', strval($iPage));
		$oTable->assign('VAL_LETTERS', array_merge(array('All'), range('A', 'Z')));
		$oTable->assign('VAL_FILTERMENUACTION', $this->sPagingMenuAction);
		$oTable->assign('VAL_FILTERSTARTROW', $this->oView->startrow);
		$oTable->assign('VAL_FILTERNUMROWS', $this->oView->numrows);
		$oTable->assign('VAL_VIEWSETTINGS', $this->oView->GetForm());

		$filterActive = DCL_Sanitize::ToYN($_REQUEST['filterActive']);
		if ($filterActive == 'Y' || $filterActive == 'N')
			$filterActive = $_REQUEST['filterActive'];

		$filterStartsWith = '';
		if (IsSet($_REQUEST['filterStartsWith']))
			$filterStartsWith = $_REQUEST['filterStartsWith'];

		$filterSearch = '';
		if (IsSet($_REQUEST['filterSearch']))
			$filterSearch = $_REQUEST['filterSearch'];

		$filterOrgID = isset($_REQUEST['org_id']) ? DCL_Sanitize::ToInt($_REQUEST['org_id']) : null;
		if ($filterOrgID !== null)
		{
			$oTable->assign('VAL_FILTERORGID', $filterOrgID);
			$aOrg = $oMetadata->GetOrganization($filterOrgID);
			$oTable->setCaption('Browse Contacts - ' . $aOrg['name']);
			if ($g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_VIEW))
				$oTable->addToolbar('htmlOrgDetail.show&org_id=' . $filterOrgID, 'Organization');
		}

		$oTable->assign('VAL_FILTERACTIVE', $filterActive);
		$oTable->assign('VAL_FILTERSTART', $filterStartsWith);
		$oTable->assign('VAL_FILTERSEARCH', $filterSearch);

		$aLastView = array(
			'VAL_FILTERACTIVE' => $filterActive,
			'VAL_FILTERSTART' => $filterStartsWith,
			'VAL_FILTERSEARCH' => $filterSearch,
			'VAL_JUMPTOPAGE' => $iPage,
			'VAL_STARTROW' => $this->oView->startrow,
			'VAL_NUMROWS' => $this->oView->numrows,
			'VAL_VIEWSETTINGS' => $this->oView->GetForm()
		);

		global $g_oSession;
		$g_oSession->Register('LAST_CONTACT_BROWSE_PAGE', $aLastView);
		$g_oSession->Edit();

		$oTable->setShowChecks($g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY));
		$oTable->sTemplate = 'htmlTableContact.tpl';
		$oTable->render();
	}

	function Page()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_VIEW))
			return PrintPermissionDenied();

		CleanArray($_REQUEST);
		$oView = CreateObject('dcl.boView');
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
		$oView->RemoveDef('filterlike', 'last_name');
		$oView->RemoveDef('filterlike', 'first_name');
		$oView->RemoveDef('filterstart', 'last_name');
		
		$filterActive = DCL_Sanitize::ToYN($_REQUEST['filterActive']);
		if ($filterActive == 'Y' || $filterActive == 'N')
			$oView->AddDef('filter', 'active', "'$filterActive'");

		$filterSearch = isset($_REQUEST['filterSearch']) ? $_REQUEST['filterSearch'] : '';
		if ($filterSearch != '')
		{
			if (strpos($filterSearch, ',') === false)
			{
				$oView->AddDef('filterlike', 'last_name', $filterSearch);
			}
			else
			{
				$oView->logiclike = 'AND';
				$aCriteria = split(',', $filterSearch);
				if (count($aCriteria) > 2)
				{
					$oView->AddDef('filterlike', 'last_name', trim($aCriteria[0]));
					array_shift($aCriteria);
					$oView->AddDef('filterlike', 'first_name', trim(join(',', $aCriteria)));
				}
				else
				{
					$oView->AddDef('filterlike', 'last_name', trim($aCriteria[0]));
					$oView->AddDef('filterlike', 'first_name', trim($aCriteria[1]));
				}
			}
		}

		$filterStartsWith = isset($_REQUEST['filterStartsWith']) ? $_REQUEST['filterStartsWith'] : '';
		if ($filterStartsWith != '')
			$oView->AddDef('filterstart', 'last_name', $filterStartsWith);

		$filterOrgID = isset($_REQUEST['org_id']) ? DCL_Sanitize::ToInt($_REQUEST['org_id']) : null;
		if ($filterOrgID !== null)
			$oView->AddDef('filter', 'dcl_org_contact.org_id', $filterOrgID);

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

		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_VIEW))
			return PrintPermissionDenied();

		CleanArray($_REQUEST);
		$oDB = new dclDB;
		$oView = CreateObject('dcl.boView');
		$oView->table = 'dcl_contact';
		$oView->AddDef('columnhdrs', '', array(STR_CMMN_ID, STR_CMMN_ACTIVE, STR_CMMN_LASTNAME, STR_CMMN_FIRSTNAME, 'Organization', 'Phone', 'Email', 'Internet'));
		$oView->AddDef('columns', '', array('contact_id', 'active', 'last_name', 'first_name'));
		$oView->AddDef('order', '', array('last_name', 'first_name', 'contact_id'));
		
		$oView->numrows = 25;

		$filterActive = DCL_Sanitize::ToYN($_REQUEST['filterActive']);
		if ($filterActive == 'Y' || $filterActive == 'N')
			$oView->AddDef('filter', 'active', "'$filterActive'");

		$filterSearch = isset($_REQUEST['filterSearch']) ? $_REQUEST['filterSearch'] : '';
		if ($filterSearch != '')
			$oView->AddDef('filterlike', 'last_name', $filterSearch);

		$filterStartsWith = isset($_REQUEST['filterStartsWith']) ? $_REQUEST['filterStartsWith'] : '';
		if ($filterStartsWith != '')
			$oView->AddDef('filterstart', 'last_name', $filterStartsWith);

		$filterOrgID = isset($_REQUEST['org_id']) ? DCL_Sanitize::ToInt($_REQUEST['org_id']) : null;
		if ($filterOrgID !== null)
			$oView->AddDef('filter', 'dcl_org_contact.org_id', $filterOrgID);

		$this->sColumnTitle = STR_CMMN_OPTIONS;
		$this->bShowPager = true;
		$this->Render($oView);
	}
}
?>
