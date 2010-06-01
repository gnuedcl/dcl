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

$setup_info['dcl'] = array();
$setup_info['dcl']['name']      = 'dcl';
$setup_info['dcl']['title']     = 'Double Choco Latte';
$setup_info['dcl']['version']   = '0.9.5RC14';
$setup_info['dcl']['enable']    = 1;
$setup_info['dcl']['app_order'] = 1;

$setup_info['dcl']['author'] = array
(
	'name'	=> 'Michael L. Dean',
	'email' => 'dcl-users@lists.sourceforge.net',
	'url'	=> 'dcl.sourceforge.net'
);

$setup_info['dcl']['maintainer'] = array
(
	'name'	=> 'Michael L. Dean',
	'email' => 'dcl-users@lists.sourceforge.net',
	'url'	=> 'dcl.sourceforge.net'
);

$setup_info['dcl']['description'] = 'Double Choco Latte - Project Management, Issue Tracking, Call Tracking';
$setup_info['dcl']['license'] = 'GPL';

$setup_info['dcl']['tables'] = array();
$setup_info['dcl']['tables'][] = 'actions';
$setup_info['dcl']['tables'][] = 'attributesetsmap';
$setup_info['dcl']['tables'][] = 'attributesets';
$setup_info['dcl']['tables'][] = 'dcl_addr_type';
$setup_info['dcl']['tables'][] = 'dcl_chklst';
$setup_info['dcl']['tables'][] = 'dcl_chklst_tpl';
$setup_info['dcl']['tables'][] = 'dcl_config';
$setup_info['dcl']['tables'][] = 'dcl_contact_addr';
$setup_info['dcl']['tables'][] = 'dcl_contact_email';
$setup_info['dcl']['tables'][] = 'dcl_contact_note';
$setup_info['dcl']['tables'][] = 'dcl_contact_phone';
$setup_info['dcl']['tables'][] = 'dcl_contact';
$setup_info['dcl']['tables'][] = 'dcl_contact_license';
$setup_info['dcl']['tables'][] = 'dcl_contact_type';
$setup_info['dcl']['tables'][] = 'dcl_contact_type_xref';
$setup_info['dcl']['tables'][] = 'dcl_contact_url';
$setup_info['dcl']['tables'][] = 'dcl_email_type';
$setup_info['dcl']['tables'][] = 'dcl_entity';
$setup_info['dcl']['tables'][] = 'dcl_entity_hotlist';
$setup_info['dcl']['tables'][] = 'dcl_entity_hotlist_audit';
$setup_info['dcl']['tables'][] = 'dcl_entity_perm';
$setup_info['dcl']['tables'][] = 'dcl_entity_source';
$setup_info['dcl']['tables'][] = 'dcl_entity_tag';
$setup_info['dcl']['tables'][] = 'dcl_hotlist';
$setup_info['dcl']['tables'][] = 'dcl_note_type';
$setup_info['dcl']['tables'][] = 'dcl_org_addr';
$setup_info['dcl']['tables'][] = 'dcl_org_alias';
$setup_info['dcl']['tables'][] = 'dcl_org_contact';
$setup_info['dcl']['tables'][] = 'dcl_org_email';
$setup_info['dcl']['tables'][] = 'dcl_org_note';
$setup_info['dcl']['tables'][] = 'dcl_org_phone';
$setup_info['dcl']['tables'][] = 'dcl_org';
$setup_info['dcl']['tables'][] = 'dcl_org_product_xref';
$setup_info['dcl']['tables'][] = 'dcl_org_type';
$setup_info['dcl']['tables'][] = 'dcl_org_type_xref';
$setup_info['dcl']['tables'][] = 'dcl_org_url';
$setup_info['dcl']['tables'][] = 'dcl_perm';
$setup_info['dcl']['tables'][] = 'dcl_phone_type';
$setup_info['dcl']['tables'][] = 'dcl_preferences';
$setup_info['dcl']['tables'][] = 'dcl_product_build';
$setup_info['dcl']['tables'][] = 'dcl_product_build_except';
$setup_info['dcl']['tables'][] = 'dcl_product_build_item';
$setup_info['dcl']['tables'][] = 'dcl_product_build_sccs';
$setup_info['dcl']['tables'][] = 'dcl_product_module';
$setup_info['dcl']['tables'][] = 'dcl_product_version';
$setup_info['dcl']['tables'][] = 'dcl_product_version_item';
$setup_info['dcl']['tables'][] = 'dcl_product_version_status';
$setup_info['dcl']['tables'][] = 'dcl_projects';
$setup_info['dcl']['tables'][] = 'dcl_projects_audit';
$setup_info['dcl']['tables'][] = 'dcl_role_perm';
$setup_info['dcl']['tables'][] = 'dcl_role';
$setup_info['dcl']['tables'][] = 'dcl_sccs';
$setup_info['dcl']['tables'][] = 'dcl_sccs_xref';
$setup_info['dcl']['tables'][] = 'dcl_sec_audit';
$setup_info['dcl']['tables'][] = 'dcl_session';
$setup_info['dcl']['tables'][] = 'dcl_status_type';
$setup_info['dcl']['tables'][] = 'dcl_tag';
$setup_info['dcl']['tables'][] = 'dcl_url_type';
$setup_info['dcl']['tables'][] = 'dcl_user_role';
$setup_info['dcl']['tables'][] = 'dcl_wiki';
$setup_info['dcl']['tables'][] = 'dcl_wo_account';
$setup_info['dcl']['tables'][] = 'dcl_wo_account_audit';
$setup_info['dcl']['tables'][] = 'dcl_wo_id';
$setup_info['dcl']['tables'][] = 'dcl_wo_task';
$setup_info['dcl']['tables'][] = 'dcl_wo_type';
$setup_info['dcl']['tables'][] = 'dcl_workspace_product';
$setup_info['dcl']['tables'][] = 'dcl_workspace_user';
$setup_info['dcl']['tables'][] = 'dcl_workspace';
$setup_info['dcl']['tables'][] = 'departments';
$setup_info['dcl']['tables'][] = 'faqanswers';
$setup_info['dcl']['tables'][] = 'faq';
$setup_info['dcl']['tables'][] = 'faqquestions';
$setup_info['dcl']['tables'][] = 'faqtopics';
$setup_info['dcl']['tables'][] = 'personnel';
$setup_info['dcl']['tables'][] = 'priorities';
$setup_info['dcl']['tables'][] = 'products';
$setup_info['dcl']['tables'][] = 'projectmap';
$setup_info['dcl']['tables'][] = 'projectmap_audit';
$setup_info['dcl']['tables'][] = 'severities';
$setup_info['dcl']['tables'][] = 'statuses';
$setup_info['dcl']['tables'][] = 'ticketresolutions';
$setup_info['dcl']['tables'][] = 'tickets';
$setup_info['dcl']['tables'][] = 'tickets_audit';
$setup_info['dcl']['tables'][] = 'timecards';
$setup_info['dcl']['tables'][] = 'views';
$setup_info['dcl']['tables'][] = 'watches';
$setup_info['dcl']['tables'][] = 'workorders';
$setup_info['dcl']['tables'][] = 'workorders_audit';
