<?php
function dcl_upgrade0_9_5RC22()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->AddColumn('workorders', 'rubric_score', array('type' => 'int', 'precision' => 4, 'nullable' => true));
	$phpgw_setup->oProc->AddColumn('workorders_audit', 'rubric_score', array('type' => 'int', 'precision' => 4, 'nullable' => true));

	$phpgw_setup->oProc->CreateTable('dcl_rubric', array(
		'fd' => array(
			'rubric_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'rubric_name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
			'create_dt' => array('type' => 'timestamp', 'nullable' => false),
			'create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'update_dt' => array('type' => 'timestamp', 'nullable' => false),
			'update_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
		),
		'pk' => array('rubric_id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	));

	$phpgw_setup->oProc->CreateTable('dcl_rubric_criteria', array(
		'fd' => array(
			'rubric_criteria_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'rubric_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'criteria_name' => array('type' => 'varchar', 'precision' => 100, 'nullable' => false),
			'criteria_order' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'level1_descriptor' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
			'level2_descriptor' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
			'level3_descriptor' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
			'level4_descriptor' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
			'create_dt' => array('type' => 'timestamp', 'nullable' => false),
			'create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'update_dt' => array('type' => 'timestamp', 'nullable' => false),
			'update_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
		),
		'pk' => array('rubric_criteria_id'),
		'fk' => array(),
		'ix' => array('ix_rubric_criteria_rubric_id' => array('rubric_id')),
		'uc' => array()
	));

	$phpgw_setup->oProc->CreateTable('dcl_product_rubric', array(
		'fd' => array(
			'product_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'rubric_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'wo_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'create_dt' => array('type' => 'timestamp', 'nullable' => false),
			'create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
		),
		'pk' => array('product_id', 'wo_type_id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	));

	$phpgw_setup->oProc->CreateTable('dcl_wo_rubric_score', array(
		'fd' => array(
			'wo_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'seq' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'rubric_criteria_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'score' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'create_dt' => array('type' => 'timestamp', 'nullable' => false),
			'create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'update_dt' => array('type' => 'timestamp', 'nullable' => false),
			'update_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
		),
		'pk' => array('wo_id', 'seq', 'rubric_criteria_id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	));

	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity (entity_id, entity_desc, active) VALUES (52, 'Rubric', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (52, 1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (52, 2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (52, 3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (52, 4)");

	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm (perm_id, perm_desc, active) VALUES (24, 'Score', 'Y')");

	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (2, 24)");

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC23' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.5RC23';
	return $setup_info['dcl']['currentver'];
}