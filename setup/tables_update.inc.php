<?php
/*
 * $Id$
 *
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2004 Free Software Foundation
 *
 * Double Choco Latte is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * Double Choco Latte is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * Select License Info from the Help menu to view the terms and conditions of this license.
 */

$test[] = '0.5.1'; // 20010327
function dcl_upgrade0_5_1()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20010327' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.2';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.5.2'; // 20010413
function dcl_upgrade0_5_2()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) VALUES ('DCL_SMTP_SERVER', 'dcl_config_varchar', 'localhost')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) VALUES ('DCL_SMTP_ENABLED', 'dcl_config_varchar', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) VALUES ('DCL_SMTP_PORT', 'dcl_config_int', 25)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) VALUES ('DCL_SMTP_TIMEOUT', 'dcl_config_int', 30)");
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20010413' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.3';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.5.3'; // 20010715
function dcl_upgrade0_5_3()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->CreateTable('dcl_chklst_tpl',
					array(
						'fd' => array(
							'dcl_chklst_tpl_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'dcl_chklst_tpl_name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
							'dcl_chklst_tpl_active' => array('type' => 'char', 'precision' => 1, 'default' => 'Y', 'nullable' => false)
						),
						'pk' => array('dcl_chklst_tpl_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);

	$phpgw_setup->oProc->CreateTable('dcl_chklst',
					array(
						'fd' => array(
							'dcl_chklst_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'dcl_chklst_tpl_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'dcl_chklst_summary' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
							'dcl_chklst_createby' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'dcl_chklst_createon' => array('type' => 'timestamp', 'nullable' => false),
							'dcl_chklst_modifyby' => array('type' => 'int', 'precision' => 4),
							'dcl_chklst_modifyon' => array('type' => 'timestamp'),
							'dcl_chklst_status' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false)
						),
						'pk' => array('dcl_chklst_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);

	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) VALUES ('DCL_PROJECT_BROWSE_PARENTS_ONLY', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) VALUES ('DCL_PROJECT_INCLUDE_PARENT_STATS', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) VALUES ('DCL_PROJECT_INCLUDE_CHILD_STATS', 'dcl_config_varchar', 'Y')");
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20010715' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.4';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.5.4'; // 20010729
function dcl_upgrade0_5_4()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20010729' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.5';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.5.5'; // 20010911
function dcl_upgrade0_5_5()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) VALUES ('DCL_FILE_PATH', 'dcl_config_varchar', '.')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) VALUES ('DCL_HTML_TITLE', 'dcl_config_varchar', 'Double Choco Latte - Copyright (C) 1998-2001 Michael L. Dean and Tim R. Norman')");
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20010911' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.6';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.5.6'; // 20010916
function dcl_upgrade0_5_6()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->CreateTable('dcl_status_type',
					array(
						'fd' => array(
							'dcl_status_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'dcl_status_type_name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false)
						),
						'pk' => array('dcl_status_type_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);

	$phpgw_setup->oProc->Query("insert into dcl_status_type values (1, 'Open')");
	$phpgw_setup->oProc->Query("insert into dcl_status_type values (2, 'Completed')");
	$phpgw_setup->oProc->Query("insert into dcl_status_type values (3, 'Deferred')");

	$phpgw_setup->oProc->AddColumn('statuses', 'dcl_status_type',
		array('type' => 'int', 'precision' => 4, 'nullable' => false, 'default' => 1));

	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) VALUES ('DCL_DEFAULT_PROJECT_STATUS', 'dcl_config_int', 1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) VALUES ('DCL_DEFAULT_TICKET_STATUS', 'dcl_config_int', 1)");
	$phpgw_setup->oProc->Query("update statuses set dcl_status_type = 1");
	$phpgw_setup->oProc->Query("update statuses set dcl_status_type = 2 where id = 2");
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20010916' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.7';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.5.7'; // 20010918
function dcl_upgrade0_5_7()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->RenameTable('projects', 'dcl_projects');
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20010918' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.8';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.5.8'; // 20010923
function dcl_upgrade0_5_8()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20010923' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.9';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.5.9'; // 20011203
function dcl_upgrade0_5_9()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_GATEWAY_TICKET_ENABLED', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_GATEWAY_TICKET_AUTORESPOND', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_GATEWAY_TICKET_REPLY', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_GATEWAY_TICKET_AUTORESPONSE_EMAIL', 'dcl_config_varchar', 'nobody@localhost')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_GATEWAY_TICKET_FILE_PATH', 'dcl_config_varchar', '/tmp')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) values ('DCL_GATEWAY_TICKET_STATUS', 'dcl_config_int', 0)");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) values ('DCL_GATEWAY_TICKET_PRIORITY', 'dcl_config_int', 0)");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) values ('DCL_GATEWAY_TICKET_SEVERITY', 'dcl_config_int', 0)");
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20011203' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.10';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.5.10'; // 20011209
function dcl_upgrade0_5_10()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20011209' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.11';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.5.11'; // 20011210
function dcl_upgrade0_5_11()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20011210' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.12';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.5.12'; // 20011215
function dcl_upgrade0_5_12()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) values ('DCL_GATEWAY_TICKET_ACCOUNT', 'dcl_config_int', 0)");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) values ('DCL_GATEWAY_TICKET_REPLY_LOGGED_BY', 'dcl_config_int', 0)");
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20011215' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.13';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.5.13'; // 20020120
function dcl_upgrade0_5_13()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20020120' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.14';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.5.14'; // 20020215
function dcl_upgrade0_5_14()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20020215' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.15';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.5.15'; // 20020706
function dcl_upgrade0_5_15()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20020706' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.16';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.5.16'; // 20021021
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

