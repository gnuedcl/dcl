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
class OrganizationTypeXrefModel extends DbProvider
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'dcl_org_type_xref';
		LoadSchema($this->TableName);
		
		parent::Clear();
	}
	
	public function Delsert($organizationId, array $orgTypes)
	{
		$organizationId = @Filter::RequireInt($organizationId);
		$orgTypes = @Filter::RequireIntArray($orgTypes);
		
		$hasOrgTypes = $orgTypes !== null && count($orgTypes) > 0;
		$sql = 'DELETE FROM dcl_org_type_xref WHERE org_id = ' . $organizationId;
		if ($hasOrgTypes)
		{
			$sTypes = join(',', $orgTypes);
			$sql .= ' AND org_type_id NOT IN (' . $sTypes . ')';
		}

		$this->Execute($sql);
		if (!$hasOrgTypes)
			return;
		
		$this->org_id = $organizationId;
		
		foreach ($orgTypes as $org_type_id)
		{
			if (!$this->Exists(array('org_id' => $this->org_id, 'org_type_id' => $org_type_id)))
			{
				$this->org_type_id = $org_type_id;
				$this->Add();
			}
		}
	}
}
