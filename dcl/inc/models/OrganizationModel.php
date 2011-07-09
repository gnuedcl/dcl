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
class OrganizationModel extends dclDB
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'dcl_org';
		LoadSchema($this->TableName);
		
		parent::Clear();
	}
	
	public function GetProductArray($aOrgID)
	{
		if (($aOrgID = Filter::ToIntArray($aOrgID)) === null)
		{
			throw new InvalidDataException();
		}
		
		$aRetVal = array();
		$sOrgID = '-1';
		if (count($aOrgID) > 0)
			$sOrgID = join(',', $aOrgID);

		$sSQL = "SELECT DISTINCT product_id FROM dcl_org_product_xref WHERE org_id IN ($sOrgID)";
		if ($this->Query($sSQL) != -1)
		{
			while ($this->next_record())
				$aRetVal[] = $this->f(0);
		}

		return $aRetVal;
	}
	
	public function ListMainContacts($org_id)
	{
		if (($org_id = Filter::ToInt($org_id)) === null)
			return;
		
		$sSQL = "SELECT DISTINCT C.last_name, C.first_name, C.contact_id
				FROM dcl_contact C 
				" . $this->JoinKeyword . " dcl_contact_type_xref CTX ON C.contact_id = CTX.contact_id 
				" . $this->JoinKeyword . " dcl_contact_type CT ON CTX.contact_type_id = CT.contact_type_id
				" . $this->JoinKeyword . " dcl_org_contact OC ON C.contact_id = OC.contact_id 
				WHERE OC.org_id = $org_id
				AND CT.contact_type_is_main = 'Y' 
				ORDER BY C.last_name, C.first_name, C.contact_id";
		
		$this->Query($sSQL);
	}

	public function GetOrganizationCount()
	{
		global $g_oSession, $g_oSec;

		$sql = 'SELECT o.name, count(*) FROM dcl_org o, statuses s, workorders w, dcl_wo_account wa WHERE ';
		$sql .= "w.jcn = wa.wo_id AND w.seq = wa.seq AND o.org_id = wa.account_id AND s.id = w.status AND s.dcl_status_type != 2 AND P.active = 'Y' ";
		if ($g_oSec->IsOrgUser() || $g_oSession->IsInWorkspace())
			$sql .= ' AND w.product IN (' . join(',', $g_oSession->GetProductFilter()) . ') ';

		$sql .= 'GROUP BY o.name ORDER BY 2 DESC';

		return $this->Query($sql);
	}

	public function GetOrganizationCountTicket()
	{
		global $g_oSession, $g_oSec;

		$sql = 'SELECT o.name, count(*) FROM dcl_org o, statuses s, tickets t WHERE ';
		$sql .= "t.account = o.org_id AND s.id = t.status AND s.dcl_status_type != 2 AND P.active = 'Y' ";
		if ($g_oSec->IsOrgUser() || $g_oSession->IsInWorkspace())
			$sql .= ' AND t.product IN (' . join(',', $g_oSession->GetProductFilter()) . ') ';

		$sql .= 'GROUP BY o.name ORDER BY 2 DESC';

		return $this->Query($sql);
	}

	public function GetStatusCount($id)
	{
		global $g_oSession, $g_oSec;

		$sql = 'SELECT s.name, count(*) FROM statuses s, workorders w, dcl_wo_account wa WHERE ';
		$sql .= "w.jcn = wa.wo_id AND w.seq = wa.seq AND s.id = w.status AND wa.account_id=$id AND s.dcl_status_type != 2 ";
		if ($g_oSec->IsOrgUser() || $g_oSession->IsInWorkspace())
			$sql .= ' AND w.product IN (' . join(',', $g_oSession->GetProductFilter()) . ') ';

		$sql .= 'GROUP BY s.name ORDER BY 2 DESC';

		return $this->Query($sql);
	}

	public function GetSeverityCount($id)
	{
		global $g_oSession, $g_oSec;

		$sql = 'SELECT s.name, count(*) FROM severities s, statuses st, workorders w, dcl_wo_account wa WHERE ';
		$sql .= "w.jcn = wa.wo_id AND w.seq = wa.seq AND s.id = w.severity AND wa.account_id=$id AND st.id = w.status AND st.dcl_status_type != 2 ";
		if ($g_oSec->IsOrgUser() || $g_oSession->IsInWorkspace())
			$sql .= ' AND w.product IN (' . join(',', $g_oSession->GetProductFilter()) . ') ';

		$sql .= 'GROUP BY s.name ORDER BY 2 DESC';

		return $this->Query($sql);
	}

	public function GetPriorityCount($id)
	{
		global $g_oSession, $g_oSec;

		$sql = 'SELECT s.name, count(*) FROM priorities s, statuses st, workorders w, dcl_wo_account wa WHERE ';
		$sql .= "w.jcn = wa.wo_id AND w.seq = wa.seq AND s.id = w.priority AND wa.account_id=$id AND st.id = w.status AND st.dcl_status_type != 2 ";
		if ($g_oSec->IsOrgUser() || $g_oSession->IsInWorkspace())
			$sql .= ' AND w.product IN (' . join(',', $g_oSession->GetProductFilter()) . ') ';

		$sql .= 'GROUP BY s.name ORDER BY 2 DESC';

		return $this->Query($sql);
	}

	public function GetDepartmentCount($id)
	{
		global $g_oSession, $g_oSec;

		$sql = 'SELECT d.name, count(*) FROM departments d, personnel u, statuses st, workorders w, dcl_wo_account wa WHERE ';
		$sql .= "w.jcn = wa.wo_id AND w.seq = wa.seq AND w.responsible = u.id AND d.id = u.department AND wa.account_id=$id AND st.id = w.status AND st.dcl_status_type != 2 ";
		if ($g_oSec->IsOrgUser() || $g_oSession->IsInWorkspace())
			$sql .= ' AND w.product IN (' . join(',', $g_oSession->GetProductFilter()) . ') ';

		$sql .= 'GROUP BY d.name ORDER BY 2 DESC';

		return $this->Query($sql);
	}

	public function GetModuleCount($id)
	{
		global $g_oSession, $g_oSec;

		$sql = 'SELECT m.module_name, count(*) FROM dcl_product_module m, statuses st, workorders w, dcl_wo_account wa WHERE ';
		$sql .= "w.jcn = wa.wo_id AND w.seq = wa.seq AND m.product_module_id = w.module_id AND wa.account_id=$id AND st.id = w.status AND st.dcl_status_type != 2 ";
		if ($g_oSec->IsOrgUser() || $g_oSession->IsInWorkspace())
			$sql .= ' AND w.product IN (' . join(',', $g_oSession->GetProductFilter()) . ') ';

		$sql .= 'GROUP BY m.module_name ORDER BY 2 DESC';

		return $this->Query($sql);
	}

	public function GetTypeCount($id)
	{
		global $g_oSession, $g_oSec;

		$sql = 'SELECT t.type_name, count(*) FROM dcl_wo_type t, statuses st, workorders w, dcl_wo_account wa WHERE ';
		$sql .= "w.jcn = wa.wo_id AND w.seq = wa.seq AND t.wo_type_id = w.wo_type_id AND wa.account_id=$id AND st.id = w.status AND st.dcl_status_type != 2 ";
		if ($g_oSec->IsOrgUser() || $g_oSession->IsInWorkspace())
			$sql .= ' AND w.product IN (' . join(',', $g_oSession->GetProductFilter()) . ') ';

		$sql .= 'GROUP BY t.type_name ORDER BY 2 DESC';

		return $this->Query($sql);
	}

	public function GetStatusCountTicket($id)
	{
		global $g_oSession, $g_oSec;

		$sql = 'SELECT s.name, count(*) FROM statuses s, tickets t WHERE ';
		$sql .= "s.id = t.status AND t.account=$id AND s.dcl_status_type != 2 ";
		if ($g_oSec->IsOrgUser() || $g_oSession->IsInWorkspace())
			$sql .= ' AND t.product IN (' . join(',', $g_oSession->GetProductFilter()) . ') ';

		$sql .= 'GROUP BY s.name ORDER BY 2 DESC';

		return $this->Query($sql);
	}

	public function GetTypeCountTicket($id)
	{
		global $g_oSession, $g_oSec;

		$sql = 'SELECT s.name, count(*) FROM severities s, statuses st, tickets t WHERE ';
		$sql .= "s.id = t.type AND t.account=$id AND st.id = t.status AND st.dcl_status_type != 2 ";
		if ($g_oSec->IsOrgUser() || $g_oSession->IsInWorkspace())
			$sql .= ' AND t.product IN (' . join(',', $g_oSession->GetProductFilter()) . ') ';

		$sql .= 'GROUP BY s.name ORDER BY 2 DESC';

		return $this->Query($sql);
	}

	public function GetPriorityCountTicket($id)
	{
		global $g_oSession, $g_oSec;

		$sql = 'SELECT s.name, count(*) FROM priorities s, statuses st, tickets t WHERE ';
		$sql .= "s.id = t.priority AND t.account=$id AND st.id = t.status AND st.dcl_status_type != 2 ";
		if ($g_oSec->IsOrgUser() || $g_oSession->IsInWorkspace())
			$sql .= ' AND t.product IN (' . join(',', $g_oSession->GetProductFilter()) . ') ';

		$sql .= 'GROUP BY s.name ORDER BY 2 DESC';

		return $this->Query($sql);
	}

	public function GetModuleCountTicket($id)
	{
		global $g_oSession, $g_oSec;

		$sql = 'SELECT m.module_name, count(*) FROM dcl_product_module m, tickets t, statuses st WHERE ';
		$sql .= "m.product_module_id = t.module_id AND t.account=$id AND st.id = t.status AND st.dcl_status_type != 2 ";
		if ($g_oSec->IsOrgUser() || $g_oSession->IsInWorkspace())
			$sql .= ' AND t.product IN (' . join(',', $g_oSession->GetProductFilter()) . ') ';

		$sql .= 'GROUP BY m.module_name ORDER BY 2 DESC';

		return $this->Query($sql);
	}
}
