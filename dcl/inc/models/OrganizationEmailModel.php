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
class OrganizationEmailModel extends DbProvider
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'dcl_org_email';
		LoadSchema($this->TableName);

		parent::Clear();
	}

	public function ListByOrg($org_id)
	{
		if (($org_id = Filter::ToInt($org_id)) === null)
		{
			throw new InvalidDataException();
		}
		
		$sql = 'SELECT e.org_email_id, e.org_id, e.email_type_id, e.email_addr, e.preferred, t.email_type_name';
		$sql .= ' FROM ' . $this->TableName . ' e, dcl_email_type t WHERE e.org_id = ' . $org_id . ' AND t.email_type_id = e.email_type_id';
		$sql .= ' ORDER BY t.email_type_name';
		return $this->Query($sql);
	}

	public function GetPrimaryEmail($iOrgID)
	{
		if (($iOrgID = Filter::ToInt($iOrgID)) === null)
		{
			throw new InvalidDataException();
		}
		
		if ($this->Query("SELECT et.email_type_name, e.email_addr FROM dcl_org_email e, dcl_email_type et WHERE e.email_type_id = et.email_type_id AND e.org_id = $iOrgID AND e.preferred = 'Y'") != -1)
		{
			return $this->next_record();
		}

		return false;
	}
}
