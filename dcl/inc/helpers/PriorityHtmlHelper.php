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

LoadStringResource('prio');

class PriorityHtmlHelper
{
	public function Select($default = 0, $cbName = 'priority', $longShort = 'name', $size = 0, $activeOnly = true, $setid = 0)
	{
		$query = "SELECT a.id, a.$longShort, NULL FROM priorities a";

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

		$oSelect = new SelectHtmlHelper();
		$oSelect->DefaultValue = $default;
		$oSelect->Id = $cbName;
		$oSelect->Size = $size;
		$oSelect->FirstOption = STR_CMMN_SELECTONE;
		$oSelect->SetFromQuery($query);

		return $oSelect->GetHTML();
	}
}