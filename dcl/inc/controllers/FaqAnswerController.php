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

class FaqAnswerController extends AbstractController
{
	public function Create()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQANSWER, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		if (($questionId = @Filter::ToInt($_REQUEST['questionid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$faqQuestionsModel = new FaqQuestionsModel();
		if ($faqQuestionsModel->Load($questionId) == -1)
		{
			printf(STR_BO_CANNOTLOADQUESTION, $questionId);
			return;
		}

		$faqAnswerPresenter = new FaqAnswerPresenter();
		$faqAnswerPresenter->Create();
	}

	public function Insert()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQANSWER, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		if (($questionId = @Filter::ToInt($_REQUEST['questionid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$faqQuestionsModel = new FaqQuestionsModel();
		if ($faqQuestionsModel->Load($questionId) == -1)
		{
			printf(STR_BO_CANNOTLOADQUESTION, $questionId);
			return;
		}

		$model = new FaqAnswersModel();
		$model->InitFromGlobals();
		$model->createby = $GLOBALS['DCLID'];
		$model->createon = DCL_NOW;
		$model->active = 'Y';
		$model->Add();

		SetRedirectMessage('Success', 'Answer added successfully.');
		RedirectToAction('FaqQuestion', 'Index', 'questionid=' . $questionId);
	}

	public function Edit()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQANSWER, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($answerId = @Filter::ToInt($_REQUEST['answerid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$model = new FaqAnswersModel();
		if ($model->Load($answerId) == -1)
			return;
			
		$presenter = new FaqAnswerPresenter();
		$presenter->Edit($model);
	}

	public function Update()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQANSWER, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($questionId = @Filter::ToInt($_REQUEST['questionid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$faqQuestionsModel = new FaqQuestionsModel();
		if ($faqQuestionsModel->Load($questionId) == -1)
		{
			return;
		}

		$faqAnswersModel = new FaqAnswersModel();
		$faqAnswersModel->InitFromGlobals();
		$faqAnswersModel->active = @Filter::ToYN($_REQUEST['active']);
		$faqAnswersModel->modifyby = $GLOBALS['DCLID'];
		$faqAnswersModel->modifyon = DCL_NOW;
		$faqAnswersModel->Edit();
		
		SetRedirectMessage('Success', 'Answer updated successfully.');
		RedirectToAction('FaqQuestion', 'Index', 'questionid=' . $questionId);
	}

	public function Delete()
	{
		global $g_oSec;
		
		if (($answerId = @Filter::ToInt($_REQUEST['answerid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQANSWER, DCL_PERM_DELETE, $answerId))
			throw new PermissionDeniedException();

		$faqAnswersModel = new FaqAnswersModel();
		if ($faqAnswersModel->Load($answerId) == -1)
			return;
			
		$presenter = new FaqAnswerPresenter();
		$presenter->Delete($faqAnswersModel);
	}

	public function Destroy()
	{
		global $g_oSec;
		
		if (($answerId = @Filter::ToInt($_REQUEST['answerid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQANSWER, DCL_PERM_DELETE, $answerId))
			throw new PermissionDeniedException();

		$faqAnswersModel = new FaqAnswersModel();
		if ($faqAnswersModel->Load($answerId) == -1)
			return;
			
		$questionId = $faqAnswersModel->questionid;
		$faqAnswersModel->Delete($answerId);
		
		$faqQuestionsModel = new FaqQuestionsModel();
		if ($faqQuestionsModel->Load($questionId) == -1)
		{
			return -1;
		}
		
		SetRedirectMessage('Success', 'Answer deleted successfully.');
		RedirectToAction('FaqQuestion', 'Index', 'questionid=' . $questionId);
	}
}
