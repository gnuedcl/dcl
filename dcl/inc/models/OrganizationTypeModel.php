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
class OrganizationTypeModel extends DbProvider
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'dcl_org_type';
		LoadSchema($this->TableName);

		parent::Clear();
	}
	
	function ListByOrg($org_id)
	{
		if (($org_id = Filter::ToInt($org_id)) === null)
		{
			throw new InvalidDataException();
		}
		
		$sql = 'SELECT u.org_type_id, u.org_id, t.org_type_name';
		$sql .= ' FROM dcl_org_type_xref u, dcl_org_type t WHERE u.org_id = ' . $org_id . ' AND t.org_type_id = u.org_type_id';
		$sql .= ' ORDER BY t.org_type_name';
		
		return $this->Query($sql);
	}

	function &GetTypes($org_id = -1)
	{
		$sSQL = 'SELECT ot.org_type_id, ot.org_type_name, otx.org_id FROM dcl_org_type ot ';
		$sSQL .= "LEFT JOIN dcl_org_type_xref otx ON ot.org_type_id = otx.org_type_id AND otx.org_id = $org_id ";
		$sSQL .= 'ORDER BY ot.org_type_name';

		if ($this->Query($sSQL) == -1)
			return null;

		$aRetVal = array();
		while ($this->next_record())
		{
			$iTypeID = (int)$this->f(0);
			$sType = $this->f(1);
			$bHasType = $this->f(2) != '' ? 'true' : 'false';

			$aRetVal[$iTypeID] = array('desc' => $sType, 'selected' => $bHasType);
		}

		return $aRetVal;
	}
}
