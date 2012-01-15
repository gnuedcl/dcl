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

class htmlHotlistForm
{
	var $public;

	function htmlHotlistForm()
	{
		$this->public = array('add', 'modify', 'delete', 'submitAdd', 'submitModify', 'submitDelete');
	}

	function add()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_HOTLIST, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$this->ShowEntryForm();
	}

	function copy()
	{
		global $g_oSec;
		
		commonHeader();
		
		if (($id = Filter::ToInt($_REQUEST['hotlist_id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_HOTLIST, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$obj = new HotlistModel();
		$obj->hotlist_id = $id;
		$this->ShowEntryForm($obj, true);
	}

	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		if (($id = Filter::ToInt($_REQUEST['hotlist_id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_HOTLIST, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$obj = new HotlistModel();
		if ($obj->Load($id) == -1)
			return;
			
		$this->ShowEntryForm($obj);
	}

	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (($id = Filter::ToInt($_REQUEST['hotlist_id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_HOTLIST, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		$obj = new HotlistModel();
		if ($obj->Load($id) == -1)
			return;
			
		ShowDeleteYesNo('Hotlist', 'htmlHotlistForm.submitDelete', $obj->hotlist_id, $obj->hotlist_tag);
	}

	function submitAdd()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_HOTLIST, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$obj = new boHotlist();
		CleanArray($_REQUEST);
		$active = @Filter::ToYN($_REQUEST['active']);
		$obj->add(array(
					'hotlist_tag' => $_REQUEST['hotlist_tag'],
					'active' => $active,
					'hotlist_desc' => $_REQUEST['hotlist_desc'],
					'created_by' => $GLOBALS['DCLID'],
					'created_on' => DCL_NOW
				)
			);

		$oWS = new htmlHotlistBrowse();
		$oWS->show();
	}

	function submitModify()
	{
		global $g_oSec;
		
		commonHeader();
		if (($id = Filter::ToInt($_REQUEST['hotlist_id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_HOTLIST, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$obj = new boHotlist();
		CleanArray($_REQUEST);
		$active = @Filter::ToYN($_REQUEST['active']);
		$obj->modify(array(
					'hotlist_id' => $id,
					'hotlist_tag' => $_REQUEST['hotlist_tag'],
					'active' => $active,
					'hotlist_desc' => $_REQUEST['hotlist_desc'],
					'closed_by' => $active == 'Y' ? null : $GLOBALS['DCLID'],
					'closed_on' => $active == 'Y' ? null : DCL_NOW
				)
			);

		$oWS = new htmlHotlistBrowse();
		$oWS->show();
	}

	function submitDelete()
	{
		global $g_oSec;
		
		commonHeader();
		if (($id = Filter::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_HOTLIST, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		$obj = new boHotlist();
		$obj->delete(array('hotlist_id' => $id));

		$oWS = new htmlHotlistBrowse();
		$oWS->show();
	}

	function ShowEntryForm($obj = '', $bCopy = false)
	{
		global $g_oSec;

		$isEdit = is_object($obj) && !$bCopy;
		if (!$g_oSec->HasPerm(DCL_ENTITY_HOTLIST, $isEdit ? DCL_PERM_MODIFY : DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$Template = new SmartyHelper();

		if ($isEdit)
		{
			$Template->assign('menuAction', 'htmlHotlistForm.submitModify');
			$Template->assign('VAL_TITLE', 'Edit Hotlist');
			$Template->assign('VAL_ID', $obj->hotlist_id);
			$Template->assign('VAL_NAME', $obj->hotlist_tag);
			$Template->assign('VAL_DESCRIPTION', $obj->hotlist_desc);
			$Template->assign('VAL_ACTIVE', $obj->active);
		}
		else
		{
			$Template->assign('menuAction', 'htmlHotlistForm.submitAdd');
			$Template->assign('VAL_TITLE', 'Add New Hotlist');
			$Template->assign('VAL_ACTIVE', 'Y');
		}

		$Template->Render('HotlistForm.tpl');
	}
}
