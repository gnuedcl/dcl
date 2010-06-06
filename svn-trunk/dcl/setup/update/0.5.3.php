<?php
function dcl_upgrade0_5_3()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->CreateTable('dcl_chklst_tpl',
					array(
						'fd' => array(
							'dcl_chklst_tpl_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'dcl_chklst_tpl_name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
							'dcl_chklst_tpl_active' => array('type' => 'char', 'precision' => 1, 'default' => 'Y', 'nullable' => false)
						),
						'pk' => array('dcl_chklst_tpl_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);

	$phpgw_setup->oProc->CreateTable('dcl_chklst',
					array(
						'fd' => array(
							'dcl_chklst_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'dcl_chklst_tpl_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'dcl_chklst_summary' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
							'dcl_chklst_createby' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'dcl_chklst_createon' => array('type' => 'timestamp', 'nullable' => false),
							'dcl_chklst_modifyby' => array('type' => 'int', 'precision' => 4),
							'dcl_chklst_modifyon' => array('type' => 'timestamp'),
							'dcl_chklst_status' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false)
						),
						'pk' => array('dcl_chklst_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);

	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) VALUES ('DCL_PROJECT_BROWSE_PARENTS_ONLY', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) VALUES ('DCL_PROJECT_INCLUDE_PARENT_STATS', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) VALUES ('DCL_PROJECT_INCLUDE_CHILD_STATS', 'dcl_config_varchar', 'Y')");
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20010715' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.4';
	return $setup_info['dcl']['currentver'];
}