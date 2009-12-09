<?php
function dcl_upgrade0_9_5RC11()
{
	global $dcl_domain_info, $dcl_domain, $phpgw_setup, $setup_info;
	
	$phpgw_setup->oProc->AddColumn('dcl_entity_hotlist', 'sort', array('type' => 'int', 'precision' => 4, 'nullable' => true));
		
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC12' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.5RC12';
	return $setup_info['dcl']['currentver'];
}
