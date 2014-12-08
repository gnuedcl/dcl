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

LoadStringResource('prod');

class ProductHtmlHelper
{
	function Select($default = 0, $cbName = 'product', $longShort = 'name', $reportTo = 0, $size = 0, $activeOnly = true, $inputHandler = false)
	{
		global $g_oSec, $g_oSession;

		$objDBProducts = new ProductModel();
		$objDBProducts->cacheEnabled = false;
		$whereClause = '';

		if ($reportTo > 0 || $activeOnly == true)
		{
			$whereClause = ' WHERE ';
			if ($reportTo > 0)
				$whereClause = " reportto=$reportTo";

			if ($activeOnly == true)
			{
				if ($reportTo > 0)
					$whereClause .= ' AND';
				$whereClause .= ' active=\'Y\'';
			}
		}

		if ($g_oSec->IsPublicUser())
		{
			if ($whereClause != '')
				$whereClause .= ' AND';
			else
				$whereClause = ' WHERE';

			$whereClause .= " is_public = 'Y'";
		}

		if ($g_oSec->IsOrgUser() || $g_oSession->IsInWorkspace())
		{
			if ($whereClause != '')
				$whereClause .= ' AND';
			else
				$whereClause = ' WHERE';

			$whereClause .= ' id IN (' . join(',', $g_oSession->GetProductFilter()) . ')';
		}

		$objDBProducts->Query("SELECT id, $longShort, NULL FROM products " . $whereClause . " ORDER BY $longShort");

		$o = new SelectHtmlHelper();
		$o->DefaultValue = $default;
		$o->Id = $cbName;
		$o->Size = $size;
		$o->FirstOption = STR_CMMN_SELECTONE;
		$o->Options = $objDBProducts->FetchAllRows();
		$objDBProducts->FreeResult();
		if ($inputHandler)
			$o->OnChange = 'productSelChange(this.form);';

		return $o->GetHTML();
	}
}