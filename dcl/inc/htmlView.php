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
class htmlView
{
	var $sColumnTitle;
	var $bColumnSort;
	var $bShowPager;
	var $sPagingMenuAction;
	var $Template;
	var $oDB;
	var $bNeedsHeader;
	var $oView;
	var $aGroups;
	var $aSumCols;

	function htmlView()
	{
		$this->oDB = new dbWorkorders();

		$this->bNeedsHeader = true;

		$this->sColumnTitle = '';
		$this->bColumnSort = false;
		$this->bShowPager = false;
		$this->sPagingMenuAction = 'htmlView.Page';

		$this->aGroups = array();
		$this->aSumCols = array();
	}

	function InitTemplate()
	{
		$this->Template = CreateTemplate(array('hForm' => 'htmlView.tpl'));
		$this->_CreateBlocks();
		$this->_ResetBlocks();
	}

	function Render(&$oView)
	{
		global $dcl_info;

		if (!is_object($oView))
		{
			trigger_error('[htmlView::Render] ' . STR_VW_VIEWOBJECTNOTPASSED);
			return;
		}

		$this->oView = &$oView;

		$this->InitTemplate();
		$this->aGroups = array();

		$this->_SetStaticOptions();
		if (!$this->_Execute())
			return;

		$this->_SetActionFormOptions();
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

		$this->_DisplayTotal();

		$this->Template->parse('hSection', 'section', true);
		$this->Template->parse('hMatches', 'matches');
		$this->_Finish();
	}

	function Page()
	{
		global $dcl_info;

		commonHeader();

		$oView = new boView();
		$oView->SetFromURL();

		if ($this->bShowPager)
		{
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
		}

		$this->Render($oView);
	}

	function GetClassNameForTable($sTable)
	{
		$whatObject = '';
		switch ($sTable)
		{
			case 'personnel':
				$whatObject = 'PersonnelModel';
				break;
			case 'tickets':
				$whatObject = 'TicketsModel';
				break;
			case 'workorders':
				$whatObject = 'dbWorkorders';
				break;
			case 'dcl_projects':
				$whatObject = 'ProjectsModel';
				break;
			case 'dcl_chklst':
				$whatObject = 'ChecklistModel';
				break;
			case 'dcl_chklst_tpl':
				$whatObject = 'ChecklistTemplateModel';
				break;
			case 'dcl_product_module':
				$whatObject = 'ProductModulesModel';
				break;
			case 'dcl_wo_type':
				$whatObject = 'WorkOrderTypeModel';
				break;
			case 'dcl_session':
				$whatObject = 'SessionModel';
				break;
			default:
				trigger_error(sprintf(STR_VW_UNKNOWNTABLE, $sTable));
				return '';
		}

		return $whatObject;
	}

