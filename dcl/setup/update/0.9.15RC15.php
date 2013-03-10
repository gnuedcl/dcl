<?php
function dcl_upgrade0_9_5RC15()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->CreateTable('dcl_error_log',
		array(
			'fd' => array(
					'error_log_id' => array('type' => 'auto', 'precision' => 8, 'nullable' => false),
					'error_timestamp' => array('type' => 'timestamp', 'nullable' => false),
					'user_id' => array('type' => 'int', 'precision' => 4),
					'server_name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
					'script_name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
					'request_uri' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
					'query_string' => array('type' => 'text'),
					'error_file' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
					'error_line' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'error_description' => array('type' => 'text', 'nullable' => false),
					'stack_trace' => array('type' => 'text'),
					'log_level' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
				),
				'pk' => array('error_log_id'),
				'fk' => array(),
				'ix' => array(),
				'uc' => array()
		)
	);

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC16' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.5RC16';
	return $setup_info['dcl']['currentver'];
}
