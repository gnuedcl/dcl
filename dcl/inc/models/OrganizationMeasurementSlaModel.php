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

class OrganizationMeasurementSlaModel extends DbProvider
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'dcl_org_measurement_sla';
		LoadSchema($this->TableName);
		parent::Clear();
	}

	public function ListByOrganization($organizationId)
	{
		$sql = "SELECT M.measurement_type_id, T.measurement_name, min_valid_value, max_valid_value, measurement_sla, measurement_sla_warn, sla_trim_pct, sla_is_trim_based, S.name AS schedule_name, U.unit_abbr FROM dcl_org_measurement_sla M JOIN dcl_measurement_type T ON M.measurement_type_id = T.measurement_type_id JOIN dcl_measurement_unit U ON T.measurement_unit_id = U.measurement_unit_id LEFT JOIN dcl_sla_schedule S ON M.sla_schedule_id = S.sla_schedule_id WHERE M.org_id = $organizationId ORDER BY T.measurement_name";
		return $this->Query($sql);
	}
}