	function execurl()
	{
		commonHeader();

		$obj = new boView();
		$obj->SetFromURL();

		$this->Render($obj);
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

	function _NoMatches()
	{
		$this->_SetVar('TXT_NOMATCHES', STR_VW_NOMATCHES);
		$this->Template->parse('hNomatches', 'nomatches');
	}

	function _Finish()
	{
		$this->Template->pparse('out', 'hForm');
	}

	function _SetVar($vKey, $vValue = '')
	{
		$this->Template->set_var($vKey, $vValue);
	}

	function _AddOption($hHandle, $sBlock, $vKey)
	{
		$this->Template->set_var($vKey);
		$this->Template->parse($hHandle, $sBlock, true);
	}

	function _CreateBlocks()
	{
		$this->Template->set_block('hForm', 'pager', 'hPager');
		$this->Template->set_block('hForm', 'noActions', 'hNoActions');
		$this->Template->set_block('hForm', 'actions', 'hActions');
		$this->Template->set_block('hForm', 'nomatches', 'hNomatches');
		$this->Template->set_block('hForm', 'matches', 'hMatches');

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
		$this->Template->set_block('detailCells', 'detailColumnLinkSet', 'hDetailColumnLinkSet');

		$this->Template->set_block('detailColumnLinkSet', 'detailColumnLinkSetLinks', 'hDetailColumnLinkSetLinks');

		$this->Template->set_block('detailColumnLinkSetLinks', 'detailColumnLinkSetLink', 'hDetailColumnLinkSetLink');
		$this->Template->set_block('detailColumnLinkSetLinks', 'detailColumnLinkSetSep', 'hDetailColumnLinkSetSep');
		$this->Template->set_block('detailColumnLinkSetLinks', 'detailColumnLinkSetLinkDisabled', 'hDetailColumnLinkSetLinkDisabled');
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
		$this->_SetVar('hDetailColumnLinkSet', '');
		$this->_SetVar('hDetailColumnLinkSetLinks', '');
		$this->_SetVar('hDetailColumnLinkSetLink', '');
		$this->_SetVar('hDetailColumnLinkSetSep', '');
		$this->_SetVar('hDetailColumnLinkSetLinkDisabled', '');
	}

	function _SetStaticOptions()
	{
		$this->_SetVar('TXT_TITLE', $this->oView->title);
		$this->_SetVar('VAL_FILTERACTION', menuLink());
		$this->_SetVar('IMG_DIR', 'templates/' . GetDefaultTemplateSet() . '/img');
		$this->_SetVar('JS_DIR', 'templates/' . GetDefaultTemplateSet() . '/js');
	}

	function _SetPager()
	{
		if (!$this->bShowPager)
			return;

		$oDB = new dclDB;

		$sSQL = $this->oView->GetSQL(true);
		if ($oDB->Query($sSQL) == -1)
			return;

		$oDB->next_record();
		$iRecords = $oDB->f(0);
		$oDB->FreeResult();

		$bNext = (($this->oView->startrow + $this->oView->numrows) < $iRecords);
		$bPrev = ($this->oView->startrow > 0);

		if ($this->oView->numrows > 0)
		{
			if ($iRecords % $this->oView->numrows == 0)
				$this->_SetVar('VAL_PAGES', strval($iRecords / $this->oView->numrows));
			else
				$this->_SetVar('VAL_PAGES', strval(ceil($iRecords / $this->oView->numrows)));

			$this->_SetVar('VAL_PAGE', strval(($this->oView->startrow / $this->oView->numrows) + 1));
		}
		else
		{
			$this->_SetVar('VAL_PAGES', '0');
			$this->_SetVar('VAL_PAGE', '0');
		}

		$this->_SetVar('VAL_JUMPDISABLED', (($bNext || $bPrev) ? '' : ' disabled'));
		$this->_SetVar('VAL_PREVDISABLED', ($bPrev ? '' : ' disabled'));
		$this->_SetVar('VAL_NEXTDISABLED', ($bNext ? '' : ' disabled'));
		$this->_SetVar('VAL_FILTERMENUACTION', $this->sPagingMenuAction);
		$this->_SetVar('VAL_FILTERSTARTROW', $this->oView->startrow);
		$this->_SetVar('VAL_FILTERNUMROWS', $this->oView->numrows);

		$this->Template->parse('hPager', 'pager');
	}

	function _SetActionFormOptions()
	{
		$this->Template->parse('hNoActions', 'noActions');
	}

	function _DisplayGroups()
	{
		global $dcl_info, $g_oSec;

		if (count($this->oView->groups) < 1)
			return;

		// Grouping the report, so check and display headings as needed
		$iExtraCols = 2;

		// FIXME: Need more perms here
		if ($g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION))
			$iExtraCols++;

		$bGroupChanged = false;
		$bSectionChanged = false;
		for ($i = 0; $i < count($this->oView->groups); $i++)
		{
			$thisGroup = $this->oDB->f($i);
			if ($thisGroup == '')
				$thisGroup = '<NULL>';

			if (!IsSet($this->aGroups[$i]) || $this->aGroups[$i] != $thisGroup)
			{
				if (!$bSectionChanged && IsSet($this->aGroups[$i]))
				{
					$this->Template->parse('hSection', 'section', true);
					$this->_SetVar('hGroup', '');
					$this->_SetVar('hDetailHeader', '');
					$this->_SetVar('hDetailRows', '');
					$bSectionChanged = true;
				}

				$this->_SetVar('VAL_GROUPCOLSPAN', count($this->oView->columns) + $iExtraCols);
				$this->_SetVar('VAL_GROUPPADDING', $i * 20);

				if ($i == 0)
					$this->_SetVar('VAL_GROUPCLASS', 'groupLevel0');
				else
					$this->_SetVar('VAL_GROUPCLASS', 'groupLevel1');

				if ($i == 0 && $thisGroup == '<NULL>')
					$this->_SetVar('VAL_GROUP', '&nbsp;');
				else
					$this->_SetVar('VAL_GROUP', htmlentities($thisGroup));

				$this->Template->parse('hGroup', 'group', true);

				if ($i < (count($this->oView->groups) - 1))
				{
					for ($j = $i + 1; $j < count($this->oView->groups); $j++)
						unset($this->aGroups[$j]);
				}

				$this->aGroups[$i] = $thisGroup;
				$this->bNeedsHeader = true;
			}
		}
	}

