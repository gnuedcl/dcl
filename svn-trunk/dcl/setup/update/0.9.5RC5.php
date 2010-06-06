<?php
function dcl_upgrade0_9_5RC5()
{
	global $phpgw_setup, $setup_info;
		
	$phpgw_setup->oProc->CreateTable('dcl_sec_audit',
		array(
			'fd' => array(
				'id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'actionon' => array('type' => 'timestamp', 'nullable' => false),
				'actiontxt' => array('type' => 'varchar', 'precision' => 75, 'nullable' => false),
				'actionparam' => array('type' => 'varchar', 'precision' => 50)
			),
			'pk' => array('id', 'actionon'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
	));
	
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC6' WHERE dcl_config_name='DCL_VERSION'");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) VALUES ('DCL_SEC_AUDIT_ENABLED', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) VALUES ('DCL_SEC_AUDIT_LOGIN_ONLY', 'dcl_config_varchar', 'N')");
	
	
	$setup_info['dcl']['currentver'] = '0.9.5RC6';
	return $setup_info['dcl']['currentver'];

}
