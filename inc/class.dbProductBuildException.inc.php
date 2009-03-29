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
class dbProductBuildException extends dclDB
{
	function dbProductBuildException()
	{
		parent::dclDB();
		$this->TableName = 'dcl_product_build_except';
		LoadSchema($this->TableName);
		
		parent::Clear();
	}
	
	function DeleteBySession($session_id, $product_build_id)
	{
		if (!is_string($session_id) || !is_int($product_build_id))
		{
			trigger_error('Invalid parameters supplied to dbProductBuildException::DeleteBySession', E_USER_ERROR);
			return;
		}
			
		$sSQL = sprintf("DELETE FROM %s WHERE session_id = %s AND product_build_id = %d",
				$this->TableName,
				$this->Quote($session_id),
				$product_build_id
			);
		
		$this->Execute($sSQL);
	}
}
?>