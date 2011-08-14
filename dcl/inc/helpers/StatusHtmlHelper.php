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

class StatusHtmlHelper
{
	public function Select($default = 0, $cbName = 'status', $longShort = 'name', $size = 0, $activeOnly = true, $setid = 0, $zeroOption = '_SELECT_ONE_')
	{
		$query = "SELECT a.id,a.$longShort FROM statuses a ";

		if ($setid > 0)
		{
			$query .= ",attributesetsmap b WHERE a.id=b.keyid AND b.typeid=4 AND b.setid=$setid ";
			if ($activeOnly)
				$query .= ' AND a.active=\'Y\' ';
		}
		else
		{
			if ($activeOnly)
				$query .= 'WHERE a.active=\'Y\' ';
		}

		$query .= "ORDER BY $longShort";

		$oSelect = new SelectHtmlHelper();
		$oSelect->DefaultValue = $default;
		$oSelect->Id = $cbName;
		$oSelect->Size = $size;

		if ($zeroOption == '_SELECT_ONE_')
			$oSelect->FirstOption = STR_CMMN_SELECTONE;
		else
			$oSelect->FirstOption = $zeroOption;

		$oSelect->SetFromQuery($query);

		return $oSelect->GetHTML();
	}

	public function SelectType($default = 0)
	{
		$oSelect = new SelectHtmlHelper();
		$oSelect->SetOptionsFromDb('dcl_status_type', 'dcl_status_type_id', 'dcl_status_type_name', '', $order = 'dcl_status_type_id');
		$oSelect->DefaultValue = $default;
		$oSelect->Id = 'dcl_status_type';
		$oSelect->FirstOption = STR_CMMN_SELECTONE;

		return $oSelect->GetHTML();
	}
}
