<?php
function dcl_upgrade0_9_5RC25()
{
	global $phpgw_setup;

	$phpgw_setup->oProc->AddColumn('dcl_outage', 'sev_level', array('type' => 'int', 'precision' => 4, 'nullable' => true));

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC26' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.5RC26';
	return $setup_info['dcl']['currentver'];
}