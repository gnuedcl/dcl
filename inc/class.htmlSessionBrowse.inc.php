<?php
/*
 * $Id: class.htmlSessionBrowse.inc.php,v 1.1.1.1 2006/11/27 05:30:43 mdean Exp $
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

import('htmlView');
class htmlSessionBrowse extends htmlView
{
	function htmlSessionBrowse()
	{
		parent::htmlView();
		$this->sColumnTitle = STR_CMMN_OPTIONS;
		$this->sSortAction = 'htmlSessionBrowse.execurl';
	}

	function InitTemplate()
	{
		$this->Template = CreateTemplate(array('hForm' => 'htmlSessionBrowse.tpl'));
		$this->_CreateBlocks();
		$this->_ResetBlocks();
	}

	function Render(&$oView)
	{
		global $g_oSec, $dcl_info;

		if (!$g_oSec->HasPerm(DCL_ENTITY_SESSION, DCL_PERM_VIEW))
			return PrintPermissionDenied();

		parent::Render($oView);
	}

	function _SetStaticOptions()
	{
		parent::_SetStaticOptions();
		$this->_SetVar('LNK_KILLSESSION', menuLink('', 'menuAction=htmlSession.Kill'));
	}

	function _SetActionFormOptions()
	{
		$aLinks = array(
				'Refresh' => menuLink('', 'menuAction=htmlSession.Show'),
				STR_CMMN_VIEW => menuLink('', 'menuAction=htmlSession.Detail')
			);

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
		global $dcl_info, $g_oSec, $g_oSession;

		$id = $this->oDB->f('dcl_session_id');

		$this->_SetVar('hDetailColumnLinkSetLinks', '');
		$this->_SetVar('LNK_COLUMNDISABLED', '');

		$this->_AddDisplayOption('Kill', $id, false, ($id == $g_oSession->dcl_session_id));

		$this->Template->parse('hDetailColumnLinkSet', 'detailColumnLinkSet');
		$this->Template->parse('hDetailCells', 'detailCells', true);

		// this avoids repeating cells
		$this->_ResetDetailCells();
	}
}
?>
