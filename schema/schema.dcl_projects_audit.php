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

$GLOBALS['phpgw_baseline']['dcl_projects_audit'] = array(
	'fd' => array(
		'projectid' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'name' => array('type' => 'varchar', 'precision' => 100),
		'reportto' => array('type' => 'int', 'precision' => 4),
		'createdby' => array('type' => 'int', 'precision' => 4),
		'createdon' => array('type' => 'timestamp'),
		'projectdeadline' => array('type' => 'date'),
		'description' => array('type' => 'text'),
		'status' => array('type' => 'int', 'precision' => 4),
		'lastactivity' => array('type' => 'timestamp'),
		'finalclose' => array('type' => 'date'),
		'parentprojectid' => array('type' => 'int', 'precision' => 4),
		'audit_on' => array('type' => 'timestamp', 'nullable' => false),
		'audit_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'audit_version' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
	),
	'pk' => array('projectid', 'audit_version'),
	'fk' => array(),
	'ix' => array(),
	'uc' => array()
);
?>
