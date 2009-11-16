<?php
function dcl_upgrade0_9_5RC8()
{
	global $dcl_domain_info, $dcl_domain, $phpgw_setup, $setup_info;
	
	// new table: dcl_contact_license
	if (!$phpgw_setup->oProc->m_odb->TableExists('dcl_contact_license'))
	{
		$phpgw_setup->oProc->CreateTable('dcl_contact_license',
						array(
							'fd' => array(
						        'contact_license_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
								'contact_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
								'product_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
								'product_version' => array('type' => 'varchar', 'precision' => 20, 'nullable' => true),
						        'license_id' => array('type' => 'varchar', 'precision' => 50, 'nullable' => true),
						        'registered_on' => array('type' => 'date', 'nullable' => true),
						        'expires_on' => array('type' => 'date', 'nullable' => true),
						        'license_notes' => array('type' => 'text', 'nullable' => true),
								'created_on' => array('type' => 'timestamp', 'nullable' => false),
								'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
								'modified_on' => array('type' => 'timestamp', 'nullable' => true),
								'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
							),
							'pk' => array('contact_license_id'),
							'fk' => array(),
							'ix' => array(),
							'uc' => array('uc_dcl_contact_license' => array('contact_id', 'product_id', 'product_version', 'license_id'))
						)
		);
	}
	
	// new table: dcl_workspace
	if (!$phpgw_setup->oProc->m_odb->TableExists('dcl_workspace'))
	{
		$phpgw_setup->oProc->CreateTable('dcl_workspace',
						array(
							'fd' => array(
								'workspace_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
								'workspace_name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
								'active' => array('type' => 'char', 'precision' => 1, 'default' => 'Y', 'nullable' => false),
								'created_on' => array('type' => 'timestamp', 'nullable' => false),
								'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
								'modified_on' => array('type' => 'timestamp', 'nullable' => true),
								'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
							),
							'pk' => array('workspace_id'),
							'fk' => array(),
							'ix' => array(),
							'uc' => array()
						)
		);
	}
	
	// new table: dcl_workspace_product
	if (!$phpgw_setup->oProc->m_odb->TableExists('dcl_workspace_product'))
	{
		$phpgw_setup->oProc->CreateTable('dcl_workspace_product',
						array(
							'fd' => array(
								'workspace_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
								'product_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
								'created_on' => array('type' => 'timestamp', 'nullable' => false),
								'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
							),
							'pk' => array('workspace_id', 'product_id'),
							'fk' => array(),
							'ix' => array(),
							'uc' => array()
						)
		);
	}
	
	// alter table: workorders
	$phpgw_setup->oProc->Query("UPDATE workorders SET revision = NULL WHERE RTRIM(revision) = ''");
	$phpgw_setup->oProc->Query("UPDATE workorders_audit SET revision = NULL WHERE RTRIM(revision) = ''");
	$phpgw_setup->oProc->Query("UPDATE timecards SET revision = NULL WHERE RTRIM(revision) = ''");
	
	$phpgw_setup->oProc->AlterColumn('dcl_product_version', 'product_version_target_date', array('type' => 'timestamp', 'nullable' => true));
	
	$phpgw_setup->oProc->DropPrimaryKey('dcl_product_version');
	$phpgw_setup->oProc->CreatePrimaryKey('dcl_product_version', array('product_id', 'product_version_text'));

	$phpgw_setup->oProc->Query("insert into dcl_product_version (product_id, product_version_text, product_version_descr)
					select distinct product, rtrim(revision), rtrim(revision) from workorders where revision is not null
					union
					select distinct product, rtrim(revision), rtrim(revision) from workorders_audit where revision is not null
					union
					select distinct w.product, rtrim(t.revision), rtrim(t.revision) from timecards t join workorders w on t.jcn = w.jcn and t.seq = w.seq where t.revision is not null
					order by 1, 2");

	$phpgw_setup->oProc->Query('update workorders set revision = product_version_id from dcl_product_version
					where product = product_id and rtrim(revision) = product_version_text');

	$phpgw_setup->oProc->Query('update workorders_audit set revision = product_version_id from dcl_product_version
					where product = product_id and rtrim(revision) = product_version_text');

	$phpgw_setup->oProc->Query('update timecards set revision = product_version_id from dcl_product_version, workorders
					where timecards.jcn = workorders.jcn and timecards.seq = workorders.seq 
					and product = product_id and rtrim(timecards.revision) = product_version_text');

	$phpgw_setup->oProc->RenameColumn('workorders', 'revision', 'reported_version_id');
	$phpgw_setup->oProc->AlterColumn('workorders', 'reported_version_id', array('type' => 'int', 'precision' => 4, 'nullable' => true));
	$phpgw_setup->oProc->AddColumn('workorders', 'targeted_version_id', array('type' => 'int', 'precision' => 4, 'nullable' => true));
	$phpgw_setup->oProc->AddColumn('workorders', 'fixed_version_id', array('type' => 'int', 'precision' => 4, 'nullable' => true));
	
	$phpgw_setup->oProc->RenameColumn('workorders_audit', 'revision', 'reported_version_id');
	$phpgw_setup->oProc->AlterColumn('workorders_audit', 'reported_version_id', array('type' => 'int', 'precision' => 4, 'nullable' => true));
	$phpgw_setup->oProc->AddColumn('workorders_audit', 'targeted_version_id', array('type' => 'int', 'precision' => 4, 'nullable' => true));
	$phpgw_setup->oProc->AddColumn('workorders_audit', 'fixed_version_id', array('type' => 'int', 'precision' => 4, 'nullable' => true));

	$sType = $dcl_domain_info[$dcl_domain]['dbType'];
	
	if ($sType == 'pgsql')
	{
		$phpgw_setup->oProc->Query('update workorders set fixed_version_id = timecards.revision::int4 
					from timecards
					where workorders.jcn = timecards.jcn and workorders.seq = timecards.seq');
	}
	else 
	{
		$phpgw_setup->oProc->Query('update workorders set fixed_version_id = timecards.revision 
					from timecards
					where workorders.jcn = timecards.jcn and workorders.seq = timecards.seq');
	}
	
	$phpgw_setup->oProc->DropColumn('timecards', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
				'jcn' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'seq' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'actionon' => array('type' => 'date', 'nullable' => false),
				'inputon' => array('type' => 'timestamp', 'nullable' => false),
				'actionby' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'status' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'action' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'hours' => array('type' => 'float', 'precision' => 8, 'nullable' => false),
				'summary' => array('type' => 'varchar', 'precision' => 100, 'nullable' => false),
				'description' => array('type' => 'text', 'nullable' => true),
				'revision' => array('type' => 'varchar', 'precision' => 20),
				'reassign_from_id' => array('type' => 'int', 'precision' => 4),
				'reassign_to_id' => array('type' => 'int', 'precision' => 4),
				'is_public' => array('type' => 'char', 'precision' => 1, 'nullable' => false, 'default' => 'N')
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		'revision');
	
	$phpgw_setup->oProc->AddColumn('dcl_product_version', 'active', array('type' => 'char', 'precision' => 1, 'nullable' => true));
	$phpgw_setup->oProc->Query("update dcl_product_version set active = 'N'");
	$phpgw_setup->oProc->AlterColumn('dcl_product_version', 'active', array('type' => 'char', 'precision' => 1, 'default' => 'N', 'nullable' => false));

	$phpgw_setup->oProc->CreateIndex('workorders', 'ix_workorders_product', array('product'));
	$phpgw_setup->oProc->CreateIndex('workorders', 'ix_workorders_responsible', array('responsible'));
	$phpgw_setup->oProc->CreateIndex('workorders', 'ix_workorders_status', array('status'));
	$phpgw_setup->oProc->CreateIndex('workorders', 'ix_workorders_contact_id', array('contact_id'));
	
	$phpgw_setup->oProc->CreateIndex('timecards', 'ix_timecards_jcnseq', array('jcn', 'seq'));

	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (37, 'Organization Type', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (38, 'Contact Type', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (39, 'Build Manager', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (40, 'Work Order Task', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (41, 'Workspace', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (42, 'Test Case', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (43, 'Functional Spec', 'Y')");
	
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC9' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.5RC9';
	return $setup_info['dcl']['currentver'];
}
