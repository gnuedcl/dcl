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

class ContactSqlQueryHelper extends AbstractSqlQueryHelper
{
	public function __construct()
	{
		parent::__construct();
		$this->table = 'dcl_contact';
	}

	protected function AppendJoin($table, $joinType)
	{
		if (!IsSet($this->joins[$table]))
		{
			if ($table == 'dcl_org' && !isset($this->joins['dcl_org_contact']))
				$this->joins['dcl_org_contact'] = 2;

			$this->joins[$table] = $joinType;
		}
	}

	protected function GetWhereSqlForOrganizationUser($bOrgFilter, &$bDoneDidWhere)
	{
		global $g_oSec, $g_oSession;

		if ($bOrgFilter || !$g_oSec->IsOrgUser())
			return '';

		$sql = '';
		if ($bDoneDidWhere == false)
		{
			$sql = ' WHERE ';
			$bDoneDidWhere = true;
		}
		else
		{
			$sql = ' AND ';
		}

		return $sql . 'dcl_org.org_id IN (' . $g_oSession->Value('member_of_orgs') . ')';
	}

	protected function GetColumnSqlForOneToMany()
	{
		$sql = '';

		if (in_array('dcl_org.name', $this->columns))
		{
			$sql .= ', (select count(*) from dcl_org_contact where org_id = dcl_org.org_id) As _num_accounts_';
		}

		return $sql;
	}

	protected function GetWhereSqlForOneToManyColumns($sTagFilter, $sHotlistFilter)
	{
		global $g_oSession, $g_oSec;

		$sql = '';

		// If we group by account, we'll join the whole lot together.  This will cause a work order
		// with n accounts to appear in the report n times.  Otherwise, if we only sort or show the account
		// column, we'll get the first account (in order) and display a link to show the other accounts
		// as needed
		if (in_array('dcl_org.name', $this->columns))
		{
			$aOrgFilter = array();
			if ($g_oSec->IsOrgUser())
				$aOrgFilter = explode(',', $g_oSession->Value('member_of_orgs'));

			if (isset($this->filter['dcl_org.org_id']) && is_array($this->filter['dcl_org.org_id']))
			{
				if (count($aOrgFilter) > 0)
					$aOrgFilter = array_intersect($this->filter['dcl_org.org_id'], $aOrgFilter);
				else
					$aOrgFilter = $this->filter['dcl_org.org_id'];
			}

			$sql .= ' And (dcl_org_contact.org_id is null OR dcl_org_contact.org_id = ';
			$sql .= '(Select min(org_id) From dcl_org_contact where contact_id = dcl_contact.contact_id';
			if (count($aOrgFilter) > 0)
				$sql .= ' AND org_id IN (' . join(',', $aOrgFilter) . ')';

			$sql .= '))';
		}

		return $sql;
	}
}