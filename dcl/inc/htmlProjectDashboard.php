<?php
/*
 * $Id$
 *
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2004 Free Software Foundation
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

LoadStringResource('prj');
LoadStringResource('wo');

class htmlProjectDashboard
{
	var $oSmarty;
	var $oProject;

	function htmlProjectDashboard()
	{
		$this->oSmarty = new DCL_Smarty();
		$this->oProject = null;
	}

	function Show()
	{
		global $g_oSec;

		commonHeader();
		if (($projectid = Filter::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEW, $projectid))
			throw new PermissionDeniedException();

		$this->oProject = new ProjectsModel();
		if ($this->oProject->Load($projectid) == -1)
		{
			trigger_error('Could not find a project with an id of ' . $projectid, E_USER_ERROR);
			return;
		}

		$this->oSmarty->assign('VAL_PROJECTID', $this->oProject->projectid);
		$this->oSmarty->assign('VAL_NAME', $this->oProject->name);

		$this->oSmarty->Render('htmlProjectDashboard.tpl');
	}
}
