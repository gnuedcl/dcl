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
class WorkspaceProductModel extends DbProvider
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'dcl_workspace_product';
		LoadSchema($this->TableName);

		parent::Clear();
	}
	
	public function serialize($workspace_id, $aProducts, $bAddOnly)
	{
		if (!is_array($aProducts) || count($aProducts) < 1)
		{
			$aProducts = array();
			$aProducts[] = -1;
		}
			
		$sProducts = join(',', $aProducts);
		
		// Delete the products that are no longer referenced if we're not in add only mode
		if (!$bAddOnly)
			$this->Execute("DELETE FROM dcl_workspace_product WHERE workspace_id = $workspace_id AND product_id NOT IN ($sProducts)");
			
		// Add the new tags
		if ($sProducts != '-1')
		{
			$personnel_id = $GLOBALS['DCLID'];
			$this->Execute("INSERT INTO dcl_workspace_product SELECT $workspace_id, id, " . $this->GetDateSQL() . ", $personnel_id FROM products WHERE id IN ($sProducts) AND id NOT IN (SELECT product_id FROM dcl_workspace_product WHERE workspace_id = $workspace_id)");
		}
	}
}
