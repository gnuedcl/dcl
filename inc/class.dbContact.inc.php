<?php
/*
 * $Id: class.dbContact.inc.php,v 1.1.1.1 2006/11/27 05:30:50 mdean Exp $
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
class dbContact extends dclDB
{
	function dbContact()
	{
		parent::dclDB();
		$this->TableName = 'dcl_contact';
		LoadSchema($this->TableName);

		parent::Clear();
	}

	function GetOrgArray($contact_id)
	{
		if (($contact_id = DCL_Sanitize::ToInt($contact_id)) === null)
		{
			trigger_error('Data sanitize failed.');
			return -1;
		}
		
		$aRetVal = array();

		$sSQL = "SELECT org_id FROM dcl_org_contact WHERE contact_id = $contact_id";
		if ($this->Query($sSQL) != -1)
		{
			while ($this->next_record())
				$aRetVal[] = $this->f(0);
		}

		return $aRetVal;
	}
	
	function GetFirstOrg($contact_id)
	{
		if (($contact_id = DCL_Sanitize::ToInt($contact_id)) === null)
		{
			trigger_error('Data sanitize failed.');
			return -1;
		}
		
		$aRetVal = array();

		$sSQL = 'SELECT O.org_id, O.name FROM dcl_org O ' . $this->JoinKeyword . " dcl_org_contact OC ON O.org_id = OC.org_id WHERE OC.contact_id = $contact_id ORDER BY O.name";
		if ($this->LimitQuery($sSQL, 0, 1) != -1)
		{
			if ($this->next_record())
				$aRetVal = $this->Record;
		}

		return $aRetVal;
	}
}
?>