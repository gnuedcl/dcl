<?php
function dcl_upgrade0_9_5RC16()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->CreateTable('dcl_environment',
		array(
			'fd' => array(
				'environment_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
				'environment_name' => array('type' => 'varchar', 'precision' => 32, 'nullable' => false),
				'create_dt' => array('type' => 'timestamp', 'nullable' => false),
				'create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'update_dt' => array('type' => 'timestamp', 'nullable' => false),
				'update_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
			),
			'pk' => array('environment_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);

	$phpgw_setup->oProc->CreateTable('dcl_environment_org',
		array(
			'fd' => array(
				'environment_org_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
				'environment_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'org_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'begin_dt' => array('type' => 'timestamp', 'nullable' => false),
				'end_dt' => array('type' => 'timestamp', 'nullable' => true),
				'create_dt' => array('type' => 'timestamp', 'nullable' => false),
				'create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'update_dt' => array('type' => 'timestamp', 'nullable' => false),
				'update_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
			),
			'pk' => array('environment_org_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);

	$phpgw_setup->oProc->CreateTable('dcl_environment_product',
		array(
			'fd' => array(
				'environment_product_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
				'environment_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'product_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'create_dt' => array('type' => 'timestamp', 'nullable' => false),
				'create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
			),
			'pk' => array('environment_product_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);

	$phpgw_setup->oProc->CreateTable('dcl_environment_wo',
		array(
			'fd' => array(
				'environment_wo_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
				'environment_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'wo_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'seq' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'create_dt' => array('type' => 'timestamp', 'nullable' => false),
				'create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
			),
			'pk' => array('environment_wo_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);

	$phpgw_setup->oProc->CreateTable('dcl_environment_outage_type',
		array(
			'fd' => array(
				'environment_outage_type_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
				'outage_name' => array('type' => 'varchar', 'precision' => 64, 'nullable' => false),
				'is_down' => array('type' => 'char', 'precision' => 1, 'nullable' => false),
				'is_infrastructure' => array('type' => 'char', 'precision' => 1, 'nullable' => false),
				'is_planned' => array('type' => 'char', 'precision' => 1, 'nullable' => false),
				'create_dt' => array('type' => 'timestamp', 'nullable' => false),
				'create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'update_dt' => array('type' => 'timestamp', 'nullable' => false),
				'update_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
			),
			'pk' => array('environment_outage_type_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);

	$phpgw_setup->oProc->CreateTable('dcl_environment_outage',
		array(
			'fd' => array(
				'environment_outage_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
				'environment_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'outage_start' => array('type' => 'timestamp', 'nullable' => false),
				'outage_end' => array('type' => 'timestamp', 'nullable' => false),
				'environment_outage_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'outage_description' => array('type' => 'text', 'nullable' => false),
				'create_dt' => array('type' => 'timestamp', 'nullable' => false),
				'create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'update_dt' => array('type' => 'timestamp', 'nullable' => false),
				'update_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
			),
			'pk' => array('environment_outage_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);

	$phpgw_setup->oProc->CreateTable('dcl_environment_outage_wo',
		array(
			'fd' => array(
				'environment_outage_wo_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
				'environment_outage_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'wo_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'seq' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'create_dt' => array('type' => 'timestamp', 'nullable' => false),
				'create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
			),
			'pk' => array('environment_outage_wo_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC17' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.5RC17';
	return $setup_info['dcl']['currentver'];
}
