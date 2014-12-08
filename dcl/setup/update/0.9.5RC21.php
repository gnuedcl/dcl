<?php
function dcl_upgrade0_9_5RC21()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity (entity_id, entity_desc, active) VALUES (46, 'Environment', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (46, 1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (46, 2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (46, 3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (46, 4)");

	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity (entity_id, entity_desc, active) VALUES (47, 'Outage Type', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (47, 1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (47, 2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (47, 3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (47, 4)");

	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity (entity_id, entity_desc, active) VALUES (48, 'Outage', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (48, 1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (48, 2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (48, 3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (48, 4)");

	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity (entity_id, entity_desc, active) VALUES (49, 'Measurement Unit', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (49, 1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (49, 2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (49, 3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (49, 4)");

	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity (entity_id, entity_desc, active) VALUES (50, 'Measurement Type', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (50, 1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (50, 2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (50, 3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (50, 4)");

	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity (entity_id, entity_desc, active) VALUES (51, 'Organization Measurement', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm (entity_id, perm_id) VALUES (51, 4)");

	$phpgw_setup->oProc->AddColumn('dcl_environment', 'active', array('type' => 'char', 'precision' => 1, 'nullable' => false, 'default' => 'Y'));

	$phpgw_setup->oProc->CreateTable('dcl_outage', array(
		'fd' => array(
			'outage_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'outage_start' => array('type' => 'timestamp', 'nullable' => true),
			'outage_end' => array('type' => 'timestamp', 'nullable' => true),
			'outage_sched_start' => array('type' => 'timestamp', 'nullable' => true),
			'outage_sched_end' => array('type' => 'timestamp', 'nullable' => true),
			'outage_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'outage_title' => array('type' => 'varchar', 'precision' => 100, 'nullable' => false),
			'outage_description' => array('type' => 'text', 'nullable' => false),
			'create_dt' => array('type' => 'timestamp', 'nullable' => false),
			'create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'update_dt' => array('type' => 'timestamp', 'nullable' => false),
			'update_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'outage_status_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
		),
		'pk' => array('outage_id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	));

	$phpgw_setup->oProc->DropTable('dcl_environment_outage');
	$phpgw_setup->oProc->CreateTable('dcl_outage_environment', array(
		'fd' => array(
			'outage_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'environment_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'create_dt' => array('type' => 'timestamp', 'nullable' => false),
			'create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
		),
		'pk' => array('outage_id', 'environment_id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	));

	$phpgw_setup->oProc->DropTable('dcl_environment_outage_wo');
	$phpgw_setup->oProc->CreateTable('dcl_outage_wo', array(
		'fd' => array(
			'outage_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'wo_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'seq' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'create_dt' => array('type' => 'timestamp', 'nullable' => false),
			'create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
		),
		'pk' => array('outage_id', 'wo_id', 'seq'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	));

	$phpgw_setup->oProc->CreateTable('dcl_outage_org', array(
		'fd' => array(
			'outage_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'org_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'create_dt' => array('type' => 'timestamp', 'nullable' => false),
			'create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
		),
		'pk' => array('outage_id', 'org_id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	));

	$phpgw_setup->oProc->DropTable('dcl_environment_outage_type');
	$phpgw_setup->oProc->CreateTable('dcl_outage_type', array(
		'fd' => array(
			'outage_type_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'outage_type_name' => array('type' => 'varchar', 'precision' => 64, 'nullable' => false),
			'is_down' => array('type' => 'char', 'precision' => 1, 'nullable' => false),
			'is_infrastructure' => array('type' => 'char', 'precision' => 1, 'nullable' => false),
			'is_planned' => array('type' => 'char', 'precision' => 1, 'nullable' => false),
			'create_dt' => array('type' => 'timestamp', 'nullable' => false),
			'create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'update_dt' => array('type' => 'timestamp', 'nullable' => false),
			'update_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
		),
		'pk' => array('outage_type_id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	));

	$phpgw_setup->oProc->CreateTable('dcl_outage_status', array(
		'fd' => array(
			'outage_status_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'status_name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
			'is_planned' => array('type' => 'char', 'precision' => 1, 'nullable' => false),
			'is_resolved' => array('type' => 'char', 'precision' => 1, 'nullable' => false),
			'status_order' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
		),
		'pk' => array('outage_status_id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	));

	$phpgw_setup->oProc->Query("INSERT INTO dcl_outage_status (outage_status_id, status_name, is_planned, is_resolved, status_order) VALUES (1, 'Scheduled', 'Y', 'N', 0)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_outage_status (outage_status_id, status_name, is_planned, is_resolved, status_order) VALUES (2, 'In Progress', 'Y', 'N', 1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_outage_status (outage_status_id, status_name, is_planned, is_resolved, status_order) VALUES (3, 'Completed', 'Y', 'Y', 2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_outage_status (outage_status_id, status_name, is_planned, is_resolved, status_order) VALUES (4, 'Cancelled', 'Y', 'Y', 3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_outage_status (outage_status_id, status_name, is_planned, is_resolved, status_order) VALUES (5, 'In Progress', 'N', 'N', 0)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_outage_status (outage_status_id, status_name, is_planned, is_resolved, status_order) VALUES (6, 'Identified', 'N', 'N', 1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_outage_status (outage_status_id, status_name, is_planned, is_resolved, status_order) VALUES (7, 'Monitoring', 'N', 'N', 2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_outage_status (outage_status_id, status_name, is_planned, is_resolved, status_order) VALUES (8, 'Resolved', 'N', 'Y', 3)");

	$phpgw_setup->oProc->CreateTable('dcl_measurement_unit', array(
		'fd' => array(
			'measurement_unit_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'unit_name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
			'unit_abbr' => array('type' => 'varchar', 'precision' => 5, 'nullable' => false),
			'create_dt' => array('type' => 'timestamp', 'nullable' => false),
			'create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'update_dt' => array('type' => 'timestamp', 'nullable' => true),
			'update_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
		),
		'pk' => array('measurement_unit_id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	));

	$phpgw_setup->oProc->CreateTable('dcl_measurement_type', array(
		'fd' => array(
			'measurement_type_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'measurement_unit_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'measurement_name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
			'create_dt' => array('type' => 'timestamp', 'nullable' => false),
			'create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'update_dt' => array('type' => 'timestamp', 'nullable' => true),
			'update_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
		),
		'pk' => array('measurement_type_id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	));

	$phpgw_setup->oProc->CreateTable('dcl_org_measurement', array(
		'fd' => array(
			'org_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'measurement_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'measurement_ts' => array('type' => 'timestamp', 'nullable' => false),
			'measurement' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
		),
		'pk' => array('org_id', 'measurement_type_id', 'measurement_ts'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	));

	$phpgw_setup->oProc->CreateTable('dcl_org_measurement_sla', array(
		'fd' => array(
			'org_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'measurement_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'min_valid_value' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
			'max_valid_value' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
			'measurement_sla' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'measurement_sla_warn' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
			'create_dt' => array('type' => 'timestamp', 'nullable' => false),
			'create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'update_dt' => array('type' => 'timestamp', 'nullable' => false),
			'update_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'sla_trim_pct' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
			'sla_is_trim_based' => array('type' => 'char', 'precision' => 4, 'nullable' => true),
			'sla_schedule_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
		),
		'pk' => array('org_id', 'measurement_type_id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	));

	$phpgw_setup->oProc->CreateTable('dcl_org_outage_sla', array(
		'fd' => array(
			'org_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'outage_sla' => array('type' => 'float', 'precision' => 8, 'nullable' => false),
			'outage_sla_warn' => array('type' => 'float', 'precision' => 8, 'nullable' => true),
			'create_dt' => array('type' => 'timestamp', 'nullable' => false),
			'create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'update_dt' => array('type' => 'timestamp', 'nullable' => false),
			'update_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
		),
		'pk' => array('org_id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	));

	$phpgw_setup->oProc->CreateTable('dcl_sla_schedule', array(
		'fd' => array(
			'sla_schedule_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
			'day0_start_hour' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day0_start_minute' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day0_end_hour' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day0_end_minute' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day1_start_hour' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day1_start_minute' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day1_end_hour' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day1_end_minute' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day2_start_hour' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day2_start_minute' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day2_end_hour' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day2_end_minute' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day3_start_hour' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day3_start_minute' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day3_end_hour' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day3_end_minute' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day4_start_hour' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day4_start_minute' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day4_end_hour' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day4_end_minute' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day5_start_hour' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day5_start_minute' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day5_end_hour' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day5_end_minute' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day6_start_hour' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day6_start_minute' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day6_end_hour' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'day6_end_minute' => array('type' => 'int', 'precision' => 2, 'nullable' => false),
			'create_dt' => array('type' => 'timestamp', 'nullable' => false),
			'create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'update_dt' => array('type' => 'timestamp', 'nullable' => false),
			'update_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
		),
		'pk' => array('sla_schedule_id'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	));

	$phpgw_setup->oProc->CreateTable('dcl_sla_schedule_exception', array(
		'fd' => array(
			'sla_schedule_exception_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'sla_schedule_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
			'start_dt' => array('type' => 'timestamp', 'nullable' => true),
			'end_dt' => array('type' => 'timestamp', 'nullable' => true),
			'create_dt' => array('type' => 'timestamp', 'nullable' => false),
			'create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
			'update_dt' => array('type' => 'timestamp', 'nullable' => false),
			'update_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
		),
		'pk' => array('sla_schedule_exception_id'),
		'fk' => array(),
		'ix' => array(
			'ix_sla_sched_exc_sched_id' => array('sla_schedule_id')
		),
		'uc' => array()
	));

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC22' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.5RC22';
	return $setup_info['dcl']['currentver'];
}