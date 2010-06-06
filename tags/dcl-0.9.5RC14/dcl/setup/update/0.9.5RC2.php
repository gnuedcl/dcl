<?php
function dcl_upgrade0_9_5RC2()
{
	global $phpgw_setup, $setup_info;
	
	$phpgw_setup->oProc->CreateTable('dcl_tag',
					array(
						'fd' => array(
							'tag_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'tag_desc' => array('type' => 'varchar', 'precision' => 20, 'nullable' => false)
						),
						'pk' => array('tag_id'),
						'fk' => array(),
						'ix' => array('ix_dcl_tag_desc' => array('tag_desc')),
						'uc' => array()
					)
	);
					
	$phpgw_setup->oProc->CreateTable('dcl_entity_tag',
					array(
						'fd' => array(
							'entity_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'entity_key_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'entity_key_id2' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'tag_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
						),
						'pk' => array('entity_id', 'entity_key_id', 'entity_key_id2', 'tag_id'),
						'fk' => array(),
						'ix' => array('ix_dcl_entity_tag_id' => array('tag_id')),
						'uc' => array()
					)
	);
	
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC3' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.5RC3';
	return $setup_info['dcl']['currentver'];
}
