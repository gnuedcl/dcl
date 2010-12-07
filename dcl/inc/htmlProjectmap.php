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

LoadStringResource('pm');

class htmlProjectmap
{
	function _display($jcn, $seq, $menuAction, $sFunction)
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK))
			throw new PermissionDeniedException();

		$objProject = new htmlProjects();
		
		$t = new DCL_Smarty();
		
		$t->assign('TXT_FUNCTION', $sFunction);
        $t->assign('menuAction', $menuAction);
		$t->assign('CMB_PROJECT', $objProject->GetCombo(0, 'projectid', 0, 0, -1, true));
		$t->assign('jcn', $jcn);
		$t->assign('seq', $seq);
		
		$t->Render('htmlProjectmapForm.tpl');
	}

	function ChooseProjectForJCN($jcn, $seq)
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK))
			throw new PermissionDeniedException();

		$this->_display($jcn, $seq, 'boProjects.dbaddtoproject', STR_PM_ADDTOPRJ);
	}

	function move()
	{
		global $dcl_info, $g_oSec;
		
		commonHeader();

		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK))
			throw new PermissionDeniedException();

		$this->_display($jcn, $seq, 'htmlProjectmap.submitMove', 'Move Work Order to Another Project');
	}

	function batchMove()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK))
			throw new PermissionDeniedException();

		if (IsSet($_REQUEST['selected']) && is_array($_REQUEST['selected']) && count($_REQUEST['selected']) > 0)
		{
			$this->_display($_REQUEST['selected'], null, 'htmlProjectmap.submitBatchMove', 'Batch Move Work Orders to Another Project');

			$obj = new htmlTimeCards();
			$obj->ShowBatchWO();
		}
		else
			throw new PermissionDeniedException();
	}

	function submitMove()
	{
		global $dcl_info, $g_oSec;

		commonHeader();

		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK))
			throw new PermissionDeniedException();

		CleanArray($_REQUEST);
		$o = new boProjects();
		$o->move($_REQUEST);
	}

	function submitBatchMove()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (($projectid = DCL_Sanitize::ToInt($_REQUEST['projectid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$wostatus = @DCL_Sanitize::ToInt($_REQUEST['wostatus']);
		if ($wostatus === null)
			$wostatus = 0;
			
		$woresponsible = @DCL_Sanitize::ToInt($_REQUEST['woresponsible']);
		if ($woresponsible === null)
			$woresponsible = 0;

		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK))
			throw new PermissionDeniedException();

		$o = new boProjects();
		$o->batchMove($_REQUEST);
		unset($o);

		$o = new htmlProjectsdetail();
		$o->show($projectid, $wostatus, $woresponsible);
	}
}
