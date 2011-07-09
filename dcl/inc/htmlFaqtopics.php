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

class htmlFaqtopics
{
	function DisplayForm($obj = '')
	{
		global $dcl_info, $g_oSec;

		$isEdit = is_object($obj);
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQTOPIC, $isEdit ? DCL_PERM_MODIFY : DCL_PERM_ADD))
			throw new PermissionDeniedException();
			
		$t = new SmartyHelper();
		$t->assign('IS_EDIT', $isEdit);

		if ($isEdit)
		{
			$t->assign('VAL_SEQ', $obj->seq);
			$t->assign('VAL_NAME', $obj->name);
			$t->assign('VAL_DESCRIPTION', $obj->description);
			$t->assign('VAL_FAQID', $obj->faqid);
			$t->assign('VAL_TOPICID', $obj->topicid);
		}
		else
		{
			if (($id = Filter::ToInt($_REQUEST['faqid'])) === null)
			{
				throw new InvalidDataException();
			}
		
			$t->assign('TXT_TITLE', STR_FAQ_ADDFAQTOPIC);
			$t->assign('VAL_SEQ', '');
			$t->assign('VAL_NAME', '');
			$t->assign('VAL_DESCRIPTION', '');
			$t->assign('VAL_FAQID', $id);
		}

		$t->Render('htmlFaqtopicsForm.tpl');
	}

	function ShowTopic($obj)
	{
		global $dcl_info, $g_oSec;

		if (!is_object($obj))
		{
			trigger_error('[htmlFaqtopics::ShowTopic] ' . STR_FAQ_TOPICOBJECTNOTPASSED);
			return;
		}

		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_VIEW, $obj->faqid))
			throw new PermissionDeniedException();

		$objFaq = new FaqModel();
		if ($objFaq->Load($obj->faqid) == -1)
		{
		    return;
		}
		
		$t = new SmartyHelper();
		$t->assign('VAL_FAQID', $objFaq->faqid);
		$t->assign('VAL_FAQNAME', $objFaq->name);
		$t->assign('VAL_DESCRIPTION', $obj->description);
		$t->assign('VAL_TOPICID', $obj->f('topicid'));
		$t->assign('VAL_TOPICNAME', $obj->name);
		$t->assign('PERM_ADDQUESTION', $g_oSec->HasPerm(DCL_ENTITY_FAQQUESTION, DCL_PERM_ADD, $obj->faqid));
		$t->assign('PERM_MODIFY', $g_oSec->HasPerm(DCL_ENTITY_FAQTOPIC, DCL_PERM_MODIFY));
		$t->assign('PERM_DELETE', $g_oSec->HasPerm(DCL_ENTITY_FAQTOPIC, DCL_PERM_DELETE));

		$objF = new FaqQuestionsModel();
		if ($objF->LoadByFaqTopicID($obj->topicid) == -1)
		{
			return;
		}
		
		$aRecords = array();
		while ($objF->next_record())
		{
			array_push($aRecords, $objF->Record);
		}

		$t->assign('VAL_QUESTIONS', $aRecords);

		$t->Render('htmlFaqtopicsDetail.tpl');
	}
}
