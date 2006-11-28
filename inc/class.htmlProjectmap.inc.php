<?php
/*
 * $Id: class.htmlProjectmap.inc.php,v 1.1.1.1 2006/11/27 05:30:48 mdean Exp $
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
	function _display($hidden_vars, $sFunction)
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK))
			return PrintPermissionDenied();

		$objProject = CreateObject('dcl.htmlProjects');

		$Template = CreateTemplate(array('hForm' => 'htmlProjectmapForm.tpl'));
		$Template->set_var('VAL_FORMACTION', menuLink());
		$Template->set_var('TXT_FUNCTION', $sFunction);

		$Template->set_var('HIDDEN_VARS', $hidden_vars);
		$Template->set_var('TXT_CHOOSEPRJ', STR_PM_CHOOSEPRJ);
		$Template->set_var('CMB_PROJECT', $objProject->GetCombo(0, 'projectid', 0, 0, -1, true));
		$Template->set_var('TXT_ADDALLSEQ', STR_PM_ADDALLSEQ);
		$Template->set_var('BTN_OK', STR_CMMN_SAVE);
		$Template->set_var('BTN_RESET', STR_CMMN_RESET);

		$Template->pparse('out', 'hForm');
	}

	function ChooseProjectForJCN($jcn, $seq)
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK))
			return PrintPermissionDenied();

		$hidden_vars = GetHiddenVar('menuAction', 'boProjects.dbaddtoproject');
		$hidden_vars .= GetHiddenVar('jcn', $jcn);
		$hidden_vars .= GetHiddenVar('seq', $seq);

		$this->_display($hidden_vars, STR_PM_ADDTOPRJ);
	}

	function move()
	{
		global $dcl_info, $g_oSec;
		
		commonHeader();

		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK))
			return PrintPermissionDenied();

		$hidden_vars = GetHiddenVar('menuAction', 'htmlProjectmap.submitMove');
		$hidden_vars .= GetHiddenVar('jcn', $jcn);
		$hidden_vars .= GetHiddenVar('seq', $seq);

		$this->_display($hidden_vars, 'Move Work Order to Another Project');
	}

	function batchMove()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK))
			return PrintPermissionDenied();

		$hidden_vars = GetHiddenVar('menuAction', 'htmlProjectmap.submitBatchMove');
		if (IsSet($_REQUEST['selected']) && is_array($_REQUEST['selected']) && count($_REQUEST['selected']) > 0)
		{
			foreach ($_REQUEST['selected'] as $val)
			{
				$hidden_vars .= GetHiddenVar('selected[]', $val);
			}

			$this->_display($hidden_vars, 'Batch Move Work Orders to Another Project');

			$obj = CreateObject('dcl.htmlTimeCards');
			$obj->ShowBatchWO();
		}
		else
			return PrintPermissionDenied();
	}

	function submitMove()
	{
		global $dcl_info, $g_oSec;

		commonHeader();

		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK))
			return PrintPermissionDenied();

		CleanArray($_REQUEST);
		$o = CreateObject('dcl.boProjects');
		$o->move($_REQUEST);
	}

	function submitBatchMove()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (($projectid = DCL_Sanitize::ToInt($_REQUEST['projectid'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$wostatus = @DCL_Sanitize::ToInt($_REQUEST['wostatus']);
		if ($wostatus === null)
			$wostatus = 0;
			
		$woresponsible = @DCL_Sanitize::ToInt($_REQUEST['woresponsible']);
		if ($woresponsible === null)
			$woresponsible = 0;

		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK))
			return PrintPermissionDenied();

		$o = CreateObject('dcl.boProjects');
		$o->batchMove($_REQUEST);
		unset($o);

		$o = CreateObject('dcl.htmlProjectsdetail');
		$o->show($projectid, $wostatus, $woresponsible);
	}
}
?>
