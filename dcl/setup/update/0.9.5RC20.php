<?php
function dcl_upgrade0_9_5RC20()
{
	global $phpgw_setup, $setup_info;

	$phpgw_setup->oProc->AlterColumn('personnel', 'pwd', array('type' => 'varchar', 'precision' => 255, 'nullable' => false));
	$phpgw_setup->oProc->AddColumn('personnel', 'last_login_dt', array('type' => 'timestamp', 'nullable' => true));
	$phpgw_setup->oProc->AddColumn('personnel', 'last_pwd_chg_dt', array('type' => 'timestamp', 'nullable' => true));

	$phpgw_setup->oProc->AddColumn('personnel', 'is_locked', array('type' => 'char', 'precision' => 1, 'default' => 'N', 'nullable' => true));
	$phpgw_setup->oProc->Query("UPDATE personnel SET is_locked = 'N'");
	$phpgw_setup->oProc->AlterColumn('personnel', 'is_locked', array('type' => 'char', 'precision' => 1, 'default' => 'N', 'nullable' => false));

	$phpgw_setup->oProc->AddColumn('personnel', 'lock_expiration', array('type' => 'timestamp', 'nullable' => true));

	$phpgw_setup->oProc->AddColumn('personnel', 'pwd_change_required', array('type' => 'char', 'precision' => 1, 'default' => 'N', 'nullable' => true));
	$phpgw_setup->oProc->Query("UPDATE personnel SET pwd_change_required = 'N'");
	$phpgw_setup->oProc->AlterColumn('personnel', 'pwd_change_required', array('type' => 'char', 'precision' => 1, 'default' => 'N', 'nullable' => false));

	$phpgw_setup->oProc->AddColumn('personnel', 'pwd_reset_token', array('type' => 'varchar', 'precision' => 255, 'nullable' => true));
	$phpgw_setup->oProc->AddColumn('personnel', 'pwd_reset_token_expiration', array('type' => 'timestamp', 'nullable' => true));

	$phpgw_setup->oProc->CreateTable('dcl_password_history', array(
		'fd' => array(
			'user_id' => array('type' => 'auto', 'precision' => 4, 'nullable' => false),
			'pwd' => array('type' => 'varchar', 'precision' => 255, 'nullable' => false),
			'history_dt' => array('type' => 'timestamp', 'nullable' => false)
		),
		'pk' => array('user_id', 'history_dt'),
		'fk' => array(),
		'ix' => array(),
		'uc' => array()
	));

	function __config_insert_int($name, $value)
	{
		global $phpgw_setup;

		$phpgw_setup->oProc->Query(sprintf("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_int) VALUES (%s, '%s', %d)",
			$phpgw_setup->oProc->m_odb->Quote($name), 'dcl_config_int', $value));
	}

	function __config_insert_varchar($name, $value)
	{
		global $phpgw_setup;

		$phpgw_setup->oProc->Query(sprintf("INSERT INTO dcl_config (dcl_config_name, dcl_config_field, dcl_config_varchar) VALUES (%s, '%s', %s)",
			$phpgw_setup->oProc->m_odb->Quote($name), 'dcl_config_varchar', $phpgw_setup->oProc->m_odb->Quote($value)));
	}

	__config_insert_int('DCL_PASSWORD_RESET_TOKEN_TTL', 15);
	__config_insert_int('DCL_PASSWORD_MIN_LENGTH', 1);
	__config_insert_int('DCL_PASSWORD_REQUIRE_THRESHOLD', 1);
	__config_insert_int('DCL_PASSWORD_MIN_AGE', 0);
	__config_insert_int('DCL_PASSWORD_MAX_AGE', 0);

	__config_insert_int('DCL_LOCKOUT_DURATION', 0);
	__config_insert_int('DCL_LOCKOUT_THRESHOLD', 0);
	__config_insert_int('DCL_LOCKOUT_WINDOW', 0);
	__config_insert_int('DCL_PASSWORD_DISALLOW_REUSE_THRESHOLD', 1);
	__config_insert_int('DCL_PASSWORD_DISALLOW_REUSE_DAYS', 1);

	__config_insert_varchar('DCL_PASSWORD_REQUIRE_UPPERCASE', 'N');
	__config_insert_varchar('DCL_PASSWORD_REQUIRE_LOWERCASE', 'N');
	__config_insert_varchar('DCL_PASSWORD_REQUIRE_NUMERIC', 'N');
	__config_insert_varchar('DCL_PASSWORD_REQUIRE_SYMBOL', 'N');
	__config_insert_varchar('DCL_PASSWORD_ALLOW_SAME_AS_USERNAME', 'N');

	$phpgw_setup->oProc->Query("UPDATE dcl_config SET dcl_config_varchar='0.9.5RC21' WHERE dcl_config_name='DCL_VERSION'");

	$setup_info['dcl']['currentver'] = '0.9.5RC21';
	return $setup_info['dcl']['currentver'];
}