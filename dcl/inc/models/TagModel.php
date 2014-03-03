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

LoadStringResource('db');
class TagModel extends DbProvider
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'dcl_tag';
		LoadSchema($this->TableName);
		
		parent::Clear();
	}
	
	public function getIdByName($sTag)
	{
		$sTag = trim(strtolower($sTag));
		$iID = $this->ExecuteScalar('SELECT tag_id FROM dcl_tag WHERE tag_desc = ' . $this->Quote($sTag));
		if ($iID !== null)
			return $iID;
			
		// Not found, so add it and return the new value
		$this->tag_desc = $sTag;
		if ($this->Add() != -1)
		{
			return $this->GetLastInsertID($this->TableName);
		}
		
		return null;
	}

	public function filterList($filter)
	{
		$this->LimitQuery("SELECT tag_id, tag_desc FROM dcl_tag WHERE tag_desc LIKE " . $this->Quote('%' . $filter . '%') . " ORDER BY tag_desc", 0, 20);

		return $this->FetchAllRows();
	}

	public function getExistingIdsByName($sTags)
	{
		if ($sTags === null || trim($sTags) == '')
			return '-1';
			
		$sTags = trim(strtolower($sTags));
		$aTags = explode(',', $sTags);
		if (count($aTags) < 1)
			return '-1';
			
		$sTagValues = '';
		foreach($aTags as $sTag)
		{
			$sTag = trim($sTag);
			if ($sTag == '')
				continue;
				
			if ($sTagValues != '')
				$sTagValues .= ',';
				
			$sTagValues .= $this->Quote($sTag);
		}
		
		if ($this->Query("SELECT tag_id FROM dcl_tag WHERE tag_desc IN ($sTagValues)") == -1)
			return '-1';

		$sID = '';
		while ($this->next_record())
		{
			if ($sID != '')
				$sID .= ',';
				
			$sID .= $this->f(0);
		}
		
		if ($sID == '')
			$sID = '-1';
			
		return $sID;
	}
	
	public function listByPopular()
	{
		global $g_oSession;

		$isPublic = IsPublicUser();
		$isOrgUser = IsOrgUser();
		$hasWorkOrder = HasPermission(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH);
		$hasTickets = HasPermission(DCL_ENTITY_TICKET, DCL_PERM_SEARCH);

		$sSQL = 'SELECT tag.tag_desc, COUNT(*) FROM dcl_tag tag JOIN dcl_entity_tag et ON tag.tag_id = et.tag_id';

		if ($isPublic || $isOrgUser)
		{
			if ($hasWorkOrder)
				$sSQL .= ' LEFT JOIN workorders w ON et.entity_id = ' . DCL_ENTITY_WORKORDER . ' AND et.entity_key_id = w.jcn AND et.entity_key_id2 = w.seq';

			if ($hasTickets)
				$sSQL .= ' LEFT JOIN tickets t ON et.entity_id = ' . DCL_ENTITY_TICKET . ' AND et.entity_key_id = t.ticketid';
		}

		$restrictedEntities = array();
		if (!$hasWorkOrder)
			$restrictedEntities[] = DCL_ENTITY_WORKORDER;

		if (!$hasTickets)
			$restrictedEntities[] = DCL_ENTITY_TICKET;

		if (count($restrictedEntities) == 2)
			throw new PermissionDeniedException();

		$hasWhereClause = false;
		if (count($restrictedEntities) == 1)
		{
			$sSQL .= ' WHERE et.entity_id != ' . $restrictedEntities[0];
			$hasWhereClause = true;
		}

		if ($isPublic)
		{
			if (!$hasWhereClause)
			{
				$sSQL .= ' WHERE ';
				$hasWhereClause = true;
			}
			else
				$sSQL .= ' AND ';

			if ($hasWorkOrder)
			{
				if ($hasTickets)
					$sSQL .= "(w.jcn IS NULL OR w.is_public = 'Y')";
				else
					$sSQL .= "w.is_public = 'Y'";
			}

			if ($hasTickets)
			{
				if ($hasWorkOrder)
					$sSQL .= ' AND ';

				if ($hasWorkOrder)
					$sSQL .= "(t.ticketid IS NULL OR t.is_public = 'Y')";
				else
					$sSQL .= "t.is_public = 'Y'";
			}
		}

		if ($isOrgUser)
		{
			$sOrgs = $g_oSession->Value('member_of_orgs');
			if ($sOrgs == '')
				$sOrgs = '-1';

			if (!$hasWhereClause)
				$sSQL .= ' WHERE ';
			else
				$sSQL .= ' AND ';

			if ($hasWorkOrder)
			{
				if ($hasTickets)
					$sSQL .= '(w.jcn IS NULL OR (';

				$sSQL .= "((w.jcn in (select wo_id from dcl_wo_account where account_id in ($sOrgs)))";
				$sSQL .= " AND (w.seq in (select seq from dcl_wo_account where w.jcn = wo_id And account_id in ($sOrgs))";
				$sSQL .= '))';

				if ($hasTickets)
					$sSQL .= '))';
			}

			if ($hasTickets)
			{
				if ($hasWorkOrder)
					$sSQL .= ' AND (t.ticketid IS NULL OR ';

				$sSQL .= "t.account IN ($sOrgs)";

				if ($hasWorkOrder)
					$sSQL .= ')';
			}
		}

		$sSQL .= ' GROUP BY tag.tag_desc ORDER BY 2 DESC';
		return $this->LimitQuery($sSQL, 0, 50);
	}
}
