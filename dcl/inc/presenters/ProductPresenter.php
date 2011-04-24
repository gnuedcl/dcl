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

class ProductPresenter
{
	public function Index()
	{
		global $g_oSec, $g_oSession;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_VIEW))
			throw new PermissionDeniedException();
			
		$filterLead = @DCL_Sanitize::ToInt($_REQUEST['filterLead']);

		$objDBProduct = new ProductModel();

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

		$query .= " ORDER BY a.name";
		$objDBProduct->Query($query);
		$allRecs = $objDBProduct->FetchAllRows();

		$oTable = new TableHtmlHelper();
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
			$oTable->addToolbar(menuLink('', 'menuAction=Product.Create'), STR_CMMN_NEW);
			
		$oTable->addToolbar(menuLink('', 'menuAction=htmlProductDashboard.ShowAll'), 'Dashboard');
			
		if ($g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW))
			$oTable->addToolbar(menuLink('', 'menuAction=SystemSetup.Index'), DCL_MENU_SYSTEMSETUP);

		for ($i = 0; $i < count($allRecs); $i++)
		{
			$allRecs[$i][1] = $allRecs[$i][1] == 'Y' ? STR_CMMN_YES : STR_CMMN_NO;
			$allRecs[$i][8] = $allRecs[$i][8] == 'Y' ? STR_CMMN_YES : STR_CMMN_NO;
			$allRecs[$i][9] = $allRecs[$i][9] == 'Y' ? STR_CMMN_YES : STR_CMMN_NO;
			$allRecs[$i][10] = $allRecs[$i][10] == 'Y' ? STR_CMMN_YES : STR_CMMN_NO;
			
			$options = '<a href="' . menuLink('', 'menuAction=Product.Detail&id=' . $allRecs[$i][0]) . '">' . STR_CMMN_VIEW . '</a>';

			if ($g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_VIEWWIKI))
				$options .= '&nbsp;|&nbsp;<a href="' . menuLink('', 'menuAction=htmlWiki.show&name=FrontPage&type=4&id=' . $allRecs[$i][0]) . '">' . STR_CMMN_WIKI . '</a>';

			$allRecs[$i][] = $options;
		}

		$oTable->setData($allRecs);
		$oTable->setShowRownum(true);
		$oTable->render();
	}

	public function Create()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$t = new DCL_Smarty();
		$t->assign('TXT_FUNCTION', STR_PROD_ADD);
		$t->assign('menuAction', 'Product.Insert');

		$objHTMLPersonnel = new PersonnelHtmlHelper();
		$objHA = new AttributeSetHtmlHelper();

		$t->assign('CMB_ACTIVE', GetYesNoCombo('Y', 'active', 0, false));
		$t->assign('CMB_ISVERSIONED', GetYesNoCombo('Y', 'is_versioned', 0, false));
		$t->assign('CMB_ISPROJECTREQUIRED', GetYesNoCombo('N', 'is_project_required', 0, false));
		$t->assign('CMB_ISPUBLIC', GetYesNoCombo('N', 'is_public', 0, false));
		$t->assign('CMB_REPORTTO', $objHTMLPersonnel->Select($GLOBALS['DCLID'], 'reportto'));
		$t->assign('CMB_TICKETSTO', $objHTMLPersonnel->Select($GLOBALS['DCLID'], 'ticketsto'));
		$t->assign('CMB_WOATTRIBUTESET', $objHA->Select(0, 'wosetid'));
		$t->assign('CMB_TCKATTRIBUTESET', $objHA->Select(0, 'tcksetid'));

		$t->Render('htmlProductsForm.tpl');
	}

	public function Edit(ProductModel $model)
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_MODIFY, $model->id))
			throw new PermissionDeniedException();

		$t = new DCL_Smarty();

		$t->assign('TXT_FUNCTION', STR_PROD_EDIT);
		$t->assign('menuAction', 'Product.Update');
		$t->assign('id', $model->id);

		// Data
		$objHTMLPersonnel = new PersonnelHtmlHelper();
		$objHA = new AttributeSetHtmlHelper();
		
		$t->assign('CMB_ACTIVE', GetYesNoCombo($model->active, 'active', 0, false));
		$t->assign('CMB_ISVERSIONED', GetYesNoCombo($model->is_versioned, 'is_versioned', 0, false));
		$t->assign('CMB_ISPROJECTREQUIRED', GetYesNoCombo($model->is_project_required, 'is_project_required', 0, false));
		$t->assign('CMB_ISPUBLIC', GetYesNoCombo($model->is_public, 'is_public', 0, false));
		$t->assign('VAL_SHORT', $model->short);
		$t->assign('VAL_NAME', $model->name);
		$t->assign('CMB_REPORTTO', $objHTMLPersonnel->Select($model->reportto, 'reportto'));
		$t->assign('CMB_TICKETSTO', $objHTMLPersonnel->Select($model->ticketsto, 'ticketsto'));
		$t->assign('CMB_WOATTRIBUTESET', $objHA->Select($model->wosetid, 'wosetid'));
		$t->assign('CMB_TCKATTRIBUTESET', $objHA->Select($model->tcksetid, 'tcksetid'));

		$t->Render('htmlProductsForm.tpl');
	}

	public function Delete(ProductModel $model)
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_DELETE, $model->id))
			throw new PermissionDeniedException();

		ShowDeleteYesNo('Product', 'Product.Destroy', $model->id, $model->name);
	}

	public function Detail(ProductModel $model)
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_VIEW, $model->id))
			throw new PermissionDeniedException();

		$t = new DCL_Smarty();
		$personnelModel = new PersonnelModel();

		$t->assign('VAL_ID', $model->id);
		$t->assign('VAL_NAME', $model->name);
		$t->assign('VAL_ACTIVE', $model->active);
		$t->assign('VAL_PUBLIC', $model->is_public);
		$t->assign('VAL_ISPROJECTREQUIRED', $model->is_project_required);

		$personnelModel->Load($model->reportto);
		$t->assign('VAL_REPORTTO', $personnelModel->short);

		$personnelModel->Load($model->ticketsto);
		$t->assign('VAL_TICKETSTO', $personnelModel->short);

		$t->assign('PERM_VIEWWO', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEW));
		$t->assign('PERM_VIEWTCK', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_VIEW));
		$t->assign('PERM_WIKI', $dcl_info['DCL_WIKI_ENABLED'] == 'Y' && $g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_VIEWWIKI));
		$t->assign('PERM_EDIT', $g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_MODIFY));
		$t->assign('PERM_DELETE', $g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_DELETE));
		$t->assign('PERM_VERSIONS', $dcl_info['DCL_BUILD_MANAGER_ENABLED'] == 'Y' && $model->is_versioned == 'Y');

		$t->Render('htmlProductsDetail.tpl');
	}

	public function DetailWorkOrderMetrics(ProductModel $model)
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_VIEW, $model->id))
			throw new PermissionDeniedException();

		$oMetrics = new htmlMetricsWorkOrders();
		$_REQUEST['products'] = $model->id;
		$_REQUEST['begindate'] = date($dcl_info['DCL_DATE_FORMAT'], time() - (86400 * 7));
		$_REQUEST['enddate'] = date($dcl_info['DCL_DATE_FORMAT']);
		$oMetrics->showAll();
	}

	public function DetailWorkOrder(ProductModel $model)
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_VIEW, $model->id))
			throw new PermissionDeniedException();

		$sqlHelper = new WorkOrderSqlQueryHelper();
		$sqlHelper->title = sprintf(STR_PROD_WOTITLE, $model->name);
		$sqlHelper->style = 'report';
		$sqlHelper->AddDef('columns', '', array('jcn', 'seq', 'priorities.name', 'severities.name', 'responsible.short', 'deadlineon', 'summary'));
		$sqlHelper->AddDef('columnhdrs', '', array(
				STR_WO_STATUS,
				STR_WO_JCN,
				STR_WO_SEQ,
				STR_WO_PRIORITY,
				STR_WO_SEVERITY,
				STR_WO_RESPONSIBLE,
				STR_WO_DEADLINE,
				STR_WO_SUMMARY));

		$sqlHelper->AddDef('filter', 'product', $model->id);
		$sqlHelper->AddDef('filternot', 'statuses.dcl_status_type', '2');
		$sqlHelper->AddDef('order', '', array('jcn', 'seq'));
		$sqlHelper->AddDef('groups', '', array('statuses.name'));

		$objHV = new htmlWorkOrderResults();
		$objHV->Render($sqlHelper);
	}

	public function DetailTicket(ProductModel $model)
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_VIEW, $model->id))
			throw new PermissionDeniedException();

		$sqlHelper = new TicketSqlQueryHelper();
		$sqlHelper->title = sprintf(STR_PROD_TICKETTITLE, $model->name);
		$sqlHelper->style = 'report';
		$sqlHelper->AddDef('columns', '', array('ticketid', 'priorities.name', 'severities.name', 'responsible.short', 'summary'));
		$sqlHelper->AddDef('columnhdrs', '', array(
				STR_TCK_STATUS,
				STR_TCK_TICKET . '#',
				STR_TCK_PRIORITY,
				STR_TCK_TYPE,
				STR_TCK_RESPONSIBLE,
				STR_TCK_SUMMARY));

		$sqlHelper->AddDef('filter', 'product', $model->id);
		$sqlHelper->AddDef('filternot', 'statuses.dcl_status_type', '2');
		$sqlHelper->AddDef('order', '', array('ticketid'));
		$sqlHelper->AddDef('groups', '', array('statuses.name'));

		$objHV = new htmlTicketResults();
		$objHV->Render($sqlHelper);
	}

	public function DetailModule(ProductModel $model)
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_VIEW, $model->id))
			throw new PermissionDeniedException();

		$oModules = new htmlProductModules();
		$_REQUEST['product_id'] = $model->id;
		$oModules->PrintAll();
	}

	public function DetailRelease(ProductModel $model)
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_VIEW, $model->id))
			throw new PermissionDeniedException();

		$sqlHelper = new ProductVersionSqlQueryHelper();
		$sqlHelper->title = sprintf(STR_PROD_RELEASEINFO, $model->name);
		$sqlHelper->style = 'report';
		$sqlHelper->AddDef('columns', '', array('product_version_id', 'product_version_text', 'active', 'product_version_descr', 'product_version_target_date', 'product_version_actual_date'));
		$sqlHelper->AddDef('columnhdrs', '', array(STR_CMMN_ID, 'Version', STR_CMMN_ACTIVE, 'Version Description', 'Target Date', 'Actual Date'));

		$sqlHelper->AddDef('filter', 'product_id', $model->id);
		$sqlHelper->AddDef('order', '', array('product_version_target_date desc'));

		$objHV = new htmlBuildManagerVersionView();
		$objHV->productid = $model->id;
		$objHV->Render($sqlHelper);
	}

	public function DetailBuild(ProductModel $model, ProductBuildModel $productBuildModel)
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_VIEW, $model->id))
			throw new PermissionDeniedException();

		$sqlHelper = new ProductVersionSqlQueryHelper();
		$sqlHelper->title = sprintf(STR_PROD_BUILDINFO, $model->name);
		$sqlHelper->style = 'report';

		$sqlHelper->AddDef('columns', '', array('dcl_product_build.product_build_id','dcl_product_build.product_version_id','dcl_product_build.product_build_descr'));
		$sqlHelper->AddDef('columnhdrs', '', array(STR_CMMN_ID, 'Version ID', 'Build Description'));

		$sqlHelper->AddDef('filter', 'dcl_product_build.product_version_id', $productBuildModel->product_version_id);
		//$objView->AddDef('order', '', array('product_version_target_date'));

		$objHV = new htmlBuildManagerBuildView();
		$objHV->productid = $model->id;
		$objHV->product_version_id = $productBuildModel->product_version_id;
		$objHV->Render($sqlHelper);
	}
}
