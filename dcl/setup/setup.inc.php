<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2013 Free Software Foundation
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

$setup_info['dcl'] = array(
    'name' => 'dcl',
    'title' => 'Double Choco Latte',
    'version' => '0.9.5RC20',
    'enable' => 1,
    'app_order' => 1,
    'author' => array(
        'name'	=> 'Michael L. Dean',
        'email' => 'dcl-users@lists.sourceforge.net',
        'url'	=> 'dcl.sourceforge.net'
    ),
    'maintainer' => array(
        'name'	=> 'Michael L. Dean',
        'email' => 'dcl-users@lists.sourceforge.net',
        'url'	=> 'dcl.sourceforge.net'
    ),
    'description' => 'Double Choco Latte - Project Management, Issue Tracking, Call Tracking',
    'license' => 'GPL',
    'tables' => array(
        'actions',
        'attributesetsmap',
        'attributesets',
        'dcl_addr_type',
        'dcl_chklst',
        'dcl_chklst_tpl',
        'dcl_config',
        'dcl_contact_addr',
        'dcl_contact_email',
        'dcl_contact_note',
        'dcl_contact_phone',
        'dcl_contact',
        'dcl_contact_license',
        'dcl_contact_type',
        'dcl_contact_type_xref',
        'dcl_contact_url',
        'dcl_email_type',
        'dcl_entity',
        'dcl_entity_hotlist',
        'dcl_entity_hotlist_audit',
        'dcl_entity_perm',
        'dcl_entity_source',
        'dcl_entity_tag',
		'dcl_environment',
		'dcl_environment_org',
		'dcl_environment_product',
		'dcl_environment_wo',
		'dcl_environment_outage_type',
		'dcl_environment_outage',
		'dcl_environment_outage_wo',
        'dcl_error_log',
        'dcl_hotlist',
        'dcl_note_type',
        'dcl_org_addr',
        'dcl_org_alias',
        'dcl_org_contact',
        'dcl_org_email',
        'dcl_org_note',
        'dcl_org_phone',
        'dcl_org',
        'dcl_org_product_xref',
        'dcl_org_type',
        'dcl_org_type_xref',
        'dcl_org_url',
        'dcl_perm',
        'dcl_phone_type',
        'dcl_preferences',
        'dcl_product_build',
        'dcl_product_build_except',
        'dcl_product_build_item',
        'dcl_product_build_sccs',
        'dcl_product_module',
        'dcl_product_version',
        'dcl_product_version_item',
        'dcl_product_version_status',
        'dcl_projects',
        'dcl_projects_audit',
        'dcl_role_perm',
        'dcl_role',
        'dcl_sccs',
        'dcl_sccs_xref',
        'dcl_sec_audit',
        'dcl_session',
        'dcl_status_type',
        'dcl_tag',
        'dcl_url_type',
        'dcl_user_role',
        'dcl_wiki',
        'dcl_wo_account',
        'dcl_wo_account_audit',
        'dcl_wo_id',
        'dcl_wo_task',
        'dcl_wo_type',
        'dcl_workspace_product',
        'dcl_workspace_user',
        'dcl_workspace',
        'departments',
        'faqanswers',
        'faq',
        'faqquestions',
        'faqtopics',
        'personnel',
        'priorities',
        'products',
        'projectmap',
        'projectmap_audit',
        'severities',
        'statuses',
        'ticketresolutions',
        'tickets',
        'tickets_audit',
        'timecards',
        'views',
        'watches',
        'workorders',
        'workorders_audit'
    )
);

