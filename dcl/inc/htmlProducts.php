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

class htmlProducts
{
	function GetCombo($default = 0, $cbName = 'product', $longShort = 'name', $reportTo = 0, $size = 0, $activeOnly = true, $inputHandler = false)
	{
		global $g_oSec, $g_oSession;

		$objDBProducts = new dbProducts();
		$objDBProducts->cacheEnabled = false;
		$whereClause = '';
		
		if ($reportTo > 0 || $activeOnly == true)
		{
			$whereClause = ' WHERE ';
			if ($reportTo > 0)
				$whereClause = " reportto=$reportTo";

			if ($activeOnly == true)
			{
				if ($reportTo > 0)
					$whereClause .= ' AND';
				$whereClause .= ' active=\'Y\'';
			}
		}

		if ($g_oSec->IsPublicUser())
		{
			if ($whereClause != '')
				$whereClause .= ' AND';
			else
				$whereClause = ' WHERE';

			$whereClause .= " is_public = 'Y'";
		}

		if ($g_oSec->IsOrgUser() || $g_oSession->IsInWorkspace())
		{
			if ($whereClause != '')
				$whereClause .= ' AND';
			else
				$whereClause = ' WHERE';

			$whereClause .= ' id IN (' . join(',', $g_oSession->GetProductFilter()) . ')';
		}

		$objDBProducts->Query("SELECT id,$longShort FROM products " . $whereClause . " ORDER BY $longShort");

		$o = new htmlSelect();
		$o->vDefault = $default;
		$o->sName = $cbName;
		$o->iSize = $size;
		$o->sZeroOption = STR_CMMN_SELECTONE;
		$o->aOptions = $objDBProducts->FetchAllRows();
		$objDBProducts->FreeResult();
		if ($inputHandler)
			$o->sOnChange = 'productSelChange(this.form);';

		return $o->GetHTML();
	}

