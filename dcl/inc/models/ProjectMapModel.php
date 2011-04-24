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
class ProjectMapModel extends dclDB
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'projectmap';
		LoadSchema($this->TableName);
		$this->AuditEnabled = true;
		parent::Clear();
	}

	public function Add()
	{
		if (!$this->Exists(array('projectid' => $this->projectid, 'jcn' => $this->jcn, 'seq' => $this->seq)))
		{
			$sValues = $this->FieldValueToSQL('projectid', $this->projectid);
			$sValues .= ',' . $this->FieldValueToSQL('jcn', $this->jcn);
			$sValues .= ',' . $this->FieldValueToSQL('seq', $this->seq);

			$query  = 'INSERT INTO projectmap (projectid, jcn, seq) VALUES (' . $sValues . ')';
			if ($this->Insert($query) == -1)
			{
				print(sprintf(STR_DB_PROJECTMAPINSERTERR, $query));
			}
			else
			{
				$this->Execute('INSERT INTO projectmap_audit VALUES (' . $sValues . ', ' . $this->GetDateSQL() . ', ' . $GLOBALS['DCLID'] . ', ' . DCL_EVENT_ADD . ')');
			}
		}
	}

	public function Edit()
	{
		// Do nothing
	}

	public function Delete()
	{
		$sSQL = 'INSERT INTO projectmap_audit VALUES (' . $this->projectid . ', ' . $this->jcn . ', ' . $this->seq . ', ' . $this->GetDateSQL() . ', ' . $GLOBALS['DCLID'] . ', ' . DCL_EVENT_DELETE . ')';
		$this->Execute($sSQL);

		$query = 'DELETE FROM projectmap WHERE projectid=' . $this->projectid . ' AND jcn=' . $this->jcn;
		if ($this->seq > 0)
			$query .= ' AND seq=' . $this->seq;

		$this->Execute($query);
	}
	
	public function AuditWorkOrderList($jcn, $seq)
	{
		$aRetVal = array();

		if ($this->Query("SELECT dcl_projects.name, jcn, seq, audit_on, personnel.short, audit_type, projectmap_audit.projectid FROM projectmap_audit, dcl_projects, personnel WHERE projectmap_audit.projectid = dcl_projects.projectid AND audit_by = personnel.id AND jcn=$jcn AND seq=$seq ORDER BY audit_on") != -1)
		{
			while ($this->next_record())
			{
				$sType = ($this->f(5) == DCL_EVENT_ADD ? 'Add' : 'Delete');
				$aRetVal[] = array('name' => $this->f(0), 'jcn' => $this->f(1), 'seq' => $this->f(2), 'projectid' => $this->f(6),
									'audit_on' => $this->FieldValueFromSQL('audit_on', $this->f(3)), 'audit_by' => $this->f(4), 'audit_type' => $sType);
			}
		}

		return $aRetVal;
	}

	public function AuditProjectList($projectid)
	{
		$aRetVal = array();

		if ($this->Query("SELECT projectmap_audit.jcn, projectmap_audit.seq, summary, audit_on, personnel.short, audit_type FROM projectmap_audit, workorders, personnel WHERE projectmap_audit.jcn = workorders.jcn AND projectmap_audit.seq IN (0, workorders.seq) AND audit_by = personnel.id AND projectid = $projectid ORDER BY audit_on") != -1)
		{
			while ($this->next_record())
			{
				$aRetVal[] = array('jcn' => $this->f(0), 'seq' => $this->f(1), 'summary' => $this->f(2),
									'audit_on' => $this->FieldValueFromSQL('audit_on', $this->f(3)), 'audit_by' => $this->f(4),
									'audit_type' => ($this->f(5) == DCL_EVENT_ADD ? 'Add' : 'Delete'));
			}
		}

		return $aRetVal;
	}

	public function Load($projectid)
	{
		$this->Clear();

		$sql = 'SELECT b.projectid,a.jcn,a.seq FROM workorders a, projectmap b WHERE ';
		$sql .= "a.jcn=b.jcn AND (b.seq=0 OR a.seq=b.seq) AND b.projectid=$projectid ";
		$sql .= 'ORDER BY a.jcn,a.seq';
		if (!$this->Query($sql))
			return -1;

		if (!$this->next_record())
			return -1;

		return $this->GetRow();
	}
	
	public function GetStatusCount($projectid)
	{
		$sql = 'SELECT s.name, count(*) FROM statuses s, workorders w, projectmap p WHERE ';
		$sql .= "w.jcn=p.jcn AND p.seq IN (0, w.seq) AND s.id = w.status AND p.projectid=$projectid ";
		$sql .= 'GROUP BY s.name ORDER BY 2 DESC';
		
		return $this->Query($sql);
	}

	public function GetSeverityCount($projectid)
	{
		$sql = 'SELECT s.name, count(*) FROM severities s, workorders w, projectmap p WHERE ';
		$sql .= "w.jcn=p.jcn AND p.seq IN (0, w.seq) AND s.id = w.severity AND p.projectid=$projectid ";
		$sql .= 'GROUP BY s.name ORDER BY 2 DESC';
		
		return $this->Query($sql);
	}

	public function GetPriorityCount($projectid)
	{
		$sql = 'SELECT s.name, count(*) FROM priorities s, workorders w, projectmap p WHERE ';
		$sql .= "w.jcn=p.jcn AND p.seq IN (0, w.seq) AND s.id = w.priority AND p.projectid=$projectid ";
		$sql .= 'GROUP BY s.name ORDER BY 2 DESC';
		
		return $this->Query($sql);
	}

	public function GetDepartmentCount($projectid)
	{
		$sql = 'SELECT d.name, count(*) FROM departments d, personnel u, workorders w, projectmap p WHERE ';
		$sql .= "w.jcn=p.jcn AND p.seq IN (0, w.seq) AND w.responsible = u.id AND d.id = u.department AND p.projectid=$projectid ";
		$sql .= 'GROUP BY d.name ORDER BY 2 DESC';
		
		return $this->Query($sql);
	}

	public function GetModuleCount($projectid)
	{
		$sql = 'SELECT m.module_name, count(*) FROM dcl_product_module m, workorders w, projectmap p WHERE ';
		$sql .= "w.jcn=p.jcn AND p.seq IN (0, w.seq) AND m.product_module_id = w.module_id AND p.projectid=$projectid ";
		$sql .= 'GROUP BY m.module_name ORDER BY 2 DESC';
		
		return $this->Query($sql);
	}

	public function GetTypeCount($projectid)
	{
		$sql = 'SELECT t.type_name, count(*) FROM dcl_wo_type t, workorders w, projectmap p WHERE ';
		$sql .= "w.jcn=p.jcn AND p.seq IN (0, w.seq) AND t.wo_type_id = w.wo_type_id AND p.projectid=$projectid ";
		$sql .= 'GROUP BY t.type_name ORDER BY 2 DESC';
		
		return $this->Query($sql);
	}

	public function LoadFilter($projectid, $status, $responsible)
	{
		$this->Clear();

		$sql = 'SELECT b.projectid,a.jcn,a.seq FROM workorders a, projectmap b WHERE ';
		$sql .= "a.jcn=b.jcn AND (b.seq=0 OR a.seq=b.seq) AND b.projectid=$projectid ";

		if ($status > 0)
			$sql .= "AND a.status=$status ";

		if ($responsible > 0)
			$sql .= "AND a.responsible=$responsible ";

		$sql .= 'ORDER BY a.jcn,a.seq';
		if (!$this->Query($sql))
			return -1;

		return 1;
	}

	public function GetProjectParents($projectid, $includethis = false)
	{
		if ($includethis == false)
		{
			$this->Clear();
			$projectids = '';
		}
		else
		{
			$projectids = $projectid;
		}

		if ($this->Query("SELECT parentprojectid FROM dcl_projects WHERE projectid =$projectid") != -1)
		{
			$aRecords = $this->FetchAllRows();
			$this->FreeResult();
			for ($i = 0; $i < count($aRecords); $i++)
			{
				if($projectids != '')
					$projectids .= ',';

				$projectids .= $this->GetProjectParents($aRecords[$i][0], true);
			}
		}

		return $projectids;
	}

	public function GetProjectChildren($projectid, $includethis = false)
	{
		if ($includethis == false)
		{
			$this->Clear();
			$projectids = '';
		}
		else
		{
			$projectids = $projectid;
		}

		if ($this->Query("SELECT projectid FROM dcl_projects WHERE parentprojectid=$projectid") != -1)
		{
			$aRecords = $this->FetchAllRows();
			$this->FreeResult();
			for ($i = 0; $i < count($aRecords); $i++)
			{
				if($projectids != '')
					$projectids .= ',';

				$projectids .= $this->GetProjectChildren($aRecords[$i][0], true);
			}
		}

		return $projectids;
	}

	public function LoadProjects($projectids)
	{
		if ($projectids == '')
			return -1;

		$this->Clear();

		$sql = 'SELECT b.projectid,a.jcn,a.seq FROM workorders a, projectmap b WHERE ';
		$sql .= 'a.jcn=b.jcn AND (b.seq=0 OR a.seq=b.seq) AND ';
		$sql .= "b.projectid in ($projectids) ";
		$sql .= 'ORDER BY b.projectid,a.jcn,a.seq';
		if (!$this->Query($sql))
			return -1;

		return 1;
	}

	public function LoadChildren($projectid)
	{
		$projectids = $this->GetProjectChildren($projectid, true);
		return $this->LoadProjects($projectids);
	}

	public function LoadParents($projectid)
	{
		$projectids = $this->GetProjectParents($projectid, true);
		return $this->LoadProjects($projectids);
	}

	public function LoadByWO($jcn, $seq)
	{
		$this->Clear();

		$sql = "SELECT projectid,jcn,seq FROM projectmap WHERE jcn=$jcn and seq in (0,$seq)";
		if (!$this->Query($sql))
			return -1;

		if (!$this->next_record())
			return -1;

		return $this->GetRow();
	}

	public function LoadByWOFilter($jcn, $seq, $seqonly = false, $allforjcn = false)
	{
		$query = "SELECT projectid, jcn, seq FROM projectmap WHERE jcn=$jcn";
		if ($allforjcn == false)
		{
			$query .= ' AND ';
			if ($seqonly)
				$query .= "seq=$seq";
			else
				$query .= "seq in (0,$seq)";
		}

		if (!$this->Query($query))
			return -1;
	}
	
	public function MapAllExcept($iProjectID, $iID, $iSeq)
	{
		$this->Execute("INSERT INTO projectmap SELECT $iProjectID, jcn, seq FROM workorders WHERE jcn = $iID AND seq != $iSeq");
	}
}
