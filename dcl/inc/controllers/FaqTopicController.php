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

class FaqTopicController extends AbstractController
{
	public function Create()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQTOPIC, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		if (($faqId = @Filter::ToInt($_REQUEST['faqid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$faqModel = new FaqModel();
		if ($faqModel->Load($faqId) == -1)
		{
			printf(STR_BO_CANNOTLOADFAQ, $faqId);
			return;
		}

		$presenter = new FaqTopicPresenter();
		$presenter->Create();
	}

	public function Insert()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQTOPIC, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		if (($faqId = @Filter::ToInt($_REQUEST['faqid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$faqModel = new FaqModel();
		if ($faqModel->Load($faqId) == -1)
		{
			printf(STR_BO_CANNOTLOADFAQ, $faqId);
			return;
		}

		$faqTopicsModel = new FaqTopicsModel();
		$faqTopicsModel->InitFromGlobals();
		$faqTopicsModel->createby = $GLOBALS['DCLID'];
		$faqTopicsModel->createon = DCL_NOW;
		$faqTopicsModel->active = 'Y';
		$faqTopicsModel->Add();

		SetRedirectMessage('Success', 'Topic added successfully.');
		RedirectToAction('Faq', 'Index', 'faqid=' . $faqId);
	}
	
	public function Edit()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQTOPIC, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($topicid = @Filter::ToInt($_REQUEST['topicid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$faqTopicsModel = new FaqTopicsModel();
		if ($faqTopicsModel->Load($topicid) == -1)
			return;
			
		$presenter = new FaqTopicPresenter();
		$presenter->Edit($faqTopicsModel);
	}

	public function Update()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQTOPIC, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($faqId = @Filter::ToInt($_REQUEST['faqid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$faqModel = new FaqModel();
		if ($faqModel->Load($faqId) == -1)
		{
			printf(STR_BO_CANNOTLOADFAQ, $faqId);
			return;
		}

		$faqTopicsModel = new FaqTopicsModel();
		$faqTopicsModel->InitFromGlobals();
		$faqTopicsModel->active = @Filter::ToYN($_REQUEST['active']);
		$faqTopicsModel->modifyby = $GLOBALS['DCLID'];
		$faqTopicsModel->modifyon = DCL_NOW;
		$faqTopicsModel->Edit();
		
		SetRedirectMessage('Success', 'Topic updated successfully.');
		RedirectToAction('Faq', 'Index', 'faqid=' . $faqId);
	}

	public function Delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (($faqId = @Filter::ToInt($_REQUEST['faqid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQTOPIC, DCL_PERM_DELETE, $faqId))
			throw new PermissionDeniedException();

		$faqTopicsModel = new FaqTopicsModel();
		if ($faqTopicsModel->Load($faqId) == -1)
			return;
		
		ShowDeleteYesNo(STR_FAQ_FAQ, 'FaqTopic.Destroy', $faqId, $faqTopicsModel->name, false, 'topicid');
	}

	public function Destroy()
	{
		global $g_oSec;
		
		commonHeader();
		if (($faqId = @Filter::ToInt($_REQUEST['faqid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQTOPIC, DCL_PERM_DELETE, $faqId))
			throw new PermissionDeniedException();

		$faqTopicsModel = new FaqTopicsModel();
		if ($faqTopicsModel->Load($faqId) == -1)
			return;
			
		$faqId = $faqTopicsModel->faqid;
		$faqTopicsModel->Delete($faqId);
		
		SetRedirectMessage('Success', 'Topic deleted successfully.');
		RedirectToAction('Faq', 'Index', 'faqid=' . $faqId);
	}

	public function Index()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		if (($topicId = @Filter::ToInt($_REQUEST['topicid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$faqTopicsModel = new FaqTopicsModel();
		if ($faqTopicsModel->Load($topicId) == -1)
			return;

		$presenter = new FaqTopicPresenter();
		$presenter->Index($faqTopicsModel);
	}
}
