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

class OutageTypePresenter
{
	public function Index()
	{
		global $g_oSec;

		commonHeader();
		RequirePermission(DCL_ENTITY_OUTAGETYPE, DCL_PERM_VIEW);

		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('PERM_ADD', $g_oSec->HasPerm(DCL_ENTITY_OUTAGETYPE, DCL_PERM_ADD));
		$smartyHelper->assign('PERM_EDIT', $g_oSec->HasPerm(DCL_ENTITY_OUTAGETYPE, DCL_PERM_MODIFY));
		$smartyHelper->assign('PERM_DELETE', $g_oSec->HasPerm(DCL_ENTITY_OUTAGETYPE, DCL_PERM_DELETE));
		$smartyHelper->assign('PERM_ADMIN', $g_oSec->HasPerm(DCL_ENTITY_OUTAGETYPE, DCL_PERM_VIEW));
		$smartyHelper->Render('OutageTypeIndex.tpl');
	}

	public function Create(OutageTypeModel $model = null, array $validatorErrors = null)
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_OUTAGETYPE, DCL_PERM_ADD);

		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('TXT_FUNCTION', 'Add New Outage Type');
		$smartyHelper->assign('menuAction', 'OutageType.Insert');
		$smartyHelper->assign('VAL_ERRORS', $validatorErrors);

		if ($model != null)
		{
			$smartyHelper->assign('VAL_NAME', $model->outage_type_name);
			$smartyHelper->assign('IS_DOWN', $model->is_down);
			$smartyHelper->assign('IS_INFRASTRUCTURE', $model->is_infrastructure);
			$smartyHelper->assign('IS_PLANNED', $model->is_planned);
		}

		$smartyHelper->Render('OutageTypeForm.tpl');
	}

	public function Edit(OutageTypeModel $model, array $validatorErrors = null)
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_OUTAGETYPE, DCL_PERM_MODIFY);

		$smartyHelper = new SmartyHelper();
		$smartyHelper->assign('TXT_FUNCTION', 'Modify Outage Type');
		$smartyHelper->assign('menuAction', 'OutageType.Update');
		$smartyHelper->assign('VAL_ERRORS', $validatorErrors);

		$smartyHelper->assign('id', $model->outage_type_id);
		$smartyHelper->assign('VAL_NAME', $model->outage_type_name);
		$smartyHelper->assign('IS_DOWN', $model->is_down);
		$smartyHelper->assign('IS_INFRASTRUCTURE', $model->is_infrastructure);
		$smartyHelper->assign('IS_PLANNED', $model->is_planned);

		$smartyHelper->Render('OutageTypeForm.tpl');
	}

	public function Delete(OutageTypeModel $model)
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_OUTAGETYPE, DCL_PERM_DELETE);

		ShowDeleteYesNo('OutageType', 'OutageType.Destroy', $model->outage_type_id, $model->outage_type_name);
	}
}