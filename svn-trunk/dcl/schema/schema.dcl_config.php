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

$GLOBALS['phpgw_baseline']['dcl_config'] = array(
	'fd' => array(
		'dcl_config_name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
		'dcl_config_field' => array('type' => 'varchar', 'precision' => 30, 'nullable' => false),
		'dcl_config_int' => array('type' => 'int', 'precision' => 4),
		'dcl_config_double' => array('type' => 'float', 'precision' => 8),
		'dcl_config_date' => array('type' => 'date'),
		'dcl_config_datetime' => array('type' => 'timestamp'),
		'dcl_config_varchar' => array('type' => 'varchar', 'precision' => 255)
	),
	'pk' => array('dcl_config_name'),
	'fk' => array(),
	'ix' => array(),
	'uc' => array()
);
?>
