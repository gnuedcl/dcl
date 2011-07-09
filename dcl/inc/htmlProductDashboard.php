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

LoadStringResource('prod');
LoadStringResource('wo');
LoadStringResource('tck');

class htmlProductDashboard
{
	var $oSmarty;
	var $oProduct;

	function htmlProductDashboard()
	{
		$this->oSmarty = new SmartyHelper();
		$this->oProduct = null;
	}

	function ShowAll()
	{
		global $g_oSec, $dcl_info;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$this->oSmarty->Render('htmlProductDashboardAll.tpl');
	}
	
	function Show()
	{
		$this->ShowPage('htmlProductDashboard.tpl');
	}

	function ShowTicket()
	{
		$this->ShowPage('htmlProductDashboardTickets.tpl');
	}
	
	function ShowPage($sPage)
	{
		global $g_oSec, $dcl_info;

		commonHeader();
		if (($productid = Filter::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_VIEW, $productid))
			throw new PermissionDeniedException();

		$this->oProduct = new ProductModel();
		if ($this->oProduct->Load($productid) == -1)
		{
			trigger_error('Could not find a product with an id of ' . $productid, E_USER_ERROR);
			return;
		}

		$this->oSmarty->assign('VAL_ID', $this->oProduct->id);
		$this->oSmarty->assign('VAL_NAME', $this->oProduct->name);

		$this->oSmarty->assign('PERM_VIEWWO', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEW));
		$this->oSmarty->assign('PERM_VIEWTCK', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_VIEW));
		$this->oSmarty->assign('PERM_WIKI', $dcl_info['DCL_WIKI_ENABLED'] == 'Y' && $g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_VIEWWIKI));
		$this->oSmarty->assign('PERM_EDIT', $g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_MODIFY));
		$this->oSmarty->assign('PERM_DELETE', $g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_DELETE));
		$this->oSmarty->assign('PERM_VERSIONS', $dcl_info['DCL_BUILD_MANAGER_ENABLED'] == 'Y' && $this->oProduct->is_versioned == 'Y');
		
		$this->oSmarty->Render($sPage);
	}
}
