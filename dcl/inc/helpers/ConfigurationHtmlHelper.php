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

class ConfigurationHtmlHelper
{
	public function GetLangCombo($sName, $sDefault)
	{
		$aOptions = array(
				'en' => 'English',
				'fr' => 'French',
				'de' => 'German',
				'it' => 'Italian',
				'ru' => 'Russian',
				'es' => 'Spanish',
				'sl' => 'Sloven��ina',
				'sv' => 'Swedish'
			);

		return $this->Select($sName, $aOptions, $sDefault);
	}
	
	private function Select($sName, $aOptions, $sDefault)
	{
		$retVal = '<select class="form-control" name="' . $sName . '">';

		if (is_array($aOptions))
		{
		    foreach ($aOptions as $key => $val)
			{
				$retVal .= '<option value="' . $key . '"';
				if ($key == $sDefault)
					$retVal .= ' selected';

				$retVal .= '>' . $val . '</option>';
			}
		}

		$retVal .= '</select>';

		return $retVal;
	}
}
