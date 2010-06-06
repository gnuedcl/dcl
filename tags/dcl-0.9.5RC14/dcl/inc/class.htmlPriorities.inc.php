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

LoadStringResource('prio');

class htmlPriorities
{
	function GetCombo($default = 0, $cbName = 'priority', $longShort = 'name', $size = 0, $activeOnly = true, $setid = 0)
	{
		$query = "SELECT a.id,a.$longShort FROM priorities a";

		if ($setid > 0)
		{
			$query .= ",attributesetsmap b WHERE a.id=b.keyid AND b.typeid=2 AND b.setid=$setid";

			if ($activeOnly)
				$query .= ' AND a.active=\'Y\'';

			$query .= ' ORDER BY b.weight';
		}
		else
		{
			if ($activeOnly)
				$query .= ' WHERE a.active=\'Y\'';

			$query .= ' ORDER BY a.name';
		}

		$oSelect = CreateObject('dcl.htmlSelect');
		$oSelect->vDefault = $default;
		$oSelect->sName = $cbName;
		$oSelect->iSize = $size;
		$oSelect->sZeroOption = STR_CMMN_SELECTONE;
		$oSelect->SetFromQuery($query);

		return $oSelect->GetHTML();
	}

	function PrintAll($orderBy = 'name')
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_PRIORITY, DCL_PERM_VIEW))
			return PrintPermissionDenied();

		$objDBPriority = CreateObject('dcl.dbPriorities');
		$objDBPriority->Query("SELECT id,active,short,name,weight FROM priorities ORDER BY $orderBy");
		$allRecs = $objDBPriority->FetchAllRows();

		$oTable =& CreateObject('dcl.htmlTable');
		$oTable->setCaption(sprintf(STR_PRIO_TABLETITLE, $orderBy));
		$oTable->addColumn(STR_PRIO_ID, 'numeric');
		$oTable->addColumn(STR_PRIO_ACTIVE, 'string');
		$oTable->addColumn(STR_PRIO_SHORT, 'string');
		$oTable->addColumn(STR_PRIO_NAME, 'string');
		$oTable->addColumn(STR_PRIO_WEIGHT, 'numeric');

		if ($g_oSec->HasPerm(DCL_ENTITY_PRIORITY, DCL_PERM_ADD))
			$oTable->addToolbar(menuLink('', 'menuAction=boPriorities.add'), STR_CMMN_NEW);

		if ($g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW))
			$oTable->addToolbar(menuLink('', 'menuAction=boAdmin.ShowSystemConfig'), DCL_MENU_SYSTEMSETUP);

		if (count($allRecs) > 0 && $g_oSec->HasAnyPerm(array(DCL_ENTITY_PRIORITY => array($g_oSec->PermArray(DCL_PERM_MODIFY), $g_oSec->PermArray(DCL_PERM_DELETE)))))
		{
			$oTable->addColumn(STR_PRIO_OPTIONS, 'html');
			$allName[] = STR_PRIO_OPTIONS;
			for ($i = 0; $i < count($allRecs); $i++)
			{
				$options = '';
				if ($g_oSec->HasPerm(DCL_ENTITY_PRIORITY, DCL_PERM_MODIFY))
					$options = '<a href="' . menuLink('', 'menuAction=boPriorities.modify&id=' . $allRecs[$i][0]) . '">' . htmlentities(STR_CMMN_EDIT) . '</a>';

				if ($g_oSec->HasPerm(DCL_ENTITY_PRIORITY, DCL_PERM_DELETE))
				{
					if ($options != '')
						$options .= '&nbsp;|&nbsp;';

					$options .= '<a href="' . menuLink('', 'menuAction=boPriorities.delete&id=' . $allRecs[$i][0]) . '">' . htmlentities(STR_CMMN_DELETE) . '</a>';
				}

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
		if (!$g_oSec->HasPerm(DCL_ENTITY_PRIORITY, $isEdit ? DCL_PERM_MODIFY : DCL_PERM_ADD))
			return PrintPermissionDenied();

		$t = CreateSmarty();

		if ($isEdit)
		{
			$t->assign('TXT_FUNCTION', STR_PRIO_EDIT);
			$t->assign('menuAction', 'boPriorities.dbmodify');
			$t->assign('id', $obj->id);
			$t->assign('CMB_ACTIVE', GetYesNoCombo($obj->active, 'active', 0, false));
			$t->assign('VAL_SHORT', $obj->short);
			$t->assign('VAL_NAME', $obj->name);
			$t->assign('VAL_WEIGHT', $obj->weight);
		}
		else
		{
			$t->assign('TXT_FUNCTION', STR_PRIO_ADD);
			$t->assign('menuAction', 'boPriorities.dbadd');
			$t->assign('CMB_ACTIVE', GetYesNoCombo('Y', 'active', 0, false));
		}

		SmartyDisplay($t, 'htmlPrioritiesForm.tpl');
	}
}
?>
