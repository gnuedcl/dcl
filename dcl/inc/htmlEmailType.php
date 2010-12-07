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

LoadStringResource('wo');
LoadStringResource('cfg');

class htmlEmailType
{
	var $public;

	function htmlEmailType()
	{
		$this->public = array('add', 'modify', 'delete', 'submitAdd', 'submitModify', 'submitDelete', 'PrintAll');
	}

	function GetCombo($default = 0, $cbName = 'email_type_id', $size = 0, $activeOnly = true)
	{
		$filter = '';
		$table = 'dcl_email_type';
		$order = 'email_type_name';

		$obj = new htmlSelect();
		$obj->SetOptionsFromDb($table, 'email_type_id', 'email_type_name', $filter, $order);
		$obj->vDefault = $default;
		$obj->sName = $cbName;
		$obj->iSize = $size;
		$obj->sZeroOption = STR_CMMN_SELECTONE;

		return $obj->GetHTML();
	}

	function ShowAll($orderBy = 'email_type_name')
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_EMAILTYPE, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$o = new boView();
		$o->table = 'dcl_email_type';
		$o->title = sprintf('Email Types');
		$o->AddDef('columns', '', array('email_type_id', 'email_type_name'));
		$o->AddDef('columnhdrs', '', array(STR_CMMN_ID, STR_CMMN_NAME));
		$o->AddDef('order', '', $orderBy);

		$oDB = new dbEmailType();
		if ($oDB->query($o->GetSQL()) == -1)
			return;
			
		$allRecs = $oDB->FetchAllRows();

		$oTable = new htmlTable();
		$oTable->setCaption('Email Types');
		$oTable->addColumn(STR_CMMN_ID, 'numeric');
		$oTable->addColumn(STR_CMMN_NAME, 'string');

		if ($g_oSec->HasPerm(DCL_ENTITY_EMAILTYPE, DCL_PERM_ADD))
			$oTable->addToolbar(menuLink('', 'menuAction=htmlEmailType.add'), STR_CMMN_NEW);

		if ($g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW))
			$oTable->addToolbar(menuLink('', 'menuAction=SystemSetup.Index'), DCL_MENU_SYSTEMSETUP);

		if (count($allRecs) > 0 && $g_oSec->HasAnyPerm(array(DCL_ENTITY_EMAILTYPE => array($g_oSec->PermArray(DCL_PERM_MODIFY), $g_oSec->PermArray(DCL_PERM_DELETE)))))
		{
			$oTable->addColumn(STR_CMMN_OPTIONS, 'html');
			for ($i = 0; $i < count($allRecs); $i++)
			{
				$options = '';
				if ($g_oSec->HasPerm(DCL_ENTITY_EMAILTYPE, DCL_PERM_MODIFY))
					$options = '<a href="' . menuLink('', 'menuAction=htmlEmailType.modify&email_type_id=' . $allRecs[$i][0]) . '">' . STR_CMMN_EDIT . '</a>';

				if ($g_oSec->HasPerm(DCL_ENTITY_EMAILTYPE, DCL_PERM_DELETE))
				{
					if ($options != '')
						$options .= '&nbsp;|&nbsp;';

					$options .= '<a href="' . menuLink('', 'menuAction=htmlEmailType.delete&email_type_id=' . $allRecs[$i][0]) . '">' . STR_CMMN_DELETE . '</a>';
				}

				$allRecs[$i][] = $options;
			}
		}
		
		$oTable->setData($allRecs);
		$oTable->setShowRownum(true);
		$oTable->render();
	}

	function add()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_EMAILTYPE, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$this->ShowEntryForm();
		print('<p>');
		$this->ShowAll();
	}

	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_EMAILTYPE, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();
			
		if (($id = DCL_Sanitize::ToInt($_REQUEST['email_type_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		$obj = new dbEmailType();
		if ($obj->Load($id) == -1)
		    return;
		    
		$this->ShowEntryForm($obj);
	}

	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_EMAILTYPE, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['email_type_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		$obj = new dbEmailType();
		if ($obj->Load($id) == -1)
		    return;
		    
		ShowDeleteYesNo('Email Type', 'htmlEmailType.submitDelete', $obj->email_type_id, $obj->email_type_name);
	}

	function submitAdd()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_EMAILTYPE, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$obj = new boEmailType();
		CleanArray($_REQUEST);
		$obj->add($_REQUEST);

		$this->ShowEntryForm();
		print('<p>');
		$this->ShowAll();
	}

	function submitModify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_EMAILTYPE, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$obj = new boEmailType();
		CleanArray($_REQUEST);
		$obj->modify($_REQUEST);

		$this->ShowAll();
	}

	function submitDelete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_EMAILTYPE, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		$obj = new boEmailType();
		CleanArray($_REQUEST);
		
		if (($id = DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		$aKey = array('email_type_id' => $id);
		$obj->delete($aKey);

		$this->ShowAll();
	}

	function ShowEntryForm($obj = '')
	{
		global $dcl_info, $g_oSec;

		$isEdit = is_object($obj);
		if (!$g_oSec->HasPerm(DCL_ENTITY_EMAILTYPE, $isEdit ? DCL_PERM_MODIFY : DCL_PERM_ADD))
			throw new PermissionDeniedException();
			
		$t = new DCL_Smarty();

		if ($isEdit)
		{
			if (($id = DCL_Sanitize::ToInt($_REQUEST['email_type_id'])) === null)
			{
				trigger_error('Data sanitize failed.');
				return;
			}
		
			$t->assign('TXT_FUNCTION', 'Edit Email Type');
			$t->assign('menuAction', 'htmlEmailType.submitModify');
			$t->assign('email_type_id', $id);
			$t->assign('VAL_NAME', $obj->email_type_name);
		}
		else
		{
			$t->assign('TXT_FUNCTION', 'Add Email Type');
			$t->assign('menuAction', 'htmlEmailType.submitAdd');
		}

		$t->Render('htmlEmailTypeForm.tpl');
	}
}
