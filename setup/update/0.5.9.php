<?php
function dcl_upgrade0_5_9()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_GATEWAY_TICKET_ENABLED', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_GATEWAY_TICKET_AUTORESPOND', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_GATEWAY_TICKET_REPLY', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_GATEWAY_TICKET_AUTORESPONSE_EMAIL', 'dcl_config_varchar', 'nobody@localhost')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_GATEWAY_TICKET_FILE_PATH', 'dcl_config_varchar', '/tmp')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) values ('DCL_GATEWAY_TICKET_STATUS', 'dcl_config_int', 0)");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) values ('DCL_GATEWAY_TICKET_PRIORITY', 'dcl_config_int', 0)");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) values ('DCL_GATEWAY_TICKET_SEVERITY', 'dcl_config_int', 0)");
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20011203' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.10';
	return $setup_info['dcl']['currentver'];
}
