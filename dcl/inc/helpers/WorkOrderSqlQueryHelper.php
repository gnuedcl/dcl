<?php
/*
 * $Id$
 *
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2010 Free Software Foundation
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

class WorkOrderSqlQueryHelper extends AbstractSqlQueryHelper
{
	public function __construct()
	{
		parent::__construct();
		$this->table = 'workorders';
	}

	public function AddDef($which, $field, $value = '')
	{
		if ($field == 'account')
			$field = 'dcl_wo_account.account_id';
		else if ($field == 'revision')
			$field = 'dcl_product_version.product_version_text';

		parent::AddDef($which, $field, $value);
	}

	public function RemoveDef($which, $field)
	{
		if ($field == 'account')
			$field = 'dcl_wo_account.account_id';
		else if ($field == 'revision')
			$field = 'dcl_product_version.product_version_text';

		parent::RemoveDef($which, $field);
	}

	protected function AppendJoins(&$arr)
	{
		global $g_oSec;
		
		parent::AppendJoins($arr);

		if ($g_oSec->IsPublicUser() && !isset($this->joins['products']))
		{
			$this->joins['products'] = 1;
		}
	}

	protected function AppendJoin($table, $joinType)
	{
		global $dcl_info;

		if (!IsSet($this->joins[$table]))
		{
			if (!($dcl_info['DCL_WO_SECONDARY_ACCOUNTS_ENABLED'] == 'Y' &&
					($table == 'dcl_wo_account' || $table == 'accounts' || $table == 'dcl_org') &&
					!in_array('accounts.name', $this->order) &&
					!in_array('accounts.name', $this->groups) &&
					!in_array('accounts.name', $this->columns) &&
					!in_array('dcl_org.name', $this->order) &&
					!in_array('dcl_org.name', $this->groups) &&
					!in_array('dcl_org.name', $this->columns)) &&
				!(($table == 'dcl_entity_tag' || $table == 'dcl_tag') &&
					!in_array('dcl_tag.tag_desc', $this->order) &&
					!in_array('dcl_tag.tag_desc', $this->groups) &&
					!in_array('dcl_tag.tag_desc', $this->columns)) &&
				!(($table == 'dcl_entity_hotlist' || $table == 'dcl_hotlist') &&
					!in_array('dcl_hotlist.hotlist_tag', $this->order) &&
					!in_array('dcl_hotlist.hotlist_tag', $this->groups) &&
					!in_array('dcl_hotlist.hotlist_tag', $this->columns)))
			{
				// work orders are associated to projects in the projectmap table
				// so append it here - we don't want it in the selected columns

				// Ensure we join dcl_wo_account before accounts
				// Join dcl_org_contact before dcl_org for dcl_contact table
				if ($table == 'dcl_projects')
					$this->joins['projectmap'] = 2;
				else if (($table == 'accounts' || $table == 'dcl_org') && !isset($this->joins['dcl_wo_account']))
					$this->joins['dcl_wo_account'] = 2;
				else if ($table == 'dcl_tag' && !isset($this->joins['dcl_entity_tag']))
					$this->joins['dcl_entity_tag'] = 2;
				else if ($table == 'dcl_hotlist' && !isset($this->joins['dcl_entity_hotlist']))
					$this->joins['dcl_entity_hotlist'] = 2;
				else if ($table == 'personnel g')
					$this->joins['timecards'] = 2;

				$this->joins[$table] = $joinType;
			}
		}
	}

	protected function GetColumnSqlForOneToMany()
	{
		global $dcl_info;
		
		$sql = '';

		// If we show account, but don't group by it, then we will want to display an icon to show
		// the extra accounts.  This count will determine if we need to display the marker for more info
		// and will be left out of the final rendering by htmlView
		if ($dcl_info['DCL_WO_SECONDARY_ACCOUNTS_ENABLED'] == 'Y' && (in_array('dcl_org.name', $this->columns) || in_array('accounts.name', $this->columns)))
		{
			$sql .= ', (select count(*) from dcl_wo_account where wo_id = workorders.jcn And seq = workorders.seq) As _num_accounts_';
		}

		// Same for tags, but to determine if we really need to query for more tags
		if (in_array('dcl_tag.tag_desc', $this->columns))
		{
			$sql .= ', (select count(*) from dcl_entity_tag where entity_id = ' . DCL_ENTITY_WORKORDER . ' AND entity_key_id = workorders.jcn And entity_key_id2 = workorders.seq) As _num_tags_';
		}

		// One more time for hotlists
		if (in_array('dcl_hotlist.hotlist_tag', $this->columns))
		{
			$sql .= ', (select count(*) from dcl_entity_hotlist where entity_id = ' . DCL_ENTITY_WORKORDER . ' AND entity_key_id = workorders.jcn And entity_key_id2 = workorders.seq) As _num_hotlist_';
		}

		return $sql;
	}

	protected function GetWhereSqlForOneToManyColumns($sTagFilter, $sHotlistFilter)
	{
		global $g_oSession, $g_oSec, $dcl_info;
		
		$sql = '';

		// If we group by account, we'll join the whole lot together.  This will cause a work order
		// with n accounts to appear in the report n times.  Otherwise, if we only sort or show the account
		// column, we'll get the first account (in order) and display a link to show the other accounts
		// as needed
		if ($dcl_info['DCL_WO_SECONDARY_ACCOUNTS_ENABLED'] == 'Y' && (in_array('accounts.name', $this->columns) || in_array('dcl_org.name', $this->columns)))
		{
			$aOrgFilter = array();
			if ($g_oSec->IsOrgUser())
				$aOrgFilter = split(',', $g_oSession->Value('member_of_orgs'));

			if (isset($this->filter['dcl_wo_account.account_id']) && is_array($this->filter['dcl_wo_account.account_id']))
			{
				if (count($aOrgFilter) > 0)
					$aOrgFilter = array_intersect($this->filter['dcl_wo_account.account_id'], $aOrgFilter);
				else
					$aOrgFilter = $this->filter['dcl_wo_account.account_id'];
			}

			$sql .= ' And (dcl_wo_account.account_id is null Or dcl_wo_account.account_id = ';
			$sql .= '(Select min(account_id) From dcl_wo_account where wo_id = workorders.jcn And seq = workorders.seq';
			if (count($aOrgFilter) > 0)
				$sql .= ' AND account_id IN (' . join(',', $aOrgFilter) . ')';

			$sql .= '))';
		}

		// Same for Tags
		if (in_array('dcl_tag.tag_desc', $this->columns))
		{
			$sql .= ' And (dcl_entity_tag.tag_id is null Or dcl_entity_tag.tag_id = ';
			$sql .= '(Select min(tag_id) From dcl_entity_tag where entity_id = ' . DCL_ENTITY_WORKORDER . ' AND entity_key_id = workorders.jcn And entity_key_id2 = workorders.seq';
			if ($sTagFilter != '')
				$sql .= ' AND tag_id IN (' . $sTagFilter . ')';

			$sql .= '))';
		}

		// And hotlists...
		if (in_array('dcl_hotlist.hotlist_tag', $this->columns))
		{
			$sql .= ' And (dcl_entity_hotlist.hotlist_id is null Or dcl_entity_hotlist.hotlist_id = ';
			$sql .= '(Select min(hotlist_id) From dcl_entity_hotlist where entity_id = ' . DCL_ENTITY_WORKORDER . ' AND entity_key_id = workorders.jcn And entity_key_id2 = workorders.seq';
			if ($sHotlistFilter != '')
				$sql .= ' AND hotlist_id IN (' . $sHotlistFilter . ')';

			$sql .= '))';
		}

		return $sql;
	}

	protected function GetWhereSqlForPublicUser()
	{
		global $g_oSec;
		
		// Add public restriction
		if ($g_oSec->IsPublicUser())
		{
			return " AND products.is_public = 'Y' AND workorders.is_public = 'Y'";
		}

		return '';
	}

	protected function GetWhereSqlForOrgWorkspace($bOrgFilter, $bProductFilter, $bDoneDidWhere)
	{
		global $g_oSec, $g_oSession;
		
		$sAccountSQL = '';
		$sql = '';

		if ($g_oSec->IsOrgUser())
		{
			if (!$bOrgFilter)
			{
				$sOrgs = $g_oSession->Value('member_of_orgs');
				if ($sOrgs != '')
					$values = split(',', $sOrgs);
				else
					$values = array('-1');

				$sAccountSQL = "((workorders.jcn in (select wo_id from dcl_wo_account where account_id in (" . $this->GetCSLFromArray($values) . ")))";
				$sAccountSQL .= " AND (workorders.seq in (select seq from dcl_wo_account where workorders.jcn = wo_id And account_id in (" . $this->GetCSLFromArray($values) . "))";
				$sAccountSQL .= '))';
			}
		}

		if (!$bProductFilter && ($g_oSec->IsOrgUser() || $g_oSession->IsInWorkspace()))
		{
			if ($bDoneDidWhere == false)
			{
				$bDoneDidWhere = true;
				$sql .= ' WHERE ';
			}
			else
				$sql .= ' AND ';

			$sql .= 'product IN (' . join(',', $g_oSession->GetProductFilter()) . ')';
		}

		if ($g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEWSUBMITTED))
		{
			if ($bDoneDidWhere == false)
			{
				$bDoneDidWhere = true;
				$sql .= ' WHERE ';
			}
			else
				$sql .= ' AND ';

			$sql .= '(' . $this->table . '.createby = ' . $GLOBALS['DCLID'];
			$sql .= ' OR ' . $this->table . '.contact_id = ' . $g_oSession->Value('contact_id');
			if ($sAccountSQL != '')
				$sql .= ' OR ' . $sAccountSQL;

			$sql .= ')';
		}
		else if ($sAccountSQL != '')
		{
			if ($bDoneDidWhere == false)
			{
				$bDoneDidWhere = true;
				$sql .= ' WHERE ';
			}
			else
				$sql .= ' AND ';

			$sql .= $sAccountSQL;
		}

		return $sql;
	}
}