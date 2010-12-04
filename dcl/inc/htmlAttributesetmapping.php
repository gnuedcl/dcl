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

LoadStringResource('attr');

class htmlAttributesetmapping
{
	function Show($setid, $typeid)
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, DCL_PERM_VIEW))
			return PrintPermissionDenied();

		if ($typeid < 1 || $typeid > 4)
		{
			trigger_error('[htmlAttributesetmapping::Show] ' . STR_ATTR_INVALIDTYPE);
			return;
		}

		$typeText = '';
		$table = '';
		switch($typeid)
		{
			case 1:
				$typeText = STR_ATTR_ACTIONS;
				$table = 'actions';
				break;
			case 2:
				$typeText = STR_ATTR_PRIORITIES;
				$table = 'priorities';
				break;
			case 3:
				$typeText = STR_ATTR_SEVERITIES;
				$table = 'severities';
				break;
			case 4:
				$typeText = STR_ATTR_STATUSES;
				$table = 'statuses';
				break;
			default:
		}

		$obj = new dbAttributesets();
		$obj->Load($setid);

		$tableClassName = 'db' . ucfirst($table);
		$objDB = new $tableClassName();

		$objDB->Query("SELECT id,name FROM $table ORDER BY name");
		$arrAll = $objDB->FetchAllRows();

		$arrSelected = array();
		$objMap = new dbAttributesetsmap();
		$objMap->LoadMapForType($setid, $typeid);
		while ($objMap->next_record())
		{
			$arrSelected[$objMap->f(2)] = 1;
		}
		
		$objMap->FreeResult();

		$htmlAvailable = '';
		$htmlSelected = '';
		$arrSelectedData = array();
		for ($i = 0; $i < count($arrAll); $i++)
		{
			$key = $arrAll[$i][0];
			$val = $arrAll[$i][1];
			if (!IsSet($arrSelected[$key]))
				$htmlAvailable .= '<option value="' . $key . '">' . $val . '</option>';
			else
				$arrSelectedData[$key] = $val;
		}

		foreach ($arrSelectedData as $key => $val)
			$htmlSelected .= '<option value="' . $key . '">' . $val . '</option>';
			
		$t = new DCL_Smarty();
		$t->assign('IS_WEIGHTED', ($typeid == 2 || $typeid == 3));
		$t->assign('VAL_NAME', $obj->name);
		$t->assign('VAL_TYPE', $typeText);
		$t->assign('VAL_SETID', $setid);
		$t->assign('VAL_TYPEID', $typeid);
		$t->assign('OPT_AVAILABLE', $htmlAvailable);
		$t->assign('OPT_SELECTED', $htmlSelected);
		
		$t->Render('htmlAttributesetmapping.tpl');
	}
}
