<?php
function dcl_upgrade0_9_5RC10()
{
	global $dcl_domain_info, $dcl_domain, $phpgw_setup, $setup_info;
	
	$phpgw_setup->oProc->AddColumn('products', 'is_project_required', array('type' => 'char', 'precision' => 1, 'nullable' => true));
	$phpgw_setup->oProc->Query("update products set is_project_required = 'N'");
	$phpgw_setup->oProc->AlterColumn('products', 'is_project_required', array('type' => 'char', 'precision' => 1, 'default' => 'N', 'nullable' => false));
	
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (44, 'Hotlist', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (44, 1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (44, 2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (44, 3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (44, 4)");
	
	if (!$phpgw_setup->oProc->m_odb->TableExists('dcl_hotlist'))
	{
		$phpgw_setup->oProc->CreateTable('dcl_hotlist',
						array(
							'fd' => array(
								'hotlist_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
								'hotlist_tag' => array('type' => 'varchar', 'precision' => 20, 'nullable' => false),
								'hotlist_desc' => array('type' => 'text', 'nullable' => false),
								'active' => array('type' => 'char', 'precision' => 1, 'nullable' => false, 'default' => 'N'),
								'created_on' => array('type' => 'timestamp', 'nullable' => false),
								'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
								'closed_on' => array('type' => 'timestamp', 'nullable' => true),
								'closed_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
							),
							'pk' => array('hotlist_id'),
							'fk' => array(),
							'ix' => array('ix_dcl_hotlist_tag' => array('hotlist_tag')),
							'uc' => array()
						)
		);
	}
		
	if (!$phpgw_setup->oProc->m_odb->TableExists('dcl_entity_hotlist'))
	{
		$phpgw_setup->oProc->CreateTable('dcl_entity_hotlist',
						array(
							'fd' => array(
								'entity_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
								'entity_key_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
								'entity_key_id2' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
								'hotlist_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
								'created_on' => array('type' => 'timestamp', 'nullable' => false),
								'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
								'deleted_on' => array('type' => 'timestamp', 'nullable' => true),
								'deleted_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
							),
							'pk' => array('entity_id', 'entity_key_id', 'entity_key_id2', 'hotlist_id'),
							'fk' => array(),
							'ix' => array('ix_dcl_entity_hotlist_id' => array('hotlist_id')),
							'uc' => array()
						)
		);
	}
		
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC11' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.5RC11';
	return $setup_info['dcl']['currentver'];
}
