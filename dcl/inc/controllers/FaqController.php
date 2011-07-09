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
LoadStringResource('faq');
class FaqController extends AbstractController
{
	public function Index()
	{
		$presenter = new FaqPresenter();
		$presenter->Index();
	}

	public function Create()
	{
		$presenter = new FaqPresenter();
		$presenter->Create();
	}

	public function Insert()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$model = new FaqModel();
		$model->InitFrom_POST();
		$model->createby = $GLOBALS['DCLID'];
		$model->createon = DCL_NOW;
		$model->Add();

		SetRedirectMessage('Success', 'New FAQ added successfully.');
		RedirectToAction('Faq', 'Index');
	}

	public function Edit()
	{
		if (($faqId = @Filter::ToInt($_REQUEST['faqid'])) === null)
			throw new InvalidDataException();
		
		$model = new FaqModel();
		if ($model->Load($faqId) == -1)
			throw new InvalidEntityException();

		$presenter = new FaqPresenter();
		$presenter->Edit($model);
	}

	public function Update()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$model = new FaqModel();
		$model->InitFrom_POST();
		$model->active = @Filter::ToYN($_POST['active']);
		$model->modifyby = $GLOBALS['DCLID'];
		$model->modifyon = DCL_NOW;
		$model->Edit();

		SetRedirectMessage('Success', 'FAQ updated successfully.');
		RedirectToAction('Faq', 'Index');
	}

	public function Delete()
	{
		global $g_oSec;
		
		if (($iID = @Filter::ToInt($_REQUEST['faqid'])) === null)
			throw new InvalidDataException();
		
		$model = new FaqModel();
		if ($model->Load($iID) == -1)
			throw new InvalidEntityException();

		$presenter = new FaqPresenter();
		$presenter->Delete($model);
	}

	public function Destroy()
	{
		global $g_oSec;
		
		if (($faqId = @Filter::ToInt($_REQUEST['faqid'])) === null)
			throw new InvalidDataException();
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_DELETE, $faqId))
			throw new PermissionDeniedException();

		$model = new FaqModel();
		if ($model->Load($faqId) == -1)
			throw new InvalidEntityException();

		$model->Delete($faqId);

		SetRedirectMessage('Success', 'FAQ deleted successfully.');
		RedirectToAction('Faq', 'Index');
	}

	public function Detail()
	{
		if (($faqId = @Filter::ToInt($_REQUEST['faqid'])) === null)
			throw new InvalidDataException();
		
		$model = new FaqModel();
		if ($model->Load($faqId) == -1)
			throw new InvalidEntityException();

		$presenter = new FaqPresenter();
		$presenter->Detail($model);
	}
}
