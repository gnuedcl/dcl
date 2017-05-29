<?php
/*
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

class boWorkspace extends boAdminObject
{
	function __construct()
	{
		parent::__construct();

		$this->oDB = new WorkspaceModel();
		$this->sKeyField = 'workspace_id';
		$this->Entity = DCL_ENTITY_WORKSPACE;

		$this->sCreatedDateField = 'created_on';
		$this->sCreatedByField = 'created_by';
		$this->sModifiedDateField = 'modified_on';
		$this->sModifiedByField = 'modified_by';
		
		$this->aIgnoreFieldsOnUpdate = array('created_on', 'created_by');
	}

	function add($aSource)
	{
		$aSource['active'] = @Filter::ToYN($aSource['active']);
		parent::add($aSource);
		
		if ($this->oDB->workspace_id > 0)
		{
			$oWSP = new WorkspaceProductModel();
			$oWSP->serialize($this->oDB->workspace_id, $aSource['products'], true);
						
			$oWSU = new WorkspaceUserModel();
			$oWSU->serialize($this->oDB->workspace_id, $aSource['users'], true);
		}
	}

	function modify($aSource)
	{
		$aSource['active'] = @Filter::ToYN($aSource['active']);
		parent::modify($aSource);

		$oWSP = new WorkspaceProductModel();
		$oWSP->serialize($aSource['workspace_id'], $aSource['products'], false);
						
		$oWSU = new WorkspaceUserModel();
		$oWSU->serialize($this->oDB->workspace_id, $aSource['users'], false);
	}
}
