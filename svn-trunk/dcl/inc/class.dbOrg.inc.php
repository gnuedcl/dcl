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
class dbOrg extends dclDB
{
	function dbOrg()
	{
		parent::dclDB();
		$this->TableName = 'dcl_org';
		LoadSchema($this->TableName);
		
		parent::Clear();
	}
	
	function GetProductArray($aOrgID)
	{
		if (($aOrgID = DCL_Sanitize::ToIntArray($aOrgID)) === null)
		{
			trigger_error('Data sanitize failed.');
			return -1;
		}
		
		$aRetVal = array();
		$sOrgID = '-1';
		if (count($aOrgID) > 0)
			$sOrgID = join(',', $aOrgID);

		$sSQL = "SELECT DISTINCT product_id FROM dcl_org_product_xref WHERE org_id IN ($sOrgID)";
		if ($this->Query($sSQL) != -1)
		{
			while ($this->next_record())
				$aRetVal[] = $this->f(0);
		}

		return $aRetVal;
	}
	
	function ListMainContacts($org_id)
	{
		if (($org_id = DCL_Sanitize::ToInt($org_id)) === null)
			return;
		
		$sSQL = "SELECT DISTINCT C.last_name, C.first_name, C.contact_id
				FROM dcl_contact C 
				" . $this->JoinKeyword . " dcl_contact_type_xref CTX ON C.contact_id = CTX.contact_id 
				" . $this->JoinKeyword . " dcl_contact_type CT ON CTX.contact_type_id = CT.contact_type_id
				" . $this->JoinKeyword . " dcl_org_contact OC ON C.contact_id = OC.contact_id 
				WHERE OC.org_id = $org_id
				AND CT.contact_type_is_main = 'Y' 
				ORDER BY C.last_name, C.first_name, C.contact_id";
		
		$this->Query($sSQL);
	}
}
?>