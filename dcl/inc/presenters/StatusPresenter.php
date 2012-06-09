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

LoadStringResource('stat');

class StatusPresenter
{
	public function Index()
	{
		global $g_oSec;

		commonHeader();
		RequirePermission(DCL_ENTITY_STATUS, DCL_PERM_VIEW);

        $smartyHelper = new SmartyHelper();
        $smartyHelper->assign('PERM_ADD', $g_oSec->HasPerm(DCL_ENTITY_STATUS, DCL_PERM_ADD));
        $smartyHelper->assign('PERM_EDIT', $g_oSec->HasPerm(DCL_ENTITY_STATUS, DCL_PERM_MODIFY));
        $smartyHelper->assign('PERM_DELETE', $g_oSec->HasPerm(DCL_ENTITY_STATUS, DCL_PERM_DELETE));
        $smartyHelper->assign('PERM_ADMIN', $g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW));
        $smartyHelper->Render('StatusGrid.tpl');
	}

	public function Create()
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_STATUS, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$t = new SmartyHelper();
		$statusHtmlHelper = new StatusHtmlHelper();

		$t->assign('TXT_FUNCTION', STR_STAT_ADD);
		$t->assign('menuAction', 'Status.Insert');
		$t->assign('CMB_ACTIVE', GetYesNoCombo('Y', 'active', 0, false));

		$t->assign('CMB_TYPE', $statusHtmlHelper->SelectType(0));
		
		$t->Render('StatusesForm.tpl');
	}

	public function Edit(StatusModel $model)
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_STATUS, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$t = new SmartyHelper();
		$statusHtmlHelper = new StatusHtmlHelper();

		$t->assign('TXT_FUNCTION', STR_STAT_EDIT);
		$t->assign('menuAction', 'Status.Update');
		$t->assign('id', $model->id);
		$t->assign('CMB_ACTIVE', GetYesNoCombo($model->active, 'active', 0, false));
		$t->assign('VAL_SHORT', $model->short);
		$t->assign('VAL_NAME', $model->name);

		$t->assign('CMB_TYPE', $statusHtmlHelper->SelectType($model->dcl_status_type));

		$t->Render('StatusesForm.tpl');
	}

	public function Delete(StatusModel $model)
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_STATUS, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		ShowDeleteYesNo('Status', 'Status.Destroy', $model->id, $model->name);
	}
}
