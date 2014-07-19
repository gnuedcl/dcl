<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2012 Free Software Foundation
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

$GLOBALS['phpgw_baseline']['dcl_error_log'] = array(
	'fd' => array(
		'error_log_id' => array('type' => 'auto', 'precision' => 8, 'nullable' => false),
		'error_timestamp' => array('type' => 'timestamp', 'nullable' => false),
		'user_id' => array('type' => 'int', 'precision' => 4),
		'server_name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
		'script_name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
		'request_uri' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
		'query_string' => array('type' => 'text'),
		'error_file' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
		'error_line' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'error_description' => array('type' => 'text', 'nullable' => false),
		'stack_trace' => array('type' => 'text'),
		'log_level' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
	),
	'pk' => array('error_log_id'),
	'fk' => array(),
	'ix' => array(),
	'uc' => array()
);

$GLOBALS['phpgw_baseline']['dcl_error_log']['joins'] = array(
	'personnel' => 'dcl_error_log.user_id=personnel.id'
);