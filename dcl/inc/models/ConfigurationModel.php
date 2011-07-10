<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2010 Free Software Foundation
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

class ConfigurationModel extends DbProvider
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'dcl_config';
		$this->cacheEnabled = true;
		
		LoadSchema($this->TableName);
		
		parent::Clear();
	}

	public function Add()
	{
		$query  = 'INSERT INTO dcl_config ';
		$query .= '(dcl_config_name, dcl_config_field, ' . $this->dcl_config_field;
		$query .= ') VALUES (';
		$query .= $this->Quote($this->dcl_config_name) . ",";
		$query .= $this->Quote($this->dcl_config_field) . ",";
		$query .= $this->FieldValueToSQL($this->dcl_config_field, $this->{$this->dcl_config_field});
		$query .= ')';

		if ($this->Insert($query) == -1)
		{
			trigger_error(sprintf(STR_DB_CFGINSERTERR, $query));
		}
	}

	public function Edit()
	{
		$query  = 'UPDATE dcl_config SET ' . $this->dcl_config_field . ' = ' . $this->FieldValueToSQL($this->dcl_config_field, $this->{$this->dcl_config_field});
		$query .= ' WHERE dcl_config_name=' . $this->Quote($this->dcl_config_name);

		$this->Execute($query);
	}

	public function UpdateTimeStamp()
	{
		$query = 'UPDATE dcl_config SET dcl_config_datetime = ' . $this->GetDateSQL();
		$query .= " WHERE dcl_config_name = 'LAST_CONFIG_UPDATE'";

		return $this->Execute($query);
	}

	public function Delete()
	{
		return parent::Delete(array('dcl_config_name' => $this->dcl_config_name));
	}

	public function Load($sName = '')
	{
		global $dcl_info, $dcl_domain, $dcl_domain_info;

		$sql = 'SELECT dcl_config_name, dcl_config_field, dcl_config_int,';
		$sql .= 'dcl_config_double, dcl_config_date, ';
		$sql .= $this->ConvertTimestamp('dcl_config_datetime', 'dcl_config_datetime');
		$sql .= ',dcl_config_varchar FROM dcl_config';

		if (!$this->Query($sql))
			return -1;

		while ($this->next_record())
		{
			$sData = $this->f($this->f('dcl_config_field'));
			if ($this->f('dcl_config_field') == 'dcl_config_date')
				$dcl_info[$this->f('dcl_config_name')] = $this->FormatDateForDisplay($sData);
			elseif ($this->f('dcl_config_field') == 'dcl_config_datetime')
				$dcl_info[$this->f('dcl_config_name')] = $this->FormatTimestampForDisplay($sData);
			else
				$dcl_info[$this->f('dcl_config_name')] = $sData;
		}

		$this->FreeResult();

		if ($dcl_domain_info[$dcl_domain]['dbType'] == 'mysql')
		{
			// mysql doesn't grok subselects, which are necessary for
			// this feature to work properly
			$dcl_info['DCL_WO_SECONDARY_ACCOUNTS_ENABLED'] = 'N';
		}

		return 1;
	}

	public function LoadForModify()
	{
		$sql = 'SELECT dcl_config_name, dcl_config_field, dcl_config_int,';
		$sql .= 'dcl_config_double, dcl_config_date, ';
		$sql .= $this->ConvertTimestamp('dcl_config_datetime', 'dcl_config_datetime');
		$sql .= ", dcl_config_varchar FROM dcl_config WHERE dcl_config_name not in ('DCL_VERSION', 'LAST_CONFIG_UPDATE')";

		if (!$this->Query($sql))
			return -1;
	}

	public function Value($sName)
	{
		global $dcl_info, $dcl_domain, $dcl_domain_info;

		$sql = 'SELECT dcl_config_name, dcl_config_field, dcl_config_int,';
		$sql .= 'dcl_config_double, dcl_config_date, ';
		$sql .= $this->ConvertTimestamp('dcl_config_datetime', 'dcl_config_datetime');
		$sql .= ', dcl_config_varchar FROM dcl_config Where dcl_config_name = ' . $this->Quote($sName);

		if (!$this->Query($sql))
			return null;

		$retVal = null;
		if ($this->next_record())
		{
			$sData = $this->f($this->f('dcl_config_field'));
			if ($this->f('dcl_config_field') == 'dcl_config_date')
				$retVal = $this->FormatDateForDisplay($sData);
			elseif ($this->f('dcl_config_field') == 'dcl_config_datetime')
				$retVal = $this->FormatTimestampForDisplay($sData);
			else
				$retVal = $sData;
		}

		$this->FreeResult();

		if ($dcl_domain_info[$dcl_domain]['dbType'] == 'mysql' && $sName = 'DCL_WO_SECONDARY_ACCOUNTS_ENABLED')
		{
			// mysql doesn't grok subselects, which are necessary for
			// this feature to work properly
			$retVal = 'N';
		}

		return $retVal;
	}
}
