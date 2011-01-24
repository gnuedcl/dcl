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

LoadStringResource('faq');

class FaqPresenter
{
	public function Index()
	{
		global $dcl_info, $g_oSec;
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$obj = new FaqModel();
		$query = "SELECT faqid,active,name,createby,createon,modifyby,modifyon FROM faq ORDER BY name";
		$obj->Query($query);
		$allRecs = $obj->FetchAllRows();

		$oTable = new TableHtmlHelper();
		$oTable->setCaption(STR_FAQ_ORDEREDBY);
		$oTable->addColumn(STR_FAQ_ID, 'numeric');
		$oTable->addColumn(STR_FAQ_ACCT, 'string');
		$oTable->addColumn(STR_FAQ_NAME, 'string');
		$oTable->addColumn(STR_FAQ_CREATEDBY, 'string');
		$oTable->addColumn(STR_FAQ_CREATEDON, 'string');
		$oTable->addColumn(STR_FAQ_MODIFIEDBY, 'string');
		$oTable->addColumn(STR_FAQ_MODIFIEDON, 'string');

		if ($g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_ADD))
			$oTable->addToolbar(menuLink('', 'menuAction=Faq.Create'), STR_CMMN_NEW);

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

				$options = '<a href="' . menuLink('', 'menuAction=Faq.Detail&faqid=' . $allRecs[$i][0]) . '">' . STR_CMMN_VIEW . '</a>';
				if ($g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_MODIFY))
					$options .= '&nbsp;|&nbsp;<a href="' . menuLink('', 'menuAction=Faq.Edit&faqid=' . $allRecs[$i][0]) . '">' . STR_CMMN_EDIT . '</a>';
				if ($g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_DELETE))
					$options .= '&nbsp;|&nbsp;<a href="' . menuLink('', 'menuAction=Faq.Delete&faqid=' . $allRecs[$i][0]) . '">' . STR_CMMN_DELETE . '</a>';

				$allRecs[$i][] = $options;
			}
		}

		$oTable->setData($allRecs);
		$oTable->setShowRownum(true);
		$oTable->render();
	}

	public function Create()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$oSmarty = new DCL_Smarty();

		$oSmarty->assign('IS_EDIT', false);
		$oSmarty->assign('VAL_MENUACTION', 'Faq.Insert');
		$oSmarty->assign('VAL_ACTIVE', 'Y');

		$oSmarty->Render('htmlFaqForm.tpl');
	}

	public function Edit(FaqModel $model)
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$oSmarty = new DCL_Smarty();

		$oSmarty->assign('IS_EDIT', true);
		$oSmarty->assign('VAL_MENUACTION', 'Faq.Update');
		$oSmarty->assign('VAL_NAME', $model->name);
		$oSmarty->assign('VAL_DESCRIPTION', $model->description);
		$oSmarty->assign('VAL_FAQID', $model->faqid);
		$oSmarty->assign('VAL_ACTIVE', $model->active);

		$oSmarty->Render('htmlFaqForm.tpl');
	}

	public function Delete(FaqModel $model)
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_DELETE, $model->faqid))
			throw new PermissionDeniedException();

		ShowDeleteYesNo(STR_FAQ_FAQ, 'Faq.Destroy', $model->faqid, $model->name, false, 'faqid');
	}

	public function Detail(FaqModel $model)
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_VIEW, $model->faqid))
			throw new PermissionDeniedException();

		$oSmarty = new DCL_Smarty();
		$oSmarty->assign('VAL_NAME', $model->name);
		$oSmarty->assign('VAL_DESCRIPTION', $model->description);
		$oSmarty->assign('PERM_ADDTOPIC', $g_oSec->HasPerm(DCL_ENTITY_FAQTOPIC, DCL_PERM_ADD, $model->faqid));
		$oSmarty->assign('VAL_FAQID', $model->faqid);
		$oSmarty->assign('PERM_MODIFY', $g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_MODIFY));
		$oSmarty->assign('PERM_DELETE', $g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_DELETE));

		$objF = new dbFaqtopics();
		$objF->LoadByFaqID($model->faqid);
		$oSmarty->assign_by_ref('VAL_TOPICS', $objF->ResultToArray());

		$oSmarty->Render('htmlFaqDetail.tpl');
	}
}
