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

LoadStringResource('db');

class SavedSearchesModel extends DbProvider
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'views';
		LoadSchema($this->TableName);
		
		parent::Clear();
	}

	public function ListByUser($user_id, $entity_id)
	{
		$sTable = 'workorders';
		if ($entity_id == DCL_ENTITY_TICKET)
			$sTable = 'tickets';
			
		return $this->Query('SELECT ' . $this->SelectAllColumns() . ' FROM ' . $this->TableName . ' WHERE whoid = ' . $user_id . " AND tablename = " . $this->Quote($sTable) . ' ORDER BY name'); 
	}
}
