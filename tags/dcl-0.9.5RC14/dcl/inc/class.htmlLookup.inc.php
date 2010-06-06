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

LoadStringResource('lkp');
class htmlLookup
{
	var $id;
	var $active;
	var $name;
	var $mode;

	function htmlLookup()
	{
		$this->id = 0;
		$this->active = 'Y';
		$this->name = '';
		$this->mode = DCL_MODE_ADD;
	}

	function Show($obj = '')
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_LOOKUP, $this->mode == DCL_MODE_EDIT ? DCL_PERM_MODIFY : DCL_PERM_ADD))
			return PrintPermissionDenied();

		$Template = CreateTemplate(array('hForm' => 'htmlLookup.tpl'));
		$Template->set_var('VAL_FORMACTION', menuLink());
		$Template->set_var('BTN_SAVE', STR_CMMN_SAVE);
		$Template->set_var('BTN_RESET', STR_CMMN_RESET);
		$Template->set_var('CMB_ACTIVE', GetYesNoCombo($this->active, 'dcl_lookup_active', 0, false));
		$Template->set_var('VAL_NAME', htmlspecialchars($this->name));
		$Template->set_var('TXT_NAME', STR_LKP_NAME);
		$Template->set_var('TXT_ACTIVE', STR_LKP_ACTIVE);
		$Template->set_var('TXT_HIGHLIGHTEDNOTE', STR_CMMN_HIGHLIGHTEDNOTE);

		if ($this->mode == DCL_MODE_EDIT)
		{
			$Template->set_var('TXT_TITLE', STR_LKP_EDITTITLE);
			$hiddenvars = GetHiddenVar('menuAction', 'boLookup.dbmodify');
			$hiddenvars .= GetHiddenVar('dcl_lookup_id', $this->id);
		}
		else
		{
			$Template->set_var('TXT_TITLE', STR_LKP_ADDTITLE);
			$hiddenvars = GetHiddenVar('menuAction', 'boLookup.dbadd');
		}

		$Template->set_var('HIDDEN_VARS', $hiddenvars);
		$Template->pparse('out', 'hForm');
	}

	function ViewItems()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_LOOKUP, DCL_PERM_VIEW))
			return PrintPermissionDenied();

		$obj = CreateObject('dcl.dbLookup');
		if ($obj->Load($this->id) == -1)
		{
			trigger_error(STR_LKP_NOTFOUND, $this->id);
			return;
		}

		$oView = CreateObject('dcl.boView');
	}

	function showall()
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_LOOKUP, DCL_PERM_VIEW))
			return PrintPermissionDenied();

		$obj = CreateObject('dcl.dbLookup');
		$obj->Query("SELECT dcl_lookup_id, dcl_lookup_active, dcl_lookup_name FROM dcl_lookup ORDER BY dcl_lookup_name");
		$allRecs = $obj->FetchAllRows();

		$oTable =& CreateObject('dcl.htmlTable');
		$oTable->setCaption(STR_LKP_TABLETITLE);
		$oTable->addColumn(STR_LKP_ID, 'numeric');
		$oTable->addColumn(STR_LKP_ACTIVEABB, 'string');
		$oTable->addColumn(STR_LKP_NAME, 'string');

		if ($g_oSec->HasPerm(DCL_ENTITY_LOOKUP, DCL_PERM_ADD))
			$oTable->addToolbar(menuLink('', 'menuAction=boLookup.add'), STR_CMMN_NEW);

		if ($g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW))
			$oTable->addToolbar(menuLink('', 'menuAction=boAdmin.ShowSystemConfig'), DCL_MENU_SYSTEMSETUP);

		if (count($allRecs) > 0)
		{
			$oTable->addColumn(STR_CMMN_OPTIONS, 'html');
			for ($i = 0; $i < count($allRecs); $i++)
			{
				$options = '<a href="' . menuLink('', 'menuAction=boLookup.view&id=' . $allRecs[$i][0]) . '">' . STR_CMMN_VIEW . '</a>';

				if ($g_oSec->HasPerm(DCL_ENTITY_LOOKUP, DCL_PERM_MODIFY))
					$options .= '&nbsp;|&nbsp;<a href="' . menuLink('', 'menuAction=boLookup.modify&id=' . $allRecs[$i][0]) . '">' . STR_CMMN_EDIT . '</a>';

				if ($g_oSec->HasPerm(DCL_ENTITY_LOOKUP, DCL_PERM_DELETE))
					$options .= '&nbsp;|&nbsp;<a href="' . menuLink('', 'menuAction=boLookup.delete&id=' . $allRecs[$i][0]) . '">' . STR_CMMN_DELETE . '</a>';

				$allRecs[$i][] = $options;
			}
		}

		$oTable->setData($allRecs);
		$oTable->setShowRownum(true);
		$oTable->render();
	}
}
?>
