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

class htmlFaqquestions
{
	function DisplayForm($obj = '')
	{
		global $dcl_info, $g_oSec;

		$isEdit = is_object($obj);
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQQUESTION, $isEdit ? DCL_PERM_MODIFY : DCL_PERM_ADD))
			throw new PermissionDeniedException();
			
		$t = new DCL_Smarty();
		$t->assign('IS_EDIT', $isEdit);

		if ($isEdit)
		{
			$t->assign('VAL_SEQ', $obj->seq);
			$t->assign('VAL_QUESTIONTEXT', $obj->questiontext);
			$t->assign('VAL_TOPICID', $obj->topicid);
			$t->assign('VAL_QUESTIONID', $obj->questionid);
		}
		else
		{
			if (($id = DCL_Sanitize::ToInt($_REQUEST['topicid'])) === null)
			{
				trigger_error('Data sanitize failed.');
				return;
			}

			$t->assign('VAL_SEQ', '');
			$t->assign('VAL_QUESTIONTEXT', '');
			$t->assign('VAL_TOPICID', $id);
		}

		$t->Render('htmlFaqquestionsForm.tpl');
	}

	function ShowQuestion($obj)
	{
		global $dcl_info, $g_oSec;

		if (!is_object($obj))
		{
			trigger_error('[htmlFaqquestions::ShowQuestion] ' . STR_FAQ_QUESTIONOBJECTNOTPASSED);
			return;
		}

		$objFaqT = new dbFaqtopics();
		if ($objFaqT->Load($obj->topicid) == -1)
		    return;

		$objFaq = new dbFaq();
		if ($objFaq->Load($objFaqT->faqid) == -1)
		    return;

		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_VIEW, $objFaq->faqid))
			throw new PermissionDeniedException();

		$t = new DCL_Smarty();
		
		$t->assign('VAL_FAQID', $objFaq->faqid);
		$t->assign('VAL_FAQNAME', htmlspecialchars($objFaq->name));
		$t->assign('VAL_TOPICID', $objFaqT->topicid);
		$t->assign('VAL_TOPICNAME', htmlspecialchars($objFaqT->name));
		$t->assign('VAL_QUESTIONTEXT', htmlspecialchars($obj->questiontext));
		$t->assign('VAL_QUESTIONID', $obj->f('questionid'));
		$t->assign('PERM_ADDANSWER', $g_oSec->HasPerm(DCL_ENTITY_FAQANSWER, DCL_PERM_ADD, $objFaq->faqid));
		$t->assign('PERM_MODIFY', $g_oSec->HasPerm(DCL_ENTITY_FAQANSWER, DCL_PERM_MODIFY, $objFaq->faqid));
		$t->assign('PERM_DELETE', $g_oSec->HasPerm(DCL_ENTITY_FAQANSWER, DCL_PERM_DELETE, $objFaq->faqid));

		$objF = new dbFaqanswers();
		if ($objF->LoadByQuestionID($obj->questionid) == -1)
		{
		    return;
		}
		
		$aRecords = array();
		while ($objF->next_record())
		{
			array_push($aRecords, $objF->Record);
		}
		
		$t->assign('VAL_ANSWERS', $aRecords);
		
		$t->Render('htmlFaqquestionsDetail.tpl');
	}
}
