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

LoadStringResource('chk');
class htmlChecklistTplView extends htmlView
{
	function htmlChecklistTplView()
	{
		parent::htmlView();
		$this->sColumnTitle = STR_CMMN_OPTIONS;
		$this->sSortAction = 'htmlChecklistTplView.execurl';
	}
	
	function _SetActionFormOptions()
	{
		global $g_oSec;
		
		$aLinks = array();
		if ($g_oSec->HasPerm(DCL_ENTITY_FORMS, DCL_PERM_VIEW))
			$aLinks[STR_CMMN_BROWSE] = menuLink('', 'menuAction=boChecklists.show');

		if ($g_oSec->HasPerm(DCL_ENTITY_FORMTEMPLATES, DCL_PERM_VIEW))
			$aLinks[STR_CHK_TEMPLATES] = menuLink('', 'menuAction=boChecklistTpl.show');
			
		if ($g_oSec->HasPerm(DCL_ENTITY_FORMTEMPLATES, DCL_PERM_ADD))
			$aLinks[STR_CHK_NEWTEMPLATE] = menuLink('', 'menuAction=boChecklistTpl.add');

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

	function _DisplayOptions()
	{
		global $dcl_info, $g_oSec;
		
		$this->_SetVar('hDetailColumnLinkSetLinks', '');
		$id = $this->oDB->f('dcl_chklst_tpl_id');

		if ($g_oSec->HasPerm(DCL_ENTITY_FORMS, DCL_PERM_ADD))
			$this->_AddDisplayOption(STR_CHK_INITIATE, menuLink('', 'menuAction=boChecklists.add&dcl_chklst_tpl_id=' . $id), false, $this->oDB->f('dcl_chklst_tpl_active') != 'Y');
		
		if ($g_oSec->HasPerm(DCL_ENTITY_FORMTEMPLATES, DCL_PERM_VIEW, $id))
			$this->_AddDisplayOption(STR_CMMN_VIEW, menuLink('', 'menuAction=boChecklistTpl.view&dcl_chklst_tpl_id=' . $id), true);

		if ($g_oSec->HasPerm(DCL_ENTITY_FORMTEMPLATES, DCL_PERM_MODIFY, $id))
			$this->_AddDisplayOption(STR_CMMN_EDIT, menuLink('', 'menuAction=boChecklistTpl.modify&dcl_chklst_tpl_id=' . $id), true);

		if ($g_oSec->HasPerm(DCL_ENTITY_FORMTEMPLATES, DCL_PERM_DELETE, $id))
			$this->_AddDisplayOption(STR_CMMN_DELETE, menuLink('', 'menuAction=boChecklistTpl.delete&dcl_chklst_tpl_id=' . $id), true);

		$this->Template->parse('hDetailColumnLinkSet', 'detailColumnLinkSet');
		$this->Template->parse('hDetailCells', 'detailCells', true);

		// this avoids repeating cells
		$this->_ResetDetailCells();
	}
}
?>
