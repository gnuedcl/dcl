<?php
function dcl_upgrade0_5_5()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) VALUES ('DCL_FILE_PATH', 'dcl_config_varchar', '.')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) VALUES ('DCL_HTML_TITLE', 'dcl_config_varchar', 'Double Choco Latte - Copyright (C) 1998-2001 Michael L. Dean and Tim R. Norman')");
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20010911' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.6';
	return $setup_info['dcl']['currentver'];
}