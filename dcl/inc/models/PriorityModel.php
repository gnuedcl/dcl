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

LoadStringResource('db');
class PriorityModel extends dclDB
{
	public function __construct()
	{
		parent::dclDB();
		$this->TableName = 'priorities';
		$this->cacheEnabled = true;
		
		LoadSchema($this->TableName);

		$this->foreignKeys = array('workorders' => 'priority');
		
		parent::Clear();
	}

	public function Add()
	{
		$this->AdjustWeights($this->weight);
		return parent::Add();
	}

	public function Edit()
	{
		$this->AdjustWeights($this->weight, $this->id);
		return parent::Edit();
	}

	private function AdjustWeights($fromThisWeight, $editID = 0)
	{
		if (($fromThisWeight = DCL_Sanitize::ToInt($fromThisWeight)) === null ||
			($editID = DCL_Sanitize::ToInt($editID)) === null)
		{
			throw new InvalidDataException();
		}
		
		$query = "SELECT id FROM priorities WHERE weight=$fromThisWeight";
		if ($editID > 0)
			$query .= " AND id != $editID";
		$this->Query($query);
		// There is one with this weight and not this ID, so adjust it
		if ($this->next_record())
		{
			$thisID = $this->f('id');
			$this->FreeResult();
			$this->AdjustWeights($fromThisWeight + 1);
			$query = "UPDATE priorities SET weight=weight+1 WHERE id=$thisID";
			$this->Execute($query);
		}
	}

	public function Delete(array $aSource)
	{
		return parent::Delete($aSource);
	}
}