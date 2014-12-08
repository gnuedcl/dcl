<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2014 Free Software Foundation
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

$GLOBALS['phpgw_baseline']['dcl_sla_schedule_exception'] = array(
	'fd' => array(
		'sla_schedule_exception_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
		'sla_schedule_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
		'start_dt' => array('type' => 'timestamp', 'nullable' => true),
		'end_dt' => array('type' => 'timestamp', 'nullable' => true),
		'create_dt' => array('type' => 'timestamp', 'nullable' => false),
		'create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'update_dt' => array('type' => 'timestamp', 'nullable' => false),
		'update_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
	),
	'pk' => array('sla_schedule_exception_id'),
	'fk' => array(),
	'ix' => array(
		'ix_sla_sched_exc_sched_id' => array('sla_schedule_id')
	),
	'uc' => array()
);
