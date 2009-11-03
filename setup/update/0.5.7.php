<?php
function dcl_upgrade0_5_7()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->RenameTable('projects', 'dcl_projects');
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20010918' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.8';
	return $setup_info['dcl']['currentver'];
}
