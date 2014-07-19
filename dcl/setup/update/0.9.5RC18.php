<?php
function dcl_upgrade0_9_5RC18()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity (entity_id, entity_desc, active) VALUES (45, 'Error Log', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (45, 4)");

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC19' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.5RC19';
	return $setup_info['dcl']['currentver'];
}