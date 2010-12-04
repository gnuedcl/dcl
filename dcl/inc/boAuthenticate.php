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

class boAuthenticate
{
	var $_uid;
	var $_pwd;
	var $_sql;
	var $_oDB;

	function boAuthenticate()
	{
		$this->_oDB = new dbPersonnel();
		$this->_oDB->cacheEnabled = false;

		$this->_SetCredentials();
		$this->_SetQuery();
	}

	function _SetCredentials()
	{
		$this->_uid = IsSet($_REQUEST['UID']) ? $_REQUEST['UID'] : '';
		$this->_pwd = IsSet($_REQUEST['PWD']) ? $_REQUEST['PWD'] : '';
	}

	function _SetQuery()
	{
		$this->_sql = sprintf("SELECT p.id, p.contact_id, p.short, e.email_addr FROM personnel p LEFT JOIN dcl_contact_email e ON p.contact_id = e.contact_id AND e.preferred = 'Y' WHERE p.short=%s AND p.pwd=%s AND p.active='Y'", $this->_oDB->Quote($this->_uid), $this->_oDB->Quote(md5($this->_pwd)));
	}

	function IsValidLogin(&$aAuthInfo)
	{
		// DCL authentication
		if (!$this->_oDB->conn)
			Refresh('index.php?cd=3');

		if ($this->_oDB->Query($this->_sql) != -1)
		{
			if ($this->_oDB->next_record())
			{
				$aAuthInfo = array(
						'id' => $this->_oDB->f(0),
						'contact_id' => $this->_oDB->f(1),
						'short' => $this->_oDB->f(2),
						'email' => $this->_oDB->f(3)
					);

				return true;
			}
		}

		return false;
	}
}
