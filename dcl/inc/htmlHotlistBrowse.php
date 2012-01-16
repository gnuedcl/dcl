<?php
/*
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

class htmlHotlistBrowse
{
	var $sPagingMenuAction;
	var $oView;
	var $oDB;

	function htmlHotlistBrowse()
	{
		$this->sPagingMenuAction = 'htmlHotlistBrowse.Page';
		
		$this->oView = null;
		$this->oDB = new HotlistModel();
	}

	function Render(&$oView)
	{
		global $g_oSec;

		if (!is_object($oView))
		{
			trigger_error('[htmlHotlistBrowse::Render] ' . STR_VW_VIEWOBJECTNOTPASSED);
			return;
		}

		if (!$g_oSec->HasPerm(DCL_ENTITY_HOTLIST, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$this->oView = &$oView;
		
		// Reset start row if filter changes
		if (isset($_REQUEST['filter']) && $_REQUEST['filter'] == 'Filter')
			$oView->startrow = 0;

		if (!$this->_Execute())
			return;

		$oTable = new TableHtmlHelper();
		$allRecs = $this->oDB->FetchAllRows();
		
		for ($iColumn = 0; $iColumn < count($this->oView->groups); $iColumn++)
		{
			$oTable->addGroup($iColumn);
			$oTable->addColumn('');
		}
		
		foreach ($this->oView->columnhdrs as $sColumn)
		{
			if ($sColumn == STR_CMMN_NAME)
				$oTable->addColumn($sColumn, 'html');
			else
				$oTable->addColumn($sColumn, 'string');
		}
		
		for ($i = 0; $i < count($allRecs); $i++)
		{
			$allRecs[$i][2] = '<a class="dcl-hotlist" href="' . menuLink('', 'menuAction=Hotlist.Browse&tag=' . $allRecs[$i][2]) . '">' . $allRecs[$i][2] . '</a>';
		}
		
		$aOptions = array(STR_CMMN_NEW => array('menuAction' => 'htmlHotlistForm.add', 'hasPermission' => $g_oSec->HasPerm(DCL_ENTITY_HOTLIST, DCL_PERM_ADD)));

		foreach ($aOptions as $sDisplay => $aOption)
		{
			if ($aOption['hasPermission'])
			{
				$oTable->addToolbar($aOption['menuAction'], $sDisplay);
			}
		}

		if (count($allRecs) > 0 && $g_oSec->HasAnyPerm(array(DCL_ENTITY_HOTLIST => array($g_oSec->PermArray(DCL_PERM_VIEW), $g_oSec->PermArray(DCL_PERM_MODIFY), $g_oSec->PermArray(DCL_PERM_DELETE)))))
		{
			$oTable->addColumn(STR_CMMN_OPTIONS, 'html');
			for ($i = 0; $i < count($allRecs); $i++)
			{
				$options = '';
				if ($g_oSec->HasPerm(DCL_ENTITY_HOTLIST, DCL_PERM_VIEW))
				{
					$options = '<a href="' . menuLink('', 'menuAction=htmlHotlistProject.View&id=' . $allRecs[$i][0]) . '">' . 'View as Project' . '</a>';
				}

				if ($g_oSec->HasPerm(DCL_ENTITY_HOTLIST, DCL_PERM_MODIFY))
				{
					if ($options != '')
						$options .= '&nbsp;|&nbsp;';

					$options .= '<a href="' . menuLink('', 'menuAction=Hotlist.Prioritize&hotlist_id=' . $allRecs[$i][0]) . '">' . 'Prioritize' . '</a>';
					$options .= '&nbsp;|&nbsp;';
					$options .= '<a href="' . menuLink('', 'menuAction=htmlHotlistForm.modify&hotlist_id=' . $allRecs[$i][0]) . '">' . STR_CMMN_EDIT . '</a>';
				}

				if ($g_oSec->HasPerm(DCL_ENTITY_HOTLIST, DCL_PERM_DELETE))
				{
					if ($options != '')
						$options .= '&nbsp;|&nbsp;';

					$options .= '<a href="' . menuLink('', 'menuAction=htmlHotlistForm.delete&hotlist_id=' . $allRecs[$i][0]) . '">' . STR_CMMN_DELETE . '</a>';
				}

				$allRecs[$i][] = $options;
			}
		}
		
		$oTable->setData($allRecs);
		
		$oDB = new DbProvider;

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

		$filterActive = @$_REQUEST['filterActive'];
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

		$oTable->setCaption('Browse Hotlists');
		$oTable->setShowChecks(false);
		$oTable->sTemplate = 'TableHotlist.tpl';
		$oTable->render();
	}

	function Page()
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_HOTLIST, DCL_PERM_VIEW))
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

		// Reset search params based on request
		$oView->RemoveDef('filter', 'active');
		$oView->RemoveDef('filterlike', 'hotlist_tag');
		$oView->RemoveDef('filterstart', 'hotlist_tag');
		
		$filterActive = @$_REQUEST['filterActive'];
		if ($filterActive == 'Y' || $filterActive == 'N')
			$oView->AddDef('filter', 'active', "'$filterActive'");

		$filterSearch = isset($_REQUEST['filterSearch']) ? $_REQUEST['filterSearch'] : '';
		if ($filterSearch != '')
			$oView->AddDef('filterlike', 'hotlist_tag', $filterSearch);

		$filterStartsWith = isset($_REQUEST['filterStartsWith']) ? $_REQUEST['filterStartsWith'] : '';
		if ($filterStartsWith != '')
			$oView->AddDef('filterstart', 'hotlist_tag', $filterStartsWith);

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
		global $g_oSec;

		commonHeader();

		if (!$g_oSec->HasPerm(DCL_ENTITY_HOTLIST, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$oView = new boView();
		$oView->table = 'dcl_hotlist';
		$oView->AddDef('columnhdrs', '', array(STR_CMMN_ID, STR_CMMN_ACTIVE, STR_CMMN_NAME, '# Items'));
		$oView->AddDef('columns', '', array('hotlist_id', 'active', 'hotlist_tag', 'count(*):dcl_entity_hotlist'));
		$oView->AddDef('order', '', array('hotlist_tag'));
		
		$oView->numrows = 25;

		$filterActive = @$_REQUEST['filterActive'];
		if ($filterActive == 'Y' || $filterActive == 'N')
			$oView->AddDef('filter', 'active', "'$filterActive'");

		$filterSearch = isset($_REQUEST['filterSearch']) ? $_REQUEST['filterSearch'] : '';
		if ($filterSearch != '')
			$oView->AddDef('filterlike', 'hotlist_tag', $filterSearch);

		$filterStartsWith = isset($_REQUEST['filterStartsWith']) ? $_REQUEST['filterStartsWith'] : '';
		if ($filterStartsWith != '')
			$oView->AddDef('filterstart', 'hotlist_tag', $filterStartsWith);

		$this->sColumnTitle = STR_CMMN_OPTIONS;
		$this->bShowPager = true;
		$this->Render($oView);
	}
}
