<?php
function dcl_upgrade0_5_12()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) values ('DCL_GATEWAY_TICKET_ACCOUNT', 'dcl_config_int', 0)");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) values ('DCL_GATEWAY_TICKET_REPLY_LOGGED_BY', 'dcl_config_int', 0)");
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20011215' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.13';
	return $setup_info['dcl']['currentver'];
}
