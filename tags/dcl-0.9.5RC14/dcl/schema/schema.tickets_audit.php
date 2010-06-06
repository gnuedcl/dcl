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

$GLOBALS['phpgw_baseline']['tickets_audit'] = array(
	'fd' => array(
		'ticketid' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'product' => array('type' => 'int', 'precision' => 4),
		'account' => array('type' => 'int', 'precision' => 4),
		'createdby' => array('type' => 'int', 'precision' => 4),
		'createdon' => array('type' => 'timestamp'),
		'responsible' => array('type' => 'int', 'precision' => 4),
		'closedby' => array('type' => 'int', 'precision' => 4),
		'closedon' => array('type' => 'timestamp'),
		'status' => array('type' => 'int', 'precision' => 4),
		'statuson' => array('type' => 'timestamp'),
		'lastactionon' => array('type' => 'timestamp'),
		'priority' => array('type' => 'int', 'precision' => 4),
		'type' => array('type' => 'int', 'precision' => 4),
		'issue' => array('type' => 'text'),
		'version' => array('type' => 'varchar', 'precision' => 20),
		'summary' => array('type' => 'varchar', 'precision' => 100),
		'seconds' => array('type' => 'int', 'precision' => 4),
		'module_id' => array('type' => 'int', 'precision' => 4),
		'entity_source_id' => array('type' => 'int', 'precision' => 4),
		'is_public' => array('type' => 'char', 'precision' => 1),
		'contact_id' => array('type' => 'int', 'precision' => 4),
		'audit_on' => array('type' => 'timestamp', 'nullable' => false),
		'audit_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'audit_version' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
	),
	'pk' => array('ticketid', 'audit_version'),
	'fk' => array(),
	'ix' => array(),
	'uc' => array()
);
?>