$test[] = '0.5.17'; // 20021023
function dcl_upgrade0_5_17()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_SMTP_DEFAULT_EMAIL', 'dcl_config_varchar', 'nobody@localhost')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_WO_NOTIFICATION_HTML', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_WO_EMAIL_TEMPLATE', 'dcl_config_varchar', 'notify_wo_en.tpl')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_TCK_NOTIFICATION_HTML', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_TCK_EMAIL_TEMPLATE', 'dcl_config_varchar', 'notify_tck_en.tpl')");
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='20021023' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.5.18';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.5.18';
function dcl_upgrade0_5_18()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.0' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.0';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.9.0';
function dcl_upgrade0_9_0()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.1' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.1';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.9.1';
function dcl_upgrade0_9_1()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->AddColumn('timecards', 'reassign_from_id',
		array('type' => 'int', 'precision' => 4, 'nullable' => true));

	$phpgw_setup->oProc->AddColumn('timecards', 'reassign_to_id',
		array('type' => 'int', 'precision' => 4, 'nullable' => true));

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.2' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.2';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.9.2';
function dcl_upgrade0_9_2()
{
	global $phpgw_setup, $setup_info;

	// One word: BRUTAL
	$phpgw_setup->oProc->CreateTable('dcl_wo_account',
					array(
						'fd' => array(
							'wo_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'seq' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'account_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
						),
						'pk' => array('wo_id', 'seq', 'account_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);

	$phpgw_setup->oProc->Query("Insert Into dcl_wo_account Select jcn, seq, account From workorders where account is not null and account > 0");

	$phpgw_setup->oProc->DropColumn('workorders',
					array(
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
							'notes' => array('type' => 'varchar', 'precision' => 1024),
							'description' => array('type' => 'varchar', 'precision' => 1024, 'nullable' => false),
							'responsible' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'revision' => array('type' => 'varchar', 'precision' => 20),
							'publicview' => array('type' => 'bool'),
							'etchours' => array('type' => 'float', 'precision' => 8),
							'module_id' => array('type' => 'int', 'precision' => 4)
						),
						'pk' => array('jcn', 'seq'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					),
					'account'
	);

	$phpgw_setup->oProc->CreateTable('dcl_wo_type',
					array(
						'fd' => array(
							'wo_type_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'type_name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
							'active' => array('type' => 'char', 'precision' => 1, 'nullable' => false)
						),
						'pk' => array('wo_type_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);

	$phpgw_setup->oProc->Query("Insert Into dcl_wo_type (type_name, active) values ('Issue', 'Y')");
	$phpgw_setup->oProc->AddColumn('workorders', 'wo_type_id',
		array('type' => 'int', 'precision' => 4, 'nullable' => false, 'default' => 1));

	$phpgw_setup->oProc->Query("Update workorders set wo_type_id = 1");

	$phpgw_setup->oProc->CreateTable('dcl_session',
					array(
						'fd' => array(
							'dcl_session_id' => array('type' => 'varchar', 'precision' => 32, 'nullable' => false),
							'personnel_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'create_date' => array('type' => 'timestamp'),
							'update_date' => array('type' => 'timestamp'),
							'session_data' => array('type' => 'text')
						),
						'pk' => array('dcl_session_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);

	$phpgw_setup->oProc->CreateTable('dcl_preferences',
					array(
						'fd' => array(
							'personnel_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'preferences_data' => array('type' => 'text')
						),
						'pk' => array('personnel_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);

	$phpgw_setup->oProc->CreateTable('dcl_wiki',
					array(
						'fd' => array(
							'dcl_entity_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'dcl_entity_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'dcl_entity_id2' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'page_name' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
							'page_text' => array('type' => 'text'),
							'page_date' => array('type' => 'timestamp'),
							'page_ip' => array('type' => 'varchar', 'precision' => 255)
						),
						'pk' => array('dcl_entity_type_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);

	// TODO: The next 2 lines may need abstraction
	$phpgw_setup->oProc->Query("create unique index uc_accounts_short on accounts(short)");
	$phpgw_setup->oProc->Query("create unique index uc_accounts_name on accounts(name)");

	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_GATEWAY_WO_AUTORESPOND', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_GATEWAY_WO_AUTORESPONSE_EMAIL', 'dcl_config_varchar', 'nobody@localhost')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_GATEWAY_WO_ENABLED', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_GATEWAY_WO_FILE_PATH', 'dcl_config_varchar', '/tmp')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) values ('DCL_GATEWAY_WO_PRIORITY', 'dcl_config_int', 0)");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_GATEWAY_WO_REPLY', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) values ('DCL_GATEWAY_WO_SEVERITY', 'dcl_config_int', 0)");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) values ('DCL_GATEWAY_WO_STATUS', 'dcl_config_int', 0)");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_WO_SECONDARY_ACCOUNTS_ENABLED', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_datetime) values ('LAST_CONFIG_UPDATE', 'dcl_config_datetime', " . $phpgw_setup->oProc->m_odb->GetDateSQL() . ')');
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) values ('DCL_SESSION_TIMEOUT', 'dcl_config_int', 20)");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_WIKI_ENABLED', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) values ('DCL_WIKI_VIEW', 'dcl_config_int', 4)");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) values ('DCL_WIKI_EDIT', 'dcl_config_int', 5)");
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.3' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.3';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.9.3';
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

$test[] = '0.9.4';
function dcl_upgrade0_9_4()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.4.1' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.4.1';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.9.4.1';
function dcl_upgrade0_9_4_1()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.4.2' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.4.2';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.9.4.2';
function dcl_upgrade0_9_4_2()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.4.3' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.4.3';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.9.4.3';
function dcl_upgrade0_9_4_3()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.4.4' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.4.4';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.9.4.4';
function dcl_upgrade0_9_4_4()
{
	// And you thought dcl_upgrade0_9_3 was nasty????  You ain't seen nothin' yet...
	function dcl_upgrade0_9_4_4_write_message($sMessage)
	{
		global $phpgw_setup;
		if (!$phpgw_setup->oProc->m_bDeltaOnly)
		{
			echo $sMessage;
			flush();
		}
	}

	global $phpgw_setup, $setup_info, $dcl_domain, $dcl_domain_info;
	
	$sType = $dcl_domain_info[$dcl_domain]['dbType'];

	$phpgw_setup->oProc->DropPrimaryKey('dcl_wiki');
	$phpgw_setup->oProc->CreatePrimaryKey('dcl_wiki', array('dcl_entity_type_id', 'dcl_entity_id', 'dcl_entity_id2', 'page_name'));

    if ($sType == 'pgsql')
    {
		// Fix table defaults for auto fields
        $aTables = array('departments' => 'id', 'faq' => 'faqid', 'faqtopics' => 'topicid', 'faqquestions' => 'questionid', 'faqanswers' => 'answerid');
        foreach ($aTables as $sTable => $sField)
			$phpgw_setup->oProc->Query("ALTER TABLE $sTable ALTER $sField SET DEFAULT nextval('seq_$sTable')");
    }

	$phpgw_setup->oProc->AddColumn('products', 'is_versioned', array('type' => 'char', 'precision' => 1, 'nullable' => false, 'default' => 'N'));

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

	$phpgw_setup->oProc->AlterColumn('personnel', 'short', array('type' => 'varchar', 'precision' => 25, 'nullable' => false));

	// Tidy up the short field
	$phpgw_setup->oProc->Query('update personnel set short = rtrim(short)');

	// Adding support for public interfaces and ticket/work order sources
	$phpgw_setup->oProc->CreateTable('dcl_entity_source',
					array(
						'fd' => array(
							'entity_source_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'entity_source_name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
							'active' => array('type' => 'char', 'precision' => 1, 'default' => 'Y', 'nullable' => false)
						),
						'pk' => array('entity_source_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);

	$phpgw_setup->oProc->AddColumn('workorders', 'entity_source_id', array('type' => 'int', 'precision' => 4));
	$phpgw_setup->oProc->AddColumn('workorders', 'is_public', array('type' => 'char', 'precision' => 1, 'nullable' => false, 'default' => 'N'));
	$phpgw_setup->oProc->AddColumn('tickets', 'entity_source_id', array('type' => 'int', 'precision' => 4));
	$phpgw_setup->oProc->AddColumn('tickets', 'is_public', array('type' => 'char', 'precision' => 1, 'nullable' => false, 'default' => 'N'));

	$phpgw_setup->oProc->AddColumn('timecards', 'is_public', array('type' => 'char', 'precision' => 1, 'nullable' => false, 'default' => 'N'));
	$phpgw_setup->oProc->AddColumn('ticketresolutions', 'is_public', array('type' => 'char', 'precision' => 1, 'nullable' => false, 'default' => 'N'));
	$phpgw_setup->oProc->AddColumn('products', 'is_public', array('type' => 'char', 'precision' => 1, 'nullable' => false, 'default' => 'N'));

	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_BUILD_MANAGER_ENABLED', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_SMTP_AUTH_REQUIRED', 'dcl_config_varchar', 'N')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_SMTP_AUTH_USER', 'dcl_config_varchar', 'smtp_user')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) values ('DCL_SMTP_AUTH_PWD', 'dcl_config_varchar', 'smtp_pass')");

	$phpgw_setup->oProc->CreateTable('dcl_addr_type', array(
						'fd' => array(
							'addr_type_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'addr_type_name' => array('type' => 'varchar', 'precision' => 30, 'nullable' => false)
						),
						'pk' => array('addr_type_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);
	$phpgw_setup->oProc->CreateTable('dcl_email_type', array(
						'fd' => array(
							'email_type_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'email_type_name' => array('type' => 'varchar', 'precision' => 30, 'nullable' => false)
						),
						'pk' => array('email_type_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);
	$phpgw_setup->oProc->CreateTable('dcl_note_type', array(
						'fd' => array(
							'note_type_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'note_type_name' => array('type' => 'varchar', 'precision' => 30, 'nullable' => false)
						),
						'pk' => array('note_type_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);
	$phpgw_setup->oProc->CreateTable('dcl_phone_type', array(
						'fd' => array(
							'phone_type_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'phone_type_name' => array('type' => 'varchar', 'precision' => 30, 'nullable' => false)
						),
						'pk' => array('phone_type_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);
	$phpgw_setup->oProc->CreateTable('dcl_url_type', array(
						'fd' => array(
							'url_type_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'url_type_name' => array('type' => 'varchar', 'precision' => 30, 'nullable' => false)
						),
						'pk' => array('url_type_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);
	$phpgw_setup->oProc->CreateTable('dcl_org_type', array(
						'fd' => array(
							'org_type_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'org_type_name' => array('type' => 'varchar', 'precision' => 30, 'nullable' => false)
						),
						'pk' => array('org_type_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);
	$phpgw_setup->oProc->CreateTable('dcl_contact_type', array(
						'fd' => array(
							'contact_type_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'contact_type_name' => array('type' => 'varchar', 'precision' => 30, 'nullable' => false)
						),
						'pk' => array('contact_type_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);

	$phpgw_setup->oProc->CreateTable('dcl_contact', array(
						'fd' => array(
							'contact_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'first_name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
							'last_name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
							'middle_name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => true),
							'active' => array('type' => 'varchar', 'precision' => 30, 'nullable' => false, 'default' => 'Y'),
							'created_on' => array('type' => 'timestamp', 'nullable' => false),
							'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'modified_on' => array('type' => 'timestamp', 'nullable' => true),
							'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
						),
						'pk' => array('contact_id'),
						'fk' => array(),
						'ix' => array(
							'ix_dcl_contact_name_id' => array('last_name', 'first_name', 'contact_id')
						),
						'uc' => array()
					)
	);
	$phpgw_setup->oProc->CreateTable('dcl_contact_addr', array(
						'fd' => array(
							'contact_addr_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'contact_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'addr_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'add1' => array('type' => 'varchar', 'precision' => 50, 'nullable' => true),
							'add2' => array('type' => 'varchar', 'precision' => 50, 'nullable' => true),
							'city' => array('type' => 'varchar', 'precision' => 50, 'nullable' => true),
							'state' => array('type' => 'varchar', 'precision' => 30, 'nullable' => true),
							'zip' => array('type' => 'varchar', 'precision' => 20, 'nullable' => true),
							'country' => array('type' => 'varchar', 'precision' => 40, 'nullable' => true),
							'preferred' => array('type' => 'char', 'precision' => 1, 'default' => 'N', 'nullable' => false),
							'created_on' => array('type' => 'timestamp', 'nullable' => false),
							'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'modified_on' => array('type' => 'timestamp', 'nullable' => true),
							'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
						),
						'pk' => array('contact_addr_id'),
						'fk' => array(),
						'ix' => array(
							'ix_dcl_contact_addr_contact' => array('contact_id')
						),
						'uc' => array()
					)
	);
	$phpgw_setup->oProc->CreateTable('dcl_contact_email', array(
						'fd' => array(
							'contact_email_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'contact_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'email_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'email_addr' => array('type' => 'varchar', 'precision' => 100, 'nullable' => true),
							'preferred' => array('type' => 'char', 'precision' => 1, 'default' => 'N', 'nullable' => false),
							'created_on' => array('type' => 'timestamp', 'nullable' => false),
							'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'modified_on' => array('type' => 'timestamp', 'nullable' => true),
							'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
						),
						'pk' => array('contact_email_id'),
						'fk' => array(),
						'ix' => array(
							'ix_dcl_contact_email_contact' => array('contact_id')
						),
						'uc' => array()
					)
	);
	$phpgw_setup->oProc->CreateTable('dcl_contact_note', array(
						'fd' => array(
							'contact_note_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'contact_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'note_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'note_text' => array('type' => 'text', 'nullable' => false),
							'created_on' => array('type' => 'timestamp', 'nullable' => false),
							'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'modified_on' => array('type' => 'timestamp', 'nullable' => true),
							'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
						),
						'pk' => array('contact_note_id'),
						'fk' => array(),
						'ix' => array(
							'ix_dcl_contact_note_contact' => array('contact_id')
						),
						'uc' => array()
					)
	);
	$phpgw_setup->oProc->CreateTable('dcl_contact_phone', array(
						'fd' => array(
							'contact_phone_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'contact_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'phone_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'phone_number' => array('type' => 'varchar', 'precision' => 30, 'nullable' => false),
							'preferred' => array('type' => 'char', 'precision' => 1, 'default' => 'N', 'nullable' => false),
							'created_on' => array('type' => 'timestamp', 'nullable' => false),
							'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'modified_on' => array('type' => 'timestamp', 'nullable' => true),
							'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
						),
						'pk' => array('contact_phone_id'),
						'fk' => array(),
						'ix' => array(
							'ix_dcl_contact_phone_contact' => array('contact_id')
						),
						'uc' => array()
					)
	);
	$phpgw_setup->oProc->CreateTable('dcl_contact_type_xref', array(
						'fd' => array(
							'contact_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'contact_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
						),
						'pk' => array('contact_id', 'contact_type_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);
	$phpgw_setup->oProc->CreateTable('dcl_contact_url', array(
						'fd' => array(
							'contact_url_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'contact_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'url_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'url_addr' => array('type' => 'varchar', 'precision' => 150, 'nullable' => false),
							'preferred' => array('type' => 'char', 'precision' => 1, 'default' => 'N', 'nullable' => false),
							'created_on' => array('type' => 'timestamp', 'nullable' => false),
							'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'modified_on' => array('type' => 'timestamp', 'nullable' => true),
							'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
						),
						'pk' => array('contact_url_id'),
						'fk' => array(),
						'ix' => array(
							'ix_dcl_contact_url_contact' => array('contact_id')
						),
						'uc' => array()
					)
	);
	$phpgw_setup->oProc->CreateTable('dcl_org', array(
						'fd' => array(
							'org_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'name' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
							'active' => array('type' => 'varchar', 'precision' => 1, 'default' => 'Y', 'nullable' => false),
							'parent' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
							'created_on' => array('type' => 'timestamp', 'nullable' => false),
							'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'modified_on' => array('type' => 'timestamp', 'nullable' => true),
							'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
						),
						'pk' => array('org_id'),
						'fk' => array(),
						'ix' => array(
							'ix_dcl_org_name_id' => array('name', 'org_id')
						),
						'uc' => array()
					)
	);
	$phpgw_setup->oProc->CreateTable('dcl_org_addr', array(
						'fd' => array(
							'org_addr_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'org_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'addr_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'add1' => array('type' => 'varchar', 'precision' => 50, 'nullable' => true),
							'add2' => array('type' => 'varchar', 'precision' => 50, 'nullable' => true),
							'city' => array('type' => 'varchar', 'precision' => 50, 'nullable' => true),
							'state' => array('type' => 'varchar', 'precision' => 30, 'nullable' => true),
							'zip' => array('type' => 'varchar', 'precision' => 20, 'nullable' => true),
							'country' => array('type' => 'varchar', 'precision' => 40, 'nullable' => true),
							'preferred' => array('type' => 'char', 'precision' => 1, 'default' => 'N', 'nullable' => false),
							'created_on' => array('type' => 'timestamp', 'nullable' => false),
							'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'modified_on' => array('type' => 'timestamp', 'nullable' => true),
							'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
						),
						'pk' => array('org_addr_id'),
						'fk' => array(),
						'ix' => array(
							'ix_dcl_org_addr_org' => array('org_id')
						),
						'uc' => array()
					)
	);
	$phpgw_setup->oProc->CreateTable('dcl_org_alias', array(
						'fd' => array(
							'org_alias_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'org_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'alias' => array('type' => 'varchar', 'precision' => 50, 'nullable' => true),
							'created_on' => array('type' => 'timestamp', 'nullable' => false),
							'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'modified_on' => array('type' => 'timestamp', 'nullable' => true),
							'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
						),
						'pk' => array('org_alias_id'),
						'fk' => array(),
						'ix' => array(
							'ix_dcl_org_alias_org' => array('org_id')
						),
						'uc' => array()
					)
	);
	$phpgw_setup->oProc->CreateTable('dcl_org_contact', array(
						'fd' => array(
							'org_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'contact_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'created_on' => array('type' => 'timestamp', 'nullable' => false),
							'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
						),
						'pk' => array('org_id', 'contact_id'),
						'fk' => array(),
						'ix' => array(
							'ix_dcl_org_contact_contact' => array('contact_id')
						),
						'uc' => array()
					)
	);
	$phpgw_setup->oProc->CreateTable('dcl_org_email', array(
						'fd' => array(
							'org_email_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'org_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'email_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'email_addr' => array('type' => 'varchar', 'precision' => 100, 'nullable' => true),
							'preferred' => array('type' => 'char', 'precision' => 1, 'default' => 'N', 'nullable' => false),
							'created_on' => array('type' => 'timestamp', 'nullable' => false),
							'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'modified_on' => array('type' => 'timestamp', 'nullable' => true),
							'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
						),
						'pk' => array('org_email_id'),
						'fk' => array(),
						'ix' => array(
							'ix_dcl_org_email_org' => array('org_id')
						),
						'uc' => array()
					)
	);
	$phpgw_setup->oProc->CreateTable('dcl_org_note', array(
						'fd' => array(
							'org_note_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'org_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'note_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'note_text' => array('type' => 'text', 'nullable' => false),
							'created_on' => array('type' => 'timestamp', 'nullable' => false),
							'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'modified_on' => array('type' => 'timestamp', 'nullable' => true),
							'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
						),
						'pk' => array('org_note_id'),
						'fk' => array(),
						'ix' => array(
							'ix_dcl_org_note_org' => array('org_id')
						),
						'uc' => array()
					)
	);
	$phpgw_setup->oProc->CreateTable('dcl_org_phone', array(
						'fd' => array(
							'org_phone_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'org_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'phone_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'phone_number' => array('type' => 'varchar', 'precision' => 30, 'nullable' => false),
							'preferred' => array('type' => 'char', 'precision' => 1, 'default' => 'N', 'nullable' => false),
							'created_on' => array('type' => 'timestamp', 'nullable' => false),
							'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'modified_on' => array('type' => 'timestamp', 'nullable' => true),
							'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
						),
						'pk' => array('org_phone_id'),
						'fk' => array(),
						'ix' => array(
							'ix_dcl_org_phone_org' => array('org_id')
						),
						'uc' => array()
					)
	);
	$phpgw_setup->oProc->CreateTable('dcl_org_type_xref', array(
						'fd' => array(
							'org_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'org_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
						),
						'pk' => array('org_id', 'org_type_id'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);
	$phpgw_setup->oProc->CreateTable('dcl_org_url', array(
						'fd' => array(
							'org_url_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'org_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'url_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'url_addr' => array('type' => 'varchar', 'precision' => 150, 'nullable' => false),
							'preferred' => array('type' => 'char', 'precision' => 1, 'default' => 'N', 'nullable' => false),
							'created_on' => array('type' => 'timestamp', 'nullable' => false),
							'created_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'modified_on' => array('type' => 'timestamp', 'nullable' => true),
							'modified_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
						),
						'pk' => array('org_url_id'),
						'fk' => array(),
						'ix' => array(
							'ix_dcl_org_url_org' => array('org_id')
						),
						'uc' => array()
					)
	);

	$phpgw_setup->oProc->CreateTable('dcl_projects_audit', array(
						'fd' => array(
							'projectid' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'name' => array('type' => 'varchar', 'precision' => 100),
							'reportto' => array('type' => 'int', 'precision' => 4),
							'createdby' => array('type' => 'int', 'precision' => 4),
							'createdon' => array('type' => 'timestamp'),
							'projectdeadline' => array('type' => 'date'),
							'description' => array('type' => 'text'),
							'status' => array('type' => 'int', 'precision' => 4),
							'lastactivity' => array('type' => 'timestamp'),
							'finalclose' => array('type' => 'date'),
							'parentprojectid' => array('type' => 'int', 'precision' => 4),
							'audit_on' => array('type' => 'timestamp', 'nullable' => false),
							'audit_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'audit_version' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
						),
						'pk' => array('projectid', 'audit_version'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);
	
	$phpgw_setup->oProc->CreateTable('dcl_wo_account_audit', array(
						'fd' => array(
							'wo_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'seq' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'account_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'audit_on' => array('type' => 'timestamp', 'nullable' => false),
							'audit_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'audit_type' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
						),
						'pk' => array(),
						'fk' => array(),
						'ix' => array('ix_dcl_wo_account_audit' => array('wo_id', 'seq', 'account_id')),
						'uc' => array()
					)
	);
	
	$phpgw_setup->oProc->CreateTable('projectmap_audit', array(
						'fd' => array(
							'projectid' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'jcn' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'seq' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'audit_on' => array('type' => 'timestamp', 'nullable' => false),
							'audit_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'audit_type' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
						),
						'pk' => array('projectid', 'jcn', 'seq'),
						'fk' => array(),
						'ix' => array(
								'ix_projectmap_audit' => array('projectid', 'jcn', 'seq'),
								'ix_projectmap_audit_wo' => array('jcn', 'seq')
							),
						'uc' => array()
					)
	);
	
	$phpgw_setup->oProc->CreateTable('tickets_audit', array(
						'fd' => array(
							'ticketid' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'product' => array('type' => 'int', 'precision' => 4),
							'account' => array('type' => 'int', 'precision' => 4),
							'createdby' => array('type' => 'int', 'precision' => 4),
							'createdon' => array('type' => 'timestamp'),
							'responsible' => array('type' => 'int', 'precision' => 4),
							'closedby' => array('type' => 'int', 'precision' => 4),
							'closedon' => array('type' => 'timestamp'),
							'status' => array('type' => 'int', 'precision' => 4),
							'statuson' => array('type' => 'timestamp'),
							'lastactionon' => array('type' => 'timestamp'),
							'priority' => array('type' => 'int', 'precision' => 4),
							'type' => array('type' => 'int', 'precision' => 4),
							'issue' => array('type' => 'text'),
							'version' => array('type' => 'varchar', 'precision' => 20),
							'summary' => array('type' => 'varchar', 'precision' => 100),
							'seconds' => array('type' => 'int', 'precision' => 4),
							'module_id' => array('type' => 'int', 'precision' => 4),
							'entity_source_id' => array('type' => 'int', 'precision' => 4),
							'is_public' => array('type' => 'char', 'precision' => 1),
							'contact_id' => array('type' => 'int', 'precision' => 4),
							'audit_on' => array('type' => 'timestamp', 'nullable' => false),
							'audit_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'audit_version' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
						),
						'pk' => array('ticketid', 'audit_version'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);
	
	$phpgw_setup->oProc->CreateTable('workorders_audit', array(
						'fd' => array(
							'jcn' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'seq' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'product' => array('type' => 'int', 'precision' => 4),
							'createby' => array('type' => 'int', 'precision' => 4),
							'createdon' => array('type' => 'timestamp'),
							'closedby' => array('type' => 'int', 'precision' => 4),
							'closedon' => array('type' => 'date'),
							'status' => array('type' => 'int', 'precision' => 4),
							'statuson' => array('type' => 'timestamp'),
							'lastactionon' => array('type' => 'timestamp'),
							'deadlineon' => array('type' => 'date'),
							'eststarton' => array('type' => 'date'),
							'estendon' => array('type' => 'date'),
							'starton' => array('type' => 'date'),
							'esthours' => array('type' => 'float', 'precision' => 8),
							'totalhours' => array('type' => 'float', 'precision' => 8),
							'priority' => array('type' => 'int', 'precision' => 4),
							'severity' => array('type' => 'int', 'precision' => 4),
							'summary' => array('type' => 'varchar', 'precision' => 100),
							'notes' => array('type' => 'text'),
							'description' => array('type' => 'text'),
							'responsible' => array('type' => 'int', 'precision' => 4),
							'revision' => array('type' => 'varchar', 'precision' => 20),
							'etchours' => array('type' => 'float', 'precision' => 8),
							'module_id' => array('type' => 'int', 'precision' => 4),
							'wo_type_id' => array('type' => 'int', 'precision' => 4),
							'entity_source_id' => array('type' => 'int', 'precision' => 4),
							'is_public' => array('type' => 'char', 'precision' => 1),
							'contact_id' => array('type' => 'int', 'precision' => 4),
							'audit_on' => array('type' => 'timestamp', 'nullable' => false),
							'audit_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'audit_version' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
						),
						'pk' => array('jcn', 'seq', 'audit_version'),
						'fk' => array(),
						'ix' => array(),
						'uc' => array()
					)
	);

	$phpgw_setup->oProc->CreateTable('dcl_entity', array(
			'fd' => array(
				'entity_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'entity_desc' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
				'active' => array('type' => 'char', 'precision' => 1, 'nullable' => false)
			),
			'pk' => array('entity_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
	
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (0, 'Global', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (1, 'Project', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (2, 'Work Order', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (3, 'Ticket', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (4, 'Product', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (5, 'Organization', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (6, 'Department', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (7, 'Personnel', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (8, 'Action', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (9, 'Status', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (10, 'Priority', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (11, 'Severity', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (12, 'Time Card', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (13, 'Ticket Resolution', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (14, 'Contact', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (15, 'FAQ', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (16, 'FAQ Topic', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (17, 'FAQ Question', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (18, 'FAQ Answer', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (19, 'Form', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (20, 'Administration', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (21, 'Attribute Set', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (22, 'Form Template', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (23, 'Address Type', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (24, 'E-Mail Type', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (25, 'Note Type', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (26, 'Phone Type', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (27, 'URL Type', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (28, 'Source', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (29, 'Lookup', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (30, 'Preferences', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (31, 'Product Module', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (32, 'Saved Search', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (33, 'Work Order Type', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (34, 'ChangeLog', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (35, 'Session', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity VALUES (36, 'Role', 'Y')");
	
	$phpgw_setup->oProc->CreateTable('dcl_perm', array(
			'fd' => array(
				'perm_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'perm_desc' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
				'active' => array('type' => 'char', 'precision' => 1, 'nullable' => false)
			),
			'pk' => array('perm_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
	
	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm VALUES (0, 'Administration', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm VALUES (1, 'Add', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm VALUES (2, 'Modify', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm VALUES (3, 'Delete', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm VALUES (4, 'View', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm VALUES (5, 'View (Private)', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm VALUES (6, 'View (Organization)', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm VALUES (7, 'View (Submitted)', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm VALUES (8, 'Copy To Work Order', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm VALUES (9, 'Assign', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm VALUES (10, 'Action', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm VALUES (11, 'Change Password', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm VALUES (12, 'Import', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm VALUES (13, 'Search', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm VALUES (14, 'Schedule', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm VALUES (15, 'Report', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm VALUES (16, 'Add Task', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm VALUES (17, 'Remove Task', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm VALUES (18, 'Attach File', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm VALUES (19, 'Remove File', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm VALUES (20, 'View Wiki', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm VALUES (21, 'Public Items Only', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm VALUES (22, 'View File Attachment', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_perm VALUES (23, 'Audit', 'Y')");
	
	$phpgw_setup->oProc->CreateTable('dcl_entity_perm', array(
			'fd' => array(
				'entity_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'perm_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
			),
			'pk' => array('entity_id', 'perm_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
	
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (0, 0)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (0,21)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (1,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (1,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (1,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (1,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (1,9)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (1,16)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (1,17)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (1,18)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (1,19)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (1,20)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (1,22)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (1,23)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (2,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (2,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (2,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (2,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (2,6)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (2,7)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (2,9)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (2,10)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (2,12)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (2,13)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (2,14)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (2,15)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (2,18)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (2,19)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (2,20)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (2,22)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (2,23)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (3,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (3,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (3,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (3,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (3,6)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (3,7)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (3,8)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (3,9)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (3,10)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (3,12)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (3,13)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (3,15)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (3,18)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (3,19)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (3,20)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (3,22)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (3,23)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (4,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (4,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (4,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (4,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (4,20)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (5,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (5,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (5,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (5,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (5,20)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (6,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (6,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (6,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (6,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (6,20)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (7,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (7,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (7,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (7,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (7,11)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (8,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (8,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (8,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (8,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (9,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (9,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (9,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (9,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (10,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (10,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (10,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (10,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (11,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (11,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (11,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (11,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (12,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (12,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (12,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (13,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (13,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (13,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (14,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (14,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (14,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (14,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (15,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (15,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (15,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (15,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (16,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (16,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (16,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (17,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (17,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (17,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (18,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (18,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (18,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (19,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (19,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (19,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (19,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (19,9)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (20,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (20,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (20,11)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (21,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (21,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (21,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (21,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (22,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (22,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (22,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (22,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (23,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (23,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (23,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (23,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (24,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (24,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (24,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (24,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (25,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (25,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (25,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (25,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (26,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (26,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (26,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (26,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (27,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (27,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (27,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (27,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (28,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (28,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (28,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (28,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (29,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (29,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (29,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (29,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (30,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (30,11)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (31,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (31,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (31,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (31,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (32,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (32,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (32,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (32,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (33,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (33,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (33,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (33,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (34,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (34,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (34,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (34,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (35,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (35,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (36,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (36,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (36,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (36,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (37,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (37,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (37,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (37,4)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (38,1)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (38,2)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (38,3)");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_entity_perm VALUES (38,4)");
	
	$phpgw_setup->oProc->CreateTable('dcl_role', array(
			'fd' => array(
				'role_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
				'role_desc' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
				'active' => array('type' => 'char', 'precision' => 1, 'nullable' => false)
			),
			'pk' => array('role_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
	
	$phpgw_setup->oProc->Query("INSERT INTO dcl_role (role_desc, active) VALUES ('Administrator (Level 9)', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_role (role_desc, active) VALUES ('Guest (Level 2)', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_role (role_desc, active) VALUES ('Submit (Level 3)', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_role (role_desc, active) VALUES ('Action (Level 4)', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_role (role_desc, active) VALUES ('Assign (Level 5)', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_role (role_desc, active) VALUES ('Modify (Level 7)', 'Y')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_role (role_desc, active) VALUES ('Delete (Level 8)', 'Y')");
	
	$phpgw_setup->oProc->CreateTable('dcl_role_perm', array(
			'fd' => array(
				'role_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'entity_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'perm_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
			),
			'pk' => array('role_id', 'entity_id', 'perm_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
	
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (1,0,0)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (2,1,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (2,1,22)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (2,2,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (2,2,13)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (2,2,22)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (2,3,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (2,3,13)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (2,3,22)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (2,12,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (2,13,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (2,15,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (2,30,11)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (3,1,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (3,1,22)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (3,2,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (3,2,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (3,2,13)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (3,2,18)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (3,2,22)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (3,3,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (3,3,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (3,3,13)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (3,3,18)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (3,3,22)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (3,12,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (3,13,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (3,15,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (3,30,11)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (4,1,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (4,1,20)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (4,1,22)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (4,2,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (4,2,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (4,2,10)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (4,2,13)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (4,2,18)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (4,2,20)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (4,2,22)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (4,3,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (4,3,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (4,3,10)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (4,3,13)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (4,3,18)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (4,3,20)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (4,3,22)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (4,5,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (4,5,20)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (4,12,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (4,13,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (4,15,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (4,30,11)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,1,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,1,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,1,9)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,1,16)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,1,17)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,1,18)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,1,19)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,1,20)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,1,22)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,2,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,2,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,2,9)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,2,10)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,2,12)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,2,13)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,2,14)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,2,15)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,2,18)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,2,19)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,2,20)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,2,22)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,3,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,3,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,3,8)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,3,9)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,3,10)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,3,12)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,3,13)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,3,15)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,3,18)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,3,19)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,3,20)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,3,22)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,4,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,4,20)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,5,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,5,20)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,6,20)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,12,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,13,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,15,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,15,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,16,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,17,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,18,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,19,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,19,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,19,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,19,9)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,22,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,30,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (5,30,11)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,1,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,1,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,1,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,1,9)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,1,16)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,1,17)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,1,18)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,1,19)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,1,20)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,1,22)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,2,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,2,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,2,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,2,9)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,2,10)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,2,12)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,2,13)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,2,14)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,2,15)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,2,18)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,2,19)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,2,20)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,2,22)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,3,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,3,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,3,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,3,8)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,3,9)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,3,10)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,3,12)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,3,13)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,3,15)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,3,18)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,3,19)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,3,20)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,3,22)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,4,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,4,20)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,5,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,5,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,5,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,5,20)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,6,20)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,12,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,12,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,13,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,13,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,14,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,14,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,14,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,15,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,15,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,15,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,16,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,16,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,17,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,17,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,18,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,18,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,19,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,19,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,19,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,19,9)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,22,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,30,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (6,30,11)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,1,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,1,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,1,3)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,1,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,1,9)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,1,16)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,1,17)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,1,18)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,1,19)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,1,20)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,1,22)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,2,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,2,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,2,3)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,2,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,2,9)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,2,10)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,2,12)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,2,13)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,2,14)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,2,15)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,2,18)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,2,19)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,2,20)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,2,22)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,3,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,3,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,3,3)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,3,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,3,8)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,3,9)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,3,10)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,3,12)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,3,13)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,3,15)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,3,18)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,3,19)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,3,20)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,3,22)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,4,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,4,20)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,5,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,5,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,5,3)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,5,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,5,20)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,6,20)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,12,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,12,3)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,12,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,13,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,13,3)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,13,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,14,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,14,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,14,3)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,14,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,15,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,15,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,15,3)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,15,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,16,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,16,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,16,3)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,17,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,17,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,17,3)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,18,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,18,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,18,3)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,19,1)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,19,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,19,3)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,19,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,19,9)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,22,4)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,30,2)');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_role_perm VALUES (7,30,11)');
	
	$phpgw_setup->oProc->CreateTable('dcl_user_role', array(
			'fd' => array(
				'personnel_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'entity_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'entity_id1' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'entity_id2' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'role_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
			),
			'pk' => array('personnel_id', 'entity_type_id', 'entity_id1', 'entity_id2', 'role_id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		)
	);
	
	$phpgw_setup->oProc->Query('INSERT INTO dcl_user_role SELECT id, 0, 0, 0, 1 FROM personnel WHERE security > 8');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_user_role SELECT id, 0, 0, 0, 2 FROM personnel WHERE security < 3');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_user_role SELECT id, 0, 0, 0, 3 FROM personnel WHERE security = 3');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_user_role SELECT id, 0, 0, 0, 4 FROM personnel WHERE security = 4');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_user_role SELECT id, 0, 0, 0, 5 FROM personnel WHERE security = 5');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_user_role SELECT id, 0, 0, 0, 6 FROM personnel WHERE security = 7 OR security = 6');
	$phpgw_setup->oProc->Query('INSERT INTO dcl_user_role SELECT id, 0, 0, 0, 7 FROM personnel WHERE security = 8');

	// Convert accounts table to real organization entries
	$phpgw_setup->oProc->Query("INSERT INTO dcl_org_type (org_type_name) VALUES ('Converted')");
	if (!$phpgw_setup->oProc->m_bDeltaOnly)
		$iOrgTypeID = $phpgw_setup->oProc->m_odb->GetLastInsertID('dcl_org_type');
	
	$phpgw_setup->oProc->Query("INSERT INTO dcl_contact_type (contact_type_name) VALUES ('Converted')");
	if (!$phpgw_setup->oProc->m_bDeltaOnly)
		$iContactTypeID = $phpgw_setup->oProc->m_odb->GetLastInsertID('dcl_contact_type');
	
	if ($sType == 'mssql' || $sType == 'sybase')
		$phpgw_setup->oProc->Query('SET IDENTITY_INSERT dcl_org ON');
	
	dcl_upgrade0_9_4_4_write_message('<br>Converting orgs...');
	$phpgw_setup->oProc->Query("insert into dcl_org (org_id, name, active, created_on, created_by) select id, name, active, " . $phpgw_setup->oProc->m_odb->GetDateSQL() . ", 1 from accounts");
	$phpgw_setup->oProc->UpdateSequence('dcl_org', 'org_id');
	
	if ($sType == 'mssql' || $sType == 'sybase')
		$phpgw_setup->oProc->Query('SET IDENTITY_INSERT dcl_org OFF');

	if (!$phpgw_setup->oProc->m_bDeltaOnly)
	{
		$phpgw_setup->oProc->Query("insert into dcl_org_alias (org_id, alias, created_on, created_by) select id, short, " . $phpgw_setup->oProc->m_odb->GetDateSQL() . ", 1 from accounts WHERE short IS NOT NULL AND short != ''");
		$phpgw_setup->oProc->Query("insert into dcl_phone_type (phone_type_name) values ('Voice')");
		$phpgw_setup->oProc->Query("insert into dcl_org_phone (org_id, phone_type_id, phone_number, preferred, created_on, created_by) select id, 1, voice, 'Y', " . $phpgw_setup->oProc->m_odb->GetDateSQL() . ", 1 from accounts where voice is not null and voice != ''");
		$phpgw_setup->oProc->Query("insert into dcl_phone_type (phone_type_name) values ('Fax')");
		$phpgw_setup->oProc->Query("insert into dcl_org_phone (org_id, phone_type_id, phone_number, preferred, created_on, created_by) select id, 2, fax, 'N', " . $phpgw_setup->oProc->m_odb->GetDateSQL() . ", 1 from accounts where fax is not null and fax != ''");
		$phpgw_setup->oProc->Query("insert into dcl_phone_type (phone_type_name) values ('Data1')");
		$phpgw_setup->oProc->Query("insert into dcl_org_phone (org_id, phone_type_id, phone_number, preferred, created_on, created_by) select id, 3, data1, 'N', " . $phpgw_setup->oProc->m_odb->GetDateSQL() . ", 1 from accounts where data1 is not null and data1 != ''");
		$phpgw_setup->oProc->Query("insert into dcl_phone_type (phone_type_name) values ('Data2')");
		$phpgw_setup->oProc->Query("insert into dcl_org_phone (org_id, phone_type_id, phone_number, preferred, created_on, created_by) select id, 4, data2, 'N', " . $phpgw_setup->oProc->m_odb->GetDateSQL() . ", 1 from accounts where data2 is not null and data2 != ''");
		$phpgw_setup->oProc->Query("insert into dcl_addr_type (addr_type_name) values ('Default')");
		$phpgw_setup->oProc->Query("insert into dcl_org_addr (org_id, addr_type_id, add1, add2, city, state, zip, preferred, created_by) select id, 1, add1, add2, city, state, zip, 'Y', 1 from accounts where (add1 is not null and add1 != '') or (add2 is not null and add2 != '') or (city is not null and city != '') or (state is not null and state != '') or (zip is not null and zip != '')");
		$phpgw_setup->oProc->Query("insert into dcl_note_type (note_type_name) values ('Converted')");
		$phpgw_setup->oProc->Query("insert into dcl_org_note (org_id, note_type_id, note_text, created_on, created_by) select id, 1, notes, " . $phpgw_setup->oProc->m_odb->GetDateSQL() . ", 1 from accounts where notes is not null and notes != ''");
		$phpgw_setup->oProc->Query("INSERT INTO dcl_org_type_xref SELECT org_id, $iOrgTypeID FROM dcl_org");
	}
	
	$phpgw_setup->oProc->DropTable('accounts');
	
	// Now is the time to convert the contacts
	if (!$phpgw_setup->oProc->m_bDeltaOnly)
	{
		// Temporary working table
		$phpgw_setup->oProc->CreateTable('dcl_contact_cnv', array(
				'fd' => array(
					'cnv_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
					'entity_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'entity_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'entity_id2' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
					'contactname' => array('type' => 'varchar', 'precision' => 80),
					'contactphone' => array('type' => 'varchar', 'precision' => 20),
					'contactemail' => array('type' => 'varchar', 'precision' => 100),
					'lastname' => array('type' => 'varchar', 'precision' => 50),
					'firstname' => array('type' => 'varchar', 'precision' => 50),
					'contactphone_num' => array('type' => 'varchar', 'precision' => 20),
					'contact_id' => array('type' => 'int', 'precision' => 4)
				),
				'pk' => array('cnv_id'),
				'fk' => array(),
				'ix' => array(
					'ix_dcl_contact_cnv_1' => array('lastname', 'firstname', 'contactphone_num'),
					'ix_dcl_contact_cnv_2' => array('entity_type_id')
				),
				'uc' => array()
			)
		);
		
		// users
		dcl_upgrade0_9_4_4_write_message('<br>Getting user contacts...');
		$phpgw_setup->oProc->Query('insert into dcl_contact_cnv (entity_type_id, entity_id, entity_id2, lastname, firstname, contactemail) select 7, id, 0, lastname, firstname, email from personnel');
		
		// work orders
		dcl_upgrade0_9_4_4_write_message('<br>Getting work order contacts...');
		$phpgw_setup->oProc->Query("insert into dcl_contact_cnv (entity_type_id, entity_id, entity_id2, contactname, contactphone) select 2, jcn, seq, contact, contactphone from workorders where (contact is not null and contact != '') or (contactphone is not null and contactphone != '')");
		
		// tickets
		dcl_upgrade0_9_4_4_write_message('<br>Getting ticket contacts...');
		$phpgw_setup->oProc->Query('insert into dcl_contact_cnv (entity_type_id, entity_id, entity_id2, contactname, contactphone, contactemail) select 3, ticketid, 0, contact, contactphone, contactemail from tickets');

		// Scrub the data - very basic, nothing fancy
		$oDB = new dclDB;
		$i = 0;
		dcl_upgrade0_9_4_4_write_message('<br/>Scrubbing contact data (# = 1000): ');
		$oDB->Query('SELECT cnv_id, contactphone, contactname FROM dcl_contact_cnv WHERE contactname IS NOT NULL ORDER BY cnv_id');
		$iStartTime = time();
		$phpgw_setup->oProc->m_odb->BeginTransaction();
		while ($oDB->next_record())
		{
			// Phone number
			$iID = $oDB->f(0);
			$sPhoneNum = trim($oDB->f(1));
			$sName = trim($oDB->f(2));
	
			$sPhoneNumOnly = ereg_replace('[^0-9]', '', $sPhoneNum);
			$sLastName = '';
			$sFirstName = '';
	
			// last, first, "first last", or just last
			$iPos = strpos($sName, ',');
			if ($iPos !== false)
			{
				$aName = split(',', $sName);
				$sLastName = trim($aName[0]);
				if (count($aName) > 1)
				{
					$sFirstName = trim($aName[1]);
				}
	
				if (count($aName) > 2)
				{
					for ($j = 2; $j < count($aName); $j++)
						$sFirstName .= ' ' . trim($aName[$j]);
				}
			}
			else
			{
				$iPos = strpos($sName, ' ');
				if ($iPos !== false)
				{
					$aName = split(' ', $sName);
					$sFirstName = trim($aName[0]);
					if (count($aName) > 1)
					{
						$sLastName = trim($aName[1]);
					}
	
					if (count($aName) > 2)
					{
						for ($j = 2; $j < count($aName); $j++)
							$sLastName .= ' ' . trim($aName[$j]);
					}
				}
				else
				{
					$sLastName = $sName;
					$sFirstName = '_';
				}
			}
	
			if ($sPhoneNumOnly != '' || $sLastName != '' || $sFirstName != '')
			{
				$sPhoneNumOnly = $phpgw_setup->oProc->m_odb->DBAddSlashes($sPhoneNumOnly);
				$sLastName = $phpgw_setup->oProc->m_odb->DBAddSlashes($sLastName);
				$sFirstName = $phpgw_setup->oProc->m_odb->DBAddSlashes($sFirstName);
				$sName = $phpgw_setup->oProc->m_odb->DBAddSlashes($sName);
	
				$phpgw_setup->oProc->Query("UPDATE dcl_contact_cnv SET contactphone_num = '$sPhoneNumOnly', lastname = '$sLastName', firstname = '$sFirstName' WHERE cnv_id = $iID");
				if (++$i % 1000 == 0)
				{
					dcl_upgrade0_9_4_4_write_message('# ');
					$phpgw_setup->oProc->m_odb->EndTransaction();
					$phpgw_setup->oProc->m_odb->BeginTransaction();
					set_time_limit(30); // add some more time
				}
			}
		}
	
		$phpgw_setup->oProc->m_odb->EndTransaction();
		$oDB->FreeResult();
		
		// More time!
		set_time_limit(30);
		
		// Create contact records for the personnel records
		$i = 0;
		$iStartTime = time();
		dcl_upgrade0_9_4_4_write_message('<br>Creating contact records for personnel (# = 500): ');
		$oDB->Query('SELECT lastname, firstname, cnv_id FROM dcl_contact_cnv WHERE entity_type_id = 7 ORDER BY lastname, firstname, contactemail');
		$phpgw_setup->oProc->m_odb->BeginTransaction();
		while ($oDB->next_record())
		{
			$sLastName = $phpgw_setup->oProc->m_odb->DBAddSlashes($oDB->f(0));
			$sFirstName = $phpgw_setup->oProc->m_odb->DBAddSlashes($oDB->f(1));
			$iCnvID = $oDB->f(2);
			$phpgw_setup->oProc->Query("INSERT INTO dcl_contact (first_name, last_name, created_by) VALUES ('$sFirstName', '$sLastName', 1)");
			
			$iID = $phpgw_setup->oProc->m_odb->GetLastInsertID('dcl_contact');
			$sSQL = "UPDATE dcl_contact_cnv SET contact_id = $iID WHERE cnv_id = $iCnvID";
			$phpgw_setup->oProc->Query($sSQL);
			
			if (++$i % 500 == 0)
			{
				dcl_upgrade0_9_4_4_write_message('# ');
				$phpgw_setup->oProc->m_odb->EndTransaction();
				$phpgw_setup->oProc->m_odb->BeginTransaction();
				set_time_limit(30); // add some more time
			}
		}
		
		$phpgw_setup->oProc->m_odb->EndTransaction();
		$oDB->FreeResult();
		
		// More time!
		set_time_limit(30);
		
		// Create contact records for the distinct name/phone combinations, this will undoubtedly
		// create duplicates, but there will be a way to "merge" contacts together
		$i = 0;
		$iStartTime = time();
		dcl_upgrade0_9_4_4_write_message('<br>Creating contact records for wo/tickets (# = 500): ');
		$oDB->Query('SELECT lastname, firstname, contactphone_num FROM dcl_contact_cnv GROUP BY lastname, firstname, contactphone_num ORDER BY UPPER(lastname), UPPER(firstname), contactphone_num');
		$phpgw_setup->oProc->m_odb->BeginTransaction();
		$sLastLastName = '';
		$sLastFirstName = '';
		$sLastPhone = '';
		$iID = -1;
		while ($oDB->next_record())
		{
			// for case-sensitive databases, we'll skip multiple instances of same uppercase name/phone
			if (strtoupper($oDB->f(0)) != $sLastLastName || strtoupper($oDB->f(1)) != $sLastFirstName || $oDB->f(2) != $sLastPhone)
			{
				$sLastLastName = strtoupper($oDB->f(0));
				$sLastFirstName = strtoupper($oDB->f(1));
				$sLastPhone = $oDB->f(2);
				
				$sLastName = $phpgw_setup->oProc->m_odb->DBAddSlashes($oDB->f(0));
				$sFirstName = $phpgw_setup->oProc->m_odb->DBAddSlashes($oDB->f(1));
				$phpgw_setup->oProc->Query("INSERT INTO dcl_contact (first_name, last_name, created_by) VALUES ('$sFirstName', '$sLastName', 1)");
				$iID = $phpgw_setup->oProc->m_odb->GetLastInsertID('dcl_contact');
			}
			
			if ($iID == -1)
				continue;
			
			$sSQL = "UPDATE dcl_contact_cnv SET contact_id = $iID WHERE ";
			$sSQL .= "lastname " . ($oDB->IsFieldNull(0) ? 'IS NULL' : "= '" . $phpgw_setup->oProc->m_odb->DBAddSlashes($oDB->f(0)) . "'");
			$sSQL .= " AND firstname " . ($oDB->IsFieldNull(1) ? 'IS NULL' : "= '" . $phpgw_setup->oProc->m_odb->DBAddSlashes($oDB->f(1)) . "'");
			$sSQL .= " AND contactphone_num " . ($oDB->IsFieldNull(2) ? 'IS NULL' : "= '" . $phpgw_setup->oProc->m_odb->DBAddSlashes($oDB->f(2)) . "'");
			
			$phpgw_setup->oProc->Query($sSQL);
			
			if (++$i % 500 == 0)
			{
				dcl_upgrade0_9_4_4_write_message('# ');
				$phpgw_setup->oProc->m_odb->EndTransaction();
				$phpgw_setup->oProc->m_odb->BeginTransaction();
				set_time_limit(30); // add some more time
			}
		}
		
		$phpgw_setup->oProc->m_odb->EndTransaction();
		$oDB->FreeResult();
		
		// Add records for phone and email
		set_time_limit(30);
		$phpgw_setup->oProc->Query("INSERT INTO dcl_contact_phone (contact_id, phone_type_id, phone_number, preferred, created_by) SELECT DISTINCT contact_id, 1, contactphone, 'N', 1 FROM dcl_contact_cnv WHERE contactphone IS NOT NULL AND contactphone != '' AND contact_id IS NOT NULL");
		$phpgw_setup->oProc->Query("INSERT INTO dcl_email_type (email_type_name) VALUES ('Default')");
		$phpgw_setup->oProc->Query("INSERT INTO dcl_contact_email (contact_id, email_type_id, email_addr, preferred, created_by) SELECT DISTINCT contact_id, 1, contactemail, 'N', 1 FROM dcl_contact_cnv WHERE contactemail IS NOT NULL AND contactemail != '' AND contact_id IS NOT NULL");
		
		// Update preferred status for email
		$i = 0;
		$iStartTime = time();
		dcl_upgrade0_9_4_4_write_message('<br>Setting preferred email address (# = 1000): ');
		$oDB->Query('SELECT MIN(contact_email_id) FROM dcl_contact_email GROUP BY contact_id');
		$phpgw_setup->oProc->m_odb->BeginTransaction();
		while ($oDB->next_record())
		{
			$iID = $oDB->f(0);
			$phpgw_setup->oProc->Query("UPDATE dcl_contact_email SET preferred = 'Y' WHERE contact_email_id = $iID");
			
			if (++$i % 1000 == 0)
			{
				dcl_upgrade0_9_4_4_write_message('# ');
				$phpgw_setup->oProc->m_odb->EndTransaction();
				$phpgw_setup->oProc->m_odb->BeginTransaction();
				set_time_limit(30); // add some more time
			}
		}
		
		$phpgw_setup->oProc->m_odb->EndTransaction();
		$oDB->FreeResult();

		// Update preferred status for phone numbers
		$i = 0;
		$iStartTime = time();
		dcl_upgrade0_9_4_4_write_message('<br>Setting preferred phone numbers (# = 1000): ');
		$oDB->Query('SELECT MIN(contact_phone_id) FROM dcl_contact_phone GROUP BY contact_id');
		$phpgw_setup->oProc->m_odb->BeginTransaction();
		while ($oDB->next_record())
		{
			$iID = $oDB->f(0);
			$phpgw_setup->oProc->Query("UPDATE dcl_contact_phone SET preferred = 'Y' WHERE contact_phone_id = $iID");
			
			if (++$i % 1000 == 0)
			{
				dcl_upgrade0_9_4_4_write_message('# ');
				$phpgw_setup->oProc->m_odb->EndTransaction();
				$phpgw_setup->oProc->m_odb->BeginTransaction();
				set_time_limit(30); // add some more time
			}
		}
		
		$phpgw_setup->oProc->m_odb->EndTransaction();
		$oDB->FreeResult();

		printf("<b>Completed contact conversion of %d records in %d seconds (%d / sec)</b><br/>", $i, time() - $iStartTime, (time() - $iStartTime > 0 ? $i / (time() - $iStartTime) : 0));
	}
	
	$phpgw_setup->oProc->AddColumn('workorders', 'contact_id', array('type' => 'int', 'precision' => 4));
	$phpgw_setup->oProc->AddColumn('tickets', 'contact_id', array('type' => 'int', 'precision' => 4));
	$phpgw_setup->oProc->AddColumn('personnel', 'contact_id', array('type' => 'int', 'precision' => 4));
	
	if ($sType == 'mysql')
	{
		dcl_upgrade0_9_4_4_write_message('<br>Setting personnel contact field ');
		$phpgw_setup->oProc->Query('UPDATE personnel P, dcl_contact_cnv C SET P.contact_id = C.contact_id WHERE C.entity_type_id = 7 AND C.entity_id = P.id');
		dcl_upgrade0_9_4_4_write_message('<br>Setting work orders contact field ');
		$phpgw_setup->oProc->Query('UPDATE workorders W, dcl_contact_cnv C SET W.contact_id = C.contact_id WHERE C.entity_type_id = 2 AND C.entity_id = W.jcn AND C.entity_id2 = W.seq');
		dcl_upgrade0_9_4_4_write_message('<br>Setting tickets contact field ');
		$phpgw_setup->oProc->Query('UPDATE tickets T, dcl_contact_cnv C SET T.contact_id = C.contact_id WHERE C.entity_type_id = 3 AND C.entity_id = T.ticketid');
	}
	else
	{
		dcl_upgrade0_9_4_4_write_message('<br>Setting personnel contact field ');
		$phpgw_setup->oProc->Query('UPDATE personnel SET contact_id = dcl_contact_cnv.contact_id FROM dcl_contact_cnv WHERE dcl_contact_cnv.entity_type_id = 7 AND dcl_contact_cnv.entity_id = personnel.id');
		dcl_upgrade0_9_4_4_write_message('<br>Setting work orders contact field ');
		$phpgw_setup->oProc->Query('UPDATE workorders SET contact_id = dcl_contact_cnv.contact_id FROM dcl_contact_cnv WHERE dcl_contact_cnv.entity_type_id = 2 AND dcl_contact_cnv.entity_id = workorders.jcn AND dcl_contact_cnv.entity_id2 = workorders.seq');
		dcl_upgrade0_9_4_4_write_message('<br>Setting tickets contact field ');
		$phpgw_setup->oProc->Query('UPDATE tickets SET contact_id = dcl_contact_cnv.contact_id FROM dcl_contact_cnv WHERE dcl_contact_cnv.entity_type_id = 3 AND dcl_contact_cnv.entity_id = tickets.ticketid');
	}
	
	// Set up a default "converted" type for contacts
	dcl_upgrade0_9_4_4_write_message('<br>Setting contact types ');
	if (!$phpgw_setup->oProc->m_bDeltaOnly)
	{
		$phpgw_setup->oProc->Query("INSERT INTO dcl_contact_type_xref SELECT contact_id, $iContactTypeID FROM dcl_contact");
	}
	
	// Now we blindly start adding all distinct combinations of orgs/contacts
	// The end result could be messy in some cases, but it's better than manually setting these up later
	dcl_upgrade0_9_4_4_write_message('<br>Setting default org/contact relationships');
	$phpgw_setup->oProc->Query('insert into dcl_org_contact select distinct account, contact_id, ' . $phpgw_setup->oProc->m_odb->GetDateSQL() . ', 1 from tickets where account is not null and contact_id is not null union select distinct a.account_id, w.contact_id, ' . $phpgw_setup->oProc->m_odb->GetDateSQL() . ', 1 from workorders w, dcl_wo_account a where w.jcn = a.wo_id and w.seq = a.seq and a.account_id is not null and w.contact_id is not null');

	if (!$phpgw_setup->oProc->m_bDeltaOnly)
		$phpgw_setup->oProc->DropTable('dcl_contact_cnv');
	
	// Bye bye free form fields
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
				'summary' => array('type' => 'varchar', 'precision' => 100, 'nullable' => false),
				'notes' => array('type' => 'text'),
				'description' => array('type' => 'text', 'nullable' => false),
				'responsible' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'revision' => array('type' => 'varchar', 'precision' => 20),
				'etchours' => array('type' => 'float', 'precision' => 8),
				'module_id' => array('type' => 'int', 'precision' => 4),
				'wo_type_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'entity_source_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
				'is_public' => array('type' => 'char', 'precision' => 1, 'nullable' => false, 'default' => 'N'),
				'contact_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
			),
			'pk' => array('jcn', 'seq'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		array('contact', 'contactphone')
	);

	$phpgw_setup->oProc->DropColumn('tickets', array(
			'fd' => array(
				'ticketid' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
				'product' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'account' => array('type' => 'int', 'precision' => 4),
				'createdby' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'createdon' => array('type' => 'timestamp'),
				'responsible' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'closedby' => array('type' => 'int', 'precision' => 4),
				'closedon' => array('type' => 'timestamp'),
				'status' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'statuson' => array('type' => 'timestamp', 'nullable' => false),
				'lastactionon' => array('type' => 'timestamp'),
				'priority' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'type' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
				'issue' => array('type' => 'text', 'nullable' => false),
				'version' => array('type' => 'varchar', 'precision' => 20),
				'summary' => array('type' => 'varchar', 'precision' => 100, 'nullable' => false),
				'seconds' => array('type' => 'int', 'precision' => 4, 'default' => 0, 'nullable' => false),
				'module_id' => array('type' => 'int', 'precision' => 4),
				'entity_source_id' => array('type' => 'int', 'precision' => 4),
				'is_public' => array('type' => 'char', 'precision' => 1, 'nullable' => false, 'default' => 'N'),
				'contact_id' => array('type' => 'int', 'precision' => 4, 'nullable' => true)
			),
			'pk' => array('ticketid'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		array('contact', 'contactphone')
	);

	$phpgw_setup->oProc->DropColumn('personnel', array(
			'fd' => array(
				'id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
				'short' => array('type' => 'varchar', 'precision' => 25, 'nullable' => false),
				'reportto' => array('type' => 'int', 'precision' => 4),
				'department' => array('type' => 'int', 'precision' => 4),
				'pwd' => array('type' => 'varchar', 'precision' => 50, 'nullable' => false),
				'active' => array('type' => 'char', 'precision' => 1, 'default' => 'Y', 'nullable' => false),
				'contact_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false)
			),
			'pk' => array('id'),
			'fk' => array(),
			'ix' => array(),
			'uc' => array()
		),
		array('lastname', 'firstname', 'security', 'email')
	);

	$phpgw_setup->oProc->Query("DELETE FROM dcl_config WHERE dcl_config_name IN ('DCL_ADD_USER', 'DCL_DEL_WO', 'DCL_MOD_WO', 'DCL_ASSIGN_WO', 'DCL_ADD_WO', 'DCL_HAVE_WO', 'DCL_CHG_PWD', 'DCL_COLOR_DARK', 'DCL_COLOR_LIGHT', 'DCL_WIKI_VIEW', 'DCL_WIKI_VIEW')");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) values ('DCL_GATEWAY_WO_ACCOUNT', 'dcl_config_int', 0)");
	$phpgw_setup->oProc->Query("insert into dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) values ('DCL_GATEWAY_WO_REPLY_LOGGED_BY', 'dcl_config_int', 0)");
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC1' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.5RC1';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.9.5RC1';
function dcl_upgrade0_9_5RC1()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->AddColumn('dcl_contact_type', 'contact_type_is_main', array('type' => 'char', 'precision' => 1, 'nullable' => false, 'default' => 'N'));

	$phpgw_setup->oProc->CreateTable('dcl_wo_task',
					array(
						'fd' => array(
							'wo_task_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
							'wo_id' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'seq' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'task_order' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'task_complete' => array('type' => 'char', 'precision' => 1, 'nullable' => false, 'default' => 'N'),
							'task_summary' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
							'task_create_by' => array('type' => 'int', 'precision' => 4, 'nullable' => false),
							'task_create_dt' => array('type' => 'timestamp', 'nullable' => false),
							'task_complete_by' => array('type' => 'int', 'precision' => 4, 'nullable' => true),
							'task_complete_dt' => array('type' => 'timestamp', 'nullable' => true)
						),
						'pk' => array('wo_task_id'),
						'fk' => array(),
						'ix' => array(
								'ix_dcl_wo_task_id_seq' => array('wo_id', 'seq')
							),
						'uc' => array()
					)
	);
	
	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) VALUES ('DCL_APP_NAME', 'dcl_config_varchar', 'GNUe DCL')");
	$phpgw_setup->oProc->Query("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) VALUES ('DCL_LOGIN_MESSAGE', 'dcl_config_varchar', 'Welcome to GNUe DCL')");
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC2' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.5RC2';
	return $setup_info['dcl']['currentver'];
}

$test[] = '0.9.5RC2';
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

$test[] = '0.9.5RC3';
function dcl_upgrade0_9_5RC3()
{
	global $phpgw_setup, $setup_info;
	
	$phpgw_setup->oProc->RefreshTable('timecards');
	
	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC4' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.5RC4';
	return $setup_info['dcl']['currentver'];
}
?>