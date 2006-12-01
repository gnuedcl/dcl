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
class dbOrgContact extends dclDB
{
	function dbOrgContact()
	{
		parent::dclDB();
		$this->TableName = 'dcl_org_contact';
		LoadSchema($this->TableName);
		
		parent::Clear();
	}
	
	function updateOrgs($contact_id, &$aOrgID)
	{
		if (($contact_id = DCL_Sanitize::ToInt($contact_id)) === null)
			return PrintPermissionDenied();
			
		$aOrgID = DCL_Sanitize::ToIntArray($aOrgID);
		if ($aOrgID === null || count($aOrgID) == 0)
			$aOrgID = array("-1");
			
		$sOrgID = join(',', $aOrgID);
		
		$this->Execute("DELETE FROM dcl_org_contact WHERE contact_id = $contact_id AND org_id NOT IN ($sOrgID)");
		$this->Execute("INSERT INTO dcl_org_contact (org_id, contact_id, created_by) SELECT org_id, $contact_id, " . $GLOBALS['DCLID'] . " FROM dcl_org WHERE org_id IN ($sOrgID) AND org_id NOT IN (SELECT org_id FROM dcl_org_contact WHERE contact_id = $contact_id)");
	}
}
?>
