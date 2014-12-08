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

class EnvironmentPresenter
{
	public function Index()
	{
		global $g_oSec;

		commonHeader();
		RequirePermission(DCL_ENTITY_ENVIRONMENT, DCL_PERM_VIEW);

		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('PERM_ADD', $g_oSec->HasPerm(DCL_ENTITY_ENVIRONMENT, DCL_PERM_ADD));
		$smartyHelper->assign('PERM_EDIT', $g_oSec->HasPerm(DCL_ENTITY_ENVIRONMENT, DCL_PERM_MODIFY));
		$smartyHelper->assign('PERM_DELETE', $g_oSec->HasPerm(DCL_ENTITY_ENVIRONMENT, DCL_PERM_DELETE));
		$smartyHelper->assign('PERM_ADMIN', $g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW));
		$smartyHelper->Render('EnvironmentIndex.tpl');
	}

	public function Create(EnvironmentModel $model = null, array $validatorErrors = null)
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_ENVIRONMENT, DCL_PERM_ADD);

		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('TXT_FUNCTION', 'Add New Environment');
		$smartyHelper->assign('menuAction', 'Environment.Insert');
		$smartyHelper->assign('VAL_ERRORS', $validatorErrors);

		if ($model != null)
		{
			$smartyHelper->assign('active', $model->active);
			$smartyHelper->assign('VAL_NAME', $model->environment_name);
		}

		$smartyHelper->Render('EnvironmentForm.tpl');
	}

	public function Edit(EnvironmentModel $model, array $validatorErrors = null)
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_ENVIRONMENT, DCL_PERM_MODIFY);

		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('TXT_FUNCTION', 'Modify Environment');
		$smartyHelper->assign('menuAction', 'Environment.Update');
		$smartyHelper->assign('VAL_ERRORS', $validatorErrors);

		$smartyHelper->assign('id', $model->environment_id);
		$smartyHelper->assign('active', $model->active);
		$smartyHelper->assign('VAL_NAME', $model->environment_name);

		$smartyHelper->Render('EnvironmentForm.tpl');
	}

	public function Delete(EnvironmentModel $model)
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_ENVIRONMENT, DCL_PERM_DELETE);

		ShowDeleteYesNo('Environment', 'Environment.Destroy', $model->environment_id, $model->environment_name);

	}
} 