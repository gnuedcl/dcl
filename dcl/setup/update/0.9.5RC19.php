<?php
function dcl_upgrade0_9_5RC19()
{
	global $phpgw_setup, $setup_info, $dcl_domain_info, $dcl_domain;

	$dbType = $dcl_domain_info[$dcl_domain]['dbType'];
	if ($dbType == 'mysql')
	{
		$tables = array(
			'actions', 'attributesetsmap', 'attributesets', 'dcl_addr_type', 'dcl_chklst', 'dcl_chklst_tpl', 'dcl_config', 'dcl_contact_addr', 'dcl_contact_email',
			'dcl_contact_note', 'dcl_contact_phone', 'dcl_contact', 'dcl_contact_license', 'dcl_contact_type', 'dcl_contact_type_xref', 'dcl_contact_url',
			'dcl_email_type', 'dcl_entity', 'dcl_entity_hotlist', 'dcl_entity_hotlist_audit', 'dcl_entity_perm', 'dcl_entity_source', 'dcl_entity_tag',
			'dcl_environment', 'dcl_environment_org', 'dcl_environment_product', 'dcl_environment_wo', 'dcl_environment_outage_type', 'dcl_environment_outage',
			'dcl_environment_outage_wo', 'dcl_error_log', 'dcl_hotlist', 'dcl_note_type', 'dcl_org_addr', 'dcl_org_alias', 'dcl_org_contact', 'dcl_org_email',
			'dcl_org_note', 'dcl_org_phone', 'dcl_org', 'dcl_org_product_xref', 'dcl_org_type', 'dcl_org_type_xref', 'dcl_org_url', 'dcl_perm', 'dcl_phone_type',
			'dcl_preferences', 'dcl_product_build', 'dcl_product_build_except', 'dcl_product_build_item', 'dcl_product_build_sccs', 'dcl_product_module',
			'dcl_product_version', 'dcl_product_version_item', 'dcl_product_version_status', 'dcl_projects', 'dcl_projects_audit', 'dcl_role_perm', 'dcl_role',
			'dcl_sccs', 'dcl_sccs_xref', 'dcl_sec_audit', 'dcl_session', 'dcl_status_type', 'dcl_tag', 'dcl_url_type', 'dcl_user_role', 'dcl_wiki', 'dcl_wo_account',
			'dcl_wo_account_audit', 'dcl_wo_id', 'dcl_wo_task', 'dcl_wo_type', 'dcl_workspace_product', 'dcl_workspace_user', 'dcl_workspace', 'departments',
			'faqanswers', 'faq', 'faqquestions', 'faqtopics', 'personnel', 'priorities', 'products', 'projectmap', 'projectmap_audit', 'severities', 'statuses',
			'ticketresolutions', 'tickets', 'tickets_audit', 'timecards', 'views', 'watches', 'workorders', 'workorders_audit'
		);

		$db = new DbProvider();
		$db->Query('SHOW TABLE STATUS');
		while ($db->next_record())
		{
			$tableName = $db->f('Name');
			if (!in_array($tableName, $tables))
				continue;

			if ($db->f('Engine') != 'InnoDB')
			{
				$phpgw_setup->oProc->Query("ALTER TABLE `$tableName` ENGINE=InnoDB");
			}
		}
	}

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC20' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.5RC20';
	return $setup_info['dcl']['currentver'];
}