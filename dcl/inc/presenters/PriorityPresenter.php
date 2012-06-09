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

LoadStringResource('prio');

class PriorityPresenter
{
	public function Index()
	{
		global $g_oSec;

		commonHeader();
        RequirePermission(DCL_ENTITY_PRIORITY, DCL_PERM_VIEW);

        $smartyHelper = new SmartyHelper();
        $smartyHelper->assign('PERM_ADD', $g_oSec->HasPerm(DCL_ENTITY_PRIORITY, DCL_PERM_ADD));
        $smartyHelper->assign('PERM_EDIT', $g_oSec->HasPerm(DCL_ENTITY_PRIORITY, DCL_PERM_MODIFY));
        $smartyHelper->assign('PERM_DELETE', $g_oSec->HasPerm(DCL_ENTITY_PRIORITY, DCL_PERM_DELETE));
        $smartyHelper->assign('PERM_ADMIN', $g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW));
        $smartyHelper->Render('PriorityGrid.tpl');
	}

	public function Create()
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_PRIORITY, DCL_PERM_ADD);

		$t = new SmartyHelper();

		$t->assign('TXT_FUNCTION', STR_PRIO_ADD);
		$t->assign('menuAction', 'Priority.Insert');
		$t->assign('CMB_ACTIVE', GetYesNoCombo('Y', 'active', 0, false));

		$t->Render('PrioritiesForm.tpl');
	}

	public function Edit(PriorityModel $model)
	{
		commonHeader();
        RequirePermission(DCL_ENTITY_PRIORITY, DCL_PERM_MODIFY);

		$t = new SmartyHelper();

		$t->assign('TXT_FUNCTION', STR_PRIO_EDIT);
		$t->assign('menuAction', 'Priority.Update');
		$t->assign('id', $model->id);
		$t->assign('CMB_ACTIVE', GetYesNoCombo($model->active, 'active', 0, false));
		$t->assign('VAL_SHORT', $model->short);
		$t->assign('VAL_NAME', $model->name);
		$t->assign('VAL_WEIGHT', $model->weight);

		$t->Render('PrioritiesForm.tpl');
	}

	public function Delete(PriorityModel $model)
	{
		commonHeader();
        RequirePermission(DCL_ENTITY_PRIORITY, DCL_PERM_DELETE);

		ShowDeleteYesNo('Priority', 'Priority.Destroy', $model->id, $model->name);
	}
}
