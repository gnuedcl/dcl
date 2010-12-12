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
 
class htmlBuildManagerVersionFileView extends htmlView
{
	var $productid;
	
	function htmlBuildManagerVersionView()
	{
		parent::htmlView();
		$this->sColumnTitle = STR_CMMN_OPTIONS;
		$this->productid = 0;
	}

	function _SetActionFormOptions()
	{
		$aLinks = array(
				'New' => menuLink('', 'menuAction=boBuildManager.add&which=release&product_id=' . $this->productid . '&add=1')
			);

		$aTitle = array('My Information');
	
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

			foreach ($aTitle as $Title => $sTitle)
				$this->_SetVar('VAL_ACTIONTITLE', $sTitle);
				
			$this->Template->parse('hActionLinkSetLinks', 'actionLinkSetLink', true);
		}
		
		$this->Template->parse('hActionLinkSet', 'actionLinkSet');
		$this->Template->parse('hActions', 'actions');
	}

	
	function _DisplayOptions()
	{
		global $dcl_info, $g_oSec, $g_oSession;

		$versionid = $this->oDB->f('product_version_id');

		$this->_SetVar('hDetailColumnLinkSetLinks', '');
		$this->_SetVar('LNK_COLUMNDISABLED', '');
		
		//$this->_AddDisplayOption(STR_CMMN_VIEW, menuLink('', 'menuAction=Product.DetailBuild&product_version_id=' . $versionid . '&product_id=' . $this->productid), false);
		//$this->_AddDisplayOption(STR_CMMN_EDIT, menuLink('', 'menuAction=htmlBuildManagerReleaseForm.Show&versionid=' . $versionid . '&product_id=' . $this->productid . '&which=release' ), true);
		//$this->_AddDisplayOption(STR_CMMN_SHOWVERSION, menuLink('', 'menuAction=boBuildManager.ShowWorkOrders&versionid=' . $versionid . '&product_id=' . $this->productid . '&from=version'), true);
		//$this->_AddDisplayOption(STR_CMMN_SHOWFILES, menuLink('', 'menuAction=boBuildManager.ShowFiles&versionid=' . $versionid . '&product_id=' . $this->productid. '&from=version'), true);

		$this->Template->parse('hDetailColumnLinkSet', 'detailColumnLinkSet');
		$this->Template->parse('hDetailCells', 'detailCells', true);

		// this avoids repeating cells
		$this->_ResetDetailCells();
	}
	
}
?>