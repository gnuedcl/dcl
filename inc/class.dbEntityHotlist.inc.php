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

LoadStringResource('db');
class dbEntityHotlist extends dclDB
{
	function dbEntityHotlist()
	{
		parent::dclDB();
		$this->TableName = 'dcl_entity_hotlist';
		LoadSchema($this->TableName);
		
		parent::Clear();
	}
	
	function deleteByEntity($entity_id, $entity_key_id, $entity_key_id2 = 0)
	{
		$entity_id = (int)$entity_id;
		$entity_key_id = (int)$entity_key_id;
		$entity_key_id2 = (int)$entity_key_id2;
		$deleted_by = $GLOBALS['DCLID'];
		$deleted_on = $this->GetDateSQL();

		if ($entity_id == DCL_ENTITY_WORKORDER)
			$this->Execute("UPDATE dcl_entity_hotlist SET deleted_on=$deleted_on, deleted_by=$deleted_by WHERE deleted_on IS NULL AND entity_id = $entity_id AND entity_key_id = $entity_key_id AND entity_key_id2 = $entity_key_id2");
		else
			$this->Execute("UPDATE dcl_entity_hotlist SET deleted_on=$deleted_on, deleted_by=$deleted_by WHERE deleted_on IS NULL AND entity_id = $entity_id AND entity_key_id = $entity_key_id");
	}
	
	function serialize($entity_id, $entity_key_id, $entity_key_id2, $sHotlists, $bAddOnly = false)
	{
		$entity_id = (int)$entity_id;
		$entity_key_id = (int)$entity_key_id;
		$entity_key_id2 = (int)$entity_key_id2;
		$sHotlists = trim($sHotlists);
		
		if ($sHotlists == '')
		{
			if (!$bAddOnly)
				$this->deleteByEntity($entity_id, $entity_key_id, $entity_key_id2);
				
			return;
		}
		
		$oHotlist =& CreateObject('dcl.dbHotlist');
		$aHotlists = split(',', $sHotlists);
		$aHotlistID = array();
		foreach ($aHotlists as $sHotlist)
		{
			$sHotlist = trim($sHotlist);
			if ($sHotlist == '')
				continue;
			
			if (strlen($sHotlist) > $GLOBALS['phpgw_baseline'][$oHotlist->TableName]['fd']['hotlist_tag']['precision'])
			{
				ShowError(sprintf(STR_DB_TAGLENGTHERR, htmlspecialchars($sHotlist), $GLOBALS['phpgw_baseline'][$oHotlist->TableName]['fd']['hotlist_tag']['precision']));
				continue;
			}
			
			$iID = $oHotlist->getIdByName($sHotlist);
			if ($iID !== null && !in_array($iID, $aHotlistID))
			{
				$aHotlistID[] = $iID;
			}
		}
		
		if (count($aHotlistID) < 1)
			$aHotlistID[] = -1;
			
		$sHotlistID = join(',', $aHotlistID);
		
		// Delete the hotlists that are no longer referenced if we're not in add only mode
		if (!$bAddOnly)
		{
			$deleted_by = $GLOBALS['DCLID'];
			$deleted_on = $this->GetDateSQL();
			if ($entity_id == DCL_ENTITY_WORKORDER)
				$this->Execute("UPDATE dcl_entity_hotlist SET deleted_on=$deleted_on, deleted_by=$deleted_by WHERE deleted_on IS NULL AND entity_id = $entity_id AND entity_key_id = $entity_key_id AND entity_key_id2 = $entity_key_id2 AND hotlist_id NOT IN ($sHotlistID)");
			else
				$this->Execute("UPDATE dcl_entity_hotlist SET deleted_on=$deleted_on, deleted_by=$deleted_by WHERE deleted_on IS NULL AND entity_id = $entity_id AND entity_key_id = $entity_key_id AND hotlist_id NOT IN ($sHotlistID)");
		}
			
		// Add the new hotlists
		if ($sHotlistID != '-1')
		{
			$created_by = $GLOBALS['DCLID'];
			$created_on = $this->GetDateSQL();
			if ($entity_id == DCL_ENTITY_WORKORDER)
				$this->Execute("INSERT INTO dcl_entity_hotlist SELECT $entity_id, $entity_key_id, $entity_key_id2, hotlist_id, $created_on, $created_by, NULL, NULL FROM dcl_hotlist WHERE hotlist_id IN ($sHotlistID) AND hotlist_id NOT IN (SELECT hotlist_id FROM dcl_entity_hotlist WHERE entity_id = $entity_id AND entity_key_id = $entity_key_id AND entity_key_id2 = $entity_key_id2)");
			else
				$this->Execute("INSERT INTO dcl_entity_hotlist SELECT $entity_id, $entity_key_id, 0, hotlist_id, $created_on, $created_by, NULL, NULL FROM dcl_hotlist WHERE hotlist_id IN ($sHotlistID) AND hotlist_id NOT IN (SELECT hotlist_id FROM dcl_entity_hotlist WHERE entity_id = $entity_id AND entity_key_id = $entity_key_id)");
				
			// Re-add items to list by removing deleted info and updating created info
			if ($entity_id == DCL_ENTITY_WORKORDER)
				$this->Execute("UPDATE dcl_entity_hotlist SET created_on = $created_on, created_by = $created_by, deleted_on = NULL, deleted_by = NULL WHERE deleted_on IS NOT NULL AND entity_id = $entity_id AND entity_key_id = $entity_key_id AND entity_key_id2 = $entity_key_id2 AND hotlist_id IN ($sHotlistID)");
			else
				$this->Execute("UPDATE dcl_entity_hotlist SET created_on = $created_on, created_by = $created_by, deleted_on = NULL, deleted_by = NULL WHERE deleted_on IS NOT NULL AND entity_id = $entity_id AND entity_key_id = $entity_key_id AND hotlist_id IN ($sHotlistID)");
		}
	}
	
