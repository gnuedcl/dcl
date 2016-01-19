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
class OutageModel extends DbProvider
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'dcl_outage';
		LoadSchema($this->TableName);

		parent::Clear();
	}

	public function ListUnplannedOutages($startDate, $endDate, array $organizations)
	{
		$queryEndDate = new DateTime($endDate);
		$queryEndDate->modify('+1 days');

		$sql = "select O.outage_id, O.outage_title, O.sev_level, O.outage_start, O.outage_end, OT.outage_type_name, OT.is_down, OS.status_name, OS.is_resolved, ";
		$sql .= "(select count(*) from dcl_outage_org where outage_id = O.outage_id) as AffectedAccounts, O.outage_description from dcl_outage O ";
		$sql .= "join dcl_outage_type OT on O.outage_type_id = OT.outage_type_id join dcl_outage_status OS on O.outage_status_id = OS.outage_status_id ";
		$sql .= "where OT.is_planned = 'N' and (O.outage_start < '" . DclDate::ToSql($endDate);
		$sql .= "' and (O.outage_end is null or O.outage_end >= '" . DclDate::ToSql($startDate) . "')) ";

		if (count($organizations) > 0)
		{
			$sql .= "AND O.outage_id IN (SELECT outage_id FROM dcl_outage_org OO WHERE OO.org_id IN (" . join(',', $organizations) . ")) ";
		}

		$sql .= 'ORDER BY O.outage_start, O.outage_end, O.outage_id';

		return $this->Query($sql);
	}
}