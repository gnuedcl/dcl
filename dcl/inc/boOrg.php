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

LoadStringResource('bo');

class boOrg extends boAdminObject
{
	function boOrg()
	{
		parent::boAdminObject();

		$this->oDB = new OrganizationModel();
		$this->sKeyField = 'org_id';
		$this->Entity = DCL_ENTITY_ORG;

		$this->sCreatedDateField = 'created_on';
		$this->sCreatedByField = 'created_by';
		$this->sModifiedDateField = 'modified_on';
		$this->sModifiedByField = 'modified_by';
		
		$this->aIgnoreFieldsOnUpdate = array('created_on', 'created_by');
	}

	function modify($aSource)
	{
		global $g_oSec;
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$aSource['active'] = @DCL_Sanitize::ToYN($aSource['active']);
		parent::modify($aSource);
		
		$sTypes = join(',', $aSource['org_type_id']);
		$sql = 'DELETE FROM dcl_org_type_xref WHERE org_id = ' . $aSource['org_id'];
		if (count($aSource['org_type_id']) > 0)
			$sql .= ' AND org_type_id NOT IN (' . $sTypes . ')';

		$this->oDB->Execute($sql);
		
		$oOrgTypeXref = new boOrgTypeXref();
		foreach ($aSource['org_type_id'] as $org_type_id)
		{
			if (!$oOrgTypeXref->exists(array('org_id' => $aSource['org_id'], 'org_type_id' => $org_type_id)))
				$oOrgTypeXref->Add(array('org_id' => $aSource['org_id'], 'org_type_id' => $org_type_id));
		}
	}
	
	function delete($aSource)
	{
		global $g_oSec;
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		if (($id = @DCL_Sanitize::ToInt($aSource['org_id'])) === null)
		{
			throw new InvalidDataException();
		}		
		
		if (!$this->oDB->HasFKRef($id))
		{
			$this->oDB->Execute("DELETE FROM dcl_org_addr WHERE org_id = $id");
			$this->oDB->Execute("DELETE FROM dcl_org_alias WHERE org_id = $id");
			$this->oDB->Execute("DELETE FROM dcl_org_contact WHERE org_id = $id");
			$this->oDB->Execute("DELETE FROM dcl_org_email WHERE org_id = $id");
			$this->oDB->Execute("DELETE FROM dcl_org_note WHERE org_id = $id");
			$this->oDB->Execute("DELETE FROM dcl_org_phone WHERE org_id = $id");
			$this->oDB->Execute("DELETE FROM dcl_org_type_xref WHERE org_id = $id");
		}
		
		parent::delete($aSource);
	}

	function ListSelected($id)
	{
		if (($id = @DCL_Sanitize::ToIntArray($id)) === null)
		{
			throw new InvalidDataException();
		}
		
		$sSQL = 'SELECT org_id, name FROM dcl_org WHERE org_id IN (' . join(',', $id) . ') ORDER BY name';

		return $this->oDB->Query($sSQL);
	}

	function ListSelectedByWorkOrder($jcn, $seq)
	{
		if (($jcn = @DCL_Sanitize::ToInt($jcn)) === null)
		{
			throw new InvalidDataException();
		}
		
		if (($seq = @DCL_Sanitize::ToInt($seq)) === null)
		{
			throw new InvalidDataException();
		}
		
		$sSQL = "SELECT o.org_id, o.name FROM dcl_org o, dcl_wo_account w WHERE o.org_id = w.account_id AND w.wo_id = $jcn AND w.seq = $seq ORDER BY o.name";

		return $this->oDB->Query($sSQL);
	}

	function ListSelectedByTicket($ticketid)
	{
		if (($ticketid = @DCL_Sanitize::ToInt($ticketid)) === null)
		{
			throw new InvalidDataException();
		}
		
		$sSQL = "SELECT o.org_id, o.name FROM dcl_org o, tickets t WHERE o.org_id = t.account AND t.ticketid = $ticketid ORDER BY o.name";

		return $this->oDB->Query($sSQL);
	}
}
