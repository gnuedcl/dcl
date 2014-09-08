<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2014 Free Software Foundation
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

LoadStringResource('db');
require_once(DCL_ROOT . 'vendor/password_compat/password.php');

class PasswordHistoryModel extends DbProvider
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'dcl_password_history';

		LoadSchema($this->TableName);

		parent::Clear();
	}

	public function ListHistory($userId, $numPasswords, $daysBack)
	{
		$dt = new DateTime();
		$dt->modify('-' . $daysBack . ' days');

		return $this->LimitQuery("SELECT pwd FROM dcl_password_history WHERE user_id = $userId AND history_dt >= " . $this->Quote($dt->format('Y-m-d H:i:s')), 0, $numPasswords);
	}
}