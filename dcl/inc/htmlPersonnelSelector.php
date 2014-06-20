<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2011 Free Software Foundation
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

class htmlPersonnelSelector
{
	var $oSmarty;
	var $bMultiSelect;
	var $oView;
	var $oDB;

	function htmlPersonnelSelector()
	{
		$this->bMultiSelect = false;
		$this->oSmarty = new SmartyHelper();
		$this->oView = new boView();
		$this->oDB = new DbProvider;
	}

	function show()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		if (isset($_REQUEST['multiple']) && $_REQUEST['multiple'] == 'true')
			$this->oSmarty->assign('VAL_MULTIPLE', 'true');
		else
			$this->oSmarty->assign('VAL_MULTIPLE', 'false');

		if (isset($_REQUEST['filterID']) && $_REQUEST['filterID'] != '')
			$this->oSmarty->assign('VAL_FILTERID', $_REQUEST['filterID']);

		$this->oSmarty->Render('PersonnelSelector.tpl');
	}

	function showControlFrame()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		if (isset($_REQUEST['multiple']) && $_REQUEST['multiple'] == 'true')
			$this->oSmarty->assign('VAL_MULTIPLE', 'true');
		else
			$this->oSmarty->assign('VAL_MULTIPLE', 'false');

		$this->oSmarty->assign('VAL_LETTERS', array_merge(array('All'), range('A', 'Z')));

		$filterActive = '';
		if (IsSet($_REQUEST['filterActive']))
			$filterActive = $_REQUEST['filterActive'];

		$filterStartsWith = '';
		if (IsSet($_REQUEST['filterStartsWith']))
			$filterStartsWith = $_REQUEST['filterStartsWith'];
			
		$filterID = '';
		if (IsSet($_REQUEST['filterID']))
			$filterID = $_REQUEST['filterID'];

		$this->oSmarty->assign('VAL_FILTERACTIVE', $filterActive);
		$this->oSmarty->assign('VAL_FILTERSTART', $filterStartsWith);
		$this->oSmarty->assign('VAL_FILTERID', $filterID);

		$this->oSmarty->Render('PersonnelSelectorControl.tpl');
		exit();
	}

	function showBrowseFrame()
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$filterActive = '';
		if (IsSet($_REQUEST['filterActive']))
			$filterActive = $_REQUEST['filterActive'];

		$filterStartsWith = '';
		if (IsSet($_REQUEST['filterStartsWith']))
			$filterStartsWith = $_REQUEST['filterStartsWith'];

		$filterSearch = '';
		if (IsSet($_REQUEST['filterSearch']))
			$filterSearch = $_REQUEST['filterSearch'];

		$filterID = '';
		if (isset($_REQUEST['filterID']))
		{
			if (preg_match('/^[0-9]+([,][0-9]+)*$/', $_REQUEST['filterID']))
				$filterID = explode(',', $_REQUEST['filterID']);
		}

		$aColumnHeaders = array('ID', 'Active', 'User ID', 'Last Name', 'First Name');
		$aColumns = array('id', 'active', 'short', 'dcl_contact.last_name', 'dcl_contact.first_name');

		$iPage = 1;
		$this->oView->startrow = 0;
		$this->oView->numrows = 15;
		if (isset($_REQUEST['page']) && Filter::ToInt($_REQUEST['page']) !== null)
		{
			$iPage = (int)$_REQUEST['page'];
			if ($iPage < 1)
				$iPage = 1;

			$this->oView->startrow = ($iPage - 1) * $this->oView->numrows;
			if ($this->oView->startrow < 0)
				$this->oView->startrow = 0;
		}

		$this->oView->table = 'personnel';
		$this->oView->AddDef('columnhdrs', '', $aColumnHeaders);
		$this->oView->AddDef('columns', '', $aColumns);
		$this->oView->AddDef('order', '', array('short'));

		if ($filterActive == 'Y' || $filterActive == 'N')
			$this->oView->AddDef('filter', 'active', "'$filterActive'");

		if ($filterSearch != '')
			$this->oView->AddDef('filterlike', 'short', $filterSearch);

		if ($filterStartsWith != '')
			$this->oView->AddDef('filterstart', 'short', $filterStartsWith);

		if (is_array($filterID))
			$this->oView->AddDef('filter', 'id', $filterID);

		if ($this->oDB->Query($this->oView->GetSQL(true)) == -1 || !$this->oDB->next_record())
			exit();

		$iRecords = (int)$this->oDB->f(0);
		$this->oSmarty->assign('VAL_COUNT', $iRecords);
		$this->oSmarty->assign('VAL_PAGE', $iPage);
		$this->oSmarty->assign('VAL_MAXPAGE', ceil($iRecords / $this->oView->numrows));
		$this->oDB->FreeResult();

		if ($this->oDB->LimitQuery($this->oView->GetSQL(), $this->oView->startrow, $this->oView->numrows) != -1)
		{
			$aUsers = array();
			while ($this->oDB->next_record())
				$aUsers[] = $this->oDB->Record;

			$this->oDB->FreeResult();

			$this->oSmarty->assignByRef('VAL_USERS', $aUsers);
			$this->oSmarty->assign('VAL_HEADERS', $aColumnHeaders);
			$this->oSmarty->assign('VAL_MULTISELECT', (isset($_REQUEST['multiple']) && $_REQUEST['multiple'] == 'true'));

 			$this->oSmarty->Render('PersonnelSelectorBrowse.tpl');
		}

		exit();
	}
}
