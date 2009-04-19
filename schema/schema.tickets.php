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

$GLOBALS['phpgw_baseline']['tickets'] = array(
	'fd' => array(
		'ticketid' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
		'product' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'account' => array('type' => 'int', 'precision' => 4),
		'createdby' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'createdon' => array('type' => 'timestamp'),
		'responsible' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'closedby' => array('type' => 'int', 'precision' => 4),
		'closedon' => array('type' => 'timestamp'),
		'status' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'statuson' => array('type' => 'timestamp', 'nullable' => false),
		'lastactionon' => array('type' => 'timestamp'),
		'priority' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'type' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'issue' => array('type' => 'text', 'nullable' => false),
		'version' => array('type' => 'varchar', 'precision' => 20),
		'summary' => array('type' => 'varchar', 'precision' => 100, 'nullable' => false),
		'seconds' => array('type' => 'int', 'precision' => 4, 'default' => 0, 'nullable' => false),
		'module_id' => array('type' => 'int', 'precision' => 4),
		'entity_source_id' => array('type' => 'int', 'precision' => 4),
		'is_public' => array('type' => 'char', 'precision' => 1, 'nullable' => false, 'default' => 'N'),
		'contact_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
	),
	'pk' => array('ticketid'),
	'fk' => array(),
	'ix' => array(),
	'uc' => array()
);

$GLOBALS['phpgw_baseline']['tickets']['joins'] = array(
	'accounts' => 'tickets.account = dcl_org.org_id',
	'dcl_contact' => 'tickets.contact_id = dcl_contact.contact_id',
	'dcl_contact_addr' => "tickets.contact_id = dcl_contact_addr.contact_id AND dcl_contact_addr.preferred = 'Y'",
	'dcl_contact_email' => "tickets.contact_id = dcl_contact_email.contact_id AND dcl_contact_email.preferred = 'Y'",
	'dcl_contact_phone' => "tickets.contact_id = dcl_contact_phone.contact_id AND dcl_contact_phone.preferred = 'Y'",
	'dcl_contact_url' => "tickets.contact_id = dcl_contact_url.contact_id AND dcl_contact_url.preferred = 'Y'",
	'dcl_entity_hotlist' => 'dcl_entity_hotlist.entity_id = ' . DCL_ENTITY_TICKET . ' AND tickets.ticketid = dcl_entity_hotlist.entity_key_id',
	'dcl_entity_source' => 'tickets.entity_source_id = dcl_entity_source.entity_source_id',
	'dcl_entity_tag' => 'dcl_entity_tag.entity_id = ' . DCL_ENTITY_TICKET . ' AND tickets.ticketid = dcl_entity_tag.entity_key_id',
	'dcl_hotlist' => 'dcl_entity_hotlist.hotlist_id = dcl_hotlist.hotlist_id',
	'dcl_org' => 'tickets.account = dcl_org.org_id',
	'dcl_org_addr' => "tickets.account = dcl_org_addr.org_id AND dcl_org_addr.preferred = 'Y'",
	'dcl_org_email' => "tickets.account = dcl_org_email.org_id AND dcl_org_email.preferred = 'Y'",
	'dcl_org_phone' => "tickets.account = dcl_org_phone.org_id AND dcl_org_phone.preferred = 'Y'",
	'dcl_org_url' => "tickets.account = dcl_org_url.org_id AND dcl_org_url.preferred = 'Y'",
	'dcl_product_module' => 'tickets.module_id = dcl_product_module.product_module_id',
	'dcl_status_type' => '(tickets.status = statuses.id AND statuses.dcl_status_type = dcl_status_type.dcl_status_type_id)',
	'dcl_tag' => 'dcl_entity_tag.tag_id = dcl_tag.tag_id',
	'personnel a' => 'tickets.responsible = a.id',
	'personnel b' => 'tickets.closedby = b.id',
	'personnel c' => 'tickets.createdby = c.id',
	'priorities' => 'tickets.priority = priorities.id',
	'products' => 'tickets.product = products.id',
	'severities' => 'tickets.type = severities.id',
	'statuses' => 'tickets.status = statuses.id'
);