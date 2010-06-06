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

class htmlAttributesetdetail
{
	function Show($obj)
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, DCL_PERM_VIEW))
			return PrintPermissionDenied();

		if (!is_object($obj))
		{
			trigger_error('[htmlAttributesets::Show] ' . STR_ATTR_OBJECTNOTPASSED);
			return;
		}
		
		$t = CreateSmarty();
		$t->assign('VAL_ATTRIBUTESETNAME', $obj->name);
		SmartyDisplay($t, 'htmlAttributesetdetail.tpl');

		$objA = CreateObject('dcl.dbActions');
		$theAttributes = array('actions', 'priorities', 'severities', 'statuses');
		$aTitles = array('actions' => STR_ATTR_ACTIONS, 'priorities' => STR_ATTR_PRIORITIES, 'severities' => STR_ATTR_SEVERITIES, 'statuses' => STR_ATTR_STATUSES);

		for ($cnt = 0; $cnt < count($theAttributes); $cnt++)
		{
			$typeid = $cnt + 1;
			$section = $theAttributes[$cnt];

			$query = 'SELECT a.name FROM ' . $section . ' a, attributesetsmap b WHERE a.id=b.keyid ';
			$query .= ' AND b.setid=' . $obj->id;
			$query .= ' AND b.typeid=' . $typeid;
			$query .= ' ORDER BY ';
			if ($section == 'priorities' || $section == 'severities')
				$query .= 'b.weight';
			else
				$query .= 'a.name';

			if ($objA->Query($query) != -1)
			{
				$oTable = CreateObject('dcl.htmlTable');
				$oTable->setCaption($aTitles[$theAttributes[$cnt]]);
				$oTable->setInline(true);
				
				$oTable->addToolbar(menuLink('', 'menuAction=boAttributesets.showall'), STR_ATTR_ATTRIBUTESET);
				if ($g_oSec->HasPerm(DCL_ENTITY_ATTRIBUTESETS, DCL_PERM_MODIFY))
					$oTable->addToolbar(menuLink('', 'menuAction=boAttributesets.showmapping&setid=' . $obj->id . '&typeid=' . $typeid), STR_ATTR_MAP);

				$oTable->setData($objA->FetchAllRows());
				$oTable->addColumn(STR_CMMN_NAME, 'string');
				$oTable->render();
			}
		}
	}
}
?>
