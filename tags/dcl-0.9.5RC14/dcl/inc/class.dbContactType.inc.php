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
class dbContactType extends dclDB
{
	function dbContactType()
	{
		parent::dclDB();
		$this->TableName = 'dcl_contact_type';
		LoadSchema($this->TableName);
		
		parent::Clear();
	}
	
	function ListByContact($contact_id)
	{
		if (($contact_id = DCL_Sanitize::ToInt($contact_id)) === null)
		{
			trigger_error('Data sanitize failed.');
			return -1;
		}
		
		$sql = 'SELECT u.contact_type_id, u.contact_id, t.contact_type_name';
		$sql .= ' FROM dcl_contact_type_xref u, dcl_contact_type t WHERE u.contact_id = ' . $contact_id . ' AND t.contact_type_id = u.contact_type_id';
		$sql .= ' ORDER BY t.contact_type_name';
		
		return $this->Query($sql);
	}

	function &GetTypes($contact_id = -1)
	{
		$sSQL = 'SELECT ct.contact_type_id, ct.contact_type_name, ctx.contact_id FROM dcl_contact_type ct ';
		$sSQL .= "LEFT JOIN dcl_contact_type_xref ctx ON ct.contact_type_id = ctx.contact_type_id AND ctx.contact_id = $contact_id ";
		$sSQL .= 'ORDER BY ct.contact_type_name';

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
?>