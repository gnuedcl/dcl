<?php
/*
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

//LoadStringResource('chklst');

class htmlChklstTpl
{
	function show()
	{
	}

	function add()
	{
		$this->showForm();
	}

	function modify()
	{
		global $dcl_info, $g_oSec;
		
		if (($id = Filter::ToInt($_REQUEST['dcl_chklst_tpl_id'])) === null)
		{
			throw new InvalidDataException();
		}

		if (!$g_oSec->HasPerm(DCL_ENTITY_FORMTEMPLATES, DCL_PERM_MODIFY, $id))
			throw new PermissionDeniedException();

		$o = new ChecklistTemplateModel();
		if ($o->Load($id) == -1)
		    return;

		$this->showForm($o);
	}

	function delete()
	{
	}

	function view()
	{
	}
	
	function showForm($obj = '')
	{
		global $g_oSec;
		
		$isEdit = is_object($obj);
		if (!$g_oSec->HasPerm(DCL_ENTITY_FORMTEMPLATES, $isEdit ? DCL_PERM_MODIFY : DCL_PERM_ADD))
			throw new PermissionDeniedException();
			
		$t = new SmartyHelper();
		
		if ($isEdit)
		{
			$t->assign('VAL_MENUACTION', 'boChecklistTpl.dbmodify');
			$t->assign('CMB_ACTIVE', GetYesNoCombo($obj->dcl_chklst_tpl_active, 'dcl_chklst_tpl_active', 0, false));
			$t->assign('VAL_NAME', $obj->dcl_chklst_tpl_name);
			$t->assign('VAL_ID', $obj->dcl_chklst_tpl_id);
		}
		else
		{
			$t->assign('VAL_MENUACTION', 'boChecklistTpl.dbadd');
			$t->assign('CMB_ACTIVE', GetYesNoCombo('Y', 'dcl_chklst_tpl_active', 0, false));
		}
		
		$t->Render('ChklstTplAdd.tpl');
	}
}
