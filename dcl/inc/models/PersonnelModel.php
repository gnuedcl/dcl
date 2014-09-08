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

class PersonnelModel extends DbProvider
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'personnel';
		$this->cacheEnabled = true;

		LoadSchema($this->TableName);

		$this->foreignKeys = array(
				'workorders' => array('responsible', 'createby', 'closedby'),
				'workorders_audit' => array('responsible', 'createby', 'closedby'),
				'timecards' => array('actionby', 'reassign_from_id', 'reassign_to_id'),
				'tickets' => array('responsible', 'createdby', 'closedby'),
				'tickets_audit' => array('responsible', 'createdby', 'closedby'),
				'ticketresolutions' => 'loggedby',
				'watches' => 'whoid',
				'views' => 'whoid',
				'products' => array('reportto', 'ticketsto'),
				'dcl_projects' => 'reportto',
				'dcl_projects_audit' => 'reportto',
				'personnel' => 'reportto',
				'dcl_password_history' => 'user_id'
		);

		parent::Clear();
	}

	public function Edit($aIgnoreFields = '')
	{
		$query = 'UPDATE personnel SET ';
		$query .= 'short=' . $this->FieldValueToSQL('short', $this->short) . ',';
		$query .= 'reportto=' . $this->FieldValueToSQL('reportto', $this->reportto) . ',';
		$query .= 'department=' . $this->FieldValueToSQL('department', $this->department) . ',';
		$query .= 'active=' . $this->FieldValueToSQL('active', $this->active) . ',';
		$query .= 'contact_id=' . $this->FieldValueToSQL('contact_id', $this->contact_id) . ',';
		$query .= 'pwd_change_required=' . $this->FieldValueToSQL('pwd_change_required', $this->pwd_change_required) . ',';
		$query .= 'is_locked=' . $this->FieldValueToSQL('is_locked', $this->is_locked) . ',';
		$query .= 'lock_expiration=' . $this->FieldValueToSQL('lock_expiration', $this->lock_expiration);
		$query .= ' WHERE id=' . $this->FieldValueToSQL('id', $this->id);

		return $this->Execute($query);
	}

	public function Delete($aID)
	{
		return parent::Delete(array('id' => $this->id));
	}

	public function Load($id)
	{
		return parent::Load(array('id' => $id));
	}
	
	public function LoadByLogin($sLogin)
	{
		if ($this->Query('SELECT ' . $this->SelectAllColumns() . ' FROM ' . $this->TableName . ' WHERE ' . $this->GetUpperSQL('short') . ' = ' . $this->Quote(mb_strtoupper($sLogin))) != -1)
		{
			if ($this->next_record())
				return $this->GetRow();
		}
		
		return -1;
	}

	public function IsPasswordOK($userID, $password)
	{
		$this->Load($userID);
		return password_verify($password, $this->pwd);
	}

	public function ChangePassword($userID, $oldPassword, $newPassword)
	{
		global $g_oSec;

		$userID = Filter::RequireInt($userID);

		if (DCLID > 1 && !$g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_PASSWORD))
		{
			if ($this->IsPasswordOK($userID, $oldPassword) == false)
				return false;
		}

		$this->SetUserPassword($userID, $newPassword);

		return true;
	}

	public function IsLocked()
	{
		if ($this->is_locked != 'Y')
			return false;

		if ($this->lock_expiration != null)
		{
			$accountLockExpiration = new DateTime($this->lock_expiration, new DateTimeZone('UTC'));
			$now = new DateTime('now', new DateTimeZone('UTC'));

			if ($now >= $accountLockExpiration)
				return false;
		}

		return true;
	}

	public function SetUserPassword($userId, $password)
	{
		try
		{
			$this->BeginTransaction();

			$this->Execute(sprintf("INSERT INTO dcl_password_history SELECT id, pwd, %s FROM personnel WHERE id = %d", $this->GetDateSQL(), $userId));

			$hashOptions = array('cost' => 10);
			$hashedPwd = password_hash($password, PASSWORD_DEFAULT, $hashOptions);

			$query = sprintf("UPDATE personnel SET pwd = %s, pwd_change_required = 'N', last_pwd_chg_dt = %s WHERE id = %d",
				$this->Quote($hashedPwd), $this->GetDateSQL(), $userId);

			$this->Execute($query);

			$this->EndTransaction();
		}
		catch (Exception $ex)
		{
			$this->RollbackTransaction();
		}
	}

	public static function SetLastLoginDate($userId)
	{
		$model = new PersonnelModel();
		$query = sprintf("UPDATE personnel SET last_login_dt = %s WHERE id = %d", $model->GetDateSQL(), $userId);
		return $model->Execute($query);
	}

	public function Encrypt()
	{
		$hashOptions = array('cost' => 10);
		$this->pwd = password_hash($this->pwd, PASSWORD_DEFAULT, $hashOptions);
	}
	
	public function GetStatusCount($personnelId)
	{
		$sql = 'SELECT s.name, count(*) FROM statuses s, workorders w WHERE ';
		$sql .= "s.id = w.status AND s.dcl_status_type IN (1, 3) AND w.responsible=$personnelId ";
		$sql .= 'GROUP BY s.name ORDER BY 2 DESC';
		
		return $this->Query($sql);
	}

	public function GetSeverityCount($personnelId)
	{
		$sql = 'SELECT s.name, count(*) FROM severities s, workorders w, statuses st WHERE ';
		$sql .= "s.id = w.severity AND st.id = w.status AND st.dcl_status_type IN (1, 3) AND w.responsible=$personnelId ";
		$sql .= 'GROUP BY s.name ORDER BY 2 DESC';
		
		return $this->Query($sql);
	}

	public function GetPriorityCount($personnelId)
	{
		$sql = 'SELECT s.name, count(*) FROM priorities s, workorders w, statuses st WHERE ';
		$sql .= "s.id = w.priority AND st.id = w.status AND st.dcl_status_type IN (1, 3) AND w.responsible=$personnelId ";
		$sql .= 'GROUP BY s.name ORDER BY 2 DESC';
		
		return $this->Query($sql);
	}

	public function GetProductCount($personnelId)
	{
		$sql = 'SELECT p.name, count(*) FROM products p, workorders w, statuses st WHERE ';
		$sql .= "p.id = w.product AND st.id = w.status AND st.dcl_status_type IN (1, 3) AND w.responsible=$personnelId ";
		$sql .= 'GROUP BY p.name ORDER BY 2 DESC';
		
		return $this->Query($sql);
	}

	public function GetTypeCount($personnelId)
	{
		$sql = 'SELECT t.type_name, count(*) FROM dcl_wo_type t, workorders w, statuses st WHERE ';
		$sql .= "t.wo_type_id = w.wo_type_id AND st.id = w.status AND st.dcl_status_type IN (1, 3) AND w.responsible=$personnelId ";
		$sql .= 'GROUP BY t.type_name ORDER BY 2 DESC';
		
		return $this->Query($sql);
	}

	public function ResetToken($personnelId)
	{
		$this->Query('UPDATE personnel SET pwd_reset_token = NULL, pwd_reset_token_expiration = NULL WHERE id = ' . $personnelId);
	}

	public static function ProcessAccountAutolock($userId)
	{
		global $dcl_info;

		$lockoutWindow = $dcl_info['DCL_LOCKOUT_WINDOW'];
		$lockoutDuration = $dcl_info['DCL_LOCKOUT_DURATION'];
		$lockoutThreshold = $dcl_info['DCL_LOCKOUT_THRESHOLD'];

		if ($dcl_info['DCL_LOCKOUT_WINDOW'] < 1 || $dcl_info['DCL_LOCKOUT_DURATION'] < 1 || $dcl_info['DCL_LOCKOUT_THRESHOLD'] < 1)
			return;

		$personnelModel = new PersonnelModel();
		$personnelModel->Load($userId);
		if ($personnelModel->is_locked == 'Y')
			return;

		$windowDt = new DateTime('now');
		$windowDt->modify('-' . $lockoutWindow . ' minutes');

		if ($personnelModel->last_login_dt != null)
		{
			// If the last login is later than the window, then start from there to look for failed attempts
			$lastLoginDt = new DateTime($personnelModel->last_login_dt);
			if ($lastLoginDt > $windowDt)
				$windowDt = $lastLoginDt;
		}

		$model = new SecurityAuditModel();
		$query = sprintf("SELECT COUNT(*) FROM dcl_sec_audit WHERE id = %d AND actionon > %s AND actiontxt = 'loginfail'", $userId, $model->Quote($windowDt->format('Y-m-d H:i:s')));

		$failureCount = $model->ExecuteScalar($query);

		if ($failureCount >= $lockoutThreshold)
		{
			SecurityAuditModel::AddAudit($userId, 'accountlocked');
			$unlockDt = new DateTime('now', new DateTimeZone('UTC'));
			$unlockDt->modify('+' . $lockoutDuration . ' minutes');

			$query = sprintf("UPDATE personnel SET is_locked = 'Y', lock_expiration = %s WHERE id = %d", $personnelModel->Quote($unlockDt->format('Y-m-d H:i:s')), $userId);
			$personnelModel->Query($query);
		}
	}

	public static function GetPasswordCharacterInfo()
	{
		global $dcl_info;

		$rules = array();
		if ($dcl_info['DCL_PASSWORD_REQUIRE_UPPERCASE'] == 'Y')
			$rules[] = 'uppercase';

		if ($dcl_info['DCL_PASSWORD_REQUIRE_LOWERCASE'] == 'Y')
			$rules[] = 'lowercase';

		if ($dcl_info['DCL_PASSWORD_REQUIRE_NUMERIC'] == 'Y')
			$rules[] = 'numeric';

		if ($dcl_info['DCL_PASSWORD_REQUIRE_SYMBOL'] == 'Y')
			$rules[] = 'symbol';

		return '{field} must contain ' . $dcl_info['DCL_PASSWORD_REQUIRE_THRESHOLD'] . ' of the following ' . count($rules) . ' characters: ' . join(', ', $rules);
	}

	public static function GetPasswordHistoryInfo()
	{
		global $dcl_info;

		$message = '{field} cannot be the same as the current password';
		if ($dcl_info['DCL_PASSWORD_DISALLOW_REUSE_THRESHOLD'] > 0)
		{
			$message .= ' or one of the last ' . $dcl_info['DCL_PASSWORD_DISALLOW_REUSE_THRESHOLD'] . ' passwords';
			$message .= ' within the last ' . $dcl_info['DCL_PASSWORD_DISALLOW_REUSE_DAYS'] . ' days';
		}

		return $message . '.';
	}

	public static function GetPasswordValidator($new, $confirm, PersonnelModel $model)
	{
		global $dcl_info;

		$v = new Valitron\Validator(array('new' => $new, 'confirm' => $confirm, 'username' => $model->short, 'lastChgDt' => $model->last_pwd_chg_dt));
		$v->rule('required', array('new', 'confirm'));
		$v->rule('equals', 'new', 'confirm');
		$v->rule('lengthMin', 'new', $dcl_info['DCL_PASSWORD_MIN_LENGTH']);

		if ($model->id > 0)
		{
			$v->addRule('satisfiesPasswordHistory', function($field, $value, array $params) {
				global $dcl_info;

				$params = $params[0];
				// Cannot change to current password
				if (password_verify($value, $params['current']))
					return false;

				if ($dcl_info['DCL_PASSWORD_DISALLOW_REUSE_THRESHOLD'] > 0)
				{
					$historyModel = new PasswordHistoryModel();
					$historyModel->ListHistory($params['userId'], $dcl_info['DCL_PASSWORD_DISALLOW_REUSE_THRESHOLD'], $dcl_info['DCL_PASSWORD_DISALLOW_REUSE_DAYS']);
					while ($historyModel->next_record())
					{
						if (password_verify($value, $historyModel->f('pwd')))
							return false;
					}
				}

				return true;
			});

			$historyParams = array('current' => $model->pwd, 'userId' => $model->id);
			$v->rule('satisfiesPasswordHistory', 'new', $historyParams)->message(self::GetPasswordHistoryInfo())->label('Password');

			if ($dcl_info['DCL_PASSWORD_MIN_AGE'] > 0 && $model->last_pwd_chg_dt != null && $model->pwd_change_required == 'N')
			{
				$minDt = new DateTime();
				$minDt->modify('-' . $dcl_info['DCL_PASSWORD_MIN_AGE'] . ' days');

				$v->rule('dateBefore', 'lastChgDt', $minDt)->message('You can only change your password every ' . $dcl_info['DCL_PASSWORD_MIN_AGE'] . ' days.')->label('Password');
			}
		}

		if ($dcl_info['DCL_PASSWORD_ALLOW_SAME_AS_USERNAME'] != 'Y')
			$v->rule('different', 'new', 'username')->message('{field} cannot be the same as the username.')->label('Password');

		if ($dcl_info['DCL_PASSWORD_REQUIRE_UPPERCASE'] == 'Y' || $dcl_info['DCL_PASSWORD_REQUIRE_LOWERCASE'] == 'Y' ||
			$dcl_info['DCL_PASSWORD_REQUIRE_NUMERIC'] == 'Y' || $dcl_info['DCL_PASSWORD_REQUIRE_SYMBOL'] == 'Y')
		{
			$v->addRule('satisfiesPasswordCharacters', function($field, $value, array $params) {
				global $dcl_info;

				$charactersSatisfied = 0;
				if ($dcl_info['DCL_PASSWORD_REQUIRE_UPPERCASE'] == 'Y' && mb_strtolower($value) !== $value)
					$charactersSatisfied++;

				if ($dcl_info['DCL_PASSWORD_REQUIRE_LOWERCASE'] == 'Y' && mb_strtoupper($value) !== $value)
					$charactersSatisfied++;

				if ($dcl_info['DCL_PASSWORD_REQUIRE_NUMERIC'] == 'Y' && preg_match('/[0-9]/', $value) == 1)
					$charactersSatisfied++;

				if ($dcl_info['DCL_PASSWORD_REQUIRE_SYMBOL'] == 'Y' && preg_match('/[\W]/', $value) == 1)
					$charactersSatisfied++;

				return $charactersSatisfied >= $dcl_info['DCL_PASSWORD_REQUIRE_THRESHOLD'];
			});

			$v->rule('satisfiesPasswordCharacters', 'new')->message(self::GetPasswordCharacterInfo())->label('Password');
		}

		return $v;
	}
}
