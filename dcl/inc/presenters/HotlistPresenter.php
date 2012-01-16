<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2012 Free Software Foundation
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

class HotlistPresenter
{
	public function Cloud(HotlistModel $model)
	{
		commonHeader();
		
		$allRecs = $model->FetchAllRows();

		$oTable = new TableHtmlHelper();
		$oTable->setCaption('Popular Hotlists');
		$oTable->addColumn(STR_CMMN_TAGS, 'html');
		$oTable->addColumn('Count', 'numeric');
		
		for ($i = 0; $i < count($allRecs); $i++)
		{
			$allRecs[$i][0] = '<a class="dcl-hotlist" href="' . menuLink('', 'menuAction=Hotlist.Browse&tag=' . urlencode($allRecs[$i][0])) . '">' . htmlspecialchars($allRecs[$i][0]) . '</a>';
		}

		$oTable->setData($allRecs);
		$oTable->setShowRownum(true);
		$oTable->render();
	}
	
	public function BrowseByTag(HotlistModel $model)
	{
		commonHeader();

		$allRecs = $model->FetchAllRows();

		$oTable = new TableHtmlHelper();
		$oTable->setCaption('Browsing Hotlists');
		$oTable->addColumn(STR_CMMN_ID, 'numeric');
		$oTable->addColumn(STR_CMMN_NAME, 'string');
		$oTable->addColumn(STR_WO_PROJECT, 'string');
		$oTable->addColumn(STR_WO_STATUS, 'string');
		$oTable->addColumn(STR_WO_RESPONSIBLE, 'string');
		$oTable->addColumn('Last Time Card By', 'string');
		$oTable->addColumn('Last Time Card Summary', 'string');
		$oTable->addColumn('Priority', 'numeric');
		$oTable->addColumn('Hotlists', 'string');

		$oHotlistDB = new EntityHotlistModel();
		for ($i = 0; $i < count($allRecs); $i++)
		{
			$sHotlists = $oHotlistDB->getTagsForEntity($allRecs[$i][0], $allRecs[$i][1], $allRecs[$i][2]);
			$allRecs[$i][11] = $sHotlists;
		}

		$oTable->setData($allRecs);
		$oTable->setShowRownum(true);

		if (!isset($_REQUEST['includeClosed']) || $_REQUEST['includeClosed'] == 'Y')
		{
			$oTable->assign('VAL_INCLUDECLOSED', 'Y');
		}
		else
		{
			$oTable->assign('VAL_INCLUDECLOSED', 'N');
		}
		
		$oTable->assign('VAL_SELECTEDTAGS', $_REQUEST['tag']);
		$oTable->sTemplate = 'TableHotlistBrowse.tpl';
		$oTable->render();
	}
	
	public function Prioritize(HotlistModel $hotlistModel, EntityHotlistModel $entityHotlistModel)
	{
		commonHeader();

		$smartyHelper = new SmartyHelper();
		$items = $entityHotlistModel->FetchAllRows();
		$smartyHelper->assign_by_ref('items', $items);
		$smartyHelper->assign('VAL_HOTLIST_ID', $hotlistModel->hotlist_id);
		$smartyHelper->assign('VAL_HOTLIST_NAME', $hotlistModel->hotlist_tag);
		$smartyHelper->Render('HotlistPrioritize.tpl');
	}
}
