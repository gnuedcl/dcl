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
class htmlBuildManagerVersionView
{
	var $productid;
	
	function htmlBuildManagerVersionView()
	{
		$this->productid = 0;
	}
	
	function Render($oView)
	{
		global $dcl_info, $g_oSec, $g_oSession;

		commonHeader();

		if (!$g_oSec->HasPerm(DCL_ENTITY_BUILDMANAGER, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$oDB = new dbBuildManager();
		if ($oDB->query($oView->GetSQL()) == -1)
			return;
			
		$allRecs = $oDB->FetchAllRows();

		$oTable = new htmlTable();
		$oTable->setCaption($oView->title);
		$oTable->addColumn(STR_CMMN_ID, 'numeric');
		$oTable->addColumn(STR_BM_RELEASE_ALIAS_TITLE, 'string');
		$oTable->addColumn(STR_CMMN_ACTIVE, 'string');
		$oTable->addColumn(STR_BM_RELEASEDATE_DESC, 'string');
		$oTable->addColumn('Target Date', 'date');
		$oTable->addColumn('Actual Date', 'date');
		
		if (IsSet($this->ModNav) && ($this->ModNav == 'WO' || $this->ModNav == 'showfiles'))
			$oTable->addToolbar(menuLink('', 'menuAction=Product.DetailRelease&id=' . $this->id), 'Back');
		else
			$oTable->addToolbar(menuLink('', 'menuAction=boBuildManager.add&which=release&product_id=' . $this->productid . '&add=1'), STR_CMMN_NEW);

		if (count($allRecs) > 0 && $g_oSec->HasPerm(DCL_ENTITY_GLOBAL, DCL_ENTITY_ADMIN))
		{
			$oTable->addColumn(STR_CMMN_OPTIONS, 'html');
			for ($i = 0; $i < count($allRecs); $i++)
			{
				$options = '';
				if (IsSet($this->ModNav))
				{
					$options = '<a href="' . menuLink('', 'menuAction=boWorkorders.viewjcn&jcn=' . $allRecs[$i][1] . '&seq=' . $allRecs[$i][2]) . '">' . STR_CMMN_VIEW . '</a>';
				}
				else
				{
					$versionid = $allRecs[$i][0];
					
					$options = '<a href="' . menuLink('', 'menuAction=Product.DetailBuild&product_version_id=' . $versionid . '&product_id=' . $this->productid) . '">' . STR_CMMN_VIEW . '</a>';
					$options .= '&nbsp;|&nbsp;<a href="' . menuLink('', 'menuAction=htmlBuildManagerReleaseForm.Show&product_version_id=' . $versionid . '&product_id=' . $this->productid . '&which=release') . '">' . STR_CMMN_EDIT . '</a>';
					$options .= '&nbsp;|&nbsp;<a href="' . menuLink('', 'menuAction=boBuildManager.ShowWorkOrders&product_version_id=' . $versionid . '&product_id=' . $this->productid . '&from=version') . '">' . STR_CMMN_SHOWVERSION . '</a>';
					$options .= '&nbsp;|&nbsp;<a href="' . menuLink('', 'menuAction=boBuildManager.ShowFiles&product_version_id=' . $versionid . '&product_id=' . $this->productid . '&from=version') . '">' . STR_CMMN_SHOWFILES . '</a>';
				}

				$allRecs[$i][6] = $options;
			}
		}
		
		$oTable->setData($allRecs);
		$oTable->setShowRownum(true);
		$oTable->render();
	}
}
