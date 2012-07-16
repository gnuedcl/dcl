<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2012 Free Software Foundation
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

class ProjectPresenter
{
	public function Dashboard($id, array $children)
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_VIEW, $id);

		$model = new ProjectsModel();
		if ($model->Load($id) == -1)
			throw new InvalidEntityException('Could not find a project with an id of ' . $id);

		$smarty = new SmartyHelper();
		$smarty->assign('VAL_PROJECTID', $id);
		$smarty->assign('VAL_NAME', $model->name);

		if (isset($children) && is_array($children))
		{
			$smarty->assign('VAL_PROJECTCHILDREN', join(',', $children));

			$projectSqlHelper = new ProjectSqlQueryHelper();
			$projectSqlHelper->columns = array('projectid', 'name');
			$projectSqlHelper->AddDef('filter', 'projectid', $children);

			$includedProjects = array();
			if ($model->Query($projectSqlHelper->GetSQL()))
			{
				while ($model->next_record())
				{
					$includedProjects[] = array('id' => $model->f(0), 'name' => $model->f(1));
				}
			}

			$smarty->assign('VAL_INCLUDEDPROJECTS', $includedProjects);
		}

		$smarty->Render('ProjectDashboard.tpl');
	}
}
