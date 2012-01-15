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

$GLOBALS['phpgw_baseline']['workorders'] = array(
	'fd' => array(
		'jcn' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'seq' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'product' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'createby' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'createdon' => array('type' => 'timestamp', 'nullable' => false),
		'closedby' => array('type' => 'int', 'precision' => 4),
		'closedon' => array('type' => 'date'),
		'status' => array('type' => 'int', 'precision' => 4),
		'statuson' => array('type' => 'timestamp', 'nullable' => false),
		'lastactionon' => array('type' => 'timestamp'),
		'deadlineon' => array('type' => 'date'),
		'eststarton' => array('type' => 'date'),
		'estendon' => array('type' => 'date'),
		'starton' => array('type' => 'date'),
		'esthours' => array('type' => 'float', 'precision' => 8),
		'totalhours' => array('type' => 'float', 'precision' => 8),
		'priority' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'severity' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'summary' => array('type' => 'varchar', 'precision' => 100, 'nullable' => false),
		'notes' => array('type' => 'text'),
		'description' => array('type' => 'text', 'nullable' => false),
		'responsible' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'reported_version_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
		'etchours' => array('type' => 'float', 'precision' => 8),
		'module_id' => array('type' => 'int', 'precision' => 4),
		'wo_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
		'entity_source_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
		'is_public' => array('type' => 'char', 'precision' => 1, 'nullable' => false, 'default' => 'N'),
		'contact_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
		'targeted_version_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
		'fixed_version_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
	),
	'pk' => array('jcn', 'seq'),
	'fk' => array(),
	'ix' => array(),
	'uc' => array()
);

$GLOBALS['phpgw_baseline']['workorders']['joins'] = array(
	'accounts' => 'dcl_wo_account.account_id = dcl_org.org_id',
	'dcl_contact' => 'workorders.contact_id = dcl_contact.contact_id',
	'dcl_contact_addr' => "workorders.contact_id = dcl_contact_addr.contact_id AND dcl_contact_addr.preferred = 'Y'",
	'dcl_contact_email' => "workorders.contact_id = dcl_contact_email.contact_id AND dcl_contact_email.preferred = 'Y'",
	'dcl_contact_phone' => "workorders.contact_id = dcl_contact_phone.contact_id AND dcl_contact_phone.preferred = 'Y'",
	'dcl_contact_url' => "workorders.contact_id = dcl_contact_url.contact_id AND dcl_contact_url.preferred = 'Y'",
	'dcl_entity_hotlist' => 'dcl_entity_hotlist.entity_id = ' . DCL_ENTITY_WORKORDER . ' AND workorders.jcn = dcl_entity_hotlist.entity_key_id AND workorders.seq = dcl_entity_hotlist.entity_key_id2',
	'dcl_entity_source' => 'workorders.entity_source_id = dcl_entity_source.entity_source_id',
	'dcl_entity_tag' => 'dcl_entity_tag.entity_id = ' . DCL_ENTITY_WORKORDER . ' AND workorders.jcn = dcl_entity_tag.entity_key_id AND workorders.seq = dcl_entity_tag.entity_key_id2',
	'dcl_hotlist' => 'dcl_entity_hotlist.hotlist_id = dcl_hotlist.hotlist_id',
	'dcl_org' => 'dcl_wo_account.account_id = dcl_org.org_id',
	'dcl_org_addr' => "dcl_wo_account.account_id = dcl_org_addr.org_id AND dcl_org_addr.preferred = 'Y'",
	'dcl_org_email' => "dcl_wo_account.account_id = dcl_org_email.org_id AND dcl_org_email.preferred = 'Y'",
	'dcl_org_phone' => "dcl_wo_account.account_id = dcl_org_phone.org_id AND dcl_org_phone.preferred = 'Y'",
	'dcl_org_url' => "dcl_wo_account.account_id = dcl_org_url.org_id AND dcl_org_url.preferred = 'Y'",
	'dcl_product_module' => 'workorders.module_id=dcl_product_module.product_module_id',
	'dcl_product_version d' => 'workorders.reported_version_id=d.product_version_id',
	'dcl_product_version e' => 'workorders.targeted_version_id=e.product_version_id',
	'dcl_product_version f' => 'workorders.fixed_version_id=f.product_version_id',
	'dcl_projects' => 'dcl_projects.projectid = projectmap.projectid',
	'dcl_status_type' => '(workorders.status = statuses.id AND statuses.dcl_status_type = dcl_status_type.dcl_status_type_id)',
	'dcl_tag' => 'dcl_entity_tag.tag_id = dcl_tag.tag_id',
	'dcl_wo_account' => 'workorders.jcn = dcl_wo_account.wo_id AND workorders.seq = dcl_wo_account.seq',
	'dcl_wo_type' => 'workorders.wo_type_id = dcl_wo_type.wo_type_id',
	'personnel a' => 'workorders.responsible=a.id',
	'personnel b' => 'workorders.closedby=b.id',
	'personnel c' => 'workorders.createby=c.id',
	'personnel g' => 'timecards.actionby=g.id',
	'priorities' => 'workorders.priority=priorities.id',
	'products' => 'workorders.product=products.id',
	'projectmap' => 'workorders.jcn = projectmap.jcn AND (workorders.seq = projectmap.seq OR projectmap.seq = 0)',
	'severities' => 'workorders.severity=severities.id',
	'statuses' => 'workorders.status=statuses.id',
	'timecards' => 'workorders.jcn = timecards.jcn AND workorders.seq = timecards.seq AND timecards.id = (select max(id) from timecards where jcn = workorders.jcn AND seq = workorders.seq)'
);

$GLOBALS['phpgw_baseline']['workorders']['aggregates'] = array(
	'count(*)' => array(
		'dcl_org' => 'select count(*) from dcl_wo_account where wo_id = workorders.jcn And seq = workorders.seq'
	)
);
