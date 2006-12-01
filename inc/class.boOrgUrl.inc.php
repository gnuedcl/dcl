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

import('boAdminObject');
class boOrgUrl extends boAdminObject
{
	function boOrgUrl()
	{
		parent::boAdminObject();

		$this->oDB =& CreateObject('dcl.dbOrgUrl');
		$this->sKeyField = 'org_url_id';
		$this->Entity = DCL_ENTITY_ORG;
		$this->PermAdd = DCL_PERM_MODIFY;
		$this->PermDelete = DCL_PERM_MODIFY;

		$this->aIgnoreFieldsOnUpdate = array('created_on', 'created_by');
	}

	function add($aSource)
	{
		$aSource['preferred'] = @DCL_Sanitize::ToYN($aSource['preferred']);
		parent::add($aSource);
	}

	function modify($aSource)
	{
		$aSource['preferred'] = @DCL_Sanitize::ToYN($aSource['preferred']);
		$aSource['modified_by'] = $GLOBALS['DCLID'];
		$aSource['modified_on'] = 'now()';

		parent::modify($aSource);
	}
}
?>
