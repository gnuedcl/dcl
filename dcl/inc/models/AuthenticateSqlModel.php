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

require_once(DCL_ROOT . 'vendor/password_compat/password.php');

class AuthenticateSqlModel
{
	private $uid;
	private $pwd;
	private $sql;
	private $model;

	public function __construct()
	{
		$this->model = new PersonnelModel();
		$this->model->cacheEnabled = false;

		$this->SetCredentials();
		$this->SetQuery();
	}

	private function SetCredentials()
	{
		$this->uid = IsSet($_REQUEST['UID']) ? $_REQUEST['UID'] : '';
		$this->pwd = IsSet($_REQUEST['PWD']) ? $_REQUEST['PWD'] : '';
	}

	private function SetQuery()
	{
		$this->sql = sprintf("SELECT p.id, p.contact_id, p.short, e.email_addr, p.pwd, p.pwd_change_required, p.is_locked, p.lock_expiration, p.last_pwd_chg_dt FROM personnel p LEFT JOIN dcl_contact_email e ON p.contact_id = e.contact_id AND e.preferred = 'Y' WHERE p.short=%s AND p.active='Y'", $this->model->Quote($this->uid));
	}

	public function IsValidLogin(&$authInfo)
	{
		// DCL authentication
		if (!$this->model->conn)
			Refresh('index.php?cd=3');

		if ($this->model->Query($this->sql) != -1)
		{
			if ($this->model->next_record())
			{
				$hashedPwd = $this->model->f(4);
				$hashOptions = array('cost' => 10);

				if (password_verify($this->pwd, $hashedPwd))
				{
					$needsRehash = password_needs_rehash($hashedPwd, PASSWORD_DEFAULT, $hashOptions);
				}
				else if (md5($this->pwd) == $hashedPwd)
				{
					$needsRehash = true;
				}
				else
				{
					SecurityAuditModel::AddAudit($this->model->f(0), 'loginfail');
					PersonnelModel::ProcessAccountAutolock($this->model->f(0));
					return false;
				}

				$accountLocked = $this->model->f(6) == 'Y';
				$needsLockReset = false;
				if ($accountLocked && $this->model->f(7) != null)
				{
					$accountLockExpiration = new DateTime($this->model->f(7), new DateTimeZone('UTC'));
					$now = new DateTime('now', new DateTimeZone('UTC'));

					if ($now >= $accountLockExpiration)
					{
						$accountLocked = false;
						$needsLockReset = true;
					}
				}

				$authInfo = array(
					'id' => $this->model->f(0),
					'contact_id' => $this->model->f(1),
					'short' => $this->model->f(2),
					'email' => $this->model->f(3),
					'forcepwdchange' => $this->model->f(5) == 'Y',
					'lastpwdchange' => $this->model->f(8),
					'locked' => $accountLocked
				);

				if ($needsRehash || $needsLockReset)
				{
					$sql = 'UPDATE personnel SET ';
					if ($needsRehash)
					{
						$hashedPwd = password_hash($this->pwd, PASSWORD_DEFAULT, $hashOptions);
						$sql .= sprintf('pwd = %s', $this->model->Quote($hashedPwd));
					}

					if ($needsLockReset)
					{
						if ($needsRehash)
							$sql .= ', ';

						$sql .= "is_locked = 'N', lock_expiration = NULL";
					}

					$sql .= sprintf(' WHERE id = %d', $this->model->f(0));

					$this->model->Query($sql);
				}

				return true;
			}
		}

		return false;
	}
}
