<?php
/*
 * $Id$
 *
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2010 Free Software Foundation
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
class AttributeSetMapPresenter
{
	public function Index(AttributeSetModel $model)
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$t = new SmartyHelper();
		$t->assign('VAL_ATTRIBUTESETNAME', $model->name);
		$t->Render('htmlAttributesetdetail.tpl');

		$objA = new ActionModel();
		$theAttributes = array('actions', 'priorities', 'severities', 'statuses');
		$aTitles = array('actions' => STR_ATTR_ACTIONS, 'priorities' => STR_ATTR_PRIORITIES, 'severities' => STR_ATTR_SEVERITIES, 'statuses' => STR_ATTR_STATUSES);

		for ($cnt = 0; $cnt < count($theAttributes); $cnt++)
		{
			$typeid = $cnt + 1;
			$section = $theAttributes[$cnt];

			$query = 'SELECT a.name FROM ' . $section . ' a, attributesetsmap b WHERE a.id=b.keyid ';
			$query .= ' AND b.setid=' . $model->id;
			$query .= ' AND b.typeid=' . $typeid;
			$query .= ' ORDER BY ';
			if ($section == 'priorities' || $section == 'severities')
				$query .= 'b.weight';
			else
				$query .= 'a.name';

			if ($objA->Query($query) != -1)
			{
				$oTable = new TableHtmlHelper();
				$oTable->setCaption($aTitles[$theAttributes[$cnt]]);
				$oTable->setInline(true);

				$oTable->addToolbar(menuLink('', 'menuAction=AttributeSet.Index'), STR_ATTR_ATTRIBUTESET);
				if ($g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, DCL_PERM_MODIFY))
					$oTable->addToolbar(menuLink('', 'menuAction=AttributeSetMap.Edit&setid=' . $model->id . '&typeid=' . $typeid), STR_ATTR_MAP);

				$oTable->setData($objA->FetchAllRows());
				$oTable->addColumn(STR_CMMN_NAME, 'string');
				$oTable->render();
			}
		}
	}

	public function Edit($setid, $typeid)
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if ($typeid < 1 || $typeid > 4)
			throw new InvalidDataException();

		$typeText = '';
		$table = '';
		$model = null;
		switch($typeid)
		{
			case 1:
				$typeText = STR_ATTR_ACTIONS;
				$table = 'actions';
				$model = new ActionModel();
				break;
			case 2:
				$typeText = STR_ATTR_PRIORITIES;
				$table = 'priorities';
				$model = new PriorityModel();
				break;
			case 3:
				$typeText = STR_ATTR_SEVERITIES;
				$table = 'severities';
				$model = new SeverityModel();
				break;
			case 4:
				$typeText = STR_ATTR_STATUSES;
				$table = 'statuses';
				$model = new StatusModel();
				break;
			default:
		}

		$attrSetModel = new AttributeSetModel();
		if ($attrSetModel->Load($setid) == -1)
			throw new InvalidEntityException();

		$model->Query("SELECT id,name FROM $table ORDER BY name");
		$arrAll = $model->FetchAllRows();

		$arrSelected = array();
		$objMap = new AttributeSetMapModel();
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

		$t = new SmartyHelper();
		$t->assign('IS_WEIGHTED', ($typeid == 2 || $typeid == 3));
		$t->assign('VAL_NAME', $attrSetModel->name);
		$t->assign('VAL_TYPE', $typeText);
		$t->assign('VAL_SETID', $setid);
		$t->assign('VAL_TYPEID', $typeid);
		$t->assign('OPT_AVAILABLE', $htmlAvailable);
		$t->assign('OPT_SELECTED', $htmlSelected);

		$t->Render('htmlAttributesetmapping.tpl');
	}
}