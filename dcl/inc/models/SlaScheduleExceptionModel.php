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

class SlaScheduleExceptionModel extends DbProvider
{
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'dcl_sla_schedule_exception';
		LoadSchema($this->TableName);
		parent::Clear();
	}

	public function LoadByScheduleAndDate($slaScheduleId, $beginDt, $endDt)
	{
		global $dcl_info;

		$endDateTime = new DateTime($endDt);
		$endDateTime->modify('+1 day');
		$endDateTime->setTime(0, 0, 0);

		$sql = sprintf('SELECT * FROM dcl_sla_schedule_exception WHERE sla_schedule_id = %1$d AND ((start_dt >= %2$s AND start_dt < %3$s) OR (end_dt >= %2$s AND end_dt < %3$s)) ORDER BY start_dt',
			$slaScheduleId,
			$this->DisplayToSQL($beginDt),
			$this->DisplayToSQL($endDateTime->format($dcl_info['DCL_DATE_FORMAT'])));

		return $this->Query($sql);
	}
}