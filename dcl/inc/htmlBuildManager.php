<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2011 Free Software Foundation
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
LoadStringResource('wo');

class htmlBuildManager 
{
	function GetCombo($default = 0, $cbName = 'product_version_descr', $longShort = 'product_version_descr', $validate = 0, $productid, $size = 0, $activeOnly = true, $minsec = 0)
	{
		$productVersionModel = new ProductVersionModel();

		$whereClause = '';
		if ($validate != 0 )
		{
			$whereClause = 'where product_id = ' . $productid . '';
		}
		
		$query = "SELECT * FROM dcl_product_version $whereClause ";
			
		$productVersionModel->Query($query);

		$oSelect = new SelectHtmlHelper();
		$oSelect->DefaultValue = $default;
		$oSelect->Id = $cbName;
		$oSelect->Size = $size;
		$oSelect->FirstOption = STR_CMMN_SELECTONE;
		$oSelect->CastToInt = true;

		while ($productVersionModel->next_record())
		{
			$productVersionModel->GetRow();

			$oSelect->AddOption($productVersionModel->product_version_id, $productVersionModel->product_version_descr);
		}
		
		return $oSelect->GetHTML();
	}
	
	function GetBuildCombo($default = 0, $cbName = 'product_build_descr', $longShort = 'product_build_descr', $validate = 0, $versionid, $size = 0, $activeOnly = true, $minsec = 0)
	{
		$productBuildModel = new ProductBuildModel();

		$whereClause = '';
		if ($validate != 0 )
		{
			$whereClause = 'where product_version_id = ' . $versionid . '';
		}
		
		$query = "SELECT * FROM dcl_product_build $whereClause ";			

		$productBuildModel->Query($query);

		$oSelect = new SelectHtmlHelper();
		$oSelect->DefaultValue = $default;
		$oSelect->Id = $cbName;
		$oSelect->Size = $size;
		$oSelect->FirstOption = STR_CMMN_SELECTONE;
		$oSelect->CastToInt = true;

		while ($productBuildModel->next_record())
		{
			$productBuildModel->GetRow();
			$name = $productBuildModel->product_build_descr;
			$oSelect->AddOption($productBuildModel->product_build_id, $name);
		}

		return $oSelect->GetHTML();
	}
	
	function ShowAddReleasePage()
	{
		$productId = @Filter::RequireInt($_REQUEST['product_id']);

		$productModel = new ProductModel();
		$productModel->Load($productId);
		
		$oSmarty = new SmartyHelper();

		$oSmarty->assign('menuAction', 'boBuildManager.addRelease');
		$oSmarty->assign('VAL_PRODUCTID', $productModel->id);
		$oSmarty->assign('VAL_PRODUCTNAME', $productModel->name);

		$oSmarty->Render('htmlBuildManagerRelease.tpl');
	}
	
	function ShowAddBuildPage()
	{
		global $dcl_info;
		$productId = @Filter::RequireInt($_REQUEST['product_id']);
		$productVersionId = @Filter::RequireInt($_REQUEST['product_version_id']);

		$productModel = new ProductModel();
		$productModel->Load($productId);
		
		$productVersionModel = new ProductVersionModel();
		$productVersionModel->Load(array('product_version_id' => $productVersionId));
		
		$oSmarty = new SmartyHelper();
		
		$oSmarty->assign('VAL_PRODUCTID', $productId);
		$oSmarty->assign('VAL_VERSIONID', $productVersionId);
		$oSmarty->assign('VAL_PRODUCT', $productModel->name);
		$oSmarty->assign('VAL_VERSION', $productVersionModel->product_version_text);
		$oSmarty->assign('VAL_WHICH', $_REQUEST['which']);
		
		$oSmarty->Render('htmlBuildManagerBuild.tpl');
	}
	
