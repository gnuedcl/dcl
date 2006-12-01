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

		$this->oProduct = CreateObject('dcl.dbProducts');
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

		$objPers = CreateObject('dcl.dbPersonnel');

		$this->t->assign('VAL_ID', $id);
		$this->t->assign('VAL_NAME', $this->oProduct->name);
		$this->t->assign('VAL_ACTIVE', $this->oProduct->active);
		$this->t->assign('VAL_PUBLIC', $this->oProduct->is_public);
		
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
		
		//$this->_ShowStatusSummary();
		//$this->_ShowActivitySummary();

		SmartyDisplay($this->t, 'htmlProductsDetail.tpl');

		if ($this->sView == 'summary')
		{
			$oMetrics = CreateObject('dcl.htmlMetricsWorkOrders');
			$_REQUEST['products'] = $id;
			$_REQUEST['begindate'] = date($dcl_info['DCL_DATE_FORMAT'], time() - (86400 * 7));
			$_REQUEST['enddate'] = date($dcl_info['DCL_DATE_FORMAT']);
			$oMetrics->showAll();
		}
		
		$this->_ShowProductItem();
	}

	function _ShowStatusSummary()
	{
		if ($this->sView != 'summary')
			return;

		$id = $this->oProduct->id;

		if ($this->sView == 'workorders')
			$this->oProduct->Query("SELECT S.name,count(*),sum(etchours),sum(totalhours) FROM workorders W, statuses S WHERE W.status = S.id AND W.product=$id GROUP BY S.name");
		else
			$this->oProduct->Query("SELECT S.name,count(*) FROM tickets T, statuses S WHERE T.status = S.id AND T.product=$id GROUP BY S.name");

		if ($this->oProduct->next_record())
		{
			do
			{
				$this->t->set_var('VAL_STATUSNAME', $this->oProduct->f(0));
				$this->t->set_var('VAL_STATUSCOUNT', $this->oProduct->f(1));

				if ($this->sView == 'workorders')
				{
					$objStat =& CreateObject('dcl.dbStatuses');
					if ($objStat->id == 2)
						$this->t->set_var('VAL_STATUSHOURS', sprintf('(%0.2fh)', $this->oProduct->f(3)));
					else
						$this->t->set_var('VAL_STATUSHOURS', sprintf('(%0.2fh)', $this->oProduct->f(2)));
				}
				else
					$this->t->set_var('VAL_STATUSHOURS', '');

				$this->t->parse('hStatuses', 'statuses', true);
			}
			while ($this->oProduct->next_record());
		}
		else
		{
			if ($this->sView == 'workorders')
				$this->t->set_var('TXT_NOITEMS', STR_PROD_NOWORKORDERS);
			else
				$this->t->set_var('TXT_NOITEMS', STR_PROD_NOTICKETS);

			$this->t->parse('hNoStatuses', 'nostatuses', true);
		}
	}

	function _ShowActivitySummary()
	{
		$dt = new DCLTimestamp;
		$dtToday = new DCLTimestamp;
		$dtToday->time = time();

		$dt->time = $dtToday->time - (86400 * 7);

		$id = $this->id;
		$version = $this->iVersion;
		$table = $this->sView;
		if ($table == 'modules')
			$table = 'workorders';

		if ($table == 'release')
		{
			$this->t->set_var('TXT_ACTIVITY', STR_PROD_RELEASEDETAILS);
			$this->t->set_var('TXT_ACTIVITYTYPE', STR_PROD_READY_BUILD);
			$this->oProduct->Query("SELECT count(*) FROM dcl_product_version_item VI, dcl_product_version V WHERE VI.product_version_id = V.product_version_id AND VI.version_status_id = 1 AND V.product_id=$id");
			$this->oProduct->next_record();
			$this->t->set_var('VAL_ACTIVITYCOUNT', $this->oProduct->f(0));

			$this->t->parse('hActivity', 'activity', true);
		}
		elseif ($table == 'build')
		{
			$this->t->set_var('TXT_ACTIVITY', STR_PROD_RELEASEDETAILS);

			$oVersion = CreateObject('dcl.dbProductVersion');
			$oVersion->Load(array('product_version_id' => $this->iVersion));
			$this->t->set_var('TXT_ACTIVITYTYPE', $oVersion->product_version_text);
			$this->t->set_var('VAL_ACTIVITYCOUNT', $oVersion->product_version_descr);
			$this->t->parse('hActivity', 'activity', true);

			$this->oProduct->Query(sprintf("SELECT Count(*) FROM dcl_product_version PV, dcl_product_build PB WHERE PV.product_version_id = PB.product_version_id AND PV.product_version_id = $version"));
			$this->oProduct->next_record();
			$this->t->set_var('TXT_ACTIVITYTYPE', STR_PROD_DCL_APPLIED);
			$this->t->set_var('VAL_ACTIVITYCOUNT', $this->oProduct->f(0));
			$this->t->parse('hActivity', 'activity', true);

			/*
			$this->oProduct->Query(sprintf("SELECT count(*) FROM dcl_build_manager BM, dcl_build_manager_xref BMX WHERE BM.dclno = BMX.jcn AND BM.version_id = $version AND BMX.ready_for_build = 1"));
			$this->oProduct->next_record();
			$this->t->set_var('TXT_ACTIVITYTYPE', STR_PROD_DCL_MODIFIED);
			$this->t->set_var('VAL_ACTIVITYCOUNT', $this->oProduct->f(0));
			$this->t->parse('hActivity', 'activity', true);

			$this->oProduct->Query(sprintf("SELECT count(*) FROM dcl_build_manager BM RIGHT OUTER JOIN dcl_build_manager_xref BMX ON BM.dclno = BMX.jcn WHERE BM.dclno IS NULL"));
			$this->oProduct->next_record();
			$this->t->set_var('TXT_ACTIVITYTYPE', STR_PROD_DCL_NEW);
			$this->t->set_var('VAL_ACTIVITYCOUNT', $this->oProduct->f(0));
			$this->t->parse('hActivity', 'activity', true);
			*/
			$this->t->set_var('TXT_ACTIVITYTYPE', 'FIXME: htmlProductDetail show by status');
			$this->t->set_var('VAL_ACTIVITYCOUNT', 0);
			$this->t->parse('hActivity', 'activity', true);
		}
		else if ($table != 'summary')
		{
			$this->oProduct->Query(sprintf("SELECT count(*) FROM $table a, statuses b WHERE a.product=$id AND a.status=b.id AND (b.dcl_status_type!=2 or (b.dcl_status_type=2 AND a.closedon between '%s' and '%s')) AND a.createdon<'%s'", $dt->ToDB(), $dtToday->ToDB(), $dt->ToDB()));
			$this->oProduct->next_record();
			$this->t->set_var('TXT_ACTIVITYTYPE', STR_PROD_PREEXISTING);
			$this->t->set_var('VAL_ACTIVITYCOUNT', $this->oProduct->f(0));
			$this->t->parse('hActivity', 'activity', true);

			$this->oProduct->Query(sprintf("SELECT count(*) FROM $table WHERE product=$id AND createdon between '%s' and '%s'", $dt->ToDB(), $dtToday->ToDB()));
			$this->oProduct->next_record();
			$this->t->set_var('TXT_ACTIVITYTYPE', STR_PROD_CREATED);
			$this->t->set_var('VAL_ACTIVITYCOUNT', $this->oProduct->f(0));
			$this->t->parse('hActivity', 'activity', true);

			$this->oProduct->Query(sprintf("SELECT count(*) FROM $table a, statuses b WHERE product=$id AND a.status=b.id AND b.dcl_status_type=2 AND closedon between '%s' and '%s'", $dt->ToDB(), $dtToday->ToDB()));
			$this->oProduct->next_record();
			$this->t->set_var('TXT_ACTIVITYTYPE', STR_PROD_CLOSED);
			$this->t->set_var('VAL_ACTIVITYCOUNT', $this->oProduct->f(0));
			$this->t->parse('hActivity', 'activity', true);

			$this->oProduct->Query("SELECT count(*) FROM $table a, statuses b WHERE product=$id AND a.status=b.id AND b.dcl_status_type!=2");
			$this->oProduct->next_record();
			$this->t->set_var('TXT_ACTIVITYTYPE', STR_PROD_OUTSTANDING);
			$this->t->set_var('VAL_ACTIVITYCOUNT', $this->oProduct->f(0));
			$this->t->parse('hActivity', 'activity', true);
		}
	}

	function _ShowProductItem()
	{
		$id = $this->id;

		if ($this->sView == 'modules')
		{
			$oModules = CreateObject('dcl.htmlProductModules');
			$_REQUEST['product_id'] = $id;
			$oModules->PrintAll();
		}
		else if ($this->sView != 'summary')
		{
			// This shows the non-closed work orders/tickets grouped by status
			$objView = CreateObject('dcl.boView');
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
				$objView->AddDef('filternot', 'status', '2');
				$objView->AddDef('order', '', array('jcn', 'seq'));
				$objView->AddDef('groups', '', array('statuses.name'));

				$objHV = CreateViewObject($objView->table);
			}
			elseif ($this->sView == 'release')
			{
				$objView->title = sprintf(STR_PROD_RELEASEINFO, $this->oProduct->name);
				$objView->style = 'report';
				$objView->table = 'dcl_product_version';
				$objView->AddDef('columns', '', array('product_version_id', 'product_version_text', 'product_version_descr', 'product_version_target_date', 'product_version_actual_date'));
				$objView->AddDef('columnhdrs', '', array('ID','Alias','Version Description','Target Date','Actual Date'));

				$objView->AddDef('filter', 'product_id', $id);
				$objView->AddDef('order', '', array('product_version_target_date'));

				$objHV = CreateObject('dcl.htmlBuildManagerVersionView');
				$objHV->productid = $id;
			}
			elseif ($this->sView == 'build')
			{
				$objView->title = sprintf(STR_PROD_BUILDINFO, $this->oProduct->name);
				$objView->style = 'report';
				$objView->table = 'dcl_product_version';

				$objView->AddDef('columns', '', array('dcl_product_build.product_build_id','dcl_product_build.product_version_id','dcl_product_build.product_build_descr'));
				$objView->AddDef('columnhdrs', '', array('ID','Version ID','Build Description'));

				$objView->AddDef('filter', 'dcl_product_build.product_version_id', $this->iVersion);
				//$objView->AddDef('order', '', array('product_version_target_date'));

				$objHV = CreateObject('dcl.htmlBuildManagerBuildView');
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
?>
