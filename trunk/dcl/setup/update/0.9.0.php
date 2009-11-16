<?php
function dcl_upgrade0_9_0()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.1' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.1';
	return $setup_info['dcl']['currentver'];
}
