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

class OrganizationProductPresenter
{
	public function Edit($orgId)
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY, $orgId))
			throw new PermissionDeniedException();

		$oOrg = new OrganizationModel();
		if ($oOrg->Load($orgId) == -1)
		    return;
		    
		// Get products for this organization
		$oViewProduct = new ProductSqlQueryHelper();
		$oViewProduct->AddDef('columnhdrs', '', array(STR_CMMN_ID, STR_CMMN_NAME));
		$oViewProduct->AddDef('columns', '', array('id', 'name'));
		$oViewProduct->AddDef('order', '', array('name'));
		$oViewProduct->AddDef('filter', 'dcl_org_product_xref.org_id', $orgId);

		$aProducts = array();
		$aProductsNames = array();
		
		$oProducts = new ProductModel();
		if ($oProducts->Query($oViewProduct->GetSQL()) != -1)
		{
			while ($oProducts->next_record())
			{
				$aProducts[] = $oProducts->f(0);
				$aProductsNames[] = $oProducts->f(1);
			}

			$oProducts->FreeResult();
		}

		$oSmarty = new SmartyHelper();

		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY, $oOrg->org_id))
			throw new PermissionDeniedException();

		$oSmarty->assign('TXT_TITLE', 'Edit Organization Products');
		$oSmarty->assign('VAL_MENUACTION', 'OrganizationProduct.Update');
		$oSmarty->assign('VAL_ORGID', $oOrg->org_id);
		$oSmarty->assign('URL_BACK', menuLink('', 'menuAction=htmlOrgDetail.show&org_id=' . $oOrg->org_id));
		$oSmarty->assign('VAL_ORGNAME', $oOrg->name);

		$oSmarty->assign_by_ref('VAL_PRODUCTID', $aProducts);
		$oSmarty->assign_by_ref('VAL_PRODUCTNAME', $aProductsNames);

		$oSmarty->Render('htmlOrgProducts.tpl');
	}
}
