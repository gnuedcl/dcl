<?php
function dcl_upgrade0_9_3()
{
	global $phpgw_setup, $setup_info, $dcl_domain_info, $dcl_domain;

	$phpgw_setup->oProc->CreateTable('dcl_sccs',
					array(
						'fd' => array(
							'dcl_sccs_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'sccs_repository' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
							'sccs_descr' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false)
						),
						'pk' => array('dcl_sccs_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);

	$phpgw_setup->oProc->CreateTable('dcl_sccs_xref',
					array(
						'fd' => array(
							'dcl_sccs_xref_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'dcl_entity_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'dcl_entity_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'dcl_entity_id2' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'dcl_sccs_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'personnel_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'sccs_project_path' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
							'sccs_file_name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
							'sccs_version' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
							'sccs_comments' => array('type' => 'text'),
							'sccs_checkin_on' => array('type' => 'timestamp', 'nullable' => false)
						),
						'pk' => array('dcl_sccs_xref_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);

	$phpgw_setup->oProc->CreateTable('dcl_wo_id',
					array(
						'fd' => array(
							'jcn' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'seq' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
						),
						'pk' => array('jcn'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);

	$sType = $dcl_domain_info[$dcl_domain]['dbType'];

	// Initialize dcl_wo_id with current workorders contents
	if ($sType == 'mssql' || $sType == 'sybase')
		$phpgw_setup->oProc->Query('SET IDENTITY_INSERT dcl_wo_id ON');

	$phpgw_setup->oProc->Query("INSERT INTO dcl_wo_id (jcn, seq) SELECT jcn, max(seq) FROM workorders GROUP BY jcn");

	if ($sType == 'mssql' || $sType == 'sybase')
		$phpgw_setup->oProc->Query('SET IDENTITY_INSERT dcl_wo_id OFF');
	else if ($sType == 'pgsql')
		$phpgw_setup->oProc->Query("SELECT SETVAL('seq_dcl_wo_id', (SELECT MAX(jcn) FROM dcl_wo_id))");

	if ($sType == 'mssql' || $sType == 'sybase')
	{
		function __fixDateField($table, $field, $nullable)
		{
			global $phpgw_setup;
			$sNull = $nullable ? "NULL" : "NOT NULL";
			$phpgw_setup->oProc->Query("ALTER TABLE $table ALTER COLUMN $field varchar(10) $sNull");
			$phpgw_setup->oProc->Query("UPDATE $table SET $field = substring($field, 1, 4) + '-' + substring($field, 5, 2) + '-' + substring($field, 7, 2) where $field is not null");
			$phpgw_setup->oProc->Query("ALTER TABLE $table ALTER COLUMN $field smalldatetime $sNull");
		}

		function __fixTimestampField($table, $field, $nullable)
		{
			global $phpgw_setup;
			$sNull = $nullable ? "NULL" : "NOT NULL";
			$phpgw_setup->oProc->Query("ALTER TABLE $table ALTER COLUMN $field varchar(19) $sNull");
			$phpgw_setup->oProc->Query("UPDATE $table SET $field = substring($field, 1, 4) + '-' + substring($field, 5, 2) + '-' + substring($field, 7, 2) + ' ' + substring($field, 9, 2) + ':' + substring($field, 11, 2) + ':' + substring($field, 13, 2) where $field is not null");
			$phpgw_setup->oProc->Query("ALTER TABLE $table ALTER COLUMN $field datetime $sNull");
		}

		// Oh, the humanity!

		// timecards
		__fixDateField('timecards', 'actionon', false);
		__fixTimestampField('timecards', 'inputon', false);

		// workorders
		__fixTimestampField('workorders', 'createdon', false);
		__fixDateField('workorders', 'closedon', true);
		__fixTimestampField('workorders', 'lastactionon', true);
		__fixDateField('workorders', 'deadlineon', true);
		__fixDateField('workorders', 'eststarton', true);
		__fixDateField('workorders', 'estendon', true);
		__fixDateField('workorders', 'starton', true);

		// dcl_projects
		__fixTimestampField('dcl_projects', 'createdon', true);
		__fixDateField('dcl_projects', 'projectdeadline', true);
		__fixTimestampField('dcl_projects', 'lastactivity', true);
		__fixTimestampField('dcl_projects', 'finalclose', true);

		// tickets
		__fixTimestampField('tickets', 'createdon', false);
		__fixTimestampField('tickets', 'closedon', true);
		__fixTimestampField('tickets', 'statuson', false);
		__fixTimestampField('tickets', 'lastactionon', true);

		// ticketresolutions
		__fixTimestampField('ticketresolutions', 'loggedon', false);
		__fixTimestampField('ticketresolutions', 'startedon', false);

		// faq
		__fixTimestampField('faq', 'createon', false);
		__fixTimestampField('faq', 'modifyon', false);

		// faqtopics
		__fixTimestampField('faqtopics', 'createon', false);
		__fixTimestampField('faqtopics', 'modifyon', false);

		// faqquestions
		__fixTimestampField('faqquestions', 'createon', false);
		__fixTimestampField('faqquestions', 'modifyon', false);

		// faqanswers
		__fixTimestampField('faqanswers', 'createon', false);
		__fixTimestampField('faqanswers', 'modifyon', false);

		// dcl_config
		__fixDateField('dcl_config', 'dcl_config_date', true);
		__fixTimestampField('dcl_config', 'dcl_config_datetime', true);

		// dcl_chklst
		__fixTimestampField('dcl_chklst', 'dcl_chklst_createon', false);
		__fixTimestampField('dcl_chklst', 'dcl_chklst_modifyon', true);

		$phpgw_setup->oProc->query("UPDATE dcl_config SET dcl_config_varchar = 'Y-m-d' WHERE dcl_config_name = 'DCL_DATE_FORMAT_DB'");
		$phpgw_setup->oProc->query("UPDATE dcl_config SET dcl_config_varchar = 'Y-m-d H:i:s' WHERE dcl_config_name = 'DCL_TIMESTAMP_FORMAT_DB'");
	}
	else if ($sType == 'mysql')
	{
		function __mysql_fixTimestampField($table, $field, $nullable, $bDefaultNow)
		{
			global $phpgw_setup;
			$sNull = $nullable ? "NULL" : "NOT NULL";
			if (!$nullable)
				$phpgw_setup->oProc->Query("UPDATE $table SET $field = now() WHERE $field IS NULL");
				
			$phpgw_setup->oProc->Query("ALTER TABLE $table CHANGE $field $field datetime $sNull");
		}

		__mysql_fixTimestampField('timecards', 'inputon', true, false);
		__mysql_fixTimestampField('workorders', 'createdon', true, false);
		__mysql_fixTimestampField('workorders', 'statuson', true, false);
		__mysql_fixTimestampField('workorders', 'lastactionon', true, false);
		__mysql_fixTimestampField('dcl_projects', 'createdon', true, false);

		__mysql_fixTimestampField('dcl_projects', 'lastactivity', true, false);
		__mysql_fixTimestampField('tickets', 'createdon', false, true);
		__mysql_fixTimestampField('tickets', 'closedon', true, false);
		__mysql_fixTimestampField('tickets', 'statuson', false, false);
		__mysql_fixTimestampField('tickets', 'lastactionon', true, false);

		__mysql_fixTimestampField('ticketresolutions', 'loggedon', false, true);
		__mysql_fixTimestampField('ticketresolutions', 'startedon', false, false);
		__mysql_fixTimestampField('faq', 'createon', false, true);
		__mysql_fixTimestampField('faq', 'modifyon', true, false);
		__mysql_fixTimestampField('faqtopics', 'createon', false, true);

		__mysql_fixTimestampField('faqtopics', 'modifyon', true, false);
		__mysql_fixTimestampField('faqquestions', 'createon', false, true);
		__mysql_fixTimestampField('faqquestions', 'modifyon', true, false);
		__mysql_fixTimestampField('faqanswers', 'createon', false, true);
		__mysql_fixTimestampField('faqanswers', 'modifyon', true, false);

		__mysql_fixTimestampField('dcl_config', 'dcl_config_datetime', true, false);
		__mysql_fixTimestampField('dcl_chklst', 'dcl_chklst_createon', false, true);
		__mysql_fixTimestampField('dcl_chklst', 'dcl_chklst_modifyon', true, false);
		__mysql_fixTimestampField('dcl_session', 'create_date', false, false);
		__mysql_fixTimestampField('dcl_session', 'update_date', false, false);

		__mysql_fixTimestampField('dcl_wiki', 'page_date', true, false);

		$phpgw_setup->oProc->query("UPDATE dcl_config SET dcl_config_varchar = 'Y-m-d H:i:s' WHERE dcl_config_name = 'DCL_TIMESTAMP_FORMAT_DB'");
	}

	if ($sType == 'pgsql')
	{
		// Change applicable tables to use text instead of varchar fields
		$phpgw_setup->oProc->RefreshTable('workorders');
		$phpgw_setup->oProc->RefreshTable('timecards');
		$phpgw_setup->oProc->RefreshTable('accounts');
		$phpgw_setup->oProc->RefreshTable('dcl_projects');

		// seq_projects will get renamed to seq_dcl_projects
		$phpgw_setup->oProc->m_odb->query("SELECT relname FROM pg_class WHERE NOT relname ~ 'pg_.*' AND relname LIKE 'seq_%projects%' AND relkind='S' ORDER BY relname",__LINE__,__FILE__);
		if ($phpgw_setup->oProc->m_odb->next_record())
		{
			$sSequenceName = $phpgw_setup->oProc->m_odb->f(0);
			$phpgw_setup->oProc->query('ALTER TABLE dcl_projects ALTER projectid DROP DEFAULT');
			$phpgw_setup->oProc->query("DROP SEQUENCE $sSequenceName");
			$phpgw_setup->oProc->query('CREATE SEQUENCE seq_dcl_projects');
			$phpgw_setup->oProc->UpdateSequence('dcl_projects', 'projectid');
			$phpgw_setup->oProc->query("ALTER TABLE dcl_projects ALTER projectid SET DEFAULT nextval('seq_dcl_projects')");
		}

		// and we have one other that needs changed
		$phpgw_setup->oProc->query('ALTER TABLE severities ALTER id DROP DEFAULT');
		$phpgw_setup->oProc->query('DROP SEQUENCE seq_severity');
		$phpgw_setup->oProc->query('CREATE SEQUENCE seq_severities');
		$phpgw_setup->oProc->UpdateSequence('severities', 'id');
		$phpgw_setup->oProc->query("ALTER TABLE severities ALTER id SET DEFAULT nextval('seq_severities')");
	}
	else
	{
		// drop (and later recreate) primary key on workorders as jcn, seq - must be done before dropping oid column
		if ($sType == 'mysql')
		{
			$phpgw_setup->oProc->query('ALTER TABLE workorders DROP COLUMN oid');
		}

		$phpgw_setup->oProc->DropPrimaryKey('workorders');

		// oid is a system field in pgsql that was used as pk in other servers
		if ($phpgw_setup->oProc->m_odb->FieldExists('workorders', 'oid'))
		{
			$phpgw_setup->oProc->DropColumn('workorders', array(
					'fd' => array(
						'jcn' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'seq' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'product' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'createby' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'createdon' => array('type' => 'timestamp', 'nullable' => false),
						'closedby' => array('type' => 'int', 'precision' => 4),
						'closedon' => array('type' => 'date'),
						'status' => array('type' => 'int', 'precision' => 4),
						'statuson' => array('type' => 'timestamp', 'nullable' => false),
						'lastactionon' => array('type' => 'timestamp'),
						'deadlineon' => array('type' => 'date'),
						'eststarton' => array('type' => 'date'),
						'estendon' => array('type' => 'date'),
						'starton' => array('type' => 'date'),
						'esthours' => array('type' => 'float', 'precision' => 8),
						'totalhours' => array('type' => 'float', 'precision' => 8),
						'priority' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'severity' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'contact' => array('type' => 'varchar', 'precision' => 50),
						'contactphone' => array('type' => 'char', 'precision' => 10),
						'summary' => array('type' => 'varchar', 'precision' => 100, 'nullable' => false),
						'notes' => array('type' => 'text'),
						'description' => array('type' => 'text', 'nullable' => false),
						'responsible' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
						'revision' => array('type' => 'varchar', 'precision' => 20),
						'publicview' => array('type' => 'bool'),
						'etchours' => array('type' => 'float', 'precision' => 8),
						'module_id' => array('type' => 'int', 'precision' => 4),
						'wo_type_id' => array('type' => 'int', 'precision' => 4)
					),
					'pk' => array('jcn', 'seq'),
					'fk' => array(),
					'ix' => array(),
					'uc' => array()
				),
				'oid');
		}

		$phpgw_setup->oProc->CreatePrimaryKey('workorders', array('jcn', 'seq'));
	}

	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_SCCS_ENABLED', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.4' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.4';
	return $setup_info['dcl']['currentver'];
}
