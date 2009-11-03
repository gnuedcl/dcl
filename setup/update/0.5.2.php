<?php
function dcl_upgrade0_5_2()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) VALUES ('DCL_SMTP_SERVER', 'dcl_config_varchar', 'localhost')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) VALUES ('DCL_SMTP_ENABLED', 'dcl_config_varchar', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) VALUES ('DCL_SMTP_PORT', 'dcl_config_int', 25)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) VALUES ('DCL_SMTP_TIMEOUT', 'dcl_config_int', 30)");
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20010413' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.3';
	return $setup_info['dcl']['currentver'];
}
