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

//LoadStringResource('db');
class PreferencesModel extends dclDB
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'dcl_preferences';
		$this->cacheEnabled = true;

		LoadSchema($this->TableName);

		$this->preferences_data = array();

		parent::Clear();
	}

	public function Add()
	{
		$this->preferences_data = serialize($this->preferences_data);
		$iRetVal = parent::Add();
		$this->preferences_data = unserialize($this->preferences_data);

		return $iRetVal;
	}

	public function Edit()
	{
		$this->preferences_data = serialize($this->preferences_data);
		$iRetVal = parent::Edit();
		$this->preferences_data = unserialize($this->preferences_data);

		return $iRetVal;
	}

	public function Delete()
	{
		return parent::Delete(array('personnel_id' => $this->personnel_id));
	}

	public function Load($id)
	{
		$iRetVal = parent::Load(array('personnel_id' => $id), false);
		if ($iRetVal != -1 && is_string($this->preferences_data))
			$this->preferences_data = unserialize($this->preferences_data);

		return $iRetVal;
	}

	public function Register($sName, $sValue)
	{
		$this->preferences_data[$sName] = $sValue;
	}

	public function Unregister($sName)
	{
		if (isset($this->preferences_data[$sName]))
			unset($this->preferences_data[$sName]);
	}

	public function Value($sName)
	{
		if (isset($this->preferences_data[$sName]))
			return $this->preferences_data[$sName];

		return null;
	}

	public function Clear()
	{
		parent::Clear();
		$this->preferences_data = array();
	}
}
