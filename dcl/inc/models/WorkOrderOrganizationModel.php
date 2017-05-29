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
class WorkOrderOrganizationModel extends DbProvider
{
	var $account_name; // not part of table, but useful for Load

	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'dcl_wo_account';
		LoadSchema($this->TableName);
		$this->AuditEnabled = true;
		parent::Clear();
	}

	public function Add()
	{
		if (!$this->Exists(array('wo_id' => $this->wo_id, 'seq' => $this->seq, 'account_id' => $this->account_id)))
		{
			$sValues = $this->wo_id . ', ';
			$sValues .= $this->seq . ', ';
			$sValues .= $this->account_id;

			$query  = 'INSERT INTO dcl_wo_account (wo_id, seq, account_id) Values (' . $sValues . ')';
			if ($this->Insert($query) == -1)
			{
				print(sprintf('Error updating accounts for work order.', $query));
			}
			else
			{
				$this->Execute('INSERT INTO dcl_wo_account_audit VALUES (' . $sValues . ', ' . $this->GetDateSQL() . ', ' . DCLID . ', ' . DCL_EVENT_ADD . ')');
			}
		}
	}

	public function Edit($aIgnoreFields = '')
	{
		// Do nothing
	}

	public function AuditWorkOrderList($jcn, $seq)
	{
		$aRetVal = array();

		if ($this->Query('SELECT wo_id, seq, dcl_org.name, ' . $this->ConvertTimestamp('audit_on', 'audit_on') . ", personnel.short, audit_type, account_id FROM dcl_wo_account_audit, dcl_org, personnel WHERE account_id = dcl_org.org_id AND audit_by = personnel.id AND wo_id=$jcn AND seq=$seq ORDER BY audit_on") != -1)
		{
			while ($this->next_record())
			{
				$aRetVal[] = array('wo_id' => $this->f(0), 'seq' => $this->f(1), 'name' => $this->f(2), 'account_id' => $this->f(6),
									'audit_on' => $this->FieldValueFromSQL('audit_on', $this->f(3)), 'audit_by' => $this->f(4),
									'audit_type' => ($this->f(5) == DCL_EVENT_ADD ? 'Add' : 'Delete'));
			}
		}

		return $aRetVal;
	}

	public function DeleteByWorkOrder($wo_id, $seq, $account_id_keep = '')
	{
		$sWhere = "WHERE wo_id = $wo_id AND seq = $seq";
		if ($account_id_keep != '')
			$sWhere .= " AND account_id NOT IN ($account_id_keep)";

		$this->Execute("INSERT INTO dcl_wo_account_audit SELECT wo_id, seq, account_id, " . $this->GetDateSQL() . ", " . DCLID . ", " . DCL_EVENT_DELETE . " FROM dcl_wo_account $sWhere");

		return $this->Execute("DELETE FROM dcl_wo_account $sWhere");
	}

	public function GetRow()
	{
		if (!$this->res || count($this->Record) < 1)
			return -1;

		$this->wo_id = $this->f('wo_id');
		$this->seq = $this->f('seq');
		$this->account_id = $this->f('account_id');
		$this->account_name = $this->f('name');
	}

	public function LoadByWorkOrder($wo_id, $seq)
	{
		$this->Clear();

		$sql = sprintf('Select woa.wo_id, woa.seq, woa.account_id, o.name From dcl_wo_account woa, dcl_org o Where woa.account_id = o.org_id And woa.wo_id = %d And woa.seq = %d ORDER BY o.name', $wo_id, $seq);
		return $this->Query($sql);
	}

	public function LoadWithPermissionFilter($wo_id, $seq)
	{
		$this->Clear();

		$productPublicSql = $this->GetProductPublicClause();
		$workOrderPublicSql = $this->GetWorkOrderPublicClause();
		$orgSql = $this->GetWorkOrderOrgWhereClause();

		$fromSql = 'dcl_wo_account woa, dcl_org o';
		if ($productPublicSql != '')
		{
			$fromSql .= ', workorders w, products p';
			$joinSql = 'woa.account_id = o.org_id AND woa.wo_id = w.jcn AND woa.seq = w.seq AND w.product = p.id';
		}
		else
		{
			$joinSql = 'woa.account_id = o.org_id';
		}

		$sql = sprintf('Select woa.wo_id, woa.seq, woa.account_id, o.name From %s Where %s And woa.wo_id = %d And woa.seq = %d', $fromSql, $joinSql, $wo_id, $seq);
		$sql .= $productPublicSql;
		$sql .= $workOrderPublicSql;
		$sql .= $orgSql;
		$sql .= ' ORDER BY o.name';

		return $this->Query($sql);
	}

	private function GetProductPublicClause()
	{
		global $g_oSec;

		if (!$g_oSec->IsPublicUser())
			return '';

		return " w.product = p.id AND p.is_public = 'Y'";
	}

	private function GetWorkOrderPublicClause()
	{
		global $g_oSec;

		if (!$g_oSec->IsPublicUser())
			return '';

		return " AND w.is_public = 'Y'";
	}

	private function GetWorkOrderOrgWhereClause()
	{
		global $g_oSec, $g_oSession;

		if (!$g_oSec->IsOrgUser())
			return '';

		$memberOfOrgs = $g_oSession->Value('member_of_orgs');
		if ($memberOfOrgs != '')
			$values = explode(',', $memberOfOrgs);
		else
			$values = array('-1');

		$organizationIds = join(',', $values);

		return " AND woa.account_id in (" . $organizationIds . ")";
	}
}
