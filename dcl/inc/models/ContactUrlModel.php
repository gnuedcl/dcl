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
class ContactUrlModel extends dclDB
{
	public function __construct()
	{
		parent::dclDB();
		$this->TableName = 'dcl_contact_url';
		LoadSchema($this->TableName);

		parent::Clear();
	}

	function ListByContact($contact_id)
	{
		if (($contact_id = Filter::ToInt($contact_id)) === null)
		{
			throw new InvalidDataException();
		}
		
		$sql = 'SELECT u.contact_url_id, u.contact_id, u.url_type_id, u.url_addr, u.preferred, t.url_type_name';
		$sql .= ' FROM ' . $this->TableName . ' u, dcl_url_type t WHERE u.contact_id = ' . $contact_id . ' AND t.url_type_id = u.url_type_id';
		$sql .= ' ORDER BY t.url_type_name';
		return $this->Query($sql);
	}

	function GetPrimaryUrl($contact_id)
	{
		if (($contact_id = Filter::ToInt($contact_id)) === null)
		{
			throw new InvalidDataException();
		}
		
		if ($this->Query("SELECT ut.url_type_name, u.url_addr FROM dcl_contact_url u, dcl_url_type ut WHERE u.url_type_id = ut.url_type_id AND u.contact_id = $contact_id AND preferred = 'Y'") != -1)
		{
			return $this->next_record();
		}

		return false;
	}
}
