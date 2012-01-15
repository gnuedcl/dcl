<?php
/*
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

class htmlRoleBrowse extends htmlView
{
	function htmlRoleBrowse()
	{
		parent::htmlView();
		$this->sPagingMenuAction = 'htmlRoleBrowse.Page';

		$this->sColumnTitle = STR_CMMN_OPTIONS;
	}
	
	function InitTemplate()
	{
		$this->Template = CreateTemplate(array('hForm' => 'RoleBrowse.tpl'));
		$this->_CreateBlocks();
		$this->_ResetBlocks();
	}

	function Render(&$oView)
	{
		global $dcl_info, $g_oSec;

		if (!is_object($oView))
		{
			print('[htmlRoleBrowse::Render] ' . STR_VW_VIEWOBJECTNOTPASSED);
			return;
		}

		if (!$g_oSec->HasPerm(DCL_ENTITY_ROLE, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$this->oView = &$oView;

		$this->InitTemplate();
		
		$this->_SetStaticOptions();
		if (!$this->_Execute())
			return;

		$this->_SetActionFormOptions();
		$this->_SetFilterActiveOptions();
		
		$this->bShowPager = true;
		$this->_SetPager();

		$this->_SetVar('VAL_SEARCHACTION', menuLink());
		$this->_SetVar('VAL_VIEWSETTINGS', $this->oView->GetForm());

		if (!$this->oDB->next_record())
		{
			$this->_NoMatches();
			$this->_Finish();
			return;
		}

		$iOffset = 0;
		if (in_array('_num_accounts_', $this->oDB->Record))
			$iOffset = -1;

		do // next_record already called to check for matches
		{
			$this->_DisplayGroups();
			$this->_DisplayDetailHeader();
			$this->_DisplayDetail();
		}
		while ($this->oDB->next_record());

		$this->Template->parse('hSection', 'section', true);
		$this->Template->parse('hMatches', 'matches');
		$this->_Finish();
	}
	
	function Page()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_ROLE, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$oView = new boView();
		$oView->SetFromURL();

		if ((IsSet($_REQUEST['btnNav']) || IsSet($_REQUEST['jumptopage'])) && IsSet($_REQUEST['startrow']) && IsSet($_REQUEST['numrows']))
		{
			if (IsSet($_REQUEST['btnNav']) && $_REQUEST['btnNav'] == '<<')
				$oView->startrow = (int)$_REQUEST['startrow'] - (int)$_REQUEST['numrows'];
			else if (IsSet($_REQUEST['btnNav']) && $_REQUEST['btnNav'] == '>>')
				$oView->startrow = (int)$_REQUEST['startrow'] + (int)$_REQUEST['numrows'];
			else
			{
				$iPage = (int)$_REQUEST['jumptopage'];
				if ($iPage < 1)
					$iPage = 1;

				$oView->startrow = ($iPage - 1) * (int)$_REQUEST['numrows'];
			}

			if ($oView->startrow < 0)
				$oView->startrow = 0;

			$oView->numrows = (int)$_REQUEST['numrows'];
		}
		else
		{
			$oView->numrows = 25;
			$oView->startrow = 0;
		}

		$filterActive = '0';
		if (IsSet($_REQUEST['filterActive']))
			$filterActive = $_REQUEST['filterActive'];

		if ($filterActive != '0')
			$oView->ReplaceDef('filter', 'active', "'$filterActive'");
		else
			$oView->RemoveDef('filter', 'active');

		$this->sColumnTitle = STR_CMMN_OPTIONS;
		$this->bShowPager = true;
		$this->Render($oView);
	}

	function _CreateBlocks()
	{
		$this->Template->set_block('hForm', 'pager', 'hPager');
		$this->Template->set_block('hForm', 'nomatches', 'hNomatches');
		$this->Template->set_block('hForm', 'matches', 'hMatches');
		$this->Template->set_block('hForm', 'noActions', 'hNoActions');
		$this->Template->set_block('hForm', 'actions', 'hActions');

		$this->Template->set_block('actions', 'actionLinkSet', 'hActionLinkSet');
		
		$this->Template->set_block('actionLinkSet', 'actionLinkSetLinks', 'hActionLinkSetLinks');

		$this->Template->set_block('actionLinkSetLinks', 'actionLinkSetLink', 'hActionLinkSetLink');
		$this->Template->set_block('actionLinkSetLinks', 'actionLinkSetSep', 'hActionLinkSetSep');

		$this->Template->set_block('matches', 'section', 'hSection');

		$this->Template->set_block('section', 'group', 'hGroup');
		$this->Template->set_block('section', 'detailHeader', 'hDetailHeader');
		$this->Template->set_block('section', 'detailRows', 'hDetailRows');

		$this->Template->set_block('detailHeader', 'detailHeaderCells', 'hDetailHeaderCells');

		$this->Template->set_block('detailHeaderCells', 'detailHeaderPadding', 'hDetailHeaderPadding');
		$this->Template->set_block('detailHeaderCells', 'detailHeaderColumnText', 'hDetailHeaderColumnText');
		$this->Template->set_block('detailHeaderCells', 'detailHeaderColumnLink', 'hDetailHeaderColumnLink');

		$this->Template->set_block('detailRows', 'detail', 'hDetail');
		$this->Template->set_block('detail', 'detailCells', 'hDetailCells');

		$this->Template->set_block('detailCells', 'detailPadding', 'hDetailPadding');
		$this->Template->set_block('detailCells', 'detailColumnText', 'hDetailColumnText');
		$this->Template->set_block('detailCells', 'detailColumnLink', 'hDetailColumnLink');

		$this->Template->set_block('hForm', 'filterActiveOptions', 'hFilterActiveOptions');
		$this->Template->set_block('detailHeaderCells', 'detailHeaderCheckbox', 'hDetailHeaderCheckbox');
		$this->Template->set_block('detailCells', 'detailCheckbox', 'hDetailCheckbox');
		$this->Template->set_block('detailCells', 'detailColumnAccount', 'hDetailColumnAccount');
		$this->Template->set_block('detailCells', 'detailColumnLinkSet', 'hDetailColumnLinkSet');
		
		$this->Template->set_block('detailColumnLinkSet', 'detailColumnLinkSetLinks', 'hDetailColumnLinkSetLinks');

		$this->Template->set_block('detailColumnLinkSetLinks', 'detailColumnLinkSetLink', 'hDetailColumnLinkSetLink');
		$this->Template->set_block('detailColumnLinkSetLinks', 'detailColumnLinkSetSep', 'hDetailColumnLinkSetSep');
	}

	function _ResetBlocks()
	{
		$this->_SetVar('hNoActions', '');
		$this->_SetVar('hActions', '');
		$this->_SetVar('hActionLinkSet', '');
		$this->_SetVar('hActionLinkSetLinks', '');
		$this->_SetVar('hActionLinkSetLink', '');
		$this->_SetVar('hActionLinkSetSep', '');
		$this->_SetVar('hPager', '');
		$this->_SetVar('hNomatches', '');
		$this->_SetVar('hMatches', '');
		$this->_SetVar('hSection', '');
		$this->_SetVar('hGroup', '');
		$this->_SetVar('hDetailHeader', '');
		$this->_SetVar('hDetailRows', '');
		$this->_SetVar('hDetailHeaderCells', '');
		$this->_SetVar('hDetailHeaderPadding', '');
		$this->_SetVar('hDetailHeaderColumnText', '');
		$this->_SetVar('hDetailHeaderColumnLink', '');
		$this->_SetVar('hDetail', '');
		$this->_SetVar('hDetailRows', '');
		$this->_SetVar('hDetailCells', '');
		$this->_SetVar('hDetailPadding', '');
		$this->_SetVar('hDetailColumnText', '');
		$this->_SetVar('hDetailColumnLink', '');
		$this->_SetVar('hFilterActiveOptions', '');
		$this->_SetVar('hDetailHeaderCheckbox', '');
		$this->_SetVar('hDetailCheckbox', '');
		$this->_SetVar('hDetailColumnAccount', '');
		$this->_SetVar('hDetailColumnLinkSet', '');
		$this->_SetVar('hDetailColumnLinkSetLinks', '');
		$this->_SetVar('hDetailColumnLinkSetLink', '');
		$this->_SetVar('hDetailColumnLinkSetSep', '');
	}

	function _SetStaticOptions()
	{
		parent::_SetStaticOptions();
		$this->_SetVar('TXT_GO', STR_CMMN_GO);
		$this->_SetVar('TXT_FILTER', 'Filter');
		$this->_SetVar('TXT_ACTIVE', STR_CMMN_ACTIVE);
	}

	function _SetActionFormOptions()
	{
		global $dcl_info, $g_oSec;

		$aLinks = array();
		if ($g_oSec->HasPerm(DCL_ENTITY_ROLE, DCL_PERM_ADD))
			$aLinks[STR_CMMN_NEW] = menuLink('', 'menuAction=htmlRoleForm.add');
		
		if ($g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW))
			$aLinks[DCL_MENU_SYSTEMSETUP] = menuLink('', 'menuAction=SystemSetup.Index');

		$this->_SetVar('hActionLinkSetLinks', '');
		$bFirst = true;
		foreach ($aLinks as $sText => $sLink)
		{
			if ($bFirst)
				$bFirst = false;
			else
				$this->Template->parse('hActionLinkSetLinks', 'actionLinkSetSep', true);

			$this->_SetVar('LNK_ACTIONVALUE', $sLink);
			$this->_SetVar('VAL_ACTIONVALUE', $sText);
			$this->Template->parse('hActionLinkSetLinks', 'actionLinkSetLink', true);
		}
		
		$this->Template->parse('hActionLinkSet', 'actionLinkSet');
		$this->Template->parse('hActions', 'actions');
	}

	function _AddFilterActiveOption($sValue, $sDescription, $bSelected = false)
	{
		$this->_AddOption('hFilterActiveOptions', 'filterActiveOptions', array(
			'VAL_FILTERACTIVEOPTION' => $sValue,
			'VAL_FILTERACTIVESELECTED' => ($bSelected ? ' selected' : ''),
			'TXT_FILTERACTIVEOPTION' => $sDescription
			));
	}

	function _SetFilterActiveOptions()
	{
		$filterActive = IsSet($_REQUEST['filterActive']) ? $_REQUEST['filterActive'] : '0';

		$this->_AddFilterActiveOption('0', 'All', $filterActive == '0');
		$this->_AddFilterActiveOption('Y', STR_CMMN_YES, $filterActive == 'Y');
		$this->_AddFilterActiveOption('N', STR_CMMN_NO, $filterActive == 'N');
	}

	function _ResetDetailHeaderCells()
	{
		$this->_SetVar('hDetailHeaderPadding', '');
		$this->_SetVar('hDetailHeaderCheckbox', '');
		$this->_SetVar('hDetailHeaderColumnText', '');
		$this->_SetVar('hDetailHeaderColumnLink', '');
	}

	function _DisplayDetailHeader()
	{
		global $dcl_info;

		if (!$this->bNeedsHeader)
			return;

		$this->_SetVar('hDetailHeader', '');
		$this->_SetVar('hDetailHeaderCells', '');
		$this->_ResetDetailHeaderCells();

		if (count($this->oView->groups) > 0)
		{
			$this->_SetVar('VAL_DETAILHEADERPADDING', (count($this->oView->groups) + 1) * 20);
			$this->Template->parse('hDetailHeaderPadding', 'detailHeaderPadding');
			$this->Template->parse('hDetailHeaderCells', 'detailHeaderCells', true);

			// this avoids repeating cells
			$this->_ResetDetailHeaderCells();
		}

		$iOffset = 0;

		for ($i = count($this->oView->groups); $i < (count($this->oView->groups) + count($this->oView->columns) + $iOffset); $i++)
		{
			if (count($this->oView->columnhdrs) > 0)
				$sHdr = $this->oView->columnhdrs[$i];
			else
				$sHdr = $this->oDB->GetFieldName($i);

			$this->_SetVar('VAL_COLUMNHEADER', $sHdr);

			if ($this->bColumnSort)
			{
				$this->oView->ClearDef('order');
				$this->oView->AddDef('order', $this->oView->columns[$i - count($this->oView->groups)], '');

				$this->_SetVar('LNK_COLUMNHEADER', menuLink('', 'menuAction=' . $this->sPagingMenuAction . '&' . $this->oView->GetURL()));
				$this->Template->parse('hDetailHeaderColumnLink', 'detailHeaderColumnLink');
			}
			else
			{
				$this->Template->parse('hDetailHeaderColumnText', 'detailHeaderColumnText');
			}

			$this->Template->parse('hDetailHeaderCells', 'detailHeaderCells', true);

			// this avoids repeating cells
			$this->_ResetDetailHeaderCells();
		}

		if ($this->sColumnTitle != '')
		{
			$this->_SetVar('VAL_COLUMNHEADER', $this->sColumnTitle);
			$this->Template->parse('hDetailHeaderColumnText', 'detailHeaderColumnText');
			$this->Template->parse('hDetailHeaderCells', 'detailHeaderCells', true);
		}

		$this->bNeedsHeader = false;
		$row = 0;

		$this->Template->parse('hDetailHeader', 'detailHeader');
	}

	function _ResetDetailCells()
	{
		$this->_SetVar('hDetailPadding', '');
		$this->_SetVar('hDetailCheckbox', '');
		$this->_SetVar('hDetailColumnText', '');
		$this->_SetVar('hDetailColumnLink', '');
		$this->_SetVar('hDetailColumnAccount', '');
		$this->_SetVar('hDetailColumnLinkSet', '');
		$this->_SetVar('hDetailColumnLinkSetLinks', '');
		$this->_SetVar('hDetailColumnLinkSetLink', '');
		$this->_SetVar('hDetailColumnLinkSetSep', '');
	}

	function _DisplayDetail()
	{
		global $dcl_info;

		$this->_SetVar('hDetail', '');
		$this->_SetVar('hDetailCells', '');

		if ($this->oDB->cur % 2 == 0)
			$this->_SetVar('VAL_DETAILCLASS', 'even');
		else
			$this->_SetVar('VAL_DETAILCLASS', 'odd');

		if (count($this->oView->groups) > 0)
		{
			$this->_SetVar('VAL_DETAILPADDING', (count($this->oView->groups) + 1) * 20);
			$this->Template->parse('hDetailPadding', 'detailPadding');
			$this->Template->parse('hDetailCells', 'detailCells', true);

			// this avoids repeating cells
			$this->_ResetDetailCells();
		}

		$iGroupCount = count($this->oView->groups);
		for ($i = $iGroupCount; $i < count($this->oView->columns) + count($this->oView->groups); $i++)
		{
			$sFieldName = $this->oDB->GetFieldName($i);
			$sFieldValue = $this->oDB->f($i);

			if ($this->oDB->IsTimestamp($i))
				$this->_SetVar('VAL_COLUMNVALUE', $this->oDB->FormatTimeStampForDisplay($this->oDB->f($i)));
			else if ($this->oDB->IsDate($i))
				$this->_SetVar('VAL_COLUMNVALUE', $this->oDB->FormatDateForDisplay($this->oDB->f($i)));
			else
				$this->_SetVar('VAL_COLUMNVALUE', $this->oDB->f($i));

			$this->Template->parse('hDetailColumnText', 'detailColumnText');

			$this->Template->parse('hDetailCells', 'detailCells', true);

			// this avoids repeating cells
			$this->_ResetDetailCells();
		}

		$this->_DisplayOptions();

		$this->Template->parse('hDetail', 'detail');
		$this->Template->parse('hDetailRows', 'detailRows', true);
	}

	function _DisplayOptions()
	{
		global $dcl_info, $g_oSec;

		$bNeedBar = false;
		if ($g_oSec->HasPerm(DCL_ENTITY_ROLE, DCL_PERM_ADD))
		{
			$this->_AddDisplayOption('Copy', menuLink('', 'menuAction=htmlRoleForm.copy&role_id=' . $this->oDB->f('role_id')), $bNeedBar);
			$bNeedBar = true;
		}
			
		if ($g_oSec->HasPerm(DCL_ENTITY_ROLE, DCL_PERM_MODIFY))
		{
			$this->_AddDisplayOption(STR_CMMN_EDIT, menuLink('', 'menuAction=htmlRoleForm.modify&role_id=' . $this->oDB->f('role_id')), $bNeedBar);
			$bNeedBar = true;
		}
			
		if ($g_oSec->HasPerm(DCL_ENTITY_ROLE, DCL_PERM_DELETE))
		{
			$this->_AddDisplayOption(STR_CMMN_DELETE, menuLink('', 'menuAction=htmlRoleForm.delete&role_id=' . $this->oDB->f('role_id')), $bNeedBar);
			$bNeedBar = true;
		}

		$this->Template->parse('hDetailColumnLinkSet', 'detailColumnLinkSet');
		$this->Template->parse('hDetailCells', 'detailCells', true);

		// this avoids repeating cells
		$this->_ResetDetailCells();
	}
}
