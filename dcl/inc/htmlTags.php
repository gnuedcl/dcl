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

class htmlTags
{
	function htmlTags()
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
		
		$oDB = new dbTag();
		$oDB->listByPopular();
		
		$allRecs = $oDB->FetchAllRows();

		$oTable = new TableHtmlHelper();
		$oTable->setCaption('Popular Tags');
		$oTable->addColumn(STR_CMMN_TAGS, 'html');
		$oTable->addColumn('Count', 'numeric');
		
		for ($i = 0; $i < count($allRecs); $i++)
		{
			$allRecs[$i][0] = '<a class="dcl-tag" href="' . menuLink('', 'menuAction=htmlTags.browse&tag=' . urlencode($allRecs[$i][0])) . '">' . htmlspecialchars($allRecs[$i][0]) . '</a>';
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
			
		$oDB = new dbEntityTag();
		$oDB->listByTag($_REQUEST['tag']);
		
		$allRecs = $oDB->FetchAllRows();

		$oTable = new TableHtmlHelper();
		$oTable->setCaption('Browsing Tags');
		$oTable->addColumn(STR_CMMN_ID, 'numeric');
		$oTable->addColumn(STR_CMMN_NAME, 'string');
		$oTable->addColumn(STR_CMMN_TAGS, 'string');
		
		$oTagDB = new dbEntityTag();
		for ($i = 0; $i < count($allRecs); $i++)
		{
			$sTags = $oTagDB->getTagsForEntity($allRecs[$i][0], $allRecs[$i][1], $allRecs[$i][2]);
			$allRecs[$i][] = $sTags;
		}

		$oTable->setData($allRecs);
		$oTable->setShowRownum(true);
		
		$oTable->assign('VAL_SELECTEDTAGS', $_REQUEST['tag']);
		$oTable->sTemplate = 'htmlTableTag.tpl';
		$oTable->render();
	}
}