	function PrintAll($orderBy = 'name')
	{
		global $g_oSec, $g_oSession;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_VIEW))
			return PrintPermissionDenied();
			
		$filterLead = @DCL_Sanitize::ToInt($_REQUEST['filterLead']);

		$objDBProduct = new dbProducts();

		$query = 'SELECT a.id,a.active,a.short,a.name,b.short,c.short,d.name,e.name,a.is_versioned,a.is_public,a.is_project_required ';
		$query .= 'FROM products a,personnel b,personnel c,attributesets d,attributesets e ';
		$query .= 'WHERE a.reportto=b.id AND a.ticketsto=c.id ';
		$query .= 'AND a.wosetid=d.id AND a.tcksetid=e.id';
		if ($filterLead !== null)
			$query .= ' AND (a.reportto=' . $filterLead . ' OR a.ticketsto=' . $filterLead . ')';

		if ($g_oSec->IsPublicUser())
			$query .= " AND is_public = 'Y'";
			
		if ($g_oSec->IsOrgUser() || $g_oSession->IsInWorkspace())
			$query .= ' AND a.id IN (' . join(',', $g_oSession->GetProductFilter()) . ')';

		$query .= " ORDER BY a.$orderBy";
		$objDBProduct->Query($query);
		$allRecs = $objDBProduct->FetchAllRows();

		$oTable = new htmlTable();
		$oTable->setCaption('Products');
		$oTable->addColumn(STR_PROD_ID, 'numeric');
		$oTable->addColumn(STR_PROD_ACTIVEABB, 'string');
		$oTable->addColumn(STR_PROD_SHORT, 'string');
		$oTable->addColumn(STR_PROD_NAME, 'string');
		$oTable->addColumn(STR_PROD_REPORTTO, 'string');
		$oTable->addColumn(STR_PROD_TICKETSTO, 'string');
		$oTable->addColumn(STR_PROD_WOATTR, 'string');
		$oTable->addColumn(STR_PROD_TCKATTR, 'string');
		$oTable->addColumn(STR_PROD_VERSIONED, 'string');
		$oTable->addColumn(STR_CMMN_PUBLIC, 'string');
		$oTable->addColumn('Project Req', 'string');
		$oTable->addColumn(STR_CMMN_OPTIONS, 'html');

		if ($g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_ADD))
			$oTable->addToolbar(menuLink('', 'menuAction=htmlProducts.add'), STR_CMMN_NEW);
			
		$oTable->addToolbar(menuLink('', 'menuAction=htmlProductDashboard.ShowAll'), 'Dashboard');
			
		if ($g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW))
			$oTable->addToolbar(menuLink('', 'menuAction=boAdmin.ShowSystemConfig'), DCL_MENU_SYSTEMSETUP);

		for ($i = 0; $i < count($allRecs); $i++)
		{
			$allRecs[$i][1] = $allRecs[$i][1] == 'Y' ? STR_CMMN_YES : STR_CMMN_NO;
			$allRecs[$i][8] = $allRecs[$i][8] == 'Y' ? STR_CMMN_YES : STR_CMMN_NO;
			$allRecs[$i][9] = $allRecs[$i][9] == 'Y' ? STR_CMMN_YES : STR_CMMN_NO;
			$allRecs[$i][10] = $allRecs[$i][10] == 'Y' ? STR_CMMN_YES : STR_CMMN_NO;
			
			$options = '<a href="' . menuLink('', 'menuAction=boProducts.view&id=' . $allRecs[$i][0]) . '">' . STR_CMMN_VIEW . '</a>';

			if ($g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_VIEWWIKI))
				$options .= '&nbsp;|&nbsp;<a href="' . menuLink('', 'menuAction=htmlWiki.show&name=FrontPage&type=4&id=' . $allRecs[$i][0]) . '">' . STR_CMMN_WIKI . '</a>';

			$allRecs[$i][] = $options;
		}

		$oTable->setData($allRecs);
		$oTable->setShowRownum(true);
		$oTable->render();
	}

	function add()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_ADD))
			return PrintPermissionDenied();

		$this->ShowEntryForm();
	}

	function submitAdd()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_ADD))
			return PrintPermissionDenied();

		$oProduct = new boProducts();
		CleanArray($_REQUEST);
		$oProduct->add($_REQUEST);

		$this->PrintAll();
	}

	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		
		if (($id = DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_MODIFY, $id))
			return PrintPermissionDenied();

		$obj = new dbProducts();
		if ($obj->Load($id) == -1)
			return;

		$this->ShowEntryForm($obj);
	}

	function submitModify()
	{
		global $g_oSec;
		
		commonHeader();
		if (($id = DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_MODIFY, $id))
			return PrintPermissionDenied();

		$oProduct = new boProducts();
		CleanArray($_REQUEST);
		$oProduct->modify($_REQUEST);
		
		$obj = new htmlProductDetail();
		$obj->Show($id);
	}

	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (($id = DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_DELETE, $id))
			return PrintPermissionDenied();

		$obj = new dbProducts();
		if ($obj->Load($id) == -1)
			return;
			
		ShowDeleteYesNo('Product', 'htmlProducts.submitDelete', $obj->id, $obj->name);
	}

	function submitDelete()
	{
		global $g_oSec;
		
		commonHeader();
		if (($id = DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_DELETE, $id))
			return PrintPermissionDenied();

		$oProduct = new boProducts();
		$oProduct->delete($id);

		$this->PrintAll();
	}

	function ShowEntryForm($obj = '')
	{
		global $dcl_info, $g_oSec;

		commonHeader();

		$isEdit = is_object($obj);
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, $isEdit ? DCL_PERM_MODIFY : DCL_PERM_ADD, $isEdit ? (int)$obj->id : 0))
			return PrintPermissionDenied();

		$t = new DCL_Smarty();
				
		if ($isEdit)
		{
			$t->assign('TXT_FUNCTION', STR_PROD_EDIT);
			$t->assign('menuAction', 'htmlProducts.submitModify');
			$t->assign('id', $obj->id);
		}
		else
		{
			$t->assign('TXT_FUNCTION', STR_PROD_ADD);
			$t->assign('menuAction', 'htmlProducts.submitAdd');
		}

		// Data
		$objHTMLPersonnel = new htmlPersonnel();
		$objHA = new htmlAttributesets();
		if ($isEdit)
		{
			$t->assign('CMB_ACTIVE', GetYesNoCombo($obj->active, 'active', 0, false));
			$t->assign('CMB_ISVERSIONED', GetYesNoCombo($obj->is_versioned, 'is_versioned', 0, false));
			$t->assign('CMB_ISPROJECTREQUIRED', GetYesNoCombo($obj->is_project_required, 'is_project_required', 0, false));
			$t->assign('CMB_ISPUBLIC', GetYesNoCombo($obj->is_public, 'is_public', 0, false));
			$t->assign('VAL_SHORT', $obj->short);
			$t->assign('VAL_NAME', $obj->name);
			$t->assign('CMB_REPORTTO', $objHTMLPersonnel->GetCombo($obj->reportto, 'reportto'));
			$t->assign('CMB_TICKETSTO', $objHTMLPersonnel->GetCombo($obj->ticketsto, 'ticketsto'));
			$t->assign('CMB_WOATTRIBUTESET', $objHA->GetCombo($obj->wosetid, 'wosetid'));
			$t->assign('CMB_TCKATTRIBUTESET', $objHA->GetCombo($obj->tcksetid, 'tcksetid'));
		}
		else
		{
			$t->assign('CMB_ACTIVE', GetYesNoCombo('Y', 'active', 0, false));
			$t->assign('CMB_ISVERSIONED', GetYesNoCombo('Y', 'is_versioned', 0, false));
			$t->assign('CMB_ISPROJECTREQUIRED', GetYesNoCombo('N', 'is_project_required', 0, false));
			$t->assign('CMB_ISPUBLIC', GetYesNoCombo('N', 'is_public', 0, false));
			$t->assign('CMB_REPORTTO', $objHTMLPersonnel->GetCombo($GLOBALS['DCLID'], 'reportto'));
			$t->assign('CMB_TICKETSTO', $objHTMLPersonnel->GetCombo($GLOBALS['DCLID'], 'ticketsto'));
			$t->assign('CMB_WOATTRIBUTESET', $objHA->GetCombo(0, 'wosetid'));
			$t->assign('CMB_TCKATTRIBUTESET', $objHA->GetCombo(0, 'tcksetid'));
		}

		$t->Render('htmlProductsForm.tpl');
	}
}
