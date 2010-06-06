<?php
function dcl_upgrade0_9_5RC4()
{
	global $phpgw_setup, $setup_info;
	
	$phpgw_setup->oProc->CreateTable('dcl_org_product_xref',
					array(
						'fd' => array(
							'org_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'product_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'product_version_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
						),
						'pk' => array(),
						'fk' => array(),
						'ix' => array(),
						'uc' => array('uc_dcl_org_product_xref' => array('org_id', 'product_id', 'product_version_id'))
					)
	);
	
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC5' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.5RC5';
	return $setup_info['dcl']['currentver'];
}
