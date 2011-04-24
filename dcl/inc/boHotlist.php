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

class boHotlist extends boAdminObject
{
	function boHotlist()
	{
		parent::boAdminObject();

		$this->oDB = new HotlistModel();
		$this->sKeyField = 'hotlist_id';
		$this->Entity = DCL_ENTITY_HOTLIST;

		$this->sCreatedDateField = 'created_on';
		$this->sCreatedByField = 'created_by';
		$this->sModifiedDateField = 'modified_on';
		$this->sModifiedByField = 'modified_by';
		
		$this->aIgnoreFieldsOnUpdate = array('created_on', 'created_by');
	}

	function add($aSource)
	{
		$aSource['active'] = @DCL_Sanitize::ToYN($aSource['active']);
		parent::add($aSource);
	}

	function modify($aSource)
	{
		$aSource['active'] = @DCL_Sanitize::ToYN($aSource['active']);
		parent::modify($aSource);
	}
}
