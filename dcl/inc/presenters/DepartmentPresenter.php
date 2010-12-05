<?php
/*
 * $Id$
 *
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2010 Free Software Foundation
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

LoadStringResource('dept');

class DepartmentPresenter
{
	public function Index()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_DEPARTMENT, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$model = new DepartmentModel();

		$model->Query("SELECT id,active,short,name FROM departments ORDER BY name");
		$allRecs = $model->FetchAllRows();

		$oTable = new htmlTable();
		$oTable->setCaption(STR_DEPT_TABLETITLE);
		$oTable->addColumn(STR_DEPT_ID, 'numeric');
		$oTable->addColumn(STR_DEPT_ACTIVE, 'string');
		$oTable->addColumn(STR_DEPT_SHORT, 'string');
		$oTable->addColumn(STR_DEPT_NAME, 'string');

		if ($g_oSec->HasPerm(DCL_ENTITY_DEPARTMENT, DCL_PERM_ADD))
			$oTable->addToolbar(menuLink('', 'menuAction=Department.Create'), STR_CMMN_NEW);

		if ($g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW))
			$oTable->addToolbar(menuLink('', 'menuAction=SystemSetup.Index'), DCL_MENU_SYSTEMSETUP);

		if (count($allRecs) > 0 && $g_oSec->HasAnyPerm(array(DCL_ENTITY_DEPARTMENT => array($g_oSec->PermArray(DCL_PERM_MODIFY), $g_oSec->PermArray(DCL_PERM_DELETE)))))
		{
			$oTable->addColumn(STR_CMMN_OPTIONS, 'html');
			for ($i = 0; $i < count($allRecs); $i++)
			{
				$options = '';
				if ($g_oSec->HasPerm(DCL_ENTITY_DEPARTMENT, DCL_PERM_MODIFY))
					$options = '<a href="' . menuLink('', 'menuAction=Department.Edit&id=' . $allRecs[$i][0]) . '">' . STR_CMMN_EDIT . '</a>';

				if ($g_oSec->HasPerm(DCL_ENTITY_DEPARTMENT, DCL_PERM_DELETE))
				{
					if ($options != '')
						$options .= '&nbsp;|&nbsp;';

					$options .= '<a href="' . menuLink('', 'menuAction=Department.Delete&id=' . $allRecs[$i][0]) . '">' . STR_CMMN_DELETE . '</a>';
				}

				$allRecs[$i][] = $options;
			}
		}
		
		$oTable->setData($allRecs);
		$oTable->setShowRownum(true);
		$oTable->render();
	}

	public function Create()
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_DEPARTMENT, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$t = new DCL_Smarty();

		$t->assign('TXT_FUNCTION', STR_DEPT_ADD);
		$t->assign('menuAction', 'Department.Insert');
		$t->assign('CMB_ACTIVE', GetYesNoCombo('Y', 'active', 0, false));

		$t->Render('htmlDepartmentsForm.tpl');
	}

	public function Edit(DepartmentModel $model)
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_DEPARTMENT, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$t = new DCL_Smarty();

		$t->assign('TXT_FUNCTION', STR_DEPT_EDIT);
		$t->assign('menuAction', 'Department.Update');
		$t->assign('id', $model->id);
		$t->assign('CMB_ACTIVE', GetYesNoCombo($model->active, 'active', 0, false));
		$t->assign('VAL_SHORT', $model->short);
		$t->assign('VAL_NAME', $model->name);

		$t->Render('htmlDepartmentsForm.tpl');
	}

	public function Delete(DepartmentModel $model)
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_DEPARTMENT, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		ShowDeleteYesNo('Department', 'Department.Destroy', $model->id, $model->name);
	}
}
