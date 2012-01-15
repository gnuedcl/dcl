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

$GLOBALS['phpgw_baseline']['dcl_org'] = array(
	'fd' => array(
		'org_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
		'name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
		'active' => array('type' => 'varchar', 'precision' => 1, 'default' => 'Y', 'nullable' => false),
		'parent' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
		'created_on' => array('type' => 'timestamp', 'nullable' => false),
		'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'modified_on' => array('type' => 'timestamp', 'nullable' => true),
		'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
	),
	'pk' => array('org_id'),
	'fk' => array(),
	'ix' => array(
		'ix_dcl_org_name_id' => array('name', 'org_id')
	),
	'uc' => array()
);

$GLOBALS['phpgw_baseline']['dcl_org']['joins'] = array(
	'dcl_org_contact' => 'dcl_org.org_id = dcl_org_contact.org_id',
	'dcl_org_addr' => "dcl_org.org_id = dcl_org_addr.org_id AND dcl_org_addr.preferred = 'Y'",
	'dcl_org_email' => "dcl_org.org_id = dcl_org_email.org_id AND dcl_org_email.preferred = 'Y'",
	'dcl_org_phone' => "dcl_org.org_id = dcl_org_phone.org_id AND dcl_org_phone.preferred = 'Y'",
	'dcl_org_url' => "dcl_org.org_id = dcl_org_url.org_id AND dcl_org_url.preferred = 'Y'"
);