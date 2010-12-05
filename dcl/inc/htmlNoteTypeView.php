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
class htmlNoteTypeView extends htmlView
{
	function htmlNoteTypeView()
	{
		parent::htmlView();
		$this->sColumnTitle = STR_CMMN_OPTIONS;
		$this->sSortAction = 'htmlNoteTypeView.execurl';
	}
	
	function Render(&$oView)
	{
		global $g_oSec, $dcl_info;
		
		if ($g_oSec->HasPerm(DCL_ENTITY_NOTETYPE, DCL_PERM_VIEW))
			parent::Render($oView);
		else
			return PrintPermissionDenied();
	}
	
	function _SetActionFormOptions()
	{
		global $dcl_info, $g_oSec;

		$aLinks = array();
		if ($g_oSec->HasPerm(DCL_ENTITY_NOTETYPE, DCL_PERM_ADD))
			$aLinks[STR_CMMN_NEW] = menuLink('', 'menuAction=htmlNoteType.add');
		
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

	function _DisplayOptions()
	{
		global $dcl_info, $g_oSec;
		
		$this->_SetVar('hDetailColumnLinkSetLinks', '');
		$id = $this->oDB->f('note_type_id');

		if ($g_oSec->HasPerm(DCL_ENTITY_NOTETYPE, DCL_PERM_MODIFY))
			$this->_AddDisplayOption(STR_CMMN_EDIT, menuLink('', 'menuAction=htmlNoteType.modify&note_type_id=' . $id), false);
			
		if ($g_oSec->HasPerm(DCL_ENTITY_NOTETYPE, DCL_PERM_DELETE))
			$this->_AddDisplayOption(STR_CMMN_DELETE, menuLink('', 'menuAction=htmlNoteType.delete&note_type_id=' . $id), true);

		$this->Template->parse('hDetailColumnLinkSet', 'detailColumnLinkSet');
		$this->Template->parse('hDetailCells', 'detailCells', true);

		// this avoids repeating cells
		$this->_ResetDetailCells();
	}
}
?>