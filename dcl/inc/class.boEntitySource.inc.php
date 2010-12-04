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

class boEntitySource extends boAdminObject
{
	function boEntitySource()
	{
		$this->oDB =& CreateObject('dcl.dbEntitySource');
		$this->sKeyField = 'entity_source_id';
		$this->sDescField = 'entity_source_name';
		$this->Entity = DCL_ENTITY_SOURCE;
	}

	function ListSelect($active = '')
	{
		$sSQL = 'SELECT entity_source_id, entity_source_name FROM dcl_entity_source';
		if ($active != '')
			$sSQL .= ' WHERE active = ' . $this->oDB->Quote($active);

		$sSQL .= ' ORDER BY entity_source_name';

		return $this->oDB->Query($sSQL);
	}

	function ListSelectWithWorkOrder($wo_id, $seq, $active = '')
	{
		$sSQL = 'SELECT es.entity_source_id, es.entity_source_name, w.entity_source_id FROM dcl_entity_source es';
		$sSQL .= ' LEFT JOIN workorders w ON es.entity_source_id = w.entity_source_id';
		if ($active != '')
			$sSQL .= ' WHERE es.active = ' . $this->oDB->Quote($active);

		$sSQL .= ' ORDER BY es.entity_source_name';

		return $this->oDB->Query($sSQL);
	}
}
?>
