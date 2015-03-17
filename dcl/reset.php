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

require_once('inc/config.php');
require_once(DCL_ROOT . 'inc/functions.inc.php');

function Refresh($toHere = 'logout.php', $session_id = '')
{
	global $dcl_info;

	$bIsLogin = (mb_substr($toHere, 0, 10) == 'logout.php');

	if ($bIsLogin)
	{
		$theCookie = '';
		if (IsSet($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != '')
			$toHere .= sprintf('%srefer_to=%s', mb_strpos($toHere, '?') > 0 ? '&' : '?', urlencode($_SERVER['QUERY_STRING']));
	}
	else
		$theCookie = $session_id;

	$httpDomain = '';
	if (preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}(:[0-9]+)*$/', $_SERVER['HTTP_HOST']))
	{
		$httpDomain = $_SERVER['HTTP_HOST'];
	}
	else if (preg_match('/.*\..*$/', $_SERVER['HTTP_HOST']))
	{
		$httpDomain = preg_replace('/^www\./i', '', $_SERVER['HTTP_HOST']);
		$httpDomain = '.' . $httpDomain;
	}

	if (($p = mb_strpos($httpDomain, ':')) !== false)
		$httpDomain = mb_substr($httpDomain, 0, $p);

	setcookie('DCLINFO', $theCookie, 0, '/', $httpDomain, UseHttps() || $dcl_info['DCL_FORCE_SECURE_COOKIE'] == 'Y', true);
	header("Location: $toHere");

	exit;
}

$config = new ConfigurationModel();
$dcl_info = array();
$config->Load();

if (!isset($_GET['token']))
{
	$t = new SmartyHelper();
	$t->assign('TXT_VERSION', $dcl_info['DCL_VERSION']);

	if ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		// Request form submitted
		$login = @$_POST['login'];
		$email = @$_POST['email'];
		if (!Filter::IsNotNullOrWhitespace($login) || !Filter::IsNotNullOrWhitespace($email))
		{
			$t->Render('reset.tpl');
			exit;
		}

		$userModel = new PersonnelModel();
		$loadUserModelResult = $userModel->LoadByLogin($login);
		if ($loadUserModelResult != -1 && !$userModel->IsLocked())
		{
			$userId = $userModel->f('id');
			$contactId = $userModel->contact_id;
			$contactEmailModel = new ContactEmailModel();
			if ($contactEmailModel->ListByContact($contactId) != -1)
			{
				$hasRequestedEmail = false;
				while (!$hasRequestedEmail && $contactEmailModel->next_record())
				{
					$hasRequestedEmail = mb_strtolower($email) == mb_strtolower($contactEmailModel->f('email_addr'));
				}

				$minPasswordAgeMet = $dcl_info['DCL_PASSWORD_MIN_AGE'] < 1 || $userModel->last_pwd_chg_dt == null;
				if (!$minPasswordAgeMet)
				{
					$minChgDt = new DateTime($userModel->last_pwd_chg_dt);
					$minChgDt->modify('+' . $dcl_info['DCL_PASSWORD_MIN_AGE'] . ' days');
					$now = new DateTime();

					$minPasswordAgeMet = $now >= $minChgDt;
				}

				if ($hasRequestedEmail && $minPasswordAgeMet)
				{
					// Have the login and it contains the email address, so send a reset
					$token = sha1(sha1(microtime()) . mt_rand());
					$ttl = $dcl_info['DCL_PASSWORD_RESET_TOKEN_TTL'];
					$expires = new DateTime();
					$expires->modify('+' . $ttl . ' minutes');

					$userModel->Query('UPDATE personnel SET pwd_reset_token = ' .
						$userModel->Quote($token) .
						', pwd_reset_token_expiration = ' . $userModel->Quote($expires->format($dcl_info['DCL_TIMESTAMP_FORMAT_DB'])) .
						' WHERE id = ' . $userId);

					$contactModel = new ContactModel();
					$contactModel->Load($contactId);

					$t->assign('VAL_TOKEN', $token);
					$t->assign('VAL_FIRSTNAME', $contactModel->first_name);

					$oMail = new Smtp();
					$oMail->isHtml = true;
					$oMail->from = $dcl_info['DCL_SMTP_DEFAULT_EMAIL'];
					$oMail->subject = 'DCL Password Reset';
					$oMail->body = $t->ToString('resetemail.tpl');
					$oMail->to = array('<' . $email . '>');
					$oMail->Send();

					SecurityAuditModel::AddAudit($userId, 'requestpasswordreset');
				}
				else
				{
					if (!$hasRequestedEmail)
						SecurityAuditModel::AddAudit($userId, 'requestpasswordreset failed: incorrect email');
					else
						SecurityAuditModel::AddAudit($userId, 'requestpasswordreset failed: minimum password age not met');
				}
			}
			else
			{
				SecurityAuditModel::AddAudit($userId, 'requestpasswordreset failed: contact has no email address');
			}
		}
		else
		{
			if ($loadUserModelResult == -1)
				SecurityAuditModel::AddAudit(0, 'requestpasswordreset failed: user not found: ' . $login);
			else
				SecurityAuditModel::AddAudit($userModel->f('id'), 'requestpasswordreset failed: user account locked');
		}

		$t->Render('resetresult.tpl');
	}
	else
	{
		// Coming to reset via link
		$t->Render('reset.tpl');
	}
}
else
{
	// Clicked on the link
	$userModel = new PersonnelModel();
	if ($userModel->Query('SELECT * FROM personnel WHERE pwd_reset_token = ' . $userModel->Quote($_GET['token'])) != -1)
	{
		if ($userModel->next_record())
		{
			$userId = $userModel->f('id');
			$pwdResetToken = $userModel->f('pwd_reset_token');
			$short = $userModel->f('short');
			$contactId = $userModel->f('contact_id');

			if ($_GET['token'] == $pwdResetToken)
			{
				$pwdResetTokenExpire = new DateTime($userModel->f('pwd_reset_token_expiration'));
				$now = new DateTime('now');

				// Good for one time, expired or not
				$userModel->ResetToken($userId);

				if ($now < $pwdResetTokenExpire)
				{
					// Token is good - start session and force user to change password
					$contactEmailModel = new ContactEmailModel();
					if ($contactEmailModel->GetPrimaryEmail($contactId))
						$email = $contactEmailModel->f('email_addr');
					else
						$email = '';

					SessionHelper::Start($userId, $email, $short, $contactId);
					$g_oSession->Register('ForcePasswordChange', '1');
					$g_oSession->Edit();

					SecurityAuditModel::AddAudit(DCLID, 'passwordreset');
					SecurityAuditModel::AddAudit(DCLID, 'login');

					Refresh('main.php?menuAction=Personnel.ForcePasswordChange', $g_oSession->dcl_session_id);
				}
			}
		}
	}

	sleep(2);
	Refresh('logout.php');
}
