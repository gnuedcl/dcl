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
class htmlBuildManagerBuildView
{
	var $productid;
	var $product_version_id;
	
	function htmlBuildManagerBuildView()
	{
		$this->productid = 0;
		$this->product_version_id = 0;
	}
	
	function Render($oView)
	{
		global $dcl_info, $g_oSec, $g_oSession;

		commonHeader();

		if (!$g_oSec->HasPerm(DCL_ENTITY_BUILDMANAGER, DCL_PERM_VIEW))
			return PrintPermissionDenied();

		$oDB = new dbBuildManager();
		if ($oDB->query($oView->GetSQL()) == -1)
			return;
			
		$allRecs = $oDB->FetchAllRows();

		$oTable = new htmlTable();
		$oTable->addColumn(STR_CMMN_ID, 'numeric');
		$oTable->addColumn('Version ID', 'numeric');
		$oTable->addColumn(STR_BM_RELEASEDATE_DESC, 'string');
		
		$oMeta = new DCL_MetadataDisplay();
		
		$oProductVersion = new dbProductVersion();
		$oProductVersion->Load(array('product_version_id' => $this->product_version_id));

		$oTable->setCaption($oView->title . ': ' . $oProductVersion->product_version_text);
		
		$oTable->addToolbar(menuLink('', 'menuAction=boBuildManager.add&which=build&product_id=' . $this->productid . '&product_version_id=' . $this->product_version_id), STR_CMMN_NEW);
		$oTable->addToolbar(menuLink('', 'menuAction=boProducts.viewRelease&id=' . $this->productid), $oMeta->GetProduct($this->productid));
		$oTable->addToolbar(menuLink('', 'menuAction=boProducts.viewBuild&product_version_id=' . $this->product_version_id . '&product_id=' . $this->productid), STR_CMMN_REFRESH);

		if (count($allRecs) > 0 && $g_oSec->HasPerm(DCL_ENTITY_GLOBAL, DCL_ENTITY_ADMIN))
		{
			$oTable->addColumn(STR_CMMN_OPTIONS, 'html');
			for ($i = 0; $i < count($allRecs); $i++)
			{
				$buildid = $allRecs[$i][0];
				$versionid = $allRecs[$i][1];
				
				$options = '<a href="' . menuLink('', 'menuAction=htmlBuildManager.ShowWOByBuild&product_build_id=' . $buildid . '&product_id=' . $this->productid) . '">' . STR_CMMN_VIEW . '</a>';
				$options .= '&nbsp;|&nbsp;<a href="' . menuLink('', 'menuAction=htmlBuildManager.ModifyBuildInfo&buildid=' . $buildid . '&product_id=' . $this->productid . '&product_version_id=' . $versionid . '&which=build' ) . '">' . STR_CMMN_EDIT . '</a>';

				$allRecs[$i][3] = $options;
			}
		}
		
		$oTable->setData($allRecs);
		$oTable->setShowRownum(true);
		$oTable->render();
	}	
}
