<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2014 Free Software Foundation
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
class EnvironmentOutageModel extends DbProvider
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'dcl_outage_environment';
		LoadSchema($this->TableName);

		parent::Clear();
	}

	public function LoadIdByOutageId($outageId)
	{
		$sql = 'SELECT environment_id FROM dcl_outage_environment WHERE outage_id = ' . (int)$outageId;
		$this->Query($sql);

		$retVal = array();
		while ($this->next_record())
			$retVal[] = $this->f(0);

		return $retVal;
	}

	public function DeleteEnvironmentsForOutage($outageId, array $environmentIds)
	{
		if (count($environmentIds) == 0)
			return;

		$this->Query('DELETE FROM dcl_outage_environment WHERE outage_id = ' . (int)$outageId . ' AND environment_id IN (' . join(',', $environmentIds) . ')');
	}
} 