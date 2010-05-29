<?php
function dcl_upgrade0_9_5RC12()
{
	global $dcl_domain_info, $dcl_domain, $phpgw_setup, $setup_info;
	
	$phpgw_setup->oProc->DropPrimaryKey('projectmap_audit');
	$phpgw_setup->oProc->CreatePrimaryKey('projectmap_audit', array('projectid', 'jcn', 'seq', 'audit_on'));
			
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC13' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.5RC13';
	return $setup_info['dcl']['currentver'];
}
