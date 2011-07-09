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

class boFaqtopics
{
	function add()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQTOPIC, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		if (($iID = @Filter::ToInt($_REQUEST['faqid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$objF = new FaqModel();
		if ($objF->Load($iID) == -1)
		{
			printf(STR_BO_CANNOTLOADFAQ, $iID);
			return;
		}

		$obj = new htmlFaqtopics();
		$obj->DisplayForm();

		$objH = new htmlFaq();
		print('<p>');
		$objH->ShowFaq($objF);
	}

	function dbadd()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQTOPIC, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		if (($iID = @Filter::ToInt($_REQUEST['faqid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$objF = new FaqModel();
		if ($objF->Load($iID) == -1)
		{
			printf(STR_BO_CANNOTLOADFAQ, $iID);
			return;
		}

		$obj = new FaqTopicsModel();
		$obj->InitFromGlobals();
		$obj->createby = $GLOBALS['DCLID'];
		$obj->createon = DCL_NOW;
		$obj->active = 'Y';
		$obj->Add();

		$objH = new htmlFaq();
		$objH->ShowFaq($objF);
	}
	
	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQTOPIC, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($iID = @Filter::ToInt($_REQUEST['topicid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$obj = new FaqTopicsModel();
		if ($obj->Load($iID) == -1)
			return;
			
		$objH = new htmlFaqtopics();
		$objH->DisplayForm($obj);
	}

	function dbmodify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQTOPIC, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($iID = @Filter::ToInt($_REQUEST['faqid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$objF = new FaqModel();
		if ($objF->Load($iID) == -1)
		{
			printf(STR_BO_CANNOTLOADFAQ, $iID);
			return;
		}

		$obj = new FaqTopicsModel();
		$obj->InitFromGlobals();
		$obj->active = @Filter::ToYN($_REQUEST['active']);
		$obj->modifyby = $GLOBALS['DCLID'];
		$obj->modifyon = DCL_NOW;
		$obj->Edit();
		
		$objH = new htmlFaq();
		$objH->ShowFaq($objF);
	}

	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (($iID = @Filter::ToInt($_REQUEST['faqid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQTOPIC, DCL_PERM_DELETE, $iID))
			throw new PermissionDeniedException();

		$obj = new FaqTopicsModel();
		if ($obj->Load($iID) == -1)
			return;
		
		ShowDeleteYesNo(STR_FAQ_FAQ, 'boFaqtopics.dbdelete', $iID, $obj->name, false, 'topicid');
	}

	function dbdelete()
	{
		global $g_oSec;
		
		commonHeader();
		if (($iID = @Filter::ToInt($_REQUEST['faqid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQTOPIC, DCL_PERM_DELETE, $iID))
			throw new PermissionDeniedException();

		$obj = new FaqTopicsModel();
		if ($obj->Load($iID) == -1)
			return;
			
		$iFaqID = $obj->faqid;
		$obj->Delete($iID);
		
		$objF = new FaqModel();
		if ($objF->Load($iFaqID) == -1)
		{
			return -1;
		}
		
		$objH = new htmlFaq();
		$objH->ShowFaq($objF);
	}

	function view()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		if (($iID = @Filter::ToInt($_REQUEST['topicid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$obj = new FaqTopicsModel();
		if ($obj->Load($iID) == -1)
			return;

		$objH = new htmlFaqtopics();
		$objH->ShowTopic($obj);
	}
}
