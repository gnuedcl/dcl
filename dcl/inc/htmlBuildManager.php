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
LoadStringResource('wo');

class htmlBuildManager 
{
	function GetCombo($default = 0, $cbName = 'product_version_descr', $longShort = 'product_version_descr', $validate=0, $productid,$size = 0, $activeOnly = true, $minsec = 0)
	{
		$objDBRDate = new dbProductVersion();
		$objDBRDate->Connect();

		$whereClause = '';
		if ($validate == 0 )
		{

		}
		else
		{
			$whereClause = 'where product_id = ' . $productid . '';
		}
			$query = "SELECT * FROM dcl_product_version $whereClause ";
			
		//$query .= "ORDER BY $orderBy";
		$objDBRDate->Query($query);

		$oSelect = new htmlSelect();
		$oSelect->vDefault = $default;
		$oSelect->sName = $cbName;
		//$oSelect->sOnChange = '';
		$oSelect->iSize = $size;
		$oSelect->sZeroOption = STR_CMMN_SELECTONE;
		$oSelect->bCastToInt = true;

		while ($objDBRDate->next_record())
		{
			$objDBRDate->GetRow();

			$name = $objDBRDate->product_version_descr;

			$oSelect->AddOption($objDBRDate->product_version_id, $name);
		}
		return $oSelect->GetHTML();
	}
	
	function GetBuildCombo($default = 0, $cbName = 'product_build_descr', $longShort = 'product_build_descr', $validate=0, $versionid,$size = 0, $activeOnly = true, $minsec = 0)
	{
		$objDBRDate = new dbProductBuild();
		$objDBRDate->Connect();

		$whereClause = '';
		if ($validate == 0 )
		{

		}
		else
		{
			$whereClause = 'where product_version_id = ' . $versionid . '';
		}
			$query = "SELECT * FROM dcl_product_build $whereClause ";			

		//$query .= "ORDER BY $orderBy";
		$objDBRDate->Query($query);

		$oSelect = new htmlSelect();
		$oSelect->vDefault = $default;
		$oSelect->sName = $cbName;
		//$oSelect->sOnChange = '';
		$oSelect->iSize = $size;
		$oSelect->sZeroOption = STR_CMMN_SELECTONE;
		$oSelect->bCastToInt = true;

		while ($objDBRDate->next_record())
		{
			$objDBRDate->GetRow();
			$name = $objDBRDate->product_build_descr;
			$oSelect->AddOption($objDBRDate->product_build_id, $name);
		}

		return $oSelect->GetHTML();
	}
	
	function ShowAddReleasePage()
	{
		global $dcl_info;

		$oProduct = new ProductModel();
		$oProduct->Load($_REQUEST['product_id']);
		
		$oSmarty = new DCL_Smarty();

		$oSmarty->assign('menuAction', 'boBuildManager.addRelease');
		$oSmarty->assign('VAL_PRODUCTID', $oProduct->id);
		$oSmarty->assign('VAL_PRODUCTNAME', $oProduct->name);

		$oSmarty->Render('htmlBuildManagerRelease.tpl');
	}
	
	function ShowAddBuildPage()
	{
		global $dcl_info;

		$oDbProduct = new ProductModel();
		$oDbProduct->Load($_REQUEST['product_id']);
		
		$oVersion = new dbProductVersion();
		$oVersion->Load(array('product_version_id' => $_REQUEST['product_version_id']));
		
		$oSmarty = new DCL_Smarty();
		
		$oSmarty->assign('VAL_PRODUCTID', $_REQUEST['product_id']);
		$oSmarty->assign('VAL_VERSIONID', $_REQUEST['product_version_id']);
		$oSmarty->assign('VAL_PRODUCT', $oDbProduct->name);
		$oSmarty->assign('VAL_VERSION', $oVersion->product_version_text);
		$oSmarty->assign('VAL_WHICH', $_REQUEST['which']);
		
		$oSmarty->Render('htmlBuildManagerBuild.tpl');
	}
	
