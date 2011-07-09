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

class htmlSelector
{
	var $vDefault;
	var $t;
	var $oDB;

	function htmlSelector()
	{
		$this->vDefault = Filter::ToIntArray($_REQUEST['initSelected']);
		$this->oDB = null;
	}

	function GetHTML()
	{
		global $dcl_info;

		$this->t = CreateTemplate(array('hForm' => 'htmlSelector.tpl'));
		$this->t->set_block('hForm', 'avail', 'hAvail');
		$this->t->set_block('hForm', 'sel', 'hSel');

		$this->t->set_var('hAvail', '');
		$this->t->set_var('hSel', '');

		$this->t->set_var('TXT_TITLE', 'Select Organizations');
		$this->t->set_var('TXT_AVAILABLE', 'Available');
		$this->t->set_var('TXT_SELECTED', 'Selected');
		$this->t->set_var('TXT_SAVE', STR_CMMN_SAVE);
		$this->t->set_var('TXT_CANCEL', STR_CMMN_CANCEL);

		switch ($_REQUEST['what'])
		{
			case 'accounts':
			case 'dcl_org':
				$this->SetOptionsFromDb('dcl_org', 'org_id', 'name', "active='Y'", 'name');
				break;
			default:
				throw new PermissionDeniedException();
		}

		return $this->t->parse('out', 'hForm');
	}

	function Render()
	{
		echo $this->GetHTML();
	}

	function SetOptionsFromDb($table, $keyField, $valField, $filter = '', $order = '')
	{
		if ($this->oDB == NULL)
		{
			$this->oDB = new dclDB;
		}

		$sql = "select $keyField, $valField From $table";
		if ($filter != '')
			$sql .= ' Where ' . $filter;

		if ($order == '')
			$sql .= " Order By $valField";
		else
			$sql .= ' Order By ' . $order;

		$oDB = new WorkOrderModel();
		$oDB->Query($sql);
		while ($oDB->next_record())
		{
			$this->t->set_var('VAL_VALUE', $oDB->f(0));
			$this->t->set_var('VAL_TEXT', $oDB->f(1));

			$bSelected = ((is_array($this->vDefault) && in_array($oDB->f(0), $this->vDefault)) || (!is_array($this->vDefault) && $this->vDefault == $oDB->f(0)));
			if ($bSelected)
				$this->t->parse('hSel', 'sel', true);
			else
				$this->t->parse('hAvail', 'avail', true);
		}
	}

	function select()
	{
		$this->Render();
	}
}