	function getTagsForEntity($entity_id, $entity_key_id, $entity_key_id2 = 0)
	{
		$entity_id = (int)$entity_id;
		$entity_key_id = (int)$entity_key_id;
		$entity_key_id2 = (int)$entity_key_id2;

		if ($entity_id == DCL_ENTITY_WORKORDER)
			$sSQL = 'SELECT T.hotlist_tag FROM dcl_hotlist T ' . $this->JoinKeyword . " dcl_entity_hotlist ET ON T.hotlist_id = ET.hotlist_id WHERE ET.entity_id = $entity_id AND ET.entity_key_id = $entity_key_id AND ET.entity_key_id2 = $entity_key_id2 AND ET.deleted_on IS NULL ORDER BY T.hotlist_tag";
		else
			$sSQL = 'SELECT T.hotlist_tag FROM dcl_hotlist T ' . $this->JoinKeyword . " dcl_entity_hotlist ET ON T.hotlist_id = ET.hotlist_id WHERE ET.entity_id = $entity_id AND ET.entity_key_id = $entity_key_id AND ET.deleted_on IS NULL ORDER BY T.hotlist_tag";
			
		if ($this->Query($sSQL) == -1)
			return '';
			
		$sHotlists = '';
		while ($this->next_record())
		{
			if ($sHotlists != '')
				$sHotlists .= ',';
				
			$sHotlists .= $this->f(0);
		}
			
		return $sHotlists;
	}
	
