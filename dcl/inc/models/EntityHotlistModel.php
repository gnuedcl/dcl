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
class EntityHotlistModel extends dclDB
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'dcl_entity_hotlist';
		LoadSchema($this->TableName);
		
		parent::Clear();
	}
	
	public function deleteByEntity($entity_id, $entity_key_id, $entity_key_id2 = 0, $except_hotlist_id = '-1')
	{
		$entity_id = (int)$entity_id;
		$entity_key_id = (int)$entity_key_id;
		$entity_key_id2 = (int)$entity_key_id2;
		$deleted_by = $GLOBALS['DCLID'];
		$deleted_on = $this->GetDateSQL();

		$this->Execute("INSERT INTO dcl_entity_hotlist_audit (entity_id, entity_key_id, entity_key_id2, hotlist_id, sort, audit_by, audit_on, audit_type)
						SELECT $entity_id, $entity_key_id, $entity_key_id2, hotlist_id, sort, $deleted_by, $deleted_on, 2
						FROM dcl_entity_hotlist WHERE entity_id = $entity_id AND entity_key_id = $entity_key_id AND entity_key_id2 = $entity_key_id2
						AND hotlist_id NOT IN ($except_hotlist_id)");

		$this->Execute("DELETE FROM dcl_entity_hotlist WHERE entity_id = $entity_id AND entity_key_id = $entity_key_id AND entity_key_id2 = $entity_key_id2 AND hotlist_id NOT IN ($except_hotlist_id)");
	}
	
	public function serialize($entity_id, $entity_key_id, $entity_key_id2, $sHotlists, $bAddOnly = false)
	{
		$entity_id = (int)$entity_id;
		$entity_key_id = (int)$entity_key_id;
		$entity_key_id2 = $entity_id == DCL_ENTITY_WORKORDER ? (int)$entity_key_id2 : 0;
		$sHotlists = trim($sHotlists);
		
		if ($sHotlists == '')
		{
			if (!$bAddOnly)
				$this->deleteByEntity($entity_id, $entity_key_id, $entity_key_id2);
				
			return;
		}
		
		$oHotlist = new HotlistModel();
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
			$this->deleteByEntity($entity_id, $entity_key_id, $entity_key_id2, $sHotlistID);
		}
			
		// Add the new hotlists
		if ($sHotlistID != '-1')
		{
			$created_by = $GLOBALS['DCLID'];
			$created_on = $this->GetDateSQL();

			$this->Execute("INSERT INTO dcl_entity_hotlist_audit (entity_id, entity_key_id, entity_key_id2, hotlist_id, sort, audit_by, audit_on, audit_type)
							SELECT $entity_id, $entity_key_id, $entity_key_id2, hotlist_id, 999999, $created_by, $created_on, 1
							FROM dcl_hotlist
							WHERE hotlist_id IN ($sHotlistID)
							AND hotlist_id NOT IN (SELECT hotlist_id FROM dcl_entity_hotlist WHERE entity_id = $entity_id AND entity_key_id = $entity_key_id AND entity_key_id2 = $entity_key_id2)");

			$this->Execute("INSERT INTO dcl_entity_hotlist SELECT $entity_id, $entity_key_id, $entity_key_id2, hotlist_id, 999999
							FROM dcl_hotlist
							WHERE hotlist_id IN ($sHotlistID)
							AND hotlist_id NOT IN (SELECT hotlist_id FROM dcl_entity_hotlist WHERE entity_id = $entity_id AND entity_key_id = $entity_key_id AND entity_key_id2 = $entity_key_id2)");
		}
	}
	
	public function getTagsForEntity($entity_id, $entity_key_id, $entity_key_id2 = 0)
	{
		$entity_id = (int)$entity_id;
		$entity_key_id = (int)$entity_key_id;
		$entity_key_id2 = (int)$entity_key_id2;

		if ($entity_id == DCL_ENTITY_WORKORDER)
			$sSQL = 'SELECT T.hotlist_tag FROM dcl_hotlist T ' . $this->JoinKeyword . " dcl_entity_hotlist ET ON T.hotlist_id = ET.hotlist_id WHERE ET.entity_id = $entity_id AND ET.entity_key_id = $entity_key_id AND ET.entity_key_id2 = $entity_key_id2 ORDER BY T.hotlist_tag";
		else
			$sSQL = 'SELECT T.hotlist_tag FROM dcl_hotlist T ' . $this->JoinKeyword . " dcl_entity_hotlist ET ON T.hotlist_id = ET.hotlist_id WHERE ET.entity_id = $entity_id AND ET.entity_key_id = $entity_key_id ORDER BY T.hotlist_tag";
			
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
	
	public function getTagsWithPriorityForEntity($entity_id, $entity_key_id, $entity_key_id2 = 0)
	{
		$entity_id = (int)$entity_id;
		$entity_key_id = (int)$entity_key_id;
		$entity_key_id2 = (int)$entity_key_id2;

		if ($entity_id == DCL_ENTITY_WORKORDER)
			$sSQL = 'SELECT T.hotlist_tag, ET.sort FROM dcl_hotlist T ' . $this->JoinKeyword . " dcl_entity_hotlist ET ON T.hotlist_id = ET.hotlist_id WHERE ET.entity_id = $entity_id AND ET.entity_key_id = $entity_key_id AND ET.entity_key_id2 = $entity_key_id2 ORDER BY T.hotlist_tag";
		else
			$sSQL = 'SELECT T.hotlist_tag, ET.sort FROM dcl_hotlist T ' . $this->JoinKeyword . " dcl_entity_hotlist ET ON T.hotlist_id = ET.hotlist_id WHERE ET.entity_id = $entity_id AND ET.entity_key_id = $entity_key_id ORDER BY T.hotlist_tag";

		if ($this->Query($sSQL) == -1)
			return '';

		$aHotlists = array();
		while ($this->next_record())
		{
			$aHotlists[] = array('hotlist' => $this->f(0), 'priority' => $this->f(1));
		}

		return $aHotlists;
	}

	public function setPriority($hotlistId, $aEntities)
	{
		$count = 1;
		foreach ($aEntities as $entity)
		{
			$entity_id = $entity[0];
			$entity_key_id = $entity[1];
			$entity_key_id2 = $entity[2];
			
			if ($entity_id == DCL_ENTITY_WORKORDER)
				$this->Execute('UPDATE dcl_entity_hotlist SET sort = ' . $count++ . " WHERE hotlist_id = $hotlistId AND entity_id = $entity_id AND entity_key_id = $entity_key_id AND entity_key_id2 = $entity_key_id2");
			else
				$this->Execute('UPDATE dcl_entity_hotlist SET sort = ' . $count++ . " WHERE hotlist_id = $hotlistId AND entity_id = $entity_id AND entity_key_id = $entity_key_id");
		}
	}
	
	public function listByTag($sHotlists)
	{
		global $g_oSec;
		
		if ($g_oSec->IsPublicUser())
		{
			throw new PermissionDeniedException();
		}
		
		$oDB = new HotlistModel();
		$sID = $oDB->getExistingIdsByName(trim($sHotlists));
		if ($sID == '-1')
		{
			trigger_error('No such hotlist.');
			return -1;
		}
		
		return $this->listById($sID);
	}
	
	public function listById($sID, $includeClosed = true)
	{
		global $g_oSec, $g_oSession;
		
		if ($g_oSec->IsPublicUser() || $sID == '-1')
		{
			throw new PermissionDeniedException();
		}
		
		$aHotlists = @Filter::ToIntArray($sID);
		$iHotlistCount = count($aHotlists);
		if ($iHotlistCount === 0)
		{
			throw new PermissionDeniedException();
		}
		
		$sID = join(',', $aHotlists);
		$bMultiHotlist = ($iHotlistCount > 1);
		
		$sSQL = '';
		$bDoneDidWhere = false;
		if ($g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH))
		{
			$sSQL = 'SELECT ' . DCL_ENTITY_WORKORDER . ' as entity_id, workorders.jcn, workorders.seq, workorders.summary, P.projectid, P.name AS project, statuses.name, R.short AS responsible, personnel.short, timecards.summary, dcl_entity_hotlist.sort FROM ';
			if ($bMultiHotlist)
			{
				$sSQL .= '(SELECT entity_key_id, entity_key_id2 FROM dcl_entity_hotlist WHERE entity_id = ' . DCL_ENTITY_WORKORDER . " AND hotlist_id IN ($sID) GROUP BY entity_key_id, entity_key_id2 HAVING COUNT(*) = $iHotlistCount) hotlist_matches ";
				$sSQL .= $this->JoinKeyword . ' workorders ON hotlist_matches.entity_key_id = workorders.jcn AND hotlist_matches.entity_key_id2 = workorders.seq ';
				$sSQL .= $this->JoinKeyword . ' statuses ON workorders.status = statuses.id ';
				$sSQL .= $this->JoinKeyword . ' personnel R ON workorders.responsible = R.id ';
				$sSQL .= 'LEFT JOIN projectmap PM ON workorders.jcn = PM.jcn AND workorders.seq in (0, PM.seq) ';
				$sSQL .= 'LEFT JOIN dcl_projects P ON P.projectid = PM.projectid ';
				$sSQL .= 'LEFT JOIN timecards ON workorders.jcn = timecards.jcn AND workorders.seq = timecards.seq AND timecards.id = (select max(id) from timecards where jcn = workorders.jcn AND seq = workorders.seq) ';
				$sSQL .= 'LEFT JOIN personnel ON timecards.actionby = personnel.id ';

				if (!$includeClosed)
				{
					$sSQL .= "WHERE statuses.dcl_status_type != 2";
					$bDoneDidWhere = true;
				}
				
				if ($g_oSec->IsPublicUser())
				{
					if ($bDoneDidWhere)
					{
						$sSQL .= ' AND ';
					}
					else
					{
						$sSQL .= ' WHERE ';
						$bDoneDidWhere = true;
					}

					$sSQL .= "workorders.is_public = 'Y'";
				}
			}
			else
			{
				$sSQL .= 'dcl_entity_hotlist JOIN workorders ON dcl_entity_hotlist.entity_id = ' . DCL_ENTITY_WORKORDER . ' AND dcl_entity_hotlist.entity_key_id = workorders.jcn AND dcl_entity_hotlist.entity_key_id2 = workorders.seq ';
				$sSQL .= $this->JoinKeyword . ' statuses ON workorders.status = statuses.id ';
				$sSQL .= $this->JoinKeyword . ' personnel R ON workorders.responsible = R.id ';
				$sSQL .= 'LEFT JOIN projectmap PM ON workorders.jcn = PM.jcn AND workorders.seq in (0, PM.seq) ';
				$sSQL .= 'LEFT JOIN dcl_projects P ON P.projectid = PM.projectid ';
				$sSQL .= 'LEFT JOIN timecards ON workorders.jcn = timecards.jcn AND workorders.seq = timecards.seq AND timecards.id = (select max(id) from timecards where jcn = workorders.jcn AND seq = workorders.seq) ';
				$sSQL .= 'LEFT JOIN personnel ON timecards.actionby = personnel.id ';
				$sSQL .= "WHERE dcl_entity_hotlist.hotlist_id = $sID";
				
				$bDoneDidWhere = true;
				
				if (!$includeClosed)
				{
					$sSQL .= " AND statuses.dcl_status_type != 2";
					$bDoneDidWhere = true;
				}

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
				
			$sSQL .= 'SELECT ' . DCL_ENTITY_TICKET . ' as entity_id, tickets.ticketid, 0, tickets.summary, NULL, NULL, R.short AS responsible, NULL, NULL, NULL, dcl_entity_hotlist.sort FROM ';
			if ($bMultiHotlist)
			{
				$sSQL .= '(SELECT entity_key_id, entity_key_id2 FROM dcl_entity_hotlist WHERE entity_id = ' . DCL_ENTITY_TICKET . " AND hotlist_id IN ($sID) GROUP BY entity_key_id, entity_key_id2 HAVING COUNT(*) = $iHotlistCount) hotlist_matches ";
				$sSQL .= $this->JoinKeyword . ' tickets ON hotlist_matches.entity_key_id = tickets.ticketid ';
				$sSQL .= $this->JoinKeyword . ' statuses ON tickets.status = statuses.id ';
				$sSQL .= $this->JoinKeyword . ' personnel R ON tickets.responsible = R.id ';

				if (!$includeClosed)
				{
					$sSQL .= "WHERE statuses.dcl_status_type != 2";
					$bDoneDidWhere = true;
				}

				if ($g_oSec->IsPublicUser())
				{
					if ($bDoneDidWhere)
					{
						$sSQL .= ' AND ';
					}
					else
					{
						$sSQL .= ' WHERE ';
						$bDoneDidWhere = true;
					}

					$sSQL .= "workorders.is_public = 'Y'";
				}
			}
			else
			{
				$sSQL .= 'dcl_entity_hotlist JOIN tickets ON dcl_entity_hotlist.entity_id = ' . DCL_ENTITY_TICKET . ' AND dcl_entity_hotlist.entity_key_id = tickets.ticketid ';
				$sSQL .= $this->JoinKeyword . ' statuses ON tickets.status = statuses.id ';
				$sSQL .= $this->JoinKeyword . ' personnel R ON tickets.responsible = R.id ';
				$sSQL .= "WHERE dcl_entity_hotlist.hotlist_id = $sID";

				$bDoneDidWhere = true;
				
				if (!$includeClosed)
				{
					$sSQL .= " AND statuses.dcl_status_type != 2";
					$bDoneDidWhere = true;
				}

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
			throw new PermissionDeniedException();
		}

		return $this->Query($sSQL . ' ORDER BY 11, 1, 2, 3');
	}
	
	public function GetStatusCount($hotlist_id)
	{
		$sql = 'SELECT s.name, count(*) FROM statuses s, workorders w, dcl_entity_hotlist h WHERE h.entity_id = ' . DCL_ENTITY_WORKORDER;
		$sql .= " AND w.jcn = h.entity_key_id AND w.seq = h.entity_key_id2 AND s.id = w.status AND h.hotlist_id = $hotlist_id";
		$sql .= ' GROUP BY s.name ORDER BY 2 DESC';

		return $this->Query($sql);
	}

	public function GetSeverityCount($hotlist_id)
	{
		$sql = 'SELECT s.name, count(*) FROM severities s, workorders w, dcl_entity_hotlist h WHERE h.entity_id = ' . DCL_ENTITY_WORKORDER;
		$sql .= " AND w.jcn = h.entity_key_id AND w.seq = h.entity_key_id2 AND s.id = w.severity AND h.hotlist_id = $hotlist_id";
		$sql .= ' GROUP BY s.name ORDER BY 2 DESC';

		return $this->Query($sql);
	}

	public function GetPriorityCount($hotlist_id)
	{
		$sql = 'SELECT s.name, count(*) FROM priorities s, workorders w, dcl_entity_hotlist h WHERE h.entity_id = ' . DCL_ENTITY_WORKORDER;
		$sql .= " AND w.jcn = h.entity_key_id AND w.seq = h.entity_key_id2 AND s.id = w.priority AND h.hotlist_id = $hotlist_id";
		$sql .= ' GROUP BY s.name ORDER BY 2 DESC';

		return $this->Query($sql);
	}

	public function GetDepartmentCount($hotlist_id)
	{
		$sql = 'SELECT d.name, count(*) FROM departments d, personnel u, workorders w, dcl_entity_hotlist h WHERE h.entity_id = ' . DCL_ENTITY_WORKORDER;
		$sql .= " AND w.jcn = h.entity_key_id AND w.seq = h.entity_key_id2 AND w.responsible = u.id AND d.id = u.department AND h.hotlist_id = $hotlist_id";
		$sql .= ' GROUP BY d.name ORDER BY 2 DESC';

		return $this->Query($sql);
	}

	public function GetModuleCount($hotlist_id)
	{
		$sql = 'SELECT m.module_name, count(*) FROM dcl_product_module m, workorders w, dcl_entity_hotlist h WHERE h.entity_id = ' . DCL_ENTITY_WORKORDER;
		$sql .= " AND w.jcn = h.entity_key_id AND w.seq = h.entity_key_id2 AND m.product_module_id = w.module_id AND h.hotlist_id = $hotlist_id";
		$sql .= ' GROUP BY m.module_name ORDER BY 2 DESC';

		return $this->Query($sql);
	}

	public function GetTypeCount($hotlist_id)
	{
		$sql = 'SELECT t.type_name, count(*) FROM dcl_wo_type t, workorders w, dcl_entity_hotlist h WHERE h.entity_id = ' . DCL_ENTITY_WORKORDER;
		$sql .= " AND w.jcn = h.entity_key_id AND w.seq = h.entity_key_id2 AND t.wo_type_id = w.wo_type_id AND h.hotlist_id = $hotlist_id";
		$sql .= ' GROUP BY t.type_name ORDER BY 2 DESC';

		return $this->Query($sql);
	}

	public function GetWorkOrderStatistics($id)
	{
		$retval = array(
				'totaltasks' => 0,
				'esthours' => 0.0,
				'totalhours' => 0.0,
				'etchours' => 0.0,
				'resources' => 0,
				'tasksclosed' => 0
			);

		$obj = new dclDB;

		$sql = 'SELECT COUNT(*), SUM(esthours), SUM(totalhours), SUM(etchours) FROM workorders a, dcl_entity_hotlist h WHERE h.entity_id = ' . DCL_ENTITY_WORKORDER;
		$sql .= " AND a.jcn = h.entity_key_id AND a.seq = h.entity_key_id2 AND h.hotlist_id = $id ";
		if ($obj->Query($sql) != -1)
		{
			if ($obj->next_record())
			{
				$retval['totaltasks'] = $obj->f(0);
				$retval['esthours'] = $obj->f(1);
				$retval['totalhours'] = $obj->f(2);
				$retval['etchours'] = $obj->f(3);
			}

			$obj->FreeResult();
		}

		$sql = 'SELECT COUNT(distinct responsible) FROM workorders a, dcl_entity_hotlist h, statuses c WHERE h.entity_id = ' . DCL_ENTITY_WORKORDER;
		$sql .= " AND a.jcn = h.entity_key_id AND a.seq = h.entity_key_id2 AND h.hotlist_id = $id AND a.status=c.id AND c.dcl_status_type!=2";
		if ($obj->Query($sql) != -1)
		{
			if ($obj->next_record())
				$retval['resources'] = $obj->f(0);

			$obj->FreeResult();
		}

		$sql = 'SELECT count(*) FROM workorders a, dcl_entity_hotlist h, statuses c WHERE h.entity_id = ' . DCL_ENTITY_WORKORDER;
		$sql .= " AND a.jcn = h.entity_key_id AND a.seq = h.entity_key_id2 AND h.hotlist_id = $id AND a.status=c.id AND c.dcl_status_type=2";
		if ($obj->Query($sql) != -1)
		{
			if ($obj->next_record())
				$retval['tasksclosed'] = $obj->f(0);

			$obj->FreeResult();
		}

		return $retval;
	}
}
