<?php
function dcl_upgrade0_9_5RC24()
{
	global $phpgw_setup;

	// Remove checklists because they don't seem to get much use, and the implementation is lacking
	$phpgw_setup->oProc->DropTable('dcl_chklst');
	$phpgw_setup->oProc->DropTable('dcl_chklst_tpl');
	$phpgw_setup->oProc->Query('DELETE FROM dcl_entity_perm WHERE entity_id = 19');
	$phpgw_setup->oProc->Query('DELETE FROM dcl_entity WHERE entity_id = 19');

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC25' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.5RC25';
	return $setup_info['dcl']['currentver'];
}