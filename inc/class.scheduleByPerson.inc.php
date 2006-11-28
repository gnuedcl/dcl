<?php
/*
 * $Id: class.scheduleByPerson.inc.php,v 1.1.1.1 2006/11/27 05:30:51 mdean Exp $
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

LoadStringResource('wost');

class scheduleByPerson
{
	function SetNextWorkDay(&$thisDate)
	{
		$oneDay = 86400;
		$thisDate += $oneDay;
		while (date('w', $thisDate) == 0 || date('w', $thisDate) == 6)
			$thisDate += $oneDay;
	}

	function ScheduleTask(&$startDate, &$endDate, $etcHours, $hoursInDay, &$hoursLeftInDay)
	{
		if ($hoursLeftInDay <= 0)
		{
			$this->SetNextWorkDay($startDate);
			$hoursLeftInDay = $hoursInDay;
		}

		// start date is the base for all humanity            
		$endDate = $startDate;
		if ($etcHours <= $hoursLeftInDay)
		{
			// It would start and end in the same day
			$hoursLeftInDay -= $etcHours;
			return;
		}

		$hoursRemaining = $etcHours;
		while ($hoursRemaining > 0)
		{
			if ($hoursRemaining > $hoursLeftInDay)
			{
				$hoursRemaining -= $hoursLeftInDay;
				$this->SetNextWorkDay($endDate);
				$hoursLeftInDay = $hoursInDay;
			}
			else
			{
				$hoursLeftInDay -= $hoursRemaining;
				$hoursRemaining = 0;
			}
		}
	}

	function ScheduleOpenTasksByPerson($beginDate = '', $hoursPerDay = 8.0)
	{
		global $dcl_info;

		commonHeader();
		
		if (($personID = DCL_Sanitize::ToInt($_REQUEST['personID'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		$dbWO = CreateObject('dcl.dbWorkorders');
		if ($beginDate == '')
			$startDate = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		else
		{
			// TODO: break apart beginDate and create timestamp
			// for now: use today
			$startDate = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		}

		// TODO: allow configuration of hoursInDay
		$hoursInDay = 8.0;

		$query = "SELECT jcn,seq FROM workorders w,priorities p,severities s, statuses t WHERE w.priority=p.id and w.severity=s.id and w.status=t.id and t.dcl_status_type=1 and responsible=$personID order by p.weight,s.weight,jcn,seq";
		if ($dbWO->Query($query) != -1)
		{
			if (!$dbWO->next_record())
			{
				print('<br><br>' . STR_WOST_NOTASKS);
				return;
			}
			$hoursLeftInDay = $hoursInDay;
			do
			{
				$dbWork = CreateObject('dcl.dbWorkorders');
				if ($dbWork->Load($dbWO->f('jcn'), $dbWO->f('seq')) != -1)
				{
					$etcHours = $dbWork->f('etchours');
					settype($etcHours, 'double');
					$this->ScheduleTask($startDate, $endDate, $etcHours, $hoursInDay, $hoursLeftInDay);
					$dbWork->eststarton = date($dcl_info['DCL_DATE_FORMAT'], $startDate);
					$dbWork->estendon = date($dcl_info['DCL_DATE_FORMAT'], $endDate);
					print('<br>');
					printf(STR_WOST_SCHEDULEDTASK, $dbWork->jcn, $dbWork->seq, $dbWork->eststarton, $dbWork->estendon);
					$dbWork->Edit();
					$startDate = $endDate;
				}
			}
			while ($dbWO->next_record());
		}
	}

	function SelectPersonToSchedule()
	{
		global $dcl_info;

		commonHeader();

		$objPersonnel = CreateObject('dcl.htmlPersonnel');
		$t =& CreateSmarty();
		
		$t->assign('CMB_PERSON', $objPersonnel->GetCombo($GLOBALS['DCLID'], 'personID', 'lastfirst'));

		SmartyDisplay($t, 'htmlScheduleByPerson.tpl');
	}
}
?>
