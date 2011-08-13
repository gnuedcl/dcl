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

class TicketSqlQueryHelper extends AbstractSqlQueryHelper
{
	public function __construct()
	{
		parent::__construct();
		$this->table = 'tickets';
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
		if (!IsSet($this->joins[$table]))
		{
			if (!(($table == 'dcl_entity_tag' || $table == 'dcl_tag') &&
					!in_array('dcl_tag.tag_desc', $this->order) &&
					!in_array('dcl_tag.tag_desc', $this->groups) &&
					!in_array('dcl_tag.tag_desc', $this->columns)) &&
				!(($table == 'dcl_entity_hotlist' || $table == 'dcl_hotlist') &&
					!in_array('dcl_hotlist.hotlist_tag', $this->order) &&
					!in_array('dcl_hotlist.hotlist_tag', $this->groups) &&
					!in_array('dcl_hotlist.hotlist_tag', $this->columns)))
			{
				if ($table == 'dcl_tag' && !isset($this->joins['dcl_entity_tag']))
					$this->joins['dcl_entity_tag'] = 2;
				else if ($table == 'dcl_hotlist' && !isset($this->joins['dcl_entity_hotlist']))
					$this->joins['dcl_entity_hotlist'] = 2;

				$this->joins[$table] = $joinType;
			}
		}
	}

	protected function GetColumnSqlForOneToMany()
	{
		$sql = '';

		// Tags will work the same in tickets as work orders
		if (in_array('dcl_tag.tag_desc', $this->columns))
		{
			$sql .= ', (select count(*) from dcl_entity_tag where entity_id = ' . DCL_ENTITY_TICKET . ' AND entity_key_id = tickets.ticketid) As _num_tags_';
		}

		// Hotlists, too...
		if (in_array('dcl_hotlist.hotlist_tag', $this->columns))
		{
			$sql .= ', (select count(*) from dcl_entity_hotlist where entity_id = ' . DCL_ENTITY_TICKET . ' AND entity_key_id = tickets.ticketid) As _num_hotlist_';
		}

		return $sql;
	}


	protected function GetWhereSqlForOneToManyColumns($sTagFilter, $sHotlistFilter)
	{
		global $g_oSession, $g_oSec;

		$sql = '';

		// Same for ticket tags as work order tags
		if (in_array('dcl_tag.tag_desc', $this->columns))
		{
			$sql .= ' And (dcl_entity_tag.tag_id is null Or dcl_entity_tag.tag_id = ';
			$sql .= '(Select min(tag_id) From dcl_entity_tag where entity_id = ' . DCL_ENTITY_TICKET . ' AND entity_key_id = tickets.ticketid';
			if ($sTagFilter != '')
				$sql .= ' AND tag_id IN (' . $sTagFilter . ')';

			$sql .= '))';
		}

		// Hotlists
		if (in_array('dcl_hotlist.hotlist_tag', $this->columns))
		{
			$sql .= ' And (dcl_entity_hotlist.hotlist_id is null Or dcl_entity_hotlist.hotlist_id = ';
			$sql .= '(Select min(hotlist_id) From dcl_entity_hotlist where entity_id = ' . DCL_ENTITY_TICKET . ' AND entity_key_id = tickets.ticketid';
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
			return " AND products.is_public = 'Y' AND tickets.is_public = 'Y'";
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
				$sAccountSQL = 'account IN (' . $g_oSession->Value('member_of_orgs') . ')';
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

		if ($g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_VIEWSUBMITTED))
		{
			if ($bDoneDidWhere == false)
			{
				$bDoneDidWhere = true;
				$sql .= ' WHERE ';
			}
			else
				$sql .= ' AND ';

			$sql .= '(' . $this->table . '.createdby = ' . $GLOBALS['DCLID'];
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