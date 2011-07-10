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

class FaqAnswerPresenter
{
	public function Create()
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQANSWER, $isEdit ? DCL_PERM_MODIFY : DCL_PERM_ADD))
			throw new PermissionDeniedException();
			
		$t = new SmartyHelper();
		$t->assign('IS_EDIT', false);
		
		if (($questionId = Filter::ToInt($_REQUEST['questionid'])) === null)
		{
			throw new InvalidDataException();
		}

		$t->assign('VAL_ANSWERTEXT', '');
		$t->assign('VAL_QUESTIONID', $questionId);

		$t->Render('htmlFaqanswersForm.tpl');
	}

	public function Edit(FaqAnswersModel $model)
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQANSWER, $isEdit ? DCL_PERM_MODIFY : DCL_PERM_ADD))
			throw new PermissionDeniedException();
			
		$t = new SmartyHelper();
		$t->assign('IS_EDIT', true);
		
		$t->assign('VAL_ANSWERTEXT', $model->answertext);
		$t->assign('VAL_ANSWERID', $model->answerid);
		$t->assign('VAL_QUESTIONID', $model->questionid);

		$t->Render('htmlFaqanswersForm.tpl');
	}
}
