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
class EntityTagModel extends DbProvider
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'dcl_entity_tag';
		LoadSchema($this->TableName);
		
		parent::Clear();
	}
	
	public function deleteByEntity($entity_id, $entity_key_id, $entity_key_id2 = 0)
	{
		$entity_id = (int)$entity_id;
		$entity_key_id = (int)$entity_key_id;
		$entity_key_id2 = (int)$entity_key_id2;
		
		if ($entity_id == DCL_ENTITY_WORKORDER)
			$this->Execute("DELETE FROM dcl_entity_tag WHERE entity_id = $entity_id AND entity_key_id = $entity_key_id AND entity_key_id2 = $entity_key_id2");
		else
			$this->Execute("DELETE FROM dcl_entity_tag WHERE entity_id = $entity_id AND entity_key_id = $entity_key_id");
	}
	
	public function serialize($entity_id, $entity_key_id, $entity_key_id2, $sTags, $bAddOnly = false)
	{
		$entity_id = (int)$entity_id;
		$entity_key_id = (int)$entity_key_id;
		$entity_key_id2 = (int)$entity_key_id2;
		$sTags = trim($sTags);
		
		if ($sTags == '')
		{
			if (!$bAddOnly)
				$this->deleteByEntity($entity_id, $entity_key_id, $entity_key_id2);
				
			return;
		}
		
		$oTag = new TagModel();
		$aTags = explode(',', $sTags);
		$aTagID = array();
		foreach ($aTags as $sTag)
		{
			$sTag = trim($sTag);
			if ($sTag == '')
				continue;
			
			if (strlen($sTag) > $GLOBALS['phpgw_baseline'][$oTag->TableName]['fd']['tag_desc']['precision'])
			{
				ShowError(sprintf(STR_DB_TAGLENGTHERR, htmlspecialchars($sTag), $GLOBALS['phpgw_baseline'][$oTag->TableName]['fd']['tag_desc']['precision']));
				continue;
			}
			
			$iID = $oTag->getIdByName($sTag);
			if ($iID !== null && !in_array($iID, $aTagID))
			{
				$aTagID[] = $iID;
			}
		}
		
		if (count($aTagID) < 1)
			$aTagID[] = -1;
			
		$sTagID = join(',', $aTagID);
		
		// Delete the tags that are no longer referenced if we're not in add only mode
		if (!$bAddOnly)
		{
			if ($entity_id == DCL_ENTITY_WORKORDER)
				$this->Execute("DELETE FROM dcl_entity_tag WHERE entity_id = $entity_id AND entity_key_id = $entity_key_id AND entity_key_id2 = $entity_key_id2 AND tag_id NOT IN ($sTagID)");
			else
				$this->Execute("DELETE FROM dcl_entity_tag WHERE entity_id = $entity_id AND entity_key_id = $entity_key_id AND tag_id NOT IN ($sTagID)");
		}
			
		// Add the new tags
		if ($sTagID != '-1')
		{
			if ($entity_id == DCL_ENTITY_WORKORDER)
				$this->Execute("INSERT INTO dcl_entity_tag SELECT $entity_id, $entity_key_id, $entity_key_id2, tag_id FROM dcl_tag WHERE tag_id IN ($sTagID) AND tag_id NOT IN (SELECT tag_id FROM dcl_entity_tag WHERE entity_id = $entity_id AND entity_key_id = $entity_key_id AND entity_key_id2 = $entity_key_id2)");
			else
				$this->Execute("INSERT INTO dcl_entity_tag SELECT $entity_id, $entity_key_id, 0, tag_id FROM dcl_tag WHERE tag_id IN ($sTagID) AND tag_id NOT IN (SELECT tag_id FROM dcl_entity_tag WHERE entity_id = $entity_id AND entity_key_id = $entity_key_id)");
		}
	}
	
	public function getTagsForEntity($entity_id, $entity_key_id, $entity_key_id2 = 0)
	{
		$entity_id = (int)$entity_id;
		$entity_key_id = (int)$entity_key_id;
		$entity_key_id2 = (int)$entity_key_id2;

		if ($entity_id == DCL_ENTITY_WORKORDER)
			$sSQL = 'SELECT T.tag_desc FROM dcl_tag T ' . $this->JoinKeyword . " dcl_entity_tag ET ON T.tag_id = ET.tag_id WHERE ET.entity_id = $entity_id AND ET.entity_key_id = $entity_key_id AND ET.entity_key_id2 = $entity_key_id2 ORDER BY T.tag_desc";
		else
			$sSQL = 'SELECT T.tag_desc FROM dcl_tag T ' . $this->JoinKeyword . " dcl_entity_tag ET ON T.tag_id = ET.tag_id WHERE ET.entity_id = $entity_id AND ET.entity_key_id = $entity_key_id ORDER BY T.tag_desc";
			
		if ($this->Query($sSQL) == -1)
			return '';
			
		$sTags = '';
		while ($this->next_record())
		{
			if ($sTags != '')
				$sTags .= ',';
				
			$sTags .= $this->f(0);
		}
			
		return $sTags;
	}
	
	public function listByTag($sTags)
	{
		global $g_oSec, $g_oSession;
		
		$oDB = new TagModel();
		$sID = $oDB->getExistingIdsByName(trim($sTags));
		if ($sID == '-1')
		{
			ShowError('No such tag.');
			return;
		}

		$aTags = explode(',', $sID);
		$iTagCount = count($aTags);
		$bMultiTag = ($iTagCount > 1);
		
		$sSQL = '';
		$bDoneDidWhere = false;
		if ($g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH))
		{
			$sSQL = 'SELECT ' . DCL_ENTITY_WORKORDER . ' as entity_id, workorders.jcn, workorders.seq, workorders.summary FROM ';
			if ($bMultiTag)
			{
				$sSQL .= '(SELECT entity_key_id, entity_key_id2 FROM dcl_entity_tag WHERE entity_id = ' . DCL_ENTITY_WORKORDER . " AND tag_id IN ($sID) GROUP BY entity_key_id, entity_key_id2 HAVING COUNT(*) = $iTagCount) tag_matches ";
				$sSQL .= 'JOIN workorders ON tag_matches.entity_key_id = workorders.jcn AND tag_matches.entity_key_id2 = workorders.seq';
				
				if ($g_oSec->IsPublicUser())
				{
					$sSQL .= " WHERE workorders.is_public = 'Y'";
					$bDoneDidWhere = true;
				}
			}
			else
			{
				$sSQL .= 'dcl_entity_tag JOIN workorders ON dcl_entity_tag.entity_id = ' . DCL_ENTITY_WORKORDER . ' AND dcl_entity_tag.entity_key_id = workorders.jcn AND dcl_entity_tag.entity_key_id2 = workorders.seq ';
				$sSQL .= "WHERE dcl_entity_tag.tag_id = $sID";
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
	
				$sSQL .= '(workorders.createby = ' . DCLID;
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
			if ($bMultiTag)
			{
				$sSQL .= '(SELECT entity_key_id, entity_key_id2 FROM dcl_entity_tag WHERE entity_id = ' . DCL_ENTITY_TICKET . " AND tag_id IN ($sID) GROUP BY entity_key_id, entity_key_id2 HAVING COUNT(*) = $iTagCount) tag_matches ";
				$sSQL .= 'JOIN tickets ON tag_matches.entity_key_id = tickets.ticketid';
				
				if ($g_oSec->IsPublicUser())
				{
					$bDoneDidWhere = true;
					$sSQL .= " WHERE tickets.is_public = 'Y'";
				}
			}
			else
			{
				$sSQL .= 'dcl_entity_tag JOIN tickets ON dcl_entity_tag.entity_id = ' . DCL_ENTITY_TICKET . ' AND dcl_entity_tag.entity_key_id = tickets.ticketid ';
				$sSQL .= "WHERE dcl_entity_tag.tag_id = $sID";
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
	
				$sSQL .= '(tickets.createdby = ' . DCLID;
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
			throw new PermissionDeniedException();
		}

		return $this->Query($sSQL . ' ORDER BY 1, 2, 3');
	}
}
