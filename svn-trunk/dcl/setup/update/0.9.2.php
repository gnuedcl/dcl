<?php
function dcl_upgrade0_9_2()
{
	global $phpgw_setup, $setup_info;

	// One word: BRUTAL
	$phpgw_setup->oProc->CreateTable('dcl_wo_account',
					array(
						'fd' => array(
							'wo_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'seq' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'account_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
						),
						'pk' => array('wo_id', 'seq', 'account_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);

	$phpgw_setup->oProc->Query("Insert Into dcl_wo_account Select jcn, seq, account From workorders where account is not null and account > 0");

	$phpgw_setup->oProc->DropColumn('workorders',
					array(
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
							'contact' => array('type' => 'varchar', 'precision' => 50),
							'contactphone' => array('type' => 'char', 'precision' => 10),
							'summary' => array('type' => 'varchar', 'precision' => 100, 'nullable' => false),
							'notes' => array('type' => 'varchar', 'precision' => 1024),
							'description' => array('type' => 'varchar', 'precision' => 1024, 'nullable' => false),
							'responsible' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'revision' => array('type' => 'varchar', 'precision' => 20),
							'publicview' => array('type' => 'bool'),
							'etchours' => array('type' => 'float', 'precision' => 8),
							'module_id' => array('type' => 'int', 'precision' => 4)
						),
						'pk' => array('jcn', 'seq'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					),
					'account'
	);

	$phpgw_setup->oProc->CreateTable('dcl_wo_type',
					array(
						'fd' => array(
							'wo_type_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'type_name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
							'active' => array('type' => 'char', 'precision' => 1, 'nullable' => false)
						),
						'pk' => array('wo_type_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);

	$phpgw_setup->oProc->Query("Insert Into dcl_wo_type (type_name, active) values ('Issue', 'Y')");
	$phpgw_setup->oProc->AddColumn('workorders', 'wo_type_id',
		array('type' => 'int', 'precision' => 4, 'nullable' => false, 'default' => 1));

	$phpgw_setup->oProc->Query("Update workorders set wo_type_id = 1");

	$phpgw_setup->oProc->CreateTable('dcl_session',
					array(
						'fd' => array(
							'dcl_session_id' => array('type' => 'varchar', 'precision' => 32, 'nullable' => false),
							'personnel_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'create_date' => array('type' => 'timestamp'),
							'update_date' => array('type' => 'timestamp'),
							'session_data' => array('type' => 'text')
						),
						'pk' => array('dcl_session_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);

	$phpgw_setup->oProc->CreateTable('dcl_preferences',
					array(
						'fd' => array(
							'personnel_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'preferences_data' => array('type' => 'text')
						),
						'pk' => array('personnel_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);

	$phpgw_setup->oProc->CreateTable('dcl_wiki',
					array(
						'fd' => array(
							'dcl_entity_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'dcl_entity_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'dcl_entity_id2' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'page_name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
							'page_text' => array('type' => 'text'),
							'page_date' => array('type' => 'timestamp'),
							'page_ip' => array('type' => 'varchar', 'precision' => 255)
						),
						'pk' => array('dcl_entity_type_id', 'dcl_entity_id', 'dcl_entity_id2', 'page_name'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);

	// TODO: The next 2 lines may need abstraction
	$phpgw_setup->oProc->Query("create unique index uc_accounts_short on accounts(short)");
	$phpgw_setup->oProc->Query("create unique index uc_accounts_name on accounts(name)");

	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_GATEWAY_WO_AUTORESPOND', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_GATEWAY_WO_AUTORESPONSE_EMAIL', 'dcl_config_varchar', 'nobody@localhost')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_GATEWAY_WO_ENABLED', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_GATEWAY_WO_FILE_PATH', 'dcl_config_varchar', '/tmp')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) values ('DCL_GATEWAY_WO_PRIORITY', 'dcl_config_int', 0)");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_GATEWAY_WO_REPLY', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) values ('DCL_GATEWAY_WO_SEVERITY', 'dcl_config_int', 0)");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) values ('DCL_GATEWAY_WO_STATUS', 'dcl_config_int', 0)");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_WO_SECONDARY_ACCOUNTS_ENABLED', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_datetime) values ('LAST_CONFIG_UPDATE', 'dcl_config_datetime', " . $phpgw_setup->oProc->m_odb->GetDateSQL() . ')');
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) values ('DCL_SESSION_TIMEOUT', 'dcl_config_int', 20)");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_WIKI_ENABLED', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) values ('DCL_WIKI_VIEW', 'dcl_config_int', 4)");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) values ('DCL_WIKI_EDIT', 'dcl_config_int', 5)");
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.3' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.3';
	return $setup_info['dcl']['currentver'];
}
