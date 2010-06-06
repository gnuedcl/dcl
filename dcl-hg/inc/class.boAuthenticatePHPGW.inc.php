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

import('boAuthenticate');
class boAuthenticatePHPGW extends boAuthenticate
{
	var $_sqlFallback;

	function boAuthenticatePHPGW()
	{
		parent::boAuthenticate();
	}

	function _SetCredentials()
	{
		global $phpgw_info;
		
		$this->_uid = $phpgw_info['user']['account_lid'];
		$this->_pwd = '';
	}

	function _SetQuery()
	{
		$this->_sql = sprintf("SELECT p.id, p.contact_id, p.short, e.email_addr FROM personnel p LEFT JOIN dcl_contact_email e ON p.contact_id = e.contact_id AND e.preferred = 'Y' WHERE p.short=%s AND p.active='Y'", $this->_oDB->Quote($this->_uid));
		$this->_sqlFallback = "SELECT p.id, p.contact_id, p.short, e.email_addr FROM personnel p LEFT JOIN dcl_contact_email e ON p.contact_id = e.contact_id AND e.preferred = 'Y' WHERE p.short='sa' AND p.active='Y'";
	}

	function IsValidLogin(&$aAuthInfo)
	{
		global $phpgw_info;

		// phpGroupWare authentication - just lookup by active login
		$this->_oDB->Query($this->_sql);
		if (!$this->_oDB->next_record())
		{
			$this->_oDB->FreeResult();
			if (isset($phpgw_info['user']['apps']['admin']) && is_array($phpgw_info['user']['apps']['admin']))
			{
				// Not in user table, but is phpgw admin, so load sa account
				$this->_oDB->Query($this->_sqlFallback);
				$this->_oDB->next_record();
			}
		}

		if (is_array($this->_oDB->Record))
		{
			$aAuthInfo = array(
					'id' => $this->_oDB->f(0),
					'contact_id' => $this->_oDB->f(1),
					'short' => $this->_oDB->f(2),
					'email' => $this->_oDB->f(3)
				);

			$this->_oDB->FreeResult();

			return true;
		}

		$this->_oDB->FreeResult();

		return false;
	}
}
?>
