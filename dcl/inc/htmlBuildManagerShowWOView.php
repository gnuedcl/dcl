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
 
LoadStringResource('wo');
class htmlBuildManagerShowWOView
{
	var $productid;
	var $versionid;
	var $buildid;
	
	function htmlBuildManagerShowWOView()
	{
		$this->sColumnTitle = STR_CMMN_OPTIONS;
		$this->productid = 0;
		$this->versionid = 0;
		$this->buildid = 0;
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
		$oTable->addColumn(STR_WO_JCN, 'html');
		$oTable->addColumn(STR_WO_SEQ, 'html');
		$oTable->addColumn(STR_WO_SUMMARY, 'string');
		$oTable->addColumn(STR_WO_STATUS, 'string');
		
		$oTable->setCaption($oView->title);
		
		if (!$this->buildid)
			$menuAction = 'menuAction=boproducts.viewRelease&id=' . $this->productid;
		else
			$menuAction = 'menuAction=boProducts.viewBuild&product_version_id=' . $this->versionid . '&product_id=' . $this->productid;		
		
		$oTable->addToolbar(menuLink('', $menuAction), 'Back');

		if (count($allRecs) > 0 && $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEW))
		{
			for ($i = 0; $i < count($allRecs); $i++)
			{
			    $jcn = $allRecs[$i][0];
			    $seq = $allRecs[$i][1];
			    
				$allRecs[$i][0] = '<a href="' . menuLink('', 'menuAction=boWorkorders.viewjcn&jcn=' . $jcn . '&seq=' . $seq) . '">' . $jcn . '</a>';
				$allRecs[$i][1] = '<a href="' . menuLink('', 'menuAction=boWorkorders.viewjcn&jcn=' . $jcn . '&seq=' . $seq) . '">' . $seq . '</a>';
			}
		}
		
		$oTable->setData($allRecs);
		$oTable->setShowRownum(true);
		$oTable->render();
	}	
}
