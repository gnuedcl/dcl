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

LoadStringResource('bm');
LoadStringResource('prod');

class htmlBuildManagerReleaseForm
{
	var $oSmarty;
	var $eState;

	function htmlBuildManagerReleaseForm()
	{
		$this->oSmarty = new DCL_Smarty();
		$this->eState = DCL_FORM_ADD;
	}
	
	function Show()
	{
	    global $dcl_info, $g_oSec;
		
		commonHeader();	
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_BUILDMANAGER, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();
			
		if (($product_id = DCL_Sanitize::ToInt($_REQUEST['product_id'])) === null)
			throw new PermissionDeniedException();
		
		if (($version_id = DCL_Sanitize::ToInt($_REQUEST['product_version_id'])) === null)
			throw new PermissionDeniedException();
		
		$oProduct = new ProductModel();
		$oProduct->Load($product_id);
		
		$oPV = new dbProductVersion();
		if ($oPV->Load(array('product_version_id' => $version_id)) == -1)
		{
			ShowError('Failed to load version ID ' . $version_id, 'Error');
			return;
		}
		
		$this->oSmarty->assign('VAL_JSDATEFORMAT', GetJSDateFormat());
		$this->oSmarty->assign('VAL_FORMACTION', menuLink());
		if (IsSet($GLOBALS['add']))
			$this->oSmarty->assign('VAL_MENUACTION', 'boBuildManager.addRelease');
		else
			$this->oSmarty->assign('VAL_MENUACTION', 'boBuildManager.modifyRelease');
			
		$this->oSmarty->assign('TXT_BM_ADD_RELEASE', STR_BM_MOD_RELEASE);
		$this->oSmarty->assign('TXT_BM_PRODUCT', STR_BM_PRODUCT);
		$this->oSmarty->assign('TXT_BM_RELEASE_ALIAS_TITLE', STR_BM_RELEASE_ALIAS_TITLE);
		$this->oSmarty->assign('TXT_BM_RELEASEDATE_DESC', STR_BM_RELEASEDATE_DESC);
		$this->oSmarty->assign('TXT_BM_RELEASEDATE', STR_BM_RELEASEDATE);
		
		$this->oSmarty->assign('VAL_PRODUCTNAME', $oProduct->name);
		$this->oSmarty->assign('VAL_PRODUCTID', $oProduct->id);
		$this->oSmarty->assign('VAL_VERSIONID', $version_id);
		$this->oSmarty->assign('VAL_VERSIONTEXT', $oPV->product_version_text);
		$this->oSmarty->assign('VAL_VERSIONDESCR', $oPV->product_version_descr);
		$this->oSmarty->assign('VAL_ACTIVE', $oPV->active);
		
		$this->oSmarty->assign('VAL_VERSIONACTUALDATE', $oPV->product_version_actual_date);
		$this->oSmarty->assign('VAL_VERSIONTARGETDATE', $oPV->product_version_target_date);

		$this->oSmarty->assign('date', $oPV->product_version_target_date);
		$this->oSmarty->assign('VAL_DATEELEMENT','product_version_target_date');
		$this->oSmarty->assign('VAL_WHICH', $GLOBALS['which']);
		
		$this->oSmarty->Render('htmlBuildManagerReleaseForm.tpl');
	}
}
