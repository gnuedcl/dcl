<?php
/*
 * $Id$
 *
 * This file is part of Double Choco Latte.
 * Copyright 1999,2000,2001,2002,2003,2004,2005,2006,2007,2008,2009,2010 Free Software Foundation
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

LoadStringResource('prj');
LoadStringResource('wo');

class htmlHotlistProject
{
	var $hotlist;
	var $entityHotlist;
	var $oSmarty;

	function htmlHotlistProject()
	{
		$this->hotlist = new HotlistModel();
		$this->entityHotlist = new EntityHotlistModel();
		$this->oSmarty = new SmartyHelper();
	}

	function View()
	{
		global $g_oSec;

		commonHeader();

		if (($id = @Filter::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}

		if ($id > 0)
		{
			if (!$g_oSec->HasPerm(DCL_ENTITY_HOTLIST, DCL_PERM_VIEW, $id))
				throw new PermissionDeniedException();

			$status = 0;
			$responsible = 0;

			if (($status = @Filter::ToSignedInt($_REQUEST['wostatus'])) === null)
				$status = 0;

			if (($responsible = @Filter::ToInt($_REQUEST['woresponsible'])) === null)
				$responsible = 0;

			$this->show($id, $status, $responsible);
		}
	}

	function Show($id, $status, $responsible)
	{
		global $dcl_info, $dcl_domain_info, $dcl_domain, $g_oSec;

		if (!IsSet($_REQUEST['wogroupby']))
			$_REQUEST['wogroupby'] = 'none';

		$bIsGrouping = ($_REQUEST['wogroupby'] != 'none');

		if (!$g_oSec->HasPerm(DCL_ENTITY_HOTLIST, DCL_PERM_VIEW, $id))
			throw new PermissionDeniedException();

		if ($this->hotlist->Load($id) == -1)
		{
			trigger_error('Could not find a hotlist with an id of ' . $id, E_USER_ERROR);
			return;
		}

		$oMeta = new DisplayHelper();

		$this->oSmarty->assign('VAL_HOTLISTID', $id);
		$this->oSmarty->assign('VAL_NAME', $this->hotlist->hotlist_tag);
		$this->oSmarty->assign('VAL_DESCRIPTION', $this->hotlist->hotlist_desc);
		$this->oSmarty->assign('VAL_FILTERSTATUS', $status);
		$this->oSmarty->assign('VAL_FILTERRESPONSIBLE', $responsible);
		$this->oSmarty->assign('VAL_FILTERGROUPBY', $_REQUEST['wogroupby']);
		$this->oSmarty->assign('OPT_GROUPBY', array('none' => STR_CMMN_SELECTONE, '3' => STR_WO_RESPONSIBLE, '7' => STR_WO_STATUS, '4' => STR_WO_PRODUCT, '5' => STR_CMMN_MODULE, '2' => STR_WO_TYPE));

		$this->oSmarty->assign('PERM_AUDIT', $g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_AUDIT));
		$this->oSmarty->assign('PERM_ATTACHFILE', $g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ATTACHFILE));
		$this->oSmarty->assign('PERM_REMOVEFILE', $g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_REMOVEFILE));

		$this->SetStatistics();
		$this->SetTasks($status, $responsible, $bIsGrouping);

		$this->oSmarty->Render('htmlHotlistProjectDetail.tpl');
	}
	
	function SetTasks($wostatus, $woresponsible, $bIsGrouping)
	{
		global $dcl_domain, $dcl_domain_info, $dcl_info;

		$cols = array('a.jcn', 'a.seq', 'h.type_name', 'b.short', 'c.name', 'g.module_name', 'd.name', 'e.name', 'a.deadlineon', 'a.totalhours', 'a.etchours', 'a.esthours', '(a.totalhours + a.etchours) - a.esthours', 'a.summary', 'f.sort');
		$sql = 'Select a.jcn, a.seq, h.type_name, b.short, c.name, g.module_name, d.name, e.name, ' . $this->entityHotlist->ConvertDate('a.deadlineon', 'deadlineon') . ', a.totalhours, a.etchours, a.esthours, (a.totalhours + a.etchours) - a.esthours, a.summary, f.sort';

		if ($dcl_info['DCL_WO_SECONDARY_ACCOUNTS_ENABLED'] == 'Y')
		{
			$sql .= ', (select count(*) from dcl_wo_account where wo_id = a.jcn And seq = a.seq) As num_accounts';
		}

		$sql .= ' From workorders a ';
		$sql .= $this->entityHotlist->JoinKeyword . ' personnel b ON a.responsible = b.id ';
		$sql .= $this->entityHotlist->JoinKeyword . ' products c ON a.product = c.id';
		$sql .= ' LEFT JOIN dcl_wo_account i ON a.jcn = i.wo_id AND a.seq = i.seq';
		$sql .= ' LEFT JOIN dcl_org d ON i.account_id = d.org_id ';
		$sql .= $this->entityHotlist->JoinKeyword . ' statuses e ON a.status = e.id ';
		$sql .= $this->entityHotlist->JoinKeyword . ' dcl_entity_hotlist f ON f.entity_id =' . DCL_ENTITY_WORKORDER . ' and a.jcn = f.entity_key_id and a.seq = f.entity_key_id2 ';
		$sql .= ' LEFT JOIN dcl_product_module g ON a.module_id = g.product_module_id ';
		$sql .= $this->entityHotlist->JoinKeyword . ' dcl_wo_type h ON a.wo_type_id = h.wo_type_id';
		$sql .= ' Where f.hotlist_id=' . $this->hotlist->hotlist_id;

		if ($wostatus > 0)
			$sql .= ' And a.status=' . $wostatus;
		else if ($wostatus == -1)
			$sql .= ' And e.dcl_status_type != 2';
		else if ($wostatus == -2)
			$sql .= ' And e.dcl_status_type = 2';

		if ($woresponsible > 0)
			$sql .= ' And a.responsible=' . $woresponsible;

		if ($dcl_info['DCL_WO_SECONDARY_ACCOUNTS_ENABLED'] == 'Y')
		{
			$sql .= ' And (i.account_id is null Or i.account_id = ';
			$sql .= '(Select min(account_id) From dcl_wo_account where wo_id = a.jcn And seq = a.seq))';
		}

		if ($bIsGrouping)
			$sql .= ' Order By ' . $cols[$_REQUEST['wogroupby']] . ', f.sort, a.jcn, a.seq';
		else
			$sql .= ' Order By f.sort, a.jcn, a.seq';

		if ($this->entityHotlist->Query($sql) != -1)
		{
			$allRecs = $this->entityHotlist->FetchAllRows();
			$this->entityHotlist->FreeResult();

			if (count($allRecs) > 0)
			{
				$aTasks = array();

				$objWOAcct = new WorkOrderOrganizationModel();
				$oDate = new DateHelper;

				for ($i = 0; $i < count($allRecs); $i++)
				{
					$oDate->SetFromDB($allRecs[$i][8]);
					$ouHours = -($allRecs[$i][11] - $allRecs[$i][9]);
					$diffHours = $ouHours;
					if ($diffHours < 0)
						$diffHours = -$diffHours;

					$ouPct = 0.0;
					$sign = '';
					if ($allRecs[$i][11] > 0)
					{
						$ouPct = $diffHours / $allRecs[$i][11] * 100;
						if ($allRecs[$i][11] > $allRecs[$i][9] && $allRecs[$i][11] > 0)
							$sign = '-';
						else if ($allRecs[$i][9] > $allRecs[$i][11] && $allRecs[$i][9] > 0)
							$sign = '+';
					}

					$fPctComplete = 0.0;
					if ($allRecs[$i][10] + $allRecs[$i][9] > 0)
						$fPctComplete = (($allRecs[$i][9] / ($allRecs[$i][10] + $allRecs[$i][9])) * 100);
					elseif ($allRecs[$i][10] == 0.0)
						$fPctComplete = 100.0;

					$aTasks[] = array(
							'woid' => $allRecs[$i][0],
							'seq' => $allRecs[$i][1],
							'type' => $allRecs[$i][2],
							'responsible' => $allRecs[$i][3],
							'product' => $allRecs[$i][4],
							'module' => $allRecs[$i][5],
							'org' => $allRecs[$i][6],
							'status' => $allRecs[$i][7],
							'hours' => $allRecs[$i][9],
							'etc' => $allRecs[$i][10],
							'projected' => $allRecs[$i][11],
							'summary' => $allRecs[$i][13],
							'deadline' => $oDate->ToDisplay(),
							'plusminus' => sprintf('%s%0.2f (%s%0.2f%%)', $sign, abs($ouHours), $sign, abs($ouPct)),
							'pctcomplete' => sprintf("%0.2f%%", $fPctComplete),
							'secorgs' => ($dcl_info['DCL_WO_SECONDARY_ACCOUNTS_ENABLED'] == 'Y' && $allRecs[$i][15] > 1),
							'sort' => ($allRecs[$i][14] != 999999 ? $allRecs[$i][14] : '?')
						);
				}

				if ($bIsGrouping)
				{
					$sGroupBy = null;
					switch ($_REQUEST['wogroupby'])
					{
						case '3': $sGroupBy = 'responsible'; break;
						case '7': $sGroupBy = 'status';      break;
						case '4': $sGroupBy = 'product';     break;
						case '5': $sGroupBy = 'module';      break;
						case '2': $sGroupBy = 'type';        break;
					}

					$this->oSmarty->assign('VAL_GROUPBY', $sGroupBy);
				}

				$this->oSmarty->assign_by_ref('VAL_TASKS', $aTasks);
			}
		}
	}
	
	function SetStatistics()
	{
		global $dcl_info;

		if ($this->hotlist == null)
		{
			return;
		}

		$entityHotlist = new EntityHotlistModel();

		$arrayStats = $entityHotlist->GetWorkOrderStatistics($this->hotlist->hotlist_id);
		$this->oSmarty->assign('VAL_TOTALTASKS', $arrayStats['totaltasks']);
		$this->oSmarty->assign('VAL_TASKSCLOSED', $arrayStats['tasksclosed']);
		$this->oSmarty->assign('VAL_ESTHOURS', $arrayStats['esthours']);
		if ($arrayStats['etchours'] > 0)
		{
			$oneDay = 24 * 60 * 60; // Just in case time scale changes in the future
			$i = 0;
			$workDays = $arrayStats['etchours'] / 8.0;
			if ($arrayStats['resources'] > 1)
				$workDays /= $arrayStats['resources'];

			$endDay = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
			while ($i < $workDays)
			{
				$endDay += $oneDay;
				if (date('w', $endDay) != 0 && date('w', $endDay) != 6)
					$i++;
			}

			$this->oSmarty->assign('VAL_ETCDATE', date($dcl_info['DCL_DATE_FORMAT'], $endDay));
		}
		else
			$this->oSmarty->assign('VAL_ETCDATE', '');

		$this->oSmarty->assign('VAL_RESOURCES', $arrayStats['resources']);

		$ouHours = -($arrayStats['esthours'] - $arrayStats['totalhours']);
		$diffHours = $ouHours;
		if ($diffHours < 0)
			$diffHours = -$diffHours;

		$ouPct = 0.0;
		$sign = '';
		if ($arrayStats['esthours'] > 0)
		{
			$ouPct = $diffHours / $arrayStats['esthours'] * 100;
			if ($arrayStats['esthours'] > $arrayStats['totalhours'] && $arrayStats['esthours'] > 0)
				$sign = '-';
			else if ($arrayStats['totalhours'] > $arrayStats['esthours'] && $arrayStats['totalhours'] > 0)
				$sign = '+';
		}

		$this->oSmarty->assign('VAL_HOURSPM', sprintf('%s%0.2f (%s%0.2f%%)', $sign, abs($ouHours), $sign, abs($ouPct)));
		$this->oSmarty->assign('VAL_TOTALHOURS', $arrayStats['totalhours']);
		$this->oSmarty->assign('VAL_ETCHOURS', $arrayStats['etchours']);

		if ($arrayStats['totalhours'] + $arrayStats['etchours'] > 0.0)
			$this->oSmarty->assign('VAL_PCTCOMP', sprintf('%0.2f%%', ($arrayStats['totalhours'] / ($arrayStats['totalhours'] + $arrayStats['etchours'])) * 100));
		else
			$this->oSmarty->assign('VAL_PCTCOMP', '0.00%');
	}
}