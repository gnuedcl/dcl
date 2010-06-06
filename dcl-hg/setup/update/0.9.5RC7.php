<?php
function dcl_upgrade0_9_5RC7()
{
	global $phpgw_setup, $setup_info;
	
	// Officially adding build manager tables - these may have already existed from the 0.9.4.4-0.9.5RC1 upgrade
	// However, the tables were not part of the install script, so anyone starting from 0.9.5 would not have the tables
	// The first table was not included, so it is unconditionally added
	$phpgw_setup->oProc->CreateTable('dcl_product_version_status',
					array(
						'fd' => array(
							'product_version_status_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'product_version_status_descr' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
							'active' => array('type' => 'char', 'precision' => 1, 'nullable' => false, 'default' => 'N')
						),
						'pk' => array('product_version_status_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);
	
	if (!$phpgw_setup->oProc->m_odb->TableExists('dcl_product_build'))
	{
		$phpgw_setup->oProc->CreateTable('dcl_product_build',
					array(
						'fd' => array(
							'product_build_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'product_version_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'product_build_descr' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
							'product_build_on' => array('type' => 'timestamp', 'nullable' => false)
						),
						'pk' => array('product_build_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array(
							'uc_dcl_product_build_descr' => array('product_build_descr')
						)
					)
		);
	}
	
	if (!$phpgw_setup->oProc->m_odb->TableExists('dcl_product_build_except'))
	{
		$phpgw_setup->oProc->CreateTable('dcl_product_build_except',
						array(
							'fd' => array(
								'product_build_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
								'entity_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
								'entity_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
								'entity_id2' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
							),
							'pk' => array('product_build_id', 'entity_type_id', 'entity_id', 'entity_id2'),
							'fk' => array(),
							'ix' => array(),
							'uc' => array()
						)
		);
	}
	
	if (!$phpgw_setup->oProc->m_odb->TableExists('dcl_product_build_sccs'))
	{
		$phpgw_setup->oProc->CreateTable('dcl_product_build_sccs',
						array(
							'fd' => array(
								'product_build_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
								'sccs_xref_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
							),
							'pk' => array('product_build_id', 'sccs_xref_id'),
							'fk' => array(),
							'ix' => array(),
							'uc' => array()
						)
		);
	}
	
	if (!$phpgw_setup->oProc->m_odb->TableExists('dcl_product_version'))
	{
		$phpgw_setup->oProc->CreateTable('dcl_product_version',
						array(
							'fd' => array(
								'product_version_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
								'product_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
								'product_version_text' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
								'product_version_descr' => array('type' => 'varchar', 'precision' => 100, 'nullable' => false),
								'product_version_target_date' => array('type' => 'timestamp', 'nullable' => false),
								'product_version_actual_date' => array('type' => 'timestamp', 'nullable' => true)
							),
							'pk' => array('product_version_id'),
							'fk' => array(),
							'ix' => array(),
							'uc' => array(
								'uc_dcl_product_version_text' => array('product_version_text')
							)
						)
		);
	}
	
	if (!$phpgw_setup->oProc->m_odb->TableExists('dcl_product_version_item'))
	{
		$phpgw_setup->oProc->CreateTable('dcl_product_version_item',
						array(
							'fd' => array(
								'product_version_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
								'entity_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
								'entity_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
								'entity_id2' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
								'version_status_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
								'version_item_submit_on' => array('type' => 'timestamp', 'nullable' => true),
								'version_item_apply_on' => array('type' => 'timestamp', 'nullable' => true)
							),
							'pk' => array('product_version_id', 'entity_type_id', 'entity_id', 'entity_id2'),
							'fk' => array(),
							'ix' => array(),
							'uc' => array()
						)
		);
	}
	
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC8' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.5RC8';
	return $setup_info['dcl']['currentver'];
}
