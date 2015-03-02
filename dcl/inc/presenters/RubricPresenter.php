<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2014 Free Software Foundation
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

class RubricPresenter
{
	public function Index()
	{
		commonHeader();

		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('PERM_ADD', HasPermission(DCL_ENTITY_OUTAGETYPE, DCL_PERM_ADD));
		$smartyHelper->assign('PERM_EDIT', HasPermission(DCL_ENTITY_OUTAGETYPE, DCL_PERM_MODIFY));
		$smartyHelper->assign('PERM_DELETE', HasPermission(DCL_ENTITY_OUTAGETYPE, DCL_PERM_DELETE));
		$smartyHelper->assign('PERM_ADMIN', HasPermission(DCL_ENTITY_OUTAGETYPE, DCL_PERM_VIEW));
		$smartyHelper->Render('RubricIndex.tpl');
	}

	public function Create(RubricModel $model = null, array $errors = null)
	{
		commonHeader();

		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('menuAction', 'Rubric.Insert');
		$smartyHelper->assign('TXT_FUNCTION', 'Add New Rubric');

		$smartyHelper->Render('RubricForm.tpl');
	}

	public function Edit(RubricModel $model, array $errors = null)
	{
		commonHeader();

		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('menuAction', 'Rubric.Update');
		$smartyHelper->assign('TXT_FUNCTION', 'Modify Rubric');
		$smartyHelper->assign('ViewData', $model);

		$smartyHelper->Render('RubricForm.tpl');
	}

	public function Destroy()
	{

	}
}