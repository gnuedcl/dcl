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

LoadStringResource('bo');

class boFaqquestions
{
	function add()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQQUESTION, DCL_PERM_ADD))
			return PrintPermissionDenied();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['topicid'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$objF =& CreateObject('dcl.dbFaqtopics');
		if ($objF->Load($iID) == -1)
		{
			printf(STR_BO_CANNOTLOADTOPIC, $iID);
			return;
		}

		$obj =& CreateObject('dcl.htmlFaqquestions');
		$obj->DisplayForm();

		$objH =& CreateObject('dcl.htmlFaqtopics');
		print('<p>');
		$objH->ShowTopic($objF);
	}

	function dbadd()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQQUESTION, DCL_PERM_ADD))
			return PrintPermissionDenied();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['topicid'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$objF =& CreateObject('dcl.dbFaqtopics');
		if ($objF->Load($iID) == -1)
		{
			printf(STR_BO_CANNOTLOADTOPIC, $iID);
			return;
		}

		$obj = CreateObject('dcl.dbFaqquestions');
		$obj->InitFromGlobals();
		$obj->createby = $GLOBALS['DCLID'];
		$obj->createon = DCL_NOW;
		$obj->active = 'Y';
		$obj->Add();

		$objH = CreateObject('dcl.htmlFaqtopics');
		$objH->ShowTopic($objF);
	}

	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQQUESTION, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['questionid'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$obj =& CreateObject('dcl.dbFaqquestions');
		if ($obj->Load($iID) == -1)
			return;
			
		$objH =& CreateObject('dcl.htmlFaqquestions');
		$objH->DisplayForm($obj);
	}

	function dbmodify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQQUESTION, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['topicid'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$objF =& CreateObject('dcl.dbFaqtopics');
		if ($objF->Load($iID) == -1)
		{
			return;
		}

		$obj =& CreateObject('dcl.dbFaqquestions');
		$obj->InitFromGlobals();
		$obj->active = @DCL_Sanitize::ToYN($_REQUEST['active']);
		$obj->modifyby = $GLOBALS['DCLID'];
		$obj->modifyon = DCL_NOW;
		$obj->Edit();
		
		$objH =& CreateObject('dcl.htmlFaqtopics');
		$objH->ShowTopic($objF);
	}

	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['questionid'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQQUESTION, DCL_PERM_DELETE, $iID))
			return PrintPermissionDenied();

		$obj =& CreateObject('dcl.dbFaqquestions');
		if ($obj->Load($iID) == -1)
			return;
		
		ShowDeleteYesNo(STR_FAQ_QUESTION, 'boFaqquestions.dbdelete', $iID, $obj->questiontext, false, 'questionid');
	}

	function dbdelete()
	{
		global $g_oSec;
		
		commonHeader();
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['questionid'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQQUESTION, DCL_PERM_DELETE, $iID))
			return PrintPermissionDenied();

		$obj =& CreateObject('dcl.dbFaqquestions');
		if ($obj->Load($iID) == -1)
			return;
			
		$iTopicID = $obj->topicid;
		$obj->Delete($iID);
		
		$objT =& CreateObject('dcl.dbFaqtopics');
		if ($objT->Load($iTopicID) == -1)
		{
			return -1;
		}
		
		$objH =& CreateObject('dcl.htmlFaqtopics');
		$objH->ShowTopic($objT);
	}

	function view()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_VIEW))
			return PrintPermissionDenied();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['questionid'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$obj =& CreateObject('dcl.dbFaqquestions');
		if ($obj->Load($iID) == -1)
			return;

		$objH =& CreateObject('dcl.htmlFaqquestions');
		$objH->ShowQuestion($obj);
	}
}
?>
