<?php
/*
 * $Id: class.dbWorkOrderAccount.inc.php,v 1.1.1.1 2006/11/27 05:30:45 mdean Exp $
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
class dbWorkOrderAccount extends dclDB
{
	var $account_name; // not part of table, but useful for Load

	function dbWorkOrderAccount()
	{
		parent::dclDB();
		$this->TableName = 'dcl_wo_account';
		LoadSchema($this->TableName);
		$this->AuditEnabled = true;
		parent::Clear();
	}

	function Add()
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
				$this->Execute('INSERT INTO dcl_wo_account_audit VALUES (' . $sValues . ', ' . $this->GetDateSQL() . ', ' . $GLOBALS['DCLID'] . ', ' . DCL_EVENT_ADD . ')');
			}
		}
	}

	function Edit()
	{
		// Why???
	}

	function Delete($wo_id, $seq, $account_id)
	{
		parent::Delete(array('wo_id' => $wo_id, 'seq' => $seq, 'account_id' => $account_id));
	}

	function AuditWorkOrderList($jcn, $seq)
	{
		$aRetVal = array();

		if ($this->Query("SELECT wo_id, seq, dcl_org.name, audit_on, personnel.short, audit_type, account_id FROM dcl_wo_account_audit, dcl_org, personnel WHERE account_id = dcl_org.org_id AND audit_by = personnel.id AND wo_id=$jcn AND seq=$seq ORDER BY audit_on") != -1)
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

	function DeleteByWorkOrder($wo_id, $seq, $account_id_keep = '')
	{
		$sWhere = "WHERE wo_id = $wo_id AND seq = $seq";
		if ($account_id_keep != '')
			$sWhere .= " AND account_id NOT IN ($account_id_keep)";

		$this->Execute("INSERT INTO dcl_wo_account_audit SELECT wo_id, seq, account_id, " . $this->GetDateSQL() . ", " . $GLOBALS['DCLID'] . ", " . DCL_EVENT_DELETE . " FROM dcl_wo_account $sWhere");

		return $this->Execute("DELETE FROM dcl_wo_account $sWhere");
	}

	function GetRow()
	{
		if (!$this->res || count($this->Record) < 1)
			return -1;

		$this->wo_id = $this->f('wo_id');
		$this->seq = $this->f('seq');
		$this->account_id = $this->f('account_id');
		$this->account_name = $this->f('name');
	}

	function Load($wo_id, $seq)
	{
		$this->Clear();

		// Get all rows since we read with GetRow
		$sql = sprintf('Select wo_id, seq, account_id, name From dcl_wo_account, dcl_org Where account_id = org_id And wo_id = %d And seq = %d Order By name', $wo_id, $seq);
		if (!$this->Query($sql))
			return -1;

		if (!$this->next_record())
			return -1;

		return $this->GetRow();
	}
}
?>
