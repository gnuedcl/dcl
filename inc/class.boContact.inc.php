<?php
/*
 * $Id: class.boContact.inc.php,v 1.1.1.1 2006/11/27 05:30:51 mdean Exp $
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

LoadStringResource('bo');

import('boAdminObject');
class boContact extends boAdminObject
{
	function boContact()
	{
		parent::boAdminObject();
		
		$this->oDB =& CreateObject('dcl.dbContact');
		$this->sKeyField = 'contact_id';
		$this->Entity = DCL_ENTITY_CONTACT;
		
		$this->aIgnoreFieldsOnUpdate = array('created_on', 'created_by');
	}

	function modify($aSource)
	{
		global $g_oSec;
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		if (!isset($aSource['active']) || $aSource['active'] != 'Y')
			$aSource['active'] = 'N';

		$aSource['modified_on'] = 'now()';
		$aSource['modified_by'] = $GLOBALS['DCLID'];

		parent::modify($aSource);
		
		$sTypes = join(',', $aSource['contact_type_id']);
		$sql = 'DELETE FROM dcl_contact_type_xref WHERE contact_id = ' . $aSource['contact_id'];
		if (count($aSource['contact_type_id']) > 0)
			$sql .= ' AND contact_type_id NOT IN (' . $sTypes . ')';

		$this->oDB->Execute($sql);
		
		$oContactTypeXref =& CreateObject('dcl.boContactTypeXref');
		foreach ($aSource['contact_type_id'] as $contact_type_id)
		{
			if (!$oContactTypeXref->exists(array('contact_id' => $aSource['contact_id'], 'contact_type_id' => $contact_type_id)))
				$oContactTypeXref->Add(array('contact_id' => $aSource['contact_id'], 'contact_type_id' => $contact_type_id));
		}
	}
	
	function delete($aSource)
	{
		global $g_oSec;
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_DELETE))
			return PrintPermissionDenied();

		if (($id = @DCL_Sanitize::ToInt($aSource['contact_id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}		
		
		if (!$this->oDB->HasFKRef($id))
		{
			$this->oDB->Execute("DELETE FROM dcl_contact_addr WHERE contact_id = $id");
			$this->oDB->Execute("DELETE FROM dcl_org_contact WHERE contact_id = $id");
			$this->oDB->Execute("DELETE FROM dcl_contact_email WHERE contact_id = $id");
			$this->oDB->Execute("DELETE FROM dcl_contact_note WHERE contact_id = $id");
			$this->oDB->Execute("DELETE FROM dcl_contact_phone WHERE contact_id = $id");
			$this->oDB->Execute("DELETE FROM dcl_contact_type_xref WHERE contact_id = $id");
		}
		
		parent::delete($aSource);
	}
}
?>
