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
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQQUESTION, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		if (($topicId = @Filter::ToInt($_REQUEST['topicid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$faqTopicsModel = new FaqTopicsModel();
		if ($faqTopicsModel->Load($topicId) == -1)
		{
			printf(STR_BO_CANNOTLOADTOPIC, $topicId);
			return;
		}

		$presenter = new FaqQuestionPresenter();
		$presenter->Create();
	}

	public function Insert()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQQUESTION, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		if (($topicId = @Filter::ToInt($_REQUEST['topicid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$faqTopicsModel = new FaqTopicsModel();
		if ($faqTopicsModel->Load($topicId) == -1)
		{
			printf(STR_BO_CANNOTLOADTOPIC, $topicId);
			return;
		}

		$faqQuestionsModel = new FaqQuestionsModel();
		$faqQuestionsModel->InitFromGlobals();
		$faqQuestionsModel->createby = $GLOBALS['DCLID'];
		$faqQuestionsModel->createon = DCL_NOW;
		$faqQuestionsModel->active = 'Y';
		$faqQuestionsModel->Add();

		SetRedirectMessage('Success', 'Question added successfully.');
		RedirectToAction('FaqTopic', 'Index', 'topicid=' . $topicId);
	}

	public function Edit()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQQUESTION, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($questionId = @Filter::ToInt($_REQUEST['questionid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$faqQuestionsModel = new FaqQuestionsModel();
		if ($faqQuestionsModel->Load($questionId) == -1)
			return;
			
		$presenter = new FaqQuestionPresenter();
		$presenter->Edit($faqQuestionsModel);
	}

	public function Update()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQQUESTION, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($topicId = @Filter::ToInt($_REQUEST['topicid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$faqTopicsModel = new FaqTopicsModel();
		if ($faqTopicsModel->Load($topicId) == -1)
		{
			return;
		}

		$faqQuestionsModel = new FaqQuestionsModel();
		$faqQuestionsModel->InitFromGlobals();
		$faqQuestionsModel->active = @Filter::ToYN($_REQUEST['active']);
		$faqQuestionsModel->modifyby = $GLOBALS['DCLID'];
		$faqQuestionsModel->modifyon = DCL_NOW;
		$faqQuestionsModel->Edit();
		
		SetRedirectMessage('Success', 'Question updated successfully.');
		RedirectToAction('FaqTopic', 'Index', 'topicid=' . $topicId);
	}

	public function Delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (($questionId = @Filter::ToInt($_REQUEST['questionid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQQUESTION, DCL_PERM_DELETE, $questionId))
			throw new PermissionDeniedException();

		$faqQuestionsModel = new FaqQuestionsModel();
		if ($faqQuestionsModel->Load($questionId) == -1)
			return;
		
		ShowDeleteYesNo(STR_FAQ_QUESTION, 'FaqQuestion.Destroy', $questionId, $faqQuestionsModel->questiontext, false, 'questionid');
	}

	public function Destroy()
	{
		global $g_oSec;
		
		commonHeader();
		if (($questionId = @Filter::ToInt($_REQUEST['questionid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQQUESTION, DCL_PERM_DELETE, $questionId))
			throw new PermissionDeniedException();

		$faqQuestionsModel = new FaqQuestionsModel();
		if ($faqQuestionsModel->Load($questionId) == -1)
			return;
			
		$topicId = $faqQuestionsModel->topicid;
		$faqQuestionsModel->Delete($questionId);
		
		SetRedirectMessage('Success', 'Question deleted successfully.');
		RedirectToAction('FaqTopic', 'Index', 'topicid=' . $topicId);
	}

	public function Index()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		if (($questionId = @Filter::ToInt($_REQUEST['questionid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$faqQuestionsModel = new FaqQuestionsModel();
		if ($faqQuestionsModel->Load($questionId) == -1)
			return;

		$presenter = new FaqQuestionPresenter();
		$presenter->Index($faqQuestionsModel);
	}
}