	function ModifyReleaseInfo()
	{
		commonHeader();
		global $dcl_info, $versionid, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_BUILDMANAGER, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();
			
		includeCalendar();			
		$Template = CreateTemplate(array('hForm' => 'htmlBuildManagerRelease.tpl'));

		$oProduct = new ProductModel();
		$oProduct->Connect();
		$oProduct->Load($_REQUEST['product_id']);
		
		$Template->set_var('COLOR_LIGHT', $dcl_info['DCL_COLOR_LIGHT']);
		$Template->set_var('VAL_FORMACTION', menuLink());
		$Template->set_var('VAL_JSDATEFORMAT', GetJSDateFormat());
		$Template->set_var('VAL_MENU', 'boBuildManager.modifySumbit');
		
		$oPV = new dbProductVersion();
		if ($oPV->Load(array('product_version_id' => $versionid)) == -1)
		{
			ShowError('Failed to load version ID ' . $versionid, 'Error');
			return;
		}

		$myDate = $oPV->product_version_target_date;
		
		$Template->set_var('TXT_BM_ADD_RELEASE', STR_BM_MOD_RELEASE);
		$Template->set_var('TXT_BM_PRODUCT', STR_BM_PRODUCT);
		$Template->set_var('TXT_BM_RELEASE_ALIAS_TITLE', STR_BM_RELEASE_ALIAS_TITLE);
		$Template->set_var('TXT_BM_RELEASE_ALIAS', '<input type="text" name="ReleaseAlias" size=10 value='. $oPV->product_version_text . '>');
		
		$Template->set_var('VAL_PRODUCTNAME', $oProduct->name);
		$Template->set_var('H_VERSIONID', $versionid);
		$Template->set_var('H_PRODUCTID', $oProduct->id);
		$Template->set_var('H_TARGETDATE', $oPV->product_version_target_date);
		$Template->set_var('VAL_VERSIONTEXT', $oPV->product_version_text);
		$Template->set_var('VAL_VERSIONDESCR', $oPV->product_version_descr);
		$Template->set_var('VAL_DATE', 'product_version_actual_date');
		$Template->set_var('VAL_VERSIONTARGETDATE', $oPV->product_version_target_date);
		$Template->set_var('TXT_BM_RELEASEDATE_DESC', STR_BM_RELEASEDATE_DESC);
		$Template->set_var('TXT_BM_RELEASEDATE', STR_BM_RELEASEDATE);
		$Template->set_var('TXT_BM_RELEASEDATEFORM', '<input type="text" name="ReleaseDesc" size=50 value="'. $oPV->product_version_descr .'">');
		$Template->set_var('date', $oPV->product_version_target_date);
		$Template->set_var('VAL_WHICH', $_REQUEST['which']);
		$Template->set_var('H_PRODUCTID', $oPV->product_id);
		
		
		$Template->set_var('BTN_SUBMIT', STR_BM_SUBMIT);
		$Template->pparse('out', 'hForm');
		
	}
	
	function ModifyBuildInfo()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_BUILDMANAGER, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();
			
		if (($product_id = @DCL_Sanitize::ToInt($_REQUEST['product_id'])) === null ||
			($product_version_id = @DCL_Sanitize::ToInt($_REQUEST['product_version_id'])) === null ||
			($buildid = @DCL_Sanitize::ToInt($_REQUEST['buildid'])) === null)
			{
				throw new InvalidDataException();
			}

		$oDbProduct = new ProductModel();
		$oDbProduct->Load($product_id);
		
		$oVersion = new dbProductVersion();
		$oVersion->Load(array('product_version_id' => $product_version_id));

		$oPB = new dbProductBuild();
		if ($oPB->Load(array('product_build_id' => $buildid)) == -1)
		{
			ShowError('Failed to load build ID ' . $buildid, 'Error');
			return;
		}
		
		$oSmarty = new DCL_Smarty();
		
		$oSmarty->assign('VAL_PRODUCTID', $product_id);
		$oSmarty->assign('VAL_VERSIONID', $product_version_id);
		$oSmarty->assign('VAL_PRODUCT', $oDbProduct->name);
		$oSmarty->assign('VAL_VERSION', $oVersion->product_version_text);
		$oSmarty->assign('VAL_WHICH', $_REQUEST['which']);
		$oSmarty->assign('VAL_BM_BUILDNAME', $oPB->product_build_descr);
		
		$oSmarty->Render('htmlBuildManagerBuild.tpl');
	}		
	
	function ShowWOByBuild()
	{
		commonHeader();

		$oBuild = new dbProductBuild();
		$oBuild->Load(array('product_build_id' => $_REQUEST['product_build_id']));
		
		$oVersion = new dbProductVersion();
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

		$t = new DCL_Smarty();

		$init = @DCL_Sanitize::ToInt($_REQUEST['init']);
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
			
			$oPV = new dbProductVersion();
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
		
	function SubmitSelectedWO($selected)
	{
		commonHeader();
		
		global $dcl_info;
		
		$Template = CreateTemplate(array('hForm' => 'htmlBuildManagerEntry.tpl'));
		$Template->set_var('COLOR_LIGHT', $dcl_info['DCL_COLOR_LIGHT']);
		$Template->set_var('VAL_FORMACTION', menuLink());
		
		$Template->set_var('TXT_BM_ADD_RELEASE', STR_BM_ADDBUILD_TITLE);
		$Template->set_var('TXT_BM_VERSIONNAME', STR_BM_VERSIONNAME);
		$Template->set_var('TXT_BM_BUILDNAME', STR_BM_BUILDNAME);
		
		$Template->set_var('CMB_PRODUCT', $this->GetCombo($_REQUEST['DCLID'], 'project', 'name'));
		
		$Template->set_var('BTN_SUBMIT', STR_BM_SUBMIT);
		$Template->pparse('out', 'hForm');
		
		$objWO = new dbWorkorders();
		$objWO->Connect();
		
		while (list($key, $jcnseq) = each($selected))
		{
			list($jcn, $seq) = explode('.', $jcnseq);
		
			$sql = "SELECT jcn,seq,status,summary FROM workorders where jcn=$jcn and seq=$seq";
		
			$objWO->Query($sql);		
			
			while ($objWO->next_record())
			{	
				echo $objWO->f(3);
				echo ("<BR>");
			}
		}
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
