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

class FaqQuestionPresenter
{
	public function Create()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQQUESTION, DCL_PERM_ADD))
			throw new PermissionDeniedException();
			
		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('IS_EDIT', false);

		if (($topicId = Filter::ToInt($_REQUEST['topicid'])) === null)
		{
			throw new InvalidDataException();
		}

		$smartyHelper->assign('VAL_SEQ', '');
		$smartyHelper->assign('VAL_QUESTIONTEXT', '');
		$smartyHelper->assign('VAL_TOPICID', $topicId);

		$smartyHelper->Render('FaqquestionsForm.tpl');
	}

	public function Edit(FaqQuestionsModel $model)
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQQUESTION, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();
			
		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('IS_EDIT', true);

		$smartyHelper->assign('VAL_SEQ', $model->seq);
		$smartyHelper->assign('VAL_QUESTIONTEXT', $model->questiontext);
		$smartyHelper->assign('VAL_TOPICID', $model->topicid);
		$smartyHelper->assign('VAL_QUESTIONID', $model->questionid);

		$smartyHelper->Render('FaqquestionsForm.tpl');
	}
	
	public function Delete(FaqQuestionsModel $model)
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQQUESTION, DCL_PERM_DELETE))
			throw new PermissionDeniedException();
			
		ShowDeleteYesNo(STR_FAQ_QUESTION, 'FaqQuestion.Destroy', $questionId, $model->questiontext, false, 'questionid');
	}

	public function Index(FaqQuestionsModel $model)
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!is_object($model))
			throw new InvalidArgumentException();

		$faqTopicsModel = new FaqTopicsModel();
		if ($faqTopicsModel->Load($model->topicid) == -1)
		    return;

		$faqModel = new FaqModel();
		if ($faqModel->Load($faqTopicsModel->faqid) == -1)
		    return;

		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_VIEW, $faqModel->faqid))
			throw new PermissionDeniedException();

		$smartyHelper = new SmartyHelper();
		
		$smartyHelper->assign('VAL_FAQID', $faqModel->faqid);
		$smartyHelper->assign('VAL_FAQNAME', htmlspecialchars($faqModel->name));
		$smartyHelper->assign('VAL_TOPICID', $faqTopicsModel->topicid);
		$smartyHelper->assign('VAL_TOPICNAME', htmlspecialchars($faqTopicsModel->name));
		$smartyHelper->assign('VAL_QUESTIONTEXT', htmlspecialchars($model->questiontext));
		$smartyHelper->assign('VAL_QUESTIONID', $model->f('questionid'));
		$smartyHelper->assign('PERM_ADDANSWER', $g_oSec->HasPerm(DCL_ENTITY_FAQANSWER, DCL_PERM_ADD, $faqModel->faqid));
		$smartyHelper->assign('PERM_MODIFY', $g_oSec->HasPerm(DCL_ENTITY_FAQANSWER, DCL_PERM_MODIFY, $faqModel->faqid));
		$smartyHelper->assign('PERM_DELETE', $g_oSec->HasPerm(DCL_ENTITY_FAQANSWER, DCL_PERM_DELETE, $faqModel->faqid));

		$faqAnswersModel = new FaqAnswersModel();
		if ($faqAnswersModel->LoadByQuestionID($model->questionid) == -1)
		{
		    return;
		}
		
		$aRecords = array();
		while ($faqAnswersModel->next_record())
		{
			array_push($aRecords, $faqAnswersModel->Record);
		}
		
		$smartyHelper->assign('VAL_ANSWERS', $aRecords);
		
		$smartyHelper->Render('FaqquestionsDetail.tpl');
	}
}
