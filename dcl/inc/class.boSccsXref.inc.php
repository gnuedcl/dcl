<?php
/*
 * $Id: class.boOrgUrl.inc.php 45 2007-02-19 19:46:28Z mdean $
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

class boSccsXref extends boAdminObject
{
	function boSccsXref()
	{
		parent::boAdminObject();

		$this->oDB = new dbSccsXref();
		$this->sKeyField = 'dcl_sccs_xref_id';
		$this->Entity = DCL_ENTITY_CHANGELOG;
	}
	
	function add()
	{
		$aSource = array(
				'dcl_entity_type_id' => $_REQUEST['dcl_entity_type_id'],
				'dcl_entity_id' => $_REQUEST['dcl_entity_id'],
				'dcl_entity_id2' => $_REQUEST['dcl_entity_id2'],
				'dcl_sccs_id' => $_REQUEST['dcl_sccs_id'],
				'personnel_id' => $_REQUEST['personnel_id'],
				'sccs_project_path' => $_REQUEST['sccs_project_path'],
				'sccs_file_name' => $_REQUEST['sccs_file_name'],
				'sccs_version' => $_REQUEST['sccs_version'],
				'sccs_comments' => $_REQUEST['sccs_comments'],
				'sccs_checkin_on' => 'now()'
			);
		
		parent::add($aSource);
	}
}
