<?php
/*
 * $Id: class.htmlAttributesets.inc.php,v 1.1.1.1 2006/11/27 05:30:44 mdean Exp $
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

LoadStringResource('attr');

class htmlAttributesets
{
	function GetCombo($default = 0, $cbName = 'setid', $longShort = 'name', $size = 0, $activeOnly = true)
	{
		$obj = CreateObject('dcl.dbAttributesets');
		$obj->cacheEnabled = false;

		$query = 'SELECT id,name FROM attributesets ';

		if ($activeOnly)
			$query .= 'WHERE active=\'Y\' ';

		$query .= "ORDER BY $longShort";
		$obj->Query($query);

		$str = "<select name=\"$cbName";
		if ($size > 0)
			$str .= '[]" multiple size="' . $size;

		$str .= '">';
		if ($size == 0)
			$str .= sprintf('<option value="0">%s</option>', STR_ATTR_SELECTONE);

		while ($obj->next_record())
		{
			$id = $obj->f(0);
			$text = $obj->f(1);
			$str .= '<option value="'. $id . '"';
			if ($id == $default)
				$str .= ' selected';
			$str .= '>' . $text . '</option>';
		}

		$str .= '</select>';

		return $str;
	}

	function PrintAll($orderBy = 'name')
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, DCL_PERM_VIEW))
			return PrintPermissionDenied();

		$objDBStatus = CreateObject('dcl.dbAttributesets');
		$objDBStatus->Query("SELECT id,active,short,name FROM attributesets ORDER BY $orderBy");
		$allRecs = $objDBStatus->FetchAllRows();

		$oTable =& CreateObject('dcl.htmlTable');
		$oTable->setCaption(STR_ATTR_ATTRIBUTESETS);
		$oTable->addColumn(STR_ATTR_ID, 'numeric');
		$oTable->addColumn(STR_ATTR_ACTIVE, 'string');
		$oTable->addColumn(STR_ATTR_SHORT, 'string');
		$oTable->addColumn(STR_ATTR_NAME, 'string');

		if ($g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, DCL_PERM_ADD))
			$oTable->addToolbar(menuLink('', 'menuAction=boAttributesets.add'), STR_CMMN_NEW);

		if ($g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW))
			$oTable->addToolbar(menuLink('', 'menuAction=boAdmin.ShowSystemConfig'), DCL_MENU_SYSTEMSETUP);

		if (count($allRecs) > 0)
		{
			$oTable->addColumn(STR_CMMN_OPTIONS, 'html');
			for ($i = 0; $i < count($allRecs); $i++)
			{
				$options = '<a href="' . menuLink('', 'menuAction=boAttributesets.view&id=' . $allRecs[$i][0]) . '">' . STR_ATTR_MAP . '</a>';
				if ($g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, DCL_PERM_MODIFY))
					$options .= '&nbsp;|&nbsp;<a href="' . menuLink('', 'menuAction=boAttributesets.modify&id=' . $allRecs[$i][0]) . '">' . STR_CMMN_EDIT . '</a>';

				if ($g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, DCL_PERM_DELETE))
					$options .= '&nbsp;|&nbsp;<a href="' . menuLink('', 'menuAction=boAttributesets.delete&id=' . $allRecs[$i][0]) . '">' . STR_CMMN_DELETE . '</a>';

				$allRecs[$i][] = $options;
			}
		}

		$oTable->setData($allRecs);
		$oTable->setShowRownum(true);
		$oTable->render();
	}

	function ShowEntryForm($obj = '')
	{
		global $dcl_info, $g_oSec;

		$isEdit = is_object($obj);
		if (!$g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, $isEdit ? DCL_PERM_MODIFY : DCL_PERM_ADD))
			return PrintPermissionDenied();

		$t = CreateSmarty();

		if ($isEdit)
		{
			$t->assign('TXT_FUNCTION', STR_ATTR_EDITATTRIBUTESET);
			$t->assign('menuAction', 'boAttributesets.dbmodify');
			$t->assign('id', $obj->id);
			$t->assign('CMB_ACTIVE', GetYesNoCombo($obj->active, 'active', 0, false));
			$t->assign('VAL_SHORT', $obj->short);
			$t->assign('VAL_NAME', $obj->name);
		}
		else
		{
			$t->assign('TXT_FUNCTION', STR_ATTR_ADDATTRIBUTESET);
			$t->assign('menuAction', 'boAttributesets.dbadd');
			$t->assign('CMB_ACTIVE', GetYesNoCombo('Y', 'active', 0, false));
		}

		SmartyDisplay($t, 'htmlAttributesetsForm.tpl');
	}
}
?>
