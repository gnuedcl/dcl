<?php
/*
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

class AttributeSetHtmlHelper
{
	function GetCombo($default = 0, $cbName = 'setid', $longShort = 'name', $size = 0, $activeOnly = true)
	{
		$obj = new AttributeSetModel();
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
}
