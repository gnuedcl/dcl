<?php
function dcl_upgrade0_9_1()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->AddColumn('timecards', 'reassign_from_id',
		array('type' => 'int', 'precision' => 4, 'nullable' => true));

	$phpgw_setup->oProc->AddColumn('timecards', 'reassign_to_id',
		array('type' => 'int', 'precision' => 4, 'nullable' => true));

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.2' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.2';
	return $setup_info['dcl']['currentver'];
}
