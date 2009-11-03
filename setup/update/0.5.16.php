<?php
function dcl_upgrade0_5_16()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->CreateTable('dcl_product_module',
					array(
						'fd' => array(
							'product_module_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'product_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'module_name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
							'active' => array('type' => 'char', 'precision' => 1, 'nullable' => false)
						),
						'pk' => array('product_module_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);

	$phpgw_setup->oProc->AddColumn('workorders', 'module_id',
		array('type' => 'int', 'precision' => 4, 'nullable' => true));

	$phpgw_setup->oProc->AddColumn('tickets', 'module_id',
		array('type' => 'int', 'precision' => 4, 'nullable' => true));

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20021021' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.17';
	return $setup_info['dcl']['currentver'];
}