	function _ResetDetailHeaderCells()
	{
		$this->_SetVar('hDetailHeaderPadding', '');
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
		$this->_SetVar('hDetailColumnText', '');
		$this->_SetVar('hDetailColumnLink', '');
		$this->_SetVar('hDetailColumnLinkSet', '');
		$this->_SetVar('hDetailColumnLinkSetLinks', '');
		$this->_SetVar('hDetailColumnLinkSetLink', '');
		$this->_SetVar('hDetailColumnLinkSetSep', '');
		$this->_SetVar('hDetailColumnLinkSetLinkDisabled', '');
	}

	function _DisplayTotal()
	{
		if (count($this->aSumCols) < 1)
			return;

		global $dcl_info;

		$this->_SetVar('hDetail', '');
		$this->_SetVar('hDetailCells', '');

		$this->_SetVar('VAL_DETAILCLASS', 'footer');

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
			if (isset($this->aSumCols[$i]))
				$this->_SetVar('VAL_COLUMNVALUE', $this->aSumCols[$i]);
			else
				$this->_SetVar('VAL_COLUMNVALUE');

			$this->Template->parse('hDetailColumnText', 'detailColumnText');

			$this->Template->parse('hDetailCells', 'detailCells', true);

			// this avoids repeating cells
			$this->_ResetDetailCells();
		}

		$this->Template->parse('hDetail', 'detail');
		$this->Template->parse('hDetailRows', 'detailRows', true);
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

			// Accumulate totals
			if (count($this->aSumCols) > 0)
			{
				if (IsSet($this->aSumCols[$i]))
					$this->aSumCols[$i] += $this->oDB->f($i);
			}
		}

		$this->_DisplayOptions();

		$this->Template->parse('hDetail', 'detail');
		$this->Template->parse('hDetailRows', 'detailRows', true);
	}

	function _DisplayOptions()
	{
	}

	function _AddDisplayOption($sText, $sLink, $bShowSep = false, $bDisabled = false)
	{
		if ($bShowSep)
			$this->Template->parse('hDetailColumnLinkSetLinks', 'detailColumnLinkSetSep', true);

		$this->_SetVar('LNK_COLUMNVALUE', $sLink);
		$this->_SetVar('VAL_COLUMNVALUE', $sText);

		if ($bDisabled)
			$this->Template->parse('hDetailColumnLinkSetLinks', 'detailColumnLinkSetLinkDisabled', true);
		else
			$this->Template->parse('hDetailColumnLinkSetLinks', 'detailColumnLinkSetLink', true);
	}
}
