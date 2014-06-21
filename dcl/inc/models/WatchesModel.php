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
LoadStringResource('wtch');

class WatchesModel extends DbProvider
{
	var $arrTypeid;
	var $arrActions;

	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'watches';
		LoadSchema($this->TableName);

		$this->arrTypeid = array(
				1 => STR_WTCH_PRODUCTWO, 
				2 => STR_WTCH_PROJECT, 
				3 => STR_WTCH_WORKORDER, 
				4 => STR_WTCH_PRODUCTTICKET, 
				5 => STR_WTCH_TICKET,
				6 => STR_WTCH_ACCTWO,
				7 => STR_WTCH_ACCTTCK
			);

		$this->arrActions = array(
				1 => STR_WTCH_OPEN, 
				2 => STR_WTCH_CLOSED, 
				3 => STR_WTCH_STATUS, 
				4 => STR_WTCH_ANYTHING
			);
		
		parent::Clear();
	}

	public function DeleteByObjectID($type, $key1, $key2 = 0)
	{
		$query = "DELETE FROM watches WHERE typeid=$type AND whatid1=$key1 AND whatid2=$key2";
		return $this->Execute($query);
	}

	public function SetActive($id, $active, $sField = 'active')
	{
		return 0;
	}
}
