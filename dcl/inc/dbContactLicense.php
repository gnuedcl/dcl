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
class dbContactLicense extends dclDB
{
	function dbContactLicense()
	{
		parent::dclDB();
		$this->TableName = 'dcl_contact_license';
		LoadSchema($this->TableName);
		
		parent::Clear();
	}

	function ListByContact($contact_id)
	{
		if (($contact_id = DCL_Sanitize::ToInt($contact_id)) === null)
		{
			throw new InvalidDataException();
		}
		
		$sql = 'SELECT l.contact_license_id, l.contact_id, l.product_id, l.product_version, l.license_id, l.registered_on, l.expires_on, l.license_notes, p.name';
		$sql .= ' FROM ' . $this->TableName . ' l, products p WHERE l.contact_id = ' . $contact_id . ' AND p.id = l.product_id';
		$sql .= ' ORDER BY p.name, l.expires_on DESC, l.license_id';
		return $this->Query($sql);
	}
}
?>