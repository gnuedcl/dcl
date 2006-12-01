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

LoadStringResource('db');
class dbSeverities extends dclDB
{
	function dbSeverities()
	{
		parent::dclDB();
		$this->TableName = 'severities';
		$this->cacheEnabled = true;
		
		LoadSchema($this->TableName);

		$this->foreignKeys = array('workorders' => 'severity');
		
		parent::Clear();
	}

	function Add()
	{
		$this->AdjustWeights($this->weight);
		return parent::Add();
	}

	function Edit()
	{
		$this->AdjustWeights($this->weight, $this->id);
		return parent::Edit();
	}

	function AdjustWeights($fromThisWeight, $editID = 0)
	{
		$query = "SELECT id FROM severities WHERE weight=$fromThisWeight";
		if ($editID > 0)
			$query .= " AND id != $editID";
			
		$this->Query($query);
		
		// There is one with this weight and not this ID, so adjust it
		if ($this->next_record())
		{
			$thisID = $this->f('id');
			$this->FreeResult();
			$this->AdjustWeights($fromThisWeight + 1);
			$query = "UPDATE severities SET weight=weight+1 WHERE id=$thisID";
			$this->Execute($query);
		}
	}

	function Delete()
	{
		return parent::Delete(array('id' => $this->id));
	}

	function Load($id)
	{
		return parent::Load(array('id' => $id));
	}
}
?>
