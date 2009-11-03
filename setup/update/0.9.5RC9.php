<?php
function dcl_upgrade0_9_5RC9()
{
	global $dcl_domain_info, $dcl_domain, $phpgw_setup, $setup_info;
	
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (37, 'Organization Type', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (38, 'Contact Type', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (39, 'Build Manager', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (40, 'Work Order Task', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (41, 'Workspace', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (42, 'Test Case', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (43, 'Functional Spec', 'Y')");
	
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC10' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.5RC10';
	return $setup_info['dcl']['currentver'];
}
