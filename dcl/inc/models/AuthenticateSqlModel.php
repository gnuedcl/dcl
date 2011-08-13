<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2011 Free Software Foundation
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
		$this->sql = sprintf("SELECT p.id, p.contact_id, p.short, e.email_addr FROM personnel p LEFT JOIN dcl_contact_email e ON p.contact_id = e.contact_id AND e.preferred = 'Y' WHERE p.short=%s AND p.pwd=%s AND p.active='Y'", $this->model->Quote($this->uid), $this->model->Quote(md5($this->pwd)));
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
				$authInfo = array(
						'id' => $this->model->f(0),
						'contact_id' => $this->model->f(1),
						'short' => $this->model->f(2),
						'email' => $this->model->f(3)
					);

				return true;
			}
		}

		return false;
	}
}
