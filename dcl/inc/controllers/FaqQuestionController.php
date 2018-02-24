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

LoadStringResource('bo');

class FaqQuestionController extends AbstractController
{
	public function Create()
	{
		RequirePermission(DCL_ENTITY_FAQQUESTION, DCL_PERM_ADD);
		
		$topicId = @Filter::RequireInt($_REQUEST['topicid']);
		$faqTopicsModel = new FaqTopicsModel();
		if ($faqTopicsModel->Load(array('topicid' => $topicId)) == -1)
		{
			printf(STR_BO_CANNOTLOADTOPIC, $topicId);
			return;
		}

		$presenter = new FaqQuestionPresenter();
		$presenter->Create();
	}

	public function Insert()
	{
		RequirePermission(DCL_ENTITY_FAQQUESTION, DCL_PERM_ADD);
		
		$topicId = @Filter::RequireInt($_REQUEST['topicid']);
		$faqTopicsModel = new FaqTopicsModel();
		if ($faqTopicsModel->Load(array('topicid' => $topicId)) == -1)
		{
			printf(STR_BO_CANNOTLOADTOPIC, $topicId);
			return;
		}

		$faqQuestionsModel = new FaqQuestionsModel();
		$faqQuestionsModel->InitFrom_POST();
		$faqQuestionsModel->createby = DCLID;
		$faqQuestionsModel->createon = DCL_NOW;
		$faqQuestionsModel->active = 'Y';
		$faqQuestionsModel->Add();

		SetRedirectMessage('Success', 'Question added successfully.');
		RedirectToAction('FaqTopic', 'Index', 'topicid=' . $topicId);
	}

	public function Edit()
	{
		RequirePermission(DCL_ENTITY_FAQQUESTION, DCL_PERM_MODIFY);
		
		$questionId = @Filter::RequireInt($_REQUEST['questionid']);
		$faqQuestionsModel = new FaqQuestionsModel();
		if ($faqQuestionsModel->Load(array('questionid' => $questionId)) == -1)
			return;
			
		$presenter = new FaqQuestionPresenter();
		$presenter->Edit($faqQuestionsModel);
	}

	public function Update()
	{
		RequirePermission(DCL_ENTITY_FAQQUESTION, DCL_PERM_MODIFY);

		$topicId = @Filter::RequireInt($_POST['topicid']);
		$faqTopicsModel = new FaqTopicsModel();
		if ($faqTopicsModel->Load(array('topicid' => $topicId)) == -1)
		{
			return;
		}

		$faqQuestionsModel = new FaqQuestionsModel();
		$faqQuestionsModel->InitFrom_POST();
		$faqQuestionsModel->active = @Filter::ToYN($_POST['active']);
		$faqQuestionsModel->modifyby = DCLID;
		$faqQuestionsModel->modifyon = DCL_NOW;
		$faqQuestionsModel->Edit();
		
		SetRedirectMessage('Success', 'Question updated successfully.');
		RedirectToAction('FaqTopic', 'Index', 'topicid=' . $topicId);
	}

	public function Delete()
	{
		$questionId = @Filter::RequireInt($_REQUEST['questionid']);
		RequirePermission(DCL_ENTITY_FAQQUESTION, DCL_PERM_DELETE, $questionId);

		$faqQuestionsModel = new FaqQuestionsModel();
		if ($faqQuestionsModel->Load(array('questionid' => $questionId)) == -1)
			return;
		
		$presenter = new FaqQuestionPresenter();
		$presenter->Delete($faqQuestionsModel);
	}

	public function Destroy()
	{
		$questionId = @Filter::RequireInt($_POST['questionid']);
		RequirePermission(DCL_ENTITY_FAQQUESTION, DCL_PERM_DELETE, $questionId);

		$faqQuestionsModel = new FaqQuestionsModel();
		if ($faqQuestionsModel->Load(array('questionid' => $questionId)) == -1)
			return;
			
		$topicId = $faqQuestionsModel->topicid;
		$faqQuestionsModel->Delete(array('questionid' => $questionId));
		
		SetRedirectMessage('Success', 'Question deleted successfully.');
		RedirectToAction('FaqTopic', 'Index', 'topicid=' . $topicId);
	}

	public function Index()
	{
		RequirePermission(DCL_ENTITY_FAQ, DCL_PERM_VIEW);

		$questionId = @Filter::RequireInt($_REQUEST['questionid']);
		$faqQuestionsModel = new FaqQuestionsModel();
		if ($faqQuestionsModel->Load(array('questionid' => $questionId)) == -1)
			return;

		$presenter = new FaqQuestionPresenter();
		$presenter->Index($faqQuestionsModel);
	}
}
