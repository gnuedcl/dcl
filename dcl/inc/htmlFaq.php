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

LoadStringResource('faq');

class htmlFaq
{
	function DisplayForm($obj = '')
	{
		global $dcl_info, $g_oSec;

		$isEdit = is_object($obj);
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQ, $isEdit ? DCL_PERM_MODIFY : DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$oSmarty = new DCL_Smarty();

		$oSmarty->assign('IS_EDIT', $isEdit);

		if ($isEdit)
		{
			$oSmarty->assign('VAL_MENUACTION', 'boFaq.dbmodify');
			$oSmarty->assign('VAL_NAME', $obj->name);
			$oSmarty->assign('VAL_DESCRIPTION', $obj->description);
			$oSmarty->assign('VAL_FAQID', $obj->faqid);
			$oSmarty->assign('VAL_ACTIVE', $obj->active);
		}
		else
		{
			$oSmarty->assign('VAL_MENUACTION', 'boFaq.dbadd');
			$oSmarty->assign('VAL_ACTIVE', 'Y');
		}

		$oSmarty->Render('htmlFaqForm.tpl');
	}

	function ExecuteSearch($searchFld, $searchText) {}

	function ShowAll($orderBy = 'name')
	{
		global $dcl_info, $g_oSec;
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$obj = new dbFaq();
		$query = "SELECT faqid,active,name,createby,createon,modifyby,modifyon FROM faq ORDER BY $orderBy";
		$obj->Query($query);
		$allRecs = $obj->FetchAllRows();

		$oTable = new TableHtmlHelper();
		$oTable->setCaption(sprintf(STR_FAQ_ORDEREDBY, $orderBy));
		$oTable->addColumn(STR_FAQ_ID, 'numeric');
		$oTable->addColumn(STR_FAQ_ACCT, 'string');
		$oTable->addColumn(STR_FAQ_NAME, 'string');
		$oTable->addColumn(STR_FAQ_CREATEDBY, 'string');
		$oTable->addColumn(STR_FAQ_CREATEDON, 'string');
		$oTable->addColumn(STR_FAQ_MODIFIEDBY, 'string');
		$oTable->addColumn(STR_FAQ_MODIFIEDON, 'string');

		if ($g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_ADD))
			$oTable->addToolbar(menuLink('', 'menuAction=boFaq.add'), STR_CMMN_NEW);

		if (count($allRecs) > 0)
		{
			$objP = new PersonnelModel();
			$oTable->addColumn(STR_FAQ_OPTIONS, 'html');
			for ($i = 0; $i < count($allRecs); $i++)
			{
				if ($allRecs[$i][3] > 0 && $objP->Load($allRecs[$i][3]) != -1)
					$allRecs[$i][3] = $objP->f('short');

				if ($allRecs[$i][5] > 0 && $objP->Load($allRecs[$i][5]) != -1)
					$allRecs[$i][5] = $objP->f('short');

				$allRecs[$i][4] = $obj->FormatTimestampForDisplay($allRecs[$i][4]);
				$allRecs[$i][6] = $obj->FormatTimestampForDisplay($allRecs[$i][6]);

				$options = '<a href="' . menuLink('', 'menuAction=boFaq.view&faqid=' . $allRecs[$i][0]) . '">' . STR_CMMN_VIEW . '</a>';
				if ($g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_MODIFY))
					$options .= '&nbsp;|&nbsp;<a href="' . menuLink('', 'menuAction=boFaq.modify&faqid=' . $allRecs[$i][0]) . '">' . STR_CMMN_EDIT . '</a>';
				if ($g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_DELETE))
					$options .= '&nbsp;|&nbsp;<a href="' . menuLink('', 'menuAction=boFaq.delete&faqid=' . $allRecs[$i][0]) . '">' . STR_CMMN_DELETE . '</a>';

				$allRecs[$i][] = $options;
			}
		}

		$oTable->setData($allRecs);
		$oTable->setShowRownum(true);
		$oTable->render();
	}

	function ShowFaq($obj)
	{
		global $dcl_info, $g_oSec;

		if (!is_object($obj))
		{
			trigger_error(STR_FAQ_NOOBJECT);
			return;
		}

		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_VIEW, $obj->faqid))
			throw new PermissionDeniedException();

		$oSmarty = new DCL_Smarty();
		$oSmarty->assign('VAL_NAME', $obj->name);
		$oSmarty->assign('VAL_DESCRIPTION', $obj->description);
		$oSmarty->assign('PERM_ADDTOPIC', $g_oSec->HasPerm(DCL_ENTITY_FAQTOPIC, DCL_PERM_ADD, $obj->faqid));
		$oSmarty->assign('VAL_FAQID', $obj->faqid);
		$oSmarty->assign('PERM_MODIFY', $g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_MODIFY));
		$oSmarty->assign('PERM_DELETE', $g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_DELETE));

		$objF = new dbFaqtopics();
		$objF->LoadByFaqID($obj->faqid);
		$oSmarty->assign_by_ref('VAL_TOPICS', $objF->ResultToArray());

		$oSmarty->Render('htmlFaqDetail.tpl');
	}
}
