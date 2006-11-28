<?php
/*
 * $Id: class.boSMTP.inc.php,v 1.1.1.1 2006/11/27 05:30:43 mdean Exp $
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

class boSMTP
{
	var $oSocket;
	var $to;
	var $from;
	var $cc;
	var $bcc;
	var $subject;
	var $body;
	var $headers;
	var $isHtml;
	var $isAuth;
	var $authUser;
	var $authPwd;

	function boSMTP()
	{
		global $dcl_info, $dcl_domain;

		$this->oSocket =& CreateObject('dcl.boSocket');
		$this->oSocket->sHost = $dcl_info['DCL_SMTP_SERVER'];
		$this->oSocket->iPort = $dcl_info['DCL_SMTP_PORT'];
		$this->oSocket->iTimeout = $dcl_info['DCL_SMTP_TIMEOUT'];

		/* FIXME: placeholder for real config entries
		$this->isAuth = ($dcl_info['DCL_SMTP_REQUIRES_AUTH'] == 'Y');
		$this->authUser = $dcl_info['DCL_SMTP_AUTH_USER'];
		$this->authPwd = $dcl_info['DCL_SMTP_AUTH_PWD'];
		*/
		$this->isAuth = false;
		$this->authUser = '';
		$this->authPwd = '';

		$this->oSocket->bDebug = false;
		$this->oSocket->sResponseMode = 'smtp';
		$this->from = '';
		$this->to = '';
		$this->cc = '';
		$this->bcc = '';
		$this->subject = '';
		$this->body = '';
		$this->isHtml = false;

		$this->headers = array('X-Mailer: Double Choco Latte/' . $dcl_info['DCL_VERSION'], 'Sender: noreply-dcl@' . $dcl_domain);
	}

	function AddHeader($sHeader)
	{
		$this->headers[] = $sHeader;
	}

	function Send()
	{
		if ($this->oSocket->Connect(true) == -1)
			return false;

		if (!$this->Ehlo())
			if (!$this->Helo())
				return false;

		if ($this->isAuth && !$this->AuthLogin())
			return false;

		if (!$this->MailFrom())
			return false;

		if (!$this->RcptTo())
			return false;

		if (!$this->Data())
			return false;

		if (!$this->Quit())
			return false;

		$this->oSocket->Disconnect();

		return true;
	}

	function Ehlo()
	{
		$cmd = 'EHLO dcl' . phpCrLf;
		$this->oSocket->Write($cmd, true);
		return ($this->GetResponseCode() == 250); // if failed, it will try HELO next
	}

	function Helo()
	{
		$cmd = 'HELO dcl' . phpCrLf;
		$this->oSocket->Write($cmd, true);
		if ($this->GetResponseCode() == 250)
			return true;

		return $this->SocketFail();
	}

	function AuthLogin()
	{
		$cmd = 'AUTH LOGIN' . phpCrLf;
		$this->oSocket->Write($cmd, true);
		if ($this->GetResponseCode() != 334)
			return $this->SocketFail();

		$cmd = base64_encode($this->authUser) . phpCrLf;
		$this->oSocket->Write($cmd, true);
		if ($this->GetResponseCode() != 334)
			return $this->SocketFail();

		$cmd = base64_encode($this->authPwd) . phpCrLf;
		$this->oSocket->Write($cmd, true);
		if ($this->GetResponseCode() != 235)
			return $this->SocketFail();

		return true;
	}

	function MailFrom()
	{
		global $dcl_info;
		
		if ($this->from == '')
			$this->from = $dcl_info['DCL_SMTP_DEFAULT_EMAIL'];

		$cmd = sprintf('MAIL FROM:%s', $this->from) . phpCrLf;
		$this->oSocket->Write($cmd, true);
		if ($this->GetResponseCode() == 250)
			return true;

		return $this->SocketFail();
	}

	function RcptTo()
	{
		$bHasRcptTo = false;

		if (is_array($this->to))
		{
			for ($i = 0; $i < count($this->to); $i++)
			{
				if (empty($this->to[$i]) || trim($this->to[$i])=='<>')
					continue;

				$cmd = sprintf('RCPT TO:%s', $this->to[$i]) . phpCrLf;
				$this->oSocket->Write($cmd, true);
				if ($this->GetResponseCode() != 250)
					return $this->SocketFail();
					
				$bHasRcptTo = true;
			}
		}
		else
		{
			if (!empty($this->to))
			{
				$cmd = sprintf('RCPT TO:%s', $this->to) . phpCrLf;
				$this->oSocket->Write($cmd, true);
				if ($this->GetResponseCode() != 250)
					return $this->SocketFail();
					
				$bHasRcptTo = true;
			}
		}

		return $bHasRcptTo;
	}

	function Data()
	{
		$cmd = 'DATA' . phpCrLf;
		$this->oSocket->Write($cmd, true);
		if ($this->GetResponseCode() != 354)
			return $this->SocketFail();

		$this->oSocket->Write('Date: ' . date('D, j M Y G:i:s T') . phpCrLf);
		$this->oSocket->Write('From: ' . $this->from . phpCrLf);
		if (is_array($this->to))
			$this->oSocket->Write('To: ' . join(', ', $this->to) . phpCrLf);
		else
			$this->oSocket->Write('To: ' . $this->to . phpCrLf);

		$this->oSocket->Write('Subject: ' . $this->subject . phpCrLf);
		if (is_array($this->headers))
		{
			for ($i = 0; $i < count($this->headers); $i++)
				$this->oSocket->Write($this->headers[$i] . phpCrLf);
		}
		elseif ($this->headers != '')
		{
			$this->oSocket->Write($this->headers);
		}
		
		if ($this->isHtml)
		{
			$this->oSocket->Write("MIME-Version: 1.0\r\nContent-Type: text/html\r\n");
		}

		$this->oSocket->Write(phpCrLf);
		$data = str_replace("\n", "\r\n", str_replace("\r", "", $this->body));
		$this->oSocket->Write(ereg_replace('^\.', '..', $data) . phpCrLf . '.' . phpCrLf, true);
		if ($this->GetResponseCode() == 250)
			return true;

		return $this->SocketFail();
	}

	function Quit()
	{
		$cmd = 'QUIT' . phpCrLf;
		$this->oSocket->Write($cmd, true);
		if ($this->GetResponseCode() == 221)
			return true;

		return $this->SocketFail();
	}
	
	function GetResponseCode()
	{
		return substr($this->oSocket->sResponse, 0, 4);
	}

	function SocketFail()
	{
		trigger_error('<b>SMTP Error:</b> ' . $this->oSocket->sResponse);
		$this->oSocket->Disconnect();
		return false;
	}
}
?>
