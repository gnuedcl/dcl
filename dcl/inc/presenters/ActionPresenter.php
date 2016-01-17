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

LoadStringResource('actn');
class ActionPresenter
{
	public function Index()
	{
		global $g_oSec;

		commonHeader();

		if (!$g_oSec->HasPerm(DCL_ENTITY_ACTION, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$model = new ActionModel();
		$model->Query("SELECT id,active,short,name FROM actions ORDER BY name");
		$allRecs = $model->FetchAllRows();

		$oTable = new TableHtmlHelper();
		$oTable->setCaption(sprintf(STR_ACTN_TABLETITLE, 'name'));
		$oTable->addColumn(STR_ACTN_ID, 'numeric');
		$oTable->addColumn(STR_ACTN_ACTIVEABB, 'string');
		$oTable->addColumn(STR_ACTN_SHORT, 'string');
		$oTable->addColumn(STR_ACTN_NAME, 'string');

		if ($g_oSec->HasPerm(DCL_ENTITY_ACTION, DCL_PERM_ADD))
			$oTable->addToolbar(menuLink('', 'menuAction=Action.Create'), STR_CMMN_NEW);

		if ($g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW))
			$oTable->addToolbar(menuLink('', 'menuAction=SystemSetup.Index'), DCL_MENU_SYSTEMSETUP);

		if (count($allRecs) > 0 && $g_oSec->HasAnyPerm(array(DCL_ENTITY_ACTION => array($g_oSec->PermArray(DCL_PERM_MODIFY), $g_oSec->PermArray(DCL_PERM_DELETE)))))
		{
			$oTable->addColumn(STR_ACTN_OPTIONS, 'html');
			for ($i = 0; $i < count($allRecs); $i++)
			{
				$options = '';
				if ($g_oSec->HasPerm(DCL_ENTITY_ACTION, DCL_PERM_MODIFY))
					$options = '<a href="' . menuLink('', 'menuAction=Action.Edit&id=' . $allRecs[$i][0]) . '">' . STR_CMMN_EDIT . '</a>';

				if ($g_oSec->HasPerm(DCL_ENTITY_ACTION, DCL_PERM_DELETE))
				{
					if ($options != '')
						$options .= '&nbsp;|&nbsp;';

					$options .= '<a href="' . menuLink('', 'menuAction=Action.Delete&id=' . $allRecs[$i][0]) . '">' . STR_CMMN_DELETE . '</a>';
				}

				$allRecs[$i][] = $options;
			}
		}

		$oTable->setData($allRecs);
		$oTable->setShowRownum(true);
		$oTable->sTemplate = 'TableView.tpl';
		$oTable->render();
	}

	public function Create()
	{
		global $dcl_info, $g_oSec;

		commonHeader();

		if (!$g_oSec->HasPerm(DCL_ENTITY_ACTION, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$t = new SmartyHelper();

		$t->assign('TXT_FUNCTION', STR_ACTN_ADD);
		$t->assign('menuAction', 'Action.Insert');
		$t->assign('CMB_ACTIVE', GetYesNoCombo('Y', 'active', 0, false));

		$t->Render('ActionsForm.tpl');
	}

	public function Edit(ActionModel $model)
	{
		global $g_oSec;

		commonHeader();

		if (!$g_oSec->HasPerm(DCL_ENTITY_ACTION, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$t = new SmartyHelper();

		$t->assign('TXT_FUNCTION', STR_ACTN_EDIT);
		$t->assign('menuAction', 'Action.Update');
		$t->assign('id', $model->id);
		$t->assign('CMB_ACTIVE', GetYesNoCombo($model->active, 'active', 0, false));
		$t->assign('VAL_SHORT', $model->short);
		$t->assign('VAL_NAME', $model->name);

		$t->Render('ActionsForm.tpl');
	}

	public function Delete(ActionModel $model)
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ACTION, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		ShowDeleteYesNo('Action', 'Action.Destroy', $model->id, $model->name);
	}
}