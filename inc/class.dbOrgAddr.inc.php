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
class dbOrgAddr extends dclDB
{
	function dbOrgAddr()
	{
		parent::dclDB();
		$this->TableName = 'dcl_org_addr';
		LoadSchema($this->TableName);
		
		parent::Clear();
	}
	
	function ListByOrg($org_id)
	{
		if (($org_id = DCL_Sanitize::ToInt($org_id)) === null)
		{
			trigger_error('Data sanitize failed.');
			return -1;
		}
		
		$sql = 'SELECT a.org_addr_id, a.org_id, a.addr_type_id, a.add1, a.add2, a.city, a.state, a.zip, a.country, a.preferred, t.addr_type_name';
		$sql .= ' FROM ' . $this->TableName . ' a, dcl_addr_type t WHERE a.org_id = ' . $org_id . ' AND t.addr_type_id = a.addr_type_id';
		$sql .= ' ORDER BY t.addr_type_name';
		return $this->Query($sql);
	}
}
?>