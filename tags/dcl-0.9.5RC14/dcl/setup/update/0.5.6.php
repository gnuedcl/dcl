<?php
function dcl_upgrade0_5_6()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->CreateTable('dcl_status_type',
					array(
						'fd' => array(
							'dcl_status_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'dcl_status_type_name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false)
						),
						'pk' => array('dcl_status_type_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);

	$phpgw_setup->oProc->Query("insert into dcl_status_type values (1, 'Open')");
	$phpgw_setup->oProc->Query("insert into dcl_status_type values (2, 'Completed')");
	$phpgw_setup->oProc->Query("insert into dcl_status_type values (3, 'Deferred')");

	$phpgw_setup->oProc->AddColumn('statuses', 'dcl_status_type',
		array('type' => 'int', 'precision' => 4, 'nullable' => false, 'default' => 1));

	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) VALUES ('DCL_DEFAULT_PROJECT_STATUS', 'dcl_config_int', 1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) VALUES ('DCL_DEFAULT_TICKET_STATUS', 'dcl_config_int', 1)");
	$phpgw_setup->oProc->Query("update statuses set dcl_status_type = 1");
	$phpgw_setup->oProc->Query("update statuses set dcl_status_type = 2 where id = 2");
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20010916' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.7';
	return $setup_info['dcl']['currentver'];
}
