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

class htmlHotlists
{
	function htmlHotlists()
	{
		
	}
	
	function browse()
	{
		if (isset($_REQUEST['tag']) && trim($_REQUEST['tag'] != ''))
		{
			$this->browseByTag();
			return;
		}
		
		$this->cloud();
	}
	
	function cloud()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH) && !$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_SEARCH))
		{
			throw new PermissionDeniedException();
		}
		
		$oDB = new dbHotlist();
		$oDB->listByPopular();
		
		$allRecs = $oDB->FetchAllRows();

		$oTable = new TableHtmlHelper();
		$oTable->setCaption('Popular Hotlists');
		$oTable->addColumn(STR_CMMN_TAGS, 'html');
		$oTable->addColumn('Count', 'numeric');
		
		for ($i = 0; $i < count($allRecs); $i++)
		{
			$allRecs[$i][0] = '<a class="dcl-hotlist" href="' . menuLink('', 'menuAction=htmlHotlists.browse&tag=' . urlencode($allRecs[$i][0])) . '">' . htmlspecialchars($allRecs[$i][0]) . '</a>';
		}

		$oTable->setData($allRecs);
		$oTable->setShowRownum(true);
		$oTable->render();
	}
	
	function browseByTag()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH) && !$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_SEARCH))
		{
			throw new PermissionDeniedException();
		}
		
		if (!isset($_REQUEST['tag']) || trim($_REQUEST['tag']) == '')
			return $this->cloud();
			
		$oDB = new EntityHotlistModel();
		$oDB->listByTag($_REQUEST['tag']);
		
		$allRecs = $oDB->FetchAllRows();

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
			$allRecs[$i][] = $sHotlists;
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
		$oTable->sTemplate = 'htmlTableHotlistBrowse.tpl';
		$oTable->render();
	}
	
	function prioritize()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_HOTLIST, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();
			
		$hotlistId = @DCL_Sanitize::ToInt($_REQUEST['hotlist_id']);
		if ($hotlistId === null || $hotlistId < 1)
			throw new PermissionDeniedException();
			
		$dbHotlist = new dbHotlist();
		if ($dbHotlist->Load($hotlistId) === -1)
			throw new PermissionDeniedException();
			
		$db = new EntityHotlistModel();
		$rs = $db->listById($hotlistId);
		if ($rs === -1)
		{
			ShowInfo('No items found in hot list.', __FILE__, __LINE__, null);
			return -1;
		}

		$t = new DCL_Smarty();
		$items = $db->FetchAllRows();
		$t->assign_by_ref('items', $items);
		$t->assign('VAL_HOTLIST_ID', $hotlistId);
		$t->assign('VAL_HOTLIST_NAME', $dbHotlist->hotlist_tag);
		$t->Render('htmlHotlistPrioritize.tpl');
	}
	
	function savePriority()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_HOTLIST, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();
			
		$hotlistId = @DCL_Sanitize::ToInt($_POST['hotlist_id']);
		if ($hotlistId === null || $hotlistId < 1)
			throw new PermissionDeniedException();
			
		$dbHotlist = new dbHotlist();
		if ($dbHotlist->Load($hotlistId) === -1)
			throw new PermissionDeniedException();

		$aEntities = array();
		foreach ($_REQUEST['item'] as $entity)
		{
			$aEntity = @DCL_Sanitize::ToIntArray(split('_', $entity));
			if (count($aEntity) === 3)
				$aEntities[] = $aEntity;
		}
			
		$db = new EntityHotlistModel();
		$db->setPriority($hotlistId, $aEntities);
	}
}
