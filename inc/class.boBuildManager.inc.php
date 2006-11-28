<?php
/*
 * $Id: class.boBuildManager.inc.php,v 1.1.1.1 2006/11/27 05:30:42 mdean Exp $
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

class boBuildManager
{
	function modifyReleaseInfo()
	{	
		/* 
		* Code is executed, when trying to modify existing release names or dates
		*/		
		commonHeader();
		$id = $GLOBALS['product_version_id'];
		
		$obj = CreateObject('dcl.dbBuildManager');
		$obj->Connect();
		
		$query = "SELECT * FROM dcl_product_version where product_version_id = $id";
		$obj->Query($query);
		//echo $query;
		$allRecs = $obj->FetchAllRows();
		
		$obj = CreateObject('dcl.htmlBuildManager');
		$obj->ModifyReleasePage($allRecs);
	}
	
	
	function add()
	{	
		// Determines if the user is trying to add a RELEASE or to add a BUILD
		commonHeader();
		$obj = CreateObject('dcl.htmlBuildManager');
		SWITCH ($GLOBALS['which'])
		{
			case "release":
				$obj->ShowAddReleasePage();
				break;
			case "build":
				$obj->ShowAddBuildPage();
				break;
		}
	}
	
	function addRelease()
	{
		global $product_id;
		
		commonHeader();
		
		$oDB = CreateObject('dcl.dbProductVersion');
		$oDB->InitFrom_POST();
		$oDB->Add();
		
		$obj = CreateObject('dcl.htmlProductDetail');
		$obj->Show($product_id, 'release');
	}
	
	function GetBuildInfoSubmit()
	{
		commonHeader();
		
		global $init;
		
		$oDB = CreateObject('dcl.dbProductBuild');
		$oDB->InitFrom_Post();
		if ($init == 0)
		{
			$oDB->objDate->time = time();
			$oDB->product_build_on = $oDB->objDate->ToDisplay();
			$oDB->Add();
		}
		else
			$oDB->Edit();

		$obj = CreateObject('dcl.htmlProductDetail');
		$obj->Show($_POST['product_id'], $_POST['which'], $_POST['product_version_id']);
	}
	
	function SubmitWO()
	{		
		global $dcl_info, $g_oSession, $product_version_id, $product;
		
		commonHeader();
			
		$obj = CreateObject('dcl.htmlBuildManager');
		if (IsSet($GLOBALS['selected']) && is_array($GLOBALS['selected']) && count($GLOBALS['selected']) > 0)
		{
			// Select a version to associate with
			$g_oSession->Register('BMselected', $GLOBALS['selected']);
			$g_oSession->Edit();
			
			$obj->AddWOForm($product);
		}
		else
		{
			if (!$g_oSession->IsRegistered('BMselected') || !is_array($g_oSession->Value('BMselected')) || count($g_oSession->Value('BMselected')) == 0)
			{
				trigger_error('Could not find selected items to add to version.', E_USER_ERROR);
				return;
			}
			
			// Add items to version
			$oVersionItem = CreateObject('dcl.dbProductVersionItem');
			$oVersionItem->product_version_id = $product_version_id;
			$oVersionItem->entity_type_id = DCL_ENTITY_WORKORDER;
			$oVersionItem->version_status_id = 1;
			$oVersionItem->version_item_submit_on = 'now()';
			
			$aSelected = $g_oSession->Value('BMselected');
			foreach ($aSelected as $woidseq)
			{
				list($woid, $seq) = explode('.', $woidseq);
				
				if ($oVersionItem->Exists(array('product_version_id' => $product_version_id, 'entity_type_id' => DCL_ENTITY_WORKORDER, 'entity_id' => $woid, 'entity_id2' => $seq)))
					continue;
					
				$oVersionItem->entity_id = $woid;
				$oVersionItem->entity_id2 = $seq;
				$oVersionItem->Add();
			}

			// Pick a build
			$obj->AddWOForm($product, $product_version_id, $GLOBALS['init']);
			$obj->ShowBatchWO();
		}
	}
	
	function InsertBM()
	{
		commonHeader();
		global $g_oSession;
		
		$selected = &$g_oSession->Value('BMselected');
		$env = $GLOBALS['env'];
		//Writing Buildid into session
		$buildid = $GLOBALS['product_build_id'];
		$g_oSession->Register('buildid', $buildid);
		$g_oSession->Register('env', $env);
		$g_oSession->Edit();
		
		$obj = CreateObject('dcl.dbBuildManager');
		$obj->Connect();
		$obj->CheckBM($selected);		
	}
}
?>
