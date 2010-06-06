<?php
function dcl_upgrade0_9_5RC13()
{
	global $dcl_domain_info, $dcl_domain, $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->CreateTable('dcl_entity_hotlist_audit',
		array(
			'fd' => array(
				'entity_hotlist_audit_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
				'entity_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'entity_key_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'entity_key_id2' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'hotlist_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'sort' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'audit_on' => array('type' => 'timestamp', 'nullable' => false),
				'audit_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'audit_type' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
			),
			'pk' => array('entity_hotlist_audit_id'),
			'fk' => array(),
			'ix' => array(
				'ix_dcl_entity_hotlist_audit_entity' => array('entity_id', 'entity_key_id', 'entity_key_id2'),
				'ix_dcl_entity_hotlist_audit_hotlist' => array('hotlist_id')
			),
			'uc' => array()
		)
	);

	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_hotlist_audit (entity_id, entity_key_id, entity_key_id2, hotlist_id, sort, audit_on, audit_by, audit_type)
		SELECT entity_id, entity_key_id, entity_key_id2, hotlist_id, sort, created_on, created_by, 1 FROM dcl_entity_hotlist
		UNION ALL
		SELECT entity_id, entity_key_id, entity_key_id2, hotlist_id, sort, deleted_on, deleted_by, 2 FROM dcl_entity_hotlist WHERE deleted_on IS NOT NULL
		ORDER BY 6");

	$phpgw_setup->oProc->Query("DELETE FROM dcl_entity_hotlist WHERE deleted_on IS NOT NULL");

	$phpgw_setup->oProc->DropColumn('dcl_entity_hotlist', array(
			'fd' => array(
				'entity_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'entity_key_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'entity_key_id2' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'hotlist_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'sort' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
			),
			'pk' => array('entity_id', 'entity_key_id', 'entity_key_id2', 'hotlist_id'),
			'fk' => array(),
			'ix' => array('ix_dcl_entity_hotlist_id' => array('hotlist_id')),
			'uc' => array()
		),
		array('created_on', 'created_by', 'deleted_on', 'deleted_by')
	);

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC14' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.5RC14';
	return $setup_info['dcl']['currentver'];
}