	function listByTag($sHotlists)
	{
		global $g_oSec, $g_oSession;
		
		if ($g_oSec->IsPublicUser())
		{
			PrintPermissionDenied();
			return;
		}
		
		$oDB = CreateObject('dcl.dbHotlist');
		$sID = $oDB->getExistingIdsByName(trim($sHotlists));
		if ($sID == '-1')
		{
			trigger_error('No such hotlist.');
			return;
		}
		
		$aHotlists = split(',', $sID);
		$iHotlistCount = count($aHotlists);
		$bMultiHotlist = ($iHotlistCount > 1);
		
		$sSQL = '';
		$bDoneDidWhere = false;
		if ($g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH))
		{
			$sSQL = 'SELECT ' . DCL_ENTITY_WORKORDER . ' as entity_id, workorders.jcn, workorders.seq, workorders.summary FROM ';
			if ($bMultiHotlist)
			{
				$sSQL .= '(SELECT entity_key_id, entity_key_id2 FROM dcl_entity_hotlist WHERE deleted_on IS NULL AND entity_id = ' . DCL_ENTITY_WORKORDER . " AND hotlist_id IN ($sID) GROUP BY entity_key_id, entity_key_id2 HAVING COUNT(*) = $iHotlistCount) hotlist_matches ";
				$sSQL .= 'JOIN workorders ON hotlist_matches.entity_key_id = workorders.jcn AND hotlist_matches.entity_key_id2 = workorders.seq';
				
				if ($g_oSec->IsPublicUser())
				{
					$sSQL .= " WHERE workorders.is_public = 'Y'";
					$bDoneDidWhere = true;
				}
			}
			else
			{
				$sSQL .= 'dcl_entity_hotlist JOIN workorders ON dcl_entity_hotlist.entity_id = ' . DCL_ENTITY_WORKORDER . ' AND dcl_entity_hotlist.entity_key_id = workorders.jcn AND dcl_entity_hotlist.entity_key_id2 = workorders.seq ';
				$sSQL .= "WHERE dcl_entity_hotlist.deleted_on IS NULL AND dcl_entity_hotlist.hotlist_id = $sID";
				$bDoneDidWhere = true;
				
				if ($g_oSec->IsPublicUser())
					$sSQL .= " AND workorders.is_public = 'Y'";					
			}
			
			$sAccountSQL = '';
			if ($g_oSec->IsOrgUser())
			{
				$sOrgs = $g_oSession->Value('member_of_orgs');
				if ($sOrgs == '')
					$sOrgs = '-1';

				$sAccountSQL = "((workorders.jcn in (select wo_id from dcl_wo_account where account_id in ($sOrgs)))";
				$sAccountSQL .= " AND (workorders.seq in (select seq from dcl_wo_account where workorders.jcn = wo_id And account_id in ($sOrgs))";
				$sAccountSQL .= '))';
			}

			if ($g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEWSUBMITTED))
			{
				if ($bDoneDidWhere == false)
				{
					$bDoneDidWhere = true;
					$sSQL .= ' WHERE ';
				}
				else
					$sSQL .= ' AND ';
	
				$sSQL .= '(workorders.createby = ' . $GLOBALS['DCLID'];
				$sSQL .= ' OR workorders.contact_id = ' . $g_oSession->Value('contact_id');
				if ($sAccountSQL != '')
					$sSQL .= ' OR ' . $sAccountSQL;
					
				$sSQL .= ')';
			}
			else if ($sAccountSQL != '')
			{
				if ($bDoneDidWhere == false)
				{
					$bDoneDidWhere = true;
					$sSQL .= ' WHERE ';
				}
				else
					$sSQL .= ' AND ';
	
				$sSQL .= $sAccountSQL;
			}
		}
		
		if ($g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_SEARCH))
		{
			if ($sSQL != '')
				$sSQL .= ' UNION ALL ';
				
			$sSQL .= 'SELECT ' . DCL_ENTITY_TICKET . ' as entity_id, tickets.ticketid, 0, tickets.summary FROM ';
			if ($bMultiHotlist)
			{
				$sSQL .= '(SELECT entity_key_id, entity_key_id2 FROM dcl_entity_hotlist WHERE deleted_on IS NULL AND entity_id = ' . DCL_ENTITY_TICKET . " AND hotlist_id IN ($sID) GROUP BY entity_key_id, entity_key_id2 HAVING COUNT(*) = $iHotlistCount) hotlist_matches ";
				$sSQL .= 'JOIN tickets ON hotlist_matches.entity_key_id = tickets.ticketid';
				
				if ($g_oSec->IsPublicUser())
				{
					$bDoneDidWhere = true;
					$sSQL .= " WHERE tickets.is_public = 'Y'";
				}
			}
			else
			{
				$sSQL .= 'dcl_entity_hotlist JOIN tickets ON dcl_entity_hotlist.entity_id = ' . DCL_ENTITY_TICKET . ' AND dcl_entity_hotlist.entity_key_id = tickets.ticketid ';
				$sSQL .= "WHERE dcl_entity_hotlist.deleted_on IS NULL AND dcl_entity_hotlist.hotlist_id = $sID";
				$bDoneDidWhere = true;
				
				if ($g_oSec->IsPublicUser())
					$sSQL .= " AND tickets.is_public = 'Y'";
			}
			
			$sAccountSQL = '';
			if ($g_oSec->IsOrgUser())
			{
				$sOrgs = $g_oSession->Value('member_of_orgs');
				if ($sOrgs == '')
					$sOrgs = '-1';

				$sAccountSQL = "account IN ($sOrgs)";
			}
			
			if ($g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_VIEWSUBMITTED))
			{
				if ($bDoneDidWhere == false)
				{
					$bDoneDidWhere = true;
					$sSQL .= ' WHERE ';
				}
				else
					$sSQL .= ' AND ';
	
				$sSQL .= '(tickets.createdby = ' . $GLOBALS['DCLID'];
				$sSQL .= ' OR tickets.contact_id = ' . $g_oSession->Value('contact_id');
				if ($sAccountSQL != '')
					$sSQL .= ' OR ' . $sAccountSQL;
					
				$sSQL .= ')';
			}
			else if ($sAccountSQL != '')
			{
				if ($bDoneDidWhere == false)
				{
					$bDoneDidWhere = true;
					$sSQL .= ' WHERE ';
				}
				else
					$sSQL .= ' AND ';
	
				$sSQL .= $sAccountSQL;
			}
		}

		if ($sSQL == '')
		{
			PrintPermissionDenied();
			return;
		}

		return $this->Query($sSQL . ' ORDER BY 1, 2, 3');
	}
}
?>