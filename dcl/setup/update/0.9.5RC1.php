<?php
function dcl_upgrade0_9_5RC1()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->AddColumn('dcl_contact_type', 'contact_type_is_main', array('type' => 'char', 'precision' => 1, 'nullable' => false, 'default' => 'N'));

	$phpgw_setup->oProc->CreateTable('dcl_wo_task',
					array(
						'fd' => array(
							'wo_task_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'wo_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'seq' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'task_order' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'task_complete' => array('type' => 'char', 'precision' => 1, 'nullable' => false, 'default' => 'N'),
							'task_summary' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
							'task_create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'task_create_dt' => array('type' => 'timestamp', 'nullable' => false),
							'task_complete_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
							'task_complete_dt' => array('type' => 'timestamp', 'nullable' => true)
						),
						'pk' => array('wo_task_id'),
						'fk' => array(),
						'ix' => array(
								'ix_dcl_wo_task_id_seq' => array('wo_id', 'seq')
							),
						'uc' => array()
					)
	);
	
	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) VALUES ('DCL_APP_NAME', 'dcl_config_varchar', 'GNUe DCL')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) VALUES ('DCL_LOGIN_MESSAGE', 'dcl_config_varchar', 'Welcome to GNUe DCL')");
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC2' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.5RC2';
	return $setup_info['dcl']['currentver'];
}
