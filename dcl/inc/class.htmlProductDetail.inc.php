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

class htmlProductDetail
{
	var $t;
	var $id;
	var $sView;
	var $iVersion;
	var $oProduct;

	function htmlProductDetail()
	{
		$this->t = CreateSmarty();

		$this->id = 0;
		$this->sView = 'summary';
		$this->iVersion = 0;

		$this->oProduct = new dbProducts();
	}

	function Show($id, $which = 'summary', $version = 0)
	{
		global $dcl_info, $g_oSec;

		$id = (int)$id;
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_VIEW, $id))
			return PrintPermissionDenied();

		$this->id = $id;
		$this->sView = $which;
		$this->iVersion = $version;

		if ($this->oProduct->Load($id) == -1)
		    return;

		$objPers = new dbPersonnel();

		$this->t->assign('VAL_ID', $id);
		$this->t->assign('VAL_NAME', $this->oProduct->name);
		$this->t->assign('VAL_ACTIVE', $this->oProduct->active);
		$this->t->assign('VAL_PUBLIC', $this->oProduct->is_public);
		$this->t->assign('VAL_ISPROJECTREQUIRED', $this->oProduct->is_project_required);
		
		$objPers->Load($this->oProduct->reportto);
		$this->t->assign('VAL_REPORTTO', $objPers->short);
		
		$objPers->Load($this->oProduct->ticketsto);
		$this->t->assign('VAL_TICKETSTO', $objPers->short);
		
		$this->t->assign('PERM_VIEWWO', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEW));
		$this->t->assign('PERM_VIEWTCK', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_VIEW));
		$this->t->assign('PERM_WIKI', $dcl_info['DCL_WIKI_ENABLED'] == 'Y' && $g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_VIEWWIKI));
		$this->t->assign('PERM_EDIT', $g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_MODIFY));
		$this->t->assign('PERM_DELETE', $g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_DELETE));
		$this->t->assign('PERM_VERSIONS', $dcl_info['DCL_BUILD_MANAGER_ENABLED'] == 'Y' && $this->oProduct->is_versioned == 'Y');
		
		SmartyDisplay($this->t, 'htmlProductsDetail.tpl');

		if ($this->sView == 'summary')
		{
			$oMetrics = new htmlMetricsWorkOrders();
			$_REQUEST['products'] = $id;
			$_REQUEST['begindate'] = date($dcl_info['DCL_DATE_FORMAT'], time() - (86400 * 7));
			$_REQUEST['enddate'] = date($dcl_info['DCL_DATE_FORMAT']);
			$oMetrics->showAll();
		}
		
		$this->_ShowProductItem();
	}

	function _ShowProductItem()
	{
		$id = $this->id;

		if ($this->sView == 'modules')
		{
			$oModules = new htmlProductModules();
			$_REQUEST['product_id'] = $id;
			$oModules->PrintAll();
		}
		else if ($this->sView != 'summary')
		{
			// This shows the non-closed work orders/tickets grouped by status
			$objView = new boView();
			if ($this->sView == 'workorders')
			{
				$objView->title = sprintf(STR_PROD_WOTITLE, $this->oProduct->name);
				$objView->style = 'report';
				$objView->table = 'workorders';
				$objView->AddDef('columns', '', array('jcn', 'seq', 'priorities.name', 'severities.name', 'responsible.short', 'deadlineon', 'summary'));
				$objView->AddDef('columnhdrs', '', array(
						STR_WO_STATUS,
						STR_WO_JCN,
						STR_WO_SEQ,
						STR_WO_PRIORITY,
						STR_WO_SEVERITY,
						STR_WO_RESPONSIBLE,
						STR_WO_DEADLINE,
						STR_WO_SUMMARY));

				$objView->AddDef('filter', 'product', $id);
				$objView->AddDef('filternot', 'statuses.dcl_status_type', '2');
				$objView->AddDef('order', '', array('jcn', 'seq'));
				$objView->AddDef('groups', '', array('statuses.name'));

				$objHV = CreateViewObject($objView->table);
			}
			elseif ($this->sView == 'release')
			{
				$objView->title = sprintf(STR_PROD_RELEASEINFO, $this->oProduct->name);
				$objView->style = 'report';
				$objView->table = 'dcl_product_version';
				$objView->AddDef('columns', '', array('product_version_id', 'product_version_text', 'active', 'product_version_descr', 'product_version_target_date', 'product_version_actual_date'));
				$objView->AddDef('columnhdrs', '', array(STR_CMMN_ID, 'Version', STR_CMMN_ACTIVE, 'Version Description', 'Target Date', 'Actual Date'));

				$objView->AddDef('filter', 'product_id', $id);
				$objView->AddDef('order', '', array('product_version_target_date desc'));

				$objHV = new htmlBuildManagerVersionView();
				$objHV->productid = $id;
			}
			elseif ($this->sView == 'build')
			{
				$objView->title = sprintf(STR_PROD_BUILDINFO, $this->oProduct->name);
				$objView->style = 'report';
				$objView->table = 'dcl_product_version';

				$objView->AddDef('columns', '', array('dcl_product_build.product_build_id','dcl_product_build.product_version_id','dcl_product_build.product_build_descr'));
				$objView->AddDef('columnhdrs', '', array(STR_CMMN_ID, 'Version ID', 'Build Description'));

				$objView->AddDef('filter', 'dcl_product_build.product_version_id', $this->iVersion);
				//$objView->AddDef('order', '', array('product_version_target_date'));

				$objHV = new htmlBuildManagerBuildView();
				$objHV->productid = $id;
				$objHV->product_version_id = $this->iVersion;
			}
			else
			{
				$objView->title = sprintf(STR_PROD_TICKETTITLE, $this->oProduct->name);
				$objView->style = 'report';
				$objView->table = 'tickets';
				$objView->AddDef('columns', '', array('ticketid', 'priorities.name', 'severities.name', 'responsible.short', 'summary'));
				$objView->AddDef('columnhdrs', '', array(
						STR_TCK_STATUS,
						STR_TCK_TICKET . '#',
						STR_TCK_PRIORITY,
						STR_TCK_TYPE,
						STR_TCK_RESPONSIBLE,
						STR_TCK_SUMMARY));

				$objView->AddDef('filter', 'product', $id);
				$objView->AddDef('filternot', 'statuses.dcl_status_type', '2');
				$objView->AddDef('order', '', array('ticketid'));
				$objView->AddDef('groups', '', array('statuses.name'));

				$objHV = CreateViewObject($objView->table);
			}

			$objHV->Render($objView);
		}
	}
}
