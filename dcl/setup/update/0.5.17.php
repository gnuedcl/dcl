<?php
function dcl_upgrade0_5_17()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_SMTP_DEFAULT_EMAIL', 'dcl_config_varchar', 'nobody@localhost')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_WO_NOTIFICATION_HTML', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_WO_EMAIL_TEMPLATE', 'dcl_config_varchar', 'notify_wo_en.tpl')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_TCK_NOTIFICATION_HTML', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_TCK_EMAIL_TEMPLATE', 'dcl_config_varchar', 'notify_tck_en.tpl')");
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20021023' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.18';
	return $setup_info['dcl']['currentver'];
}
