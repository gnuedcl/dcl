<?php
function dcl_upgrade0_9_5RC6()
{
	global $phpgw_setup, $setup_info;
		
	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) VALUES ('DCL_WO_EMAIL_TEMPLATE_PUBLIC', 'dcl_config_varchar', 'notify_wo_en_public.tpl')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) VALUES ('DCL_TCK_EMAIL_TEMPLATE_PUBLIC', 'dcl_config_varchar', 'notify_tck_en_public.tpl')");
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC7' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.5RC7';
	return $setup_info['dcl']['currentver'];
}
