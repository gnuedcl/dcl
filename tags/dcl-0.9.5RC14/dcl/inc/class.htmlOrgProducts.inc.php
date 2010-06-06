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

class htmlOrgProducts
{
	var $public;

	function htmlOrgProducts()
	{
		$this->public = array('modify', 'submitModify');
	}

	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		
		if (($id = DCL_Sanitize::ToInt($_REQUEST['org_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY, $id))
			return PrintPermissionDenied();

		$oOrg = CreateObject('dcl.dbOrg');
		if ($oOrg->Load($id) == -1)
		    return;
		    
		// Get orgs for this contact
		$oViewProduct =& CreateObject('dcl.boView');
		$oViewProduct->table = 'products';
		$oViewProduct->AddDef('columnhdrs', '', array(STR_CMMN_ID, STR_CMMN_NAME));
		$oViewProduct->AddDef('columns', '', array('id', 'name'));
		$oViewProduct->AddDef('order', '', array('name'));
		$oViewProduct->AddDef('filter', 'dcl_org_product_xref.org_id', $id);

		$aProducts = array();
		$aProductsNames = array();
		
		$oProducts =& CreateObject('dcl.dbProducts');
		if ($oProducts->Query($oViewProduct->GetSQL()) != -1)
		{
			while ($oProducts->next_record())
			{
				$aProducts[] = $oProducts->f(0);
				$aProductsNames[] = $oProducts->f(1);
			}

			$oProducts->FreeResult();
		}

		$this->ShowEntryForm($oOrg, $aProducts, $aProductsNames);
	}

	function submitModify()
	{
		global $g_oSec;
		
		commonHeader();
		
		if (($id = DCL_Sanitize::ToInt($_REQUEST['org_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY, $id))
			return PrintPermissionDenied();

		CleanArray($_REQUEST);

		$aProducts = @DCL_Sanitize::ToIntArray($_REQUEST['product_id']);
		$oDbProduct = CreateObject('dcl.dbOrgProduct');
		$oDbProduct->updateProducts($id, $aProducts);

		$oOrgDetail = CreateObject('dcl.htmlOrgDetail');
		$oOrgDetail->show();
	}

	function ShowEntryForm(&$oOrg, &$aProductID, &$aProductName)
	{
		global $dcl_info, $g_oSec;

		$oSmarty =& CreateSmarty();
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY, $oOrg->org_id))
			return PrintPermissionDenied();
			
		$oSmarty->assign('TXT_TITLE', 'Edit Organization Products');
		$oSmarty->assign('VAL_MENUACTION', 'htmlOrgProducts.submitModify');
		$oSmarty->assign('VAL_ORGID', $oOrg->org_id);
		$oSmarty->assign('URL_BACK', menuLink('', 'menuAction=htmlOrgDetail.show&org_id=' . $oOrg->org_id));			
		$oSmarty->assign('VAL_ORGNAME', $oOrg->name);

		$oSmarty->assign_by_ref('VAL_PRODUCTID', $aProductID);
		$oSmarty->assign_by_ref('VAL_PRODUCTNAME', $aProductName);

		SmartyDisplay($oSmarty, 'htmlOrgProducts.tpl');
	}
}
?>