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
		RequirePermission(DCL_ENTITY_FAQTOPIC, DCL_PERM_ADD);

		$faqId = @Filter::RequireInt($_REQUEST['faqid']);
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
		RequirePermission(DCL_ENTITY_FAQTOPIC, DCL_PERM_ADD);

		$faqId = @Filter::RequireInt($_REQUEST['faqid']);
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
		RedirectToAction('Faq', 'Detail', 'faqid=' . $faqId);
	}
	
	public function Edit()
	{
		RequirePermission(DCL_ENTITY_FAQTOPIC, DCL_PERM_MODIFY);

		$topicId = @Filter::RequireInt($_REQUEST['topicid']);
		$faqTopicsModel = new FaqTopicsModel();
		if ($faqTopicsModel->Load($topicId) == -1)
			return;
			
		$presenter = new FaqTopicPresenter();
		$presenter->Edit($faqTopicsModel);
	}

	public function Update()
	{
		RequirePersmission(DCL_ENTITY_FAQTOPIC, DCL_PERM_MODIFY);

		$faqId = @Filter::RequireInt($_POST['faqid']);
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
		RedirectToAction('Faq', 'Detail', 'faqid=' . $faqId);
	}

	public function Delete()
	{
		$topicId = @Filter::RequireInt($_REQUEST['topicid']);
		
		RequirePermission(DCL_ENTITY_FAQTOPIC, DCL_PERM_DELETE, $topicId);
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQTOPIC, DCL_PERM_DELETE, $topicId))
			throw new PermissionDeniedException();

		$faqTopicsModel = new FaqTopicsModel();
		if ($faqTopicsModel->Load($topicId) == -1)
			return;
		
		$presenter = new FaqTopicPresenter();
		$presenter->Delete($faqTopicsModel);
	}

	public function Destroy()
	{
		$topicId = @Filter::RequireInt($_REQUEST['topicid']);
		
		RequirePermission(DCL_ENTITY_FAQTOPIC, DCL_PERM_DELETE, $topicId);

		$faqTopicsModel = new FaqTopicsModel();
		if ($faqTopicsModel->Load($topicId) == -1)
			return;
			
		$faqId = $faqTopicsModel->faqid;
		$faqTopicsModel->Delete($topicId);
		
		SetRedirectMessage('Success', 'Topic deleted successfully.');
		RedirectToAction('Faq', 'Detail', 'faqid=' . $faqId);
	}

	public function Index()
	{
		RequirePermission(DCL_ENTITY_FAQ, DCL_PERM_VIEW);
		
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$topicId = @Filter::RequireInt($_REQUEST['topicid']);
		$faqTopicsModel = new FaqTopicsModel();
		if ($faqTopicsModel->Load($topicId) == -1)
			return;

		$presenter = new FaqTopicPresenter();
		$presenter->Index($faqTopicsModel);
	}
}
