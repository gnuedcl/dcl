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

class FaqTopicPresenter
{
	public function Create()
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQTOPIC, DCL_PERM_ADD))
			throw new PermissionDeniedException();
			
		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('IS_EDIT', false);

		if (($faqId = Filter::ToInt($_REQUEST['faqid'])) === null)
		{
			throw new InvalidDataException();
		}

		$smartyHelper->assign('TXT_TITLE', STR_FAQ_ADDFAQTOPIC);
		$smartyHelper->assign('VAL_SEQ', '');
		$smartyHelper->assign('VAL_NAME', '');
		$smartyHelper->assign('VAL_DESCRIPTION', '');
		$smartyHelper->assign('VAL_FAQID', $faqId);

		$smartyHelper->Render('htmlFaqtopicsForm.tpl');
	}

	public function Edit(FaqTopicsModel $model)
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQTOPIC, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();
			
		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('IS_EDIT', true);

		$smartyHelper->assign('VAL_SEQ', $model->seq);
		$smartyHelper->assign('VAL_NAME', $model->name);
		$smartyHelper->assign('VAL_DESCRIPTION', $model->description);
		$smartyHelper->assign('VAL_FAQID', $model->faqid);
		$smartyHelper->assign('VAL_TOPICID', $model->topicid);

		$smartyHelper->Render('htmlFaqtopicsForm.tpl');
	}

	public function Index(FaqTopicsModel $obj)
	{
		global $dcl_info, $g_oSec;

		if (!is_object($obj))
		{
			trigger_error('[htmlFaqtopics::ShowTopic] ' . STR_FAQ_TOPICOBJECTNOTPASSED);
			return;
		}

		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_VIEW, $obj->faqid))
			throw new PermissionDeniedException();

		$faqModel = new FaqModel();
		if ($faqModel->Load($obj->faqid) == -1)
		{
		    return;
		}
		
		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('VAL_FAQID', $faqModel->faqid);
		$smartyHelper->assign('VAL_FAQNAME', $faqModel->name);
		$smartyHelper->assign('VAL_DESCRIPTION', $obj->description);
		$smartyHelper->assign('VAL_TOPICID', $obj->f('topicid'));
		$smartyHelper->assign('VAL_TOPICNAME', $obj->name);
		$smartyHelper->assign('PERM_ADDQUESTION', $g_oSec->HasPerm(DCL_ENTITY_FAQQUESTION, DCL_PERM_ADD, $obj->faqid));
		$smartyHelper->assign('PERM_MODIFY', $g_oSec->HasPerm(DCL_ENTITY_FAQTOPIC, DCL_PERM_MODIFY));
		$smartyHelper->assign('PERM_DELETE', $g_oSec->HasPerm(DCL_ENTITY_FAQTOPIC, DCL_PERM_DELETE));

		$faqQuestionsModel = new FaqQuestionsModel();
		if ($faqQuestionsModel->LoadByFaqTopicID($obj->topicid) == -1)
		{
			return;
		}
		
		$aRecords = array();
		while ($faqQuestionsModel->next_record())
		{
			array_push($aRecords, $faqQuestionsModel->Record);
		}

		$smartyHelper->assign('VAL_QUESTIONS', $aRecords);

		$smartyHelper->Render('htmlFaqtopicsDetail.tpl');
	}
}
