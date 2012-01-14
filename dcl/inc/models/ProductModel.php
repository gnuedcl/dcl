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

class ProductModel extends DbProvider
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'products';
		$this->cacheEnabled = true;
		
		LoadSchema($this->TableName);

		$this->foreignKeys = array(
				'workorders' => 'product',
				'tickets' => 'product',
				'dcl_product_module' => 'product_id');
		
		parent::Clear();
	}

	public function Delete($id)
	{
		return parent::Delete(array('id' => $id));
	}
	
	public function GetProductCount()
	{
		global $g_oSession, $g_oSec;
		
		$sql = 'SELECT p.name, count(*) FROM products p, statuses s, workorders w WHERE ';
		$sql .= "p.id = w.product AND s.id = w.status AND s.dcl_status_type != 2 AND P.active = 'Y' ";
		if ($g_oSec->IsOrgUser() || $g_oSession->IsInWorkspace())
			$sql .= ' AND p.id IN (' . join(',', $g_oSession->GetProductFilter()) . ') ';
		
		$sql .= 'GROUP BY p.name ORDER BY 2 DESC';
		
		return $this->Query($sql);
	}
	
	public function GetProductCountTicket()
	{
		global $g_oSession, $g_oSec;
		
		$sql = 'SELECT p.name, count(*) FROM products p, statuses s, tickets t WHERE ';
		$sql .= "p.id = t.product AND s.id = t.status AND s.dcl_status_type != 2 AND P.active = 'Y' ";
		if ($g_oSec->IsOrgUser() || $g_oSession->IsInWorkspace())
			$sql .= ' AND p.id IN (' . join(',', $g_oSession->GetProductFilter()) . ') ';
		
		$sql .= 'GROUP BY p.name ORDER BY 2 DESC';
		
		return $this->Query($sql);
	}
	
	public function GetStatusCount($id)
	{
		$sql = 'SELECT s.name, count(*) FROM statuses s, workorders w WHERE ';
		$sql .= "s.id = w.status AND w.product=$id AND s.dcl_status_type != 2 ";
		$sql .= 'GROUP BY s.name ORDER BY 2 DESC';
		
		return $this->Query($sql);
	}

	public function GetSeverityCount($id)
	{
		$sql = 'SELECT s.name, count(*) FROM severities s, statuses st, workorders w WHERE ';
		$sql .= "s.id = w.severity AND w.product=$id AND st.id = w.status AND st.dcl_status_type != 2 ";
		$sql .= 'GROUP BY s.name ORDER BY 2 DESC';
		
		return $this->Query($sql);
	}

	public function GetPriorityCount($id)
	{
		$sql = 'SELECT s.name, count(*) FROM priorities s, statuses st, workorders w WHERE ';
		$sql .= "s.id = w.priority AND w.product=$id AND st.id = w.status AND st.dcl_status_type != 2 ";
		$sql .= 'GROUP BY s.name ORDER BY 2 DESC';
		
		return $this->Query($sql);
	}

	public function GetDepartmentCount($id)
	{
		$sql = 'SELECT d.name, count(*) FROM departments d, personnel u, statuses st, workorders w WHERE ';
		$sql .= "w.responsible = u.id AND d.id = u.department AND w.product=$id AND st.id = w.status AND st.dcl_status_type != 2 ";
		$sql .= 'GROUP BY d.name ORDER BY 2 DESC';
		
		return $this->Query($sql);
	}

	public function GetModuleCount($id)
	{
		$sql = 'SELECT m.module_name, count(*) FROM dcl_product_module m, statuses st, workorders w WHERE ';
		$sql .= "m.product_module_id = w.module_id AND w.product=$id AND st.id = w.status AND st.dcl_status_type != 2 ";
		$sql .= 'GROUP BY m.module_name ORDER BY 2 DESC';
		
		return $this->Query($sql);
	}

	public function GetTypeCount($id)
	{
		$sql = 'SELECT t.type_name, count(*) FROM dcl_wo_type t, statuses st, workorders w WHERE ';
		$sql .= "t.wo_type_id = w.wo_type_id AND w.product=$id AND st.id = w.status AND st.dcl_status_type != 2 ";
		$sql .= 'GROUP BY t.type_name ORDER BY 2 DESC';
		
		return $this->Query($sql);
	}
	
	public function GetStatusCountTicket($id)
	{
		$sql = 'SELECT s.name, count(*) FROM statuses s, tickets t WHERE ';
		$sql .= "s.id = t.status AND t.product=$id AND s.dcl_status_type != 2 ";
		$sql .= 'GROUP BY s.name ORDER BY 2 DESC';
		
		return $this->Query($sql);
	}

	public function GetTypeCountTicket($id)
	{
		$sql = 'SELECT s.name, count(*) FROM severities s, statuses st, tickets t WHERE ';
		$sql .= "s.id = t.type AND t.product=$id AND st.id = t.status AND st.dcl_status_type != 2 ";
		$sql .= 'GROUP BY s.name ORDER BY 2 DESC';
		
		return $this->Query($sql);
	}

	public function GetPriorityCountTicket($id)
	{
		$sql = 'SELECT s.name, count(*) FROM priorities s, statuses st, tickets t WHERE ';
		$sql .= "s.id = t.priority AND t.product=$id AND st.id = t.status AND st.dcl_status_type != 2 ";
		$sql .= 'GROUP BY s.name ORDER BY 2 DESC';
		
		return $this->Query($sql);
	}

	public function GetModuleCountTicket($id)
	{
		$sql = 'SELECT m.module_name, count(*) FROM dcl_product_module m, tickets t, statuses st WHERE ';
		$sql .= "m.product_module_id = t.module_id AND t.product=$id AND st.id = t.status AND st.dcl_status_type != 2 ";
		$sql .= 'GROUP BY m.module_name ORDER BY 2 DESC';
		
		return $this->Query($sql);
	}
	
	public function GetWorkOrderAttributeSet($productId)
	{
		$this->Query('SELECT wosetid FROM products WHERE id=' . (int)$productId);
		if ($this->next_record())
			return $this->f(0);
		
		throw new InvalidArgumentException();
	}
}
