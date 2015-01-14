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
class EnvironmentOrgModel extends DbProvider
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'dcl_environment_org';
		LoadSchema($this->TableName);

		parent::Clear();
	}

	public function ListByOrg($orgId)
	{
		$orgId = Filter::RequireInt($orgId);

		$sql = 'SELECT e.environment_name, eo.begin_dt, eo.end_dt, eo.environment_org_id FROM dcl_environment_org eo JOIN dcl_environment e ON e.environment_id = eo.environment_id WHERE eo.org_id = ' . $orgId;
		$sql .= ' ORDER BY eo.begin_dt DESC, e.environment_name';

		return $this->Query($sql);
	}

	public function GetOrganizationIdsByEnvironments($timestamp, array $environmentId)
	{
		$environmentIds = Filter::ToIntArray($environmentId);
		if ($environmentIds == null || count($environmentIds) == 0)
			return null;

		$sql = 'SELECT org_id FROM dcl_environment_org WHERE environment_id IN (' . join(',', $environmentIds) . ') AND ';
		$sql .= ' begin_dt >= ' . $this->FieldValueToSQL('begin_dt', $timestamp);
		$sql .= ' AND (end_dt IS NULL OR end_dt <= ' . $this->FieldValueToSQL('end_dt', $timestamp) . ')';

		if ($this->Query($sql) == -1)
			return null;

		$retVal = array();
		while ($this->next_record())
			$retVal[] = $this->f(0);

		return $retVal;
	}
} 