	function ModifyBuildInfo()
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_BUILDMANAGER, DCL_PERM_MODIFY);
		
		$productId = @Filter::RequireInt($_REQUEST['product_id']);
		$productVersionId = @Filter::RequireInt($_REQUEST['product_version_id']);
		$buildId = @Filter::RequireInt($_REQUEST['buildid']);
			
		$productModel = new ProductModel();
		$productModel->Load($productId);
		
		$productVersionModel = new ProductVersionModel();
		$productVersionModel->Load(array('product_version_id' => $productVersionId));

		$oPB = new ProductBuildModel();
		if ($oPB->Load(array('product_build_id' => $buildId)) == -1)
		{
			ShowError('Failed to load build ID ' . $buildId, 'Error');
			return;
		}
		
		$oSmarty = new SmartyHelper();
		
		$oSmarty->assign('VAL_PRODUCTID', $productId);
		$oSmarty->assign('VAL_VERSIONID', $productVersionId);
		$oSmarty->assign('VAL_PRODUCT', $productModel->name);
		$oSmarty->assign('VAL_VERSION', $productVersionModel->product_version_text);
		$oSmarty->assign('VAL_WHICH', $_REQUEST['which']);
		$oSmarty->assign('VAL_BM_BUILDNAME', $oPB->product_build_descr);
		
		$oSmarty->Render('htmlBuildManagerBuild.tpl');
	}		
	
	function ShowWOByBuild()
	{
		commonHeader();
		
		$productBuildId = @Filter::RequireInt($_REQUEST['product_build_id']);

		$oBuild = new ProductBuildModel();
		$oBuild->Load(array('product_build_id' => $productBuildId));
		
		$oVersion = new ProductVersionModel();
		$oVersion->Load(array('product_version_id' => $oBuild->product_version_id));
		
		$obj = new htmlProductDetail();
		$obj->Show($oVersion->product_id, 'build', $oBuild->product_version_id);

		$objView = new boView();
		$objView->title = sprintf(STR_PROD_WOBUILDINFO, $oBuild->product_build_descr);
		$objView->table = 'dcl_product_build_item';
		
		$objView->AddDef('columns', '', array('workorders.jcn', 'workorders.seq', 'workorders.summary', 'statuses.name'));
		$objView->AddDef('columnhdrs', '', array(STR_WO_JCN, STR_WO_SEQ, STR_WO_SUMMARY, STR_WO_STATUS));
		$objView->AddDef('filter', 'dcl_product_build.product_build_id', $oBuild->product_build_id);

		$objHV = new htmlBuildManagerShowWOView();
		$objHV->buildid = $oBuild->product_build_id;
		$objHV->versionid = $oBuild->product_version_id;
		$objHV->productid = $oVersion->product_id;
		$objHV->bCount = true;
		$objHV->Render($objView);
	}
	
	function AddWOForm($productid, $versionid = 0, $init=0, $statusid=0)
	{
		commonHeader();
		
		global $dcl_info, $init, $g_oSession;	

		$t = new SmartyHelper();

		$init = @Filter::ToInt($_REQUEST['init']);
		if ($init != 1)
		{  
			$t->assign('VAL_INIT', 1);
			$t->assign('VAL_PRODUCTID', $productid);
			$t->assign('TXT_TITLE', STR_BM_ADDRELEASE_TITLE);
			$t->assign('CMB_RELEASE', $this->GetCombo(0, 'product_version_id', 'product_version_descr', 1, $productid));
			
			$t->assign('VAL_MENUACTION', 'boBuildManager.SubmitWO');
		}
		else
		{
			$g_oSession->Register('releaseid', $versionid);
			$g_oSession->Edit();			
			
			$oPV = new ProductVersionModel();
			if ($oPV->Load(array('product_version_id' => $versionid)) == -1)
			{
				ShowError('Failed to load version ID ' . $versionid, 'Error');
				return;
			}

			$t->assign('TXT_TITLE', STR_BM_ADDBUILD_TITLE);
			$t->assign('TXT_BM_STATUSID', $statusid);			
			$t->assign('CMB_RELEASE', $oPV->product_version_text);
			$t->assign('CMB_BUILD', $this->GetBuildCombo(0, 'product_build_id', 'product_build_descr', 1, $versionid));
			
			$t->assign('VAL_MENUACTION', 'boBuildManager.InsertBM');
		}
		
		$t->Render('htmlBuildManagerForm.tpl');
	}
	
	
	function ShowBatchWO()		
	{
		global $dcl_info, $g_oSession;

		$obj = new htmlTimeCards();
		$_REQUEST['selected'] =& $g_oSession->Value('BMselected');
		$obj->ShowBatchWO();
	}
		
	function ShowErrorReport()
	{
		global $dcl_info, $g_oSession;
		
		$obj = new htmlViews();
		$obj->name = "Build Manager Error";		
	
		$objView = new boView();
		$reportname = "BuildManager Error Report";
		$objView->title = sprintf(STR_PROD_WOTITLE, $obj->name);
		$objView->style = 'report';
		$objView->table = 'dcl_product_build_except';
		
		$objView->AddDef('columns', '', array('workorders.jcn', 'workorders.seq',  'statuses.name', 'priorities.name', 'responsible.short', 'workorders.summary'));
		
		$objView->AddDef('columnhdrs', '', array(
				STR_WO_JCN,
				STR_WO_SEQ,
				STR_WO_STATUS,
				STR_WO_PRIORITY,
				STR_WO_RESPONSIBLE,
				STR_WO_DEADLINE,
				STR_WO_SUMMARY));
				
		$objView->AddDef('filter', 'session_id', "'" . $g_oSession->dcl_session_id . "'");
		$objView->AddDef('filter', 'product_build_id', $g_oSession->Value('buildid'));

		$objHV = new htmlView();
		$objHV->Render($objView);
	}
}
