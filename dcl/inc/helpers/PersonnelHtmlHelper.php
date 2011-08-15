<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2011 Free Software Foundation
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

class PersonnelHtmlHelper
{
	public function Select($default = 0, $cbName = 'responsible', $longShort = 'short', $size = 0, $activeOnly = true, $minsec = 0, $projectid = 0)
	{
		$objDBPersonnel = new PersonnelModel();
		$objDBPersonnel->cacheEnabled = false;

		if ($projectid > 0)
		{
			// Show people in the project only
			$query = "SELECT DISTINCT personnel.id, personnel.short FROM personnel ";
			$query .= ', workorders a, projectmap b WHERE ';
			$query .= "a.jcn=b.jcn AND (b.seq=0 OR a.seq=b.seq) AND b.projectid=$projectid ";
			$query .= 'AND id=a.responsible ';
		}
		else
		{
			$query = 'select distinct p.id, p.short from personnel p join dcl_user_role ur on p.id = ur.personnel_id ';
			$query .= 'join dcl_role_perm rp on ur.role_id = rp.role_id where ((entity_id = ';
			$query .= DCL_ENTITY_WORKORDER . ' and perm_id = ' . DCL_PERM_ACTION . ') or (entity_id = ';
			$query .= DCL_ENTITY_GLOBAL . ' and perm_id = ' . DCL_PERM_ADMIN . '))';

			if ($activeOnly)
				$query .= " AND p.active = 'Y' ";
		}

		$query .= "ORDER BY short";
		$objDBPersonnel->Query($query);

		$oSelect = new SelectHtmlHelper();
		$oSelect->DefaultValue = $default;
		$oSelect->Id = $cbName;
		$oSelect->Size = $size;
		$oSelect->FirstOption = STR_CMMN_SELECTONE;
		$oSelect->CastToInt = true;

		while ($objDBPersonnel->next_record())
			$oSelect->AddOption($objDBPersonnel->f(0), $objDBPersonnel->f(1));

		return $oSelect->GetHTML();
	}
}