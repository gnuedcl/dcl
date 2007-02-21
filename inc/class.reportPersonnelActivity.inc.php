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

LoadStringResource('wost');
LoadStringResource('vw');
LoadStringResource('wo');
class reportPersonnelActivity
{
	function getparameters($needHdr = true)
	{
		global $dcl_info;

		if ($needHdr == true)
			commonHeader();

		$objPersonnel = CreateObject('dcl.htmlPersonnel');
		$oDept = CreateObject('dcl.htmlDepartments');

		$oDBPersonnel = CreateObject('dcl.dbPersonnel');
		if ($oDBPersonnel->Load($GLOBALS['DCLID']) == -1)
			return;

		$t =& CreateSmarty();
		$oSelect = CreateObject('dcl.htmlSelect');

		$t->assign('CMB_RESPONSIBLE', $objPersonnel->GetCombo($GLOBALS['DCLID'], 'responsible', 'lastfirst', 0, false));
		$t->assign('CMB_DEPARTMENTS', $oDept->GetCombo($oDBPersonnel->department, 'department', 'name', 0, false, true));

		// By department or responsible
		$oSelect->sZeroOption = '';
		$oSelect->sName = 'bytype';
		$oSelect->sOnChange = 'onChangeByType();';
		$oSelect->aOptions = array(array('1', 'By Responsible'), array('2', 'By Department'));
		$t->assign('CMB_BYTYPE', $oSelect->GetHTML());

		// Optional group by
		$oSelect->sZeroOption = 'None';
		$oSelect->sName = 'groupby';
		$oSelect->sOnChange = '';
		$oSelect->aOptions = array(array('1', 'Project'), array('2', 'Action'), array('3', 'Date'), array('4', 'Product'));
		$t->assign('CMB_GROUPBY', $oSelect->GetHTML());

		$begindate = @DCL_Sanitize::ToDate($_REQUEST['begindate']);
		if ($begindate !== null)
			$t->assign('VAL_BEGINDATE', $begindate);
		else
			$t->assign('VAL_BEGINDATE', '');

		$enddate = @DCL_Sanitize::ToDate($_REQUEST['enddate']);
		if ($enddate !== null)
			$t->assign('VAL_ENDDATE', $enddate);
		else
			$t->assign('VAL_ENDDATE', '');

		SmartyDisplay($t, 'htmlPersonnelActivity.tpl');
	}

	function execute()
	{
		$bExport = (IsSet($_REQUEST['export']) && $_REQUEST['export'] == '1');

		if (!$bExport)
			commonHeader();

		$begindate = @DCL_Sanitize::ToDate($_REQUEST['begindate']);
		$enddate = @DCL_Sanitize::ToDate($_REQUEST['enddate']);
		if ($begindate === null || $enddate === null)
		{
			if ($bExport)
				commonHeader();

			trigger_error(STR_WOST_DATEERR, E_USER_ERROR);
			$this->GetParameters(false);
			return;
		}

		$bTimesheet = isset($_REQUEST['timesheet']) && $_REQUEST['timesheet'] == 'Y';
		if ($bTimesheet && $_REQUEST['groupby'] != '1' && $_REQUEST['groupby'] != '2' && $_REQUEST['groupby'] != '4')
		{
			if ($bExport)
				commonHeader();

			trigger_error('Timesheet report must by grouped by project, action, or product.', E_USER_ERROR);
			$this->GetParameters(false);
			return;
		}

		$objDB = new dclDB;
		
		$sReportFor = '';
		$sCols = 'timecards.jcn, timecards.seq, timecards.hours';
		if ($_REQUEST['bytype'] == '2')
			$sCols .= ', personnel.short';

		if ($_REQUEST['groupby'] == '1')
			$sCols .= ', dcl_projects.name';
		else if ($_REQUEST['groupby'] == '2')
			$sCols .= ', actions.name';
		else if ($_REQUEST['groupby'] == '3')
			$sCols .= ', ' . $objDB->ConvertDate('timecards.actionon', 'actionon');
		else if ($_REQUEST['groupby'] == '4')
			$sCols .= ', products.name';
			
		if ($bTimesheet)
			$sCols .= ', ' . $objDB->ConvertDate('timecards.actionon', 'actionon');

		$iGroupColumn = -1;
		$query = "select $sCols from timecards ";
		if ($_REQUEST['groupby'] == '0' || $_REQUEST['groupby'] == '3')
		{
			// None (0) or date (3)
			if ($_REQUEST['bytype'] == '1')
			{
				if (($responsible = DCL_Sanitize::ToInt($_REQUEST['responsible'])) === null)
				{
					trigger_error('Data sanitize failed.');
					return;
				}
				
				$query .= ' where actionby=' . $responsible;
				$query .= ' and actionon between ' . $objDB->DisplayToSQL($begindate) . ' and ' . $objDB->DisplayToSQL($enddate);
			}
			else
			{
				if (($department = DCL_Sanitize::ToInt($_REQUEST['department'])) === null)
				{
					trigger_error('Data sanitize failed.');
					return;
				}
				
				$query .= $objDB->JoinKeyword . ' personnel on actionby = personnel.id ';
				$query .= 'where personnel.department=' . $department;
				$query .= ' and actionon between ' . $objDB->DisplayToSQL($begindate) . ' and ' . $objDB->DisplayToSQL($enddate);
			}

			if ($_REQUEST['groupby'] == '0')
				$query .= ' order by jcn, seq';
			else
			{
				$query .= ' order by actionon, jcn, seq';
				$iGroupColumn = 13;
				if ($_REQUEST['bytype'] != '1')
					$iGroupColumn++;
			}
		}
		else if ($_REQUEST['groupby'] == '1')
		{
			// projects
			if ($_REQUEST['bytype'] == '1')
			{
				if (($responsible = DCL_Sanitize::ToInt($_REQUEST['responsible'])) === null)
				{
					trigger_error('Data sanitize failed.');
					return;
				}
				
				$query .= 'left join projectmap on timecards.jcn = projectmap.jcn and projectmap.seq in (timecards.seq, 0) ';
				$query .= 'left join dcl_projects on dcl_projects.projectid = projectmap.projectid ';
				$query .= ' where timecards.actionby=' . $responsible;
				$query .= ' and timecards.actionon between ' . $objDB->DisplayToSQL($begindate) . ' and ' . $objDB->DisplayToSQL($enddate);
				$iGroupColumn = 13;
			}
			else
			{
				if (($department = DCL_Sanitize::ToInt($_REQUEST['department'])) === null)
				{
					trigger_error('Data sanitize failed.');
					return;
				}
				
				$query .= $objDB->JoinKeyword . ' personnel on actionby = personnel.id ';
				$query .= 'left join projectmap on timecards.jcn = projectmap.jcn and projectmap.seq in (timecards.seq, 0) ';
				$query .= 'left join dcl_projects on dcl_projects.projectid = projectmap.projectid ';
				$query .= 'where personnel.department=' . $department;
				$query .= ' and actionon between ' . $objDB->DisplayToSQL($begindate) . ' and ' . $objDB->DisplayToSQL($enddate);
				$iGroupColumn = 14;
			}

			$query .= ' order by dcl_projects.name, timecards.jcn, timecards.seq';
		}
		else if ($_REQUEST['groupby'] == '2')
		{
			// actions
			if ($_REQUEST['bytype'] == '1')
			{
				if (($responsible = DCL_Sanitize::ToInt($_REQUEST['responsible'])) === null)
				{
					trigger_error('Data sanitize failed.');
					return;
				}
				
				$query .= $objDB->JoinKeyword . ' actions on timecards.action = actions.id ';
				$query .= ' where timecards.actionby=' . $responsible;
				$query .= ' and timecards.actionon between ' . $objDB->DisplayToSQL($begindate) . ' and ' . $objDB->DisplayToSQL($enddate);
				$iGroupColumn = 13;
			}
			else
			{
				if (($department = DCL_Sanitize::ToInt($_REQUEST['department'])) === null)
				{
					trigger_error('Data sanitize failed.');
					return;
				}
				
				$query .= $objDB->JoinKeyword . ' personnel on actionby = personnel.id ';
				$query .= $objDB->JoinKeyword . ' actions on timecards.action = actions.id ';
				$query .= 'where personnel.department=' . $department;
				$query .= ' and actionon between ' . $objDB->DisplayToSQL($begindate) . ' and ' . $objDB->DisplayToSQL($enddate);
				$iGroupColumn = 14;
			}

			$query .= ' order by actions.name, timecards.jcn, timecards.seq';
		}
		else
		{
			// product
			if ($_REQUEST['bytype'] == '1')
			{
				if (($responsible = DCL_Sanitize::ToInt($_REQUEST['responsible'])) === null)
				{
					trigger_error('Data sanitize failed.');
					return;
				}
				
				$query .= $objDB->JoinKeyword . ' workorders on timecards.jcn = workorders.jcn and timecards.seq = workorders.seq ';
				$query .= $objDB->JoinKeyword . ' products on workorders.product = products.id ';
				$query .= ' where timecards.actionby=' . $responsible;
				$query .= ' and timecards.actionon between ' . $objDB->DisplayToSQL($begindate) . ' and ' . $objDB->DisplayToSQL($enddate);
				$iGroupColumn = 13;
			}
			else
			{
				if (($department = DCL_Sanitize::ToInt($_REQUEST['department'])) === null)
				{
					trigger_error('Data sanitize failed.');
					return;
				}
				
				$query .= $objDB->JoinKeyword . ' personnel on actionby = personnel.id ';
				$query .= $objDB->JoinKeyword . ' workorders on timecards.jcn = workorders.jcn and timecards.seq = workorders.seq ';
				$query .= $objDB->JoinKeyword . ' products on workorders.product = products.id ';
				$query .= 'where personnel.department=' . $department;
				$query .= ' and actionon between ' . $objDB->DisplayToSQL($begindate) . ' and ' . $objDB->DisplayToSQL($enddate);
				$iGroupColumn = 14;
			}

			$query .= ' order by products.name, timecards.jcn, timecards.seq';
		}

		if (isset($_REQUEST['timesheet']) && $_REQUEST['timesheet'] == 'Y')
			$this->ShowTimesheet($query, $iGroupColumn);
		else
			$this->ShowReport($query, $iGroupColumn);
	}
	
	function ShowTimesheet($query, $iGroupColumn)
	{
		$bExport = (IsSet($_REQUEST['export']) && $_REQUEST['export'] == '1');

		$oMeta =& CreateObject('dcl.DCL_MetadataDisplay');
		if ($_REQUEST['bytype'] == '1')
		{
			if (($responsible = DCL_Sanitize::ToInt($_REQUEST['responsible'])) === null)
			{
				trigger_error('Data sanitize failed.');
				return;
			}
				
			$sReportFor = $oMeta->GetPersonnel($responsible);
		}
		else
		{
			if (($department = DCL_Sanitize::ToInt($_REQUEST['department'])) === null)
			{
				trigger_error('Data sanitize failed.');
				return;
			}
				
			$sReportFor = $oMeta->GetDepartment($department);
		}

		if (($begindate = DCL_Sanitize::ToDate($_REQUEST['begindate'])) === null ||
			($enddate = DCL_Sanitize::ToDate($_REQUEST['enddate'])) === null
			)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$oBeginDate = new DCLDate();
		$oBeginDate->SetFromDisplay($begindate);
		$oEndDate = new DCLDate();
		$oEndDate->SetFromDisplay($enddate);
		
		$aDateArray = array();
		for ($iTime = $oBeginDate->time; $iTime <= $oEndDate->time; $iTime += 86400)
		{
			$oBeginDate->time = $iTime;
			$aDateArray[$oBeginDate->ToDisplay()] = 0.0;
		}
		
		$aReportArray = array();
		$objDB = new dclDB();
		if ($objDB->Query($query) != -1)
		{
			if ($objDB->next_record())
			{
				do
				{
					$sArrayIndex = $objDB->f('name');
					if (!isset($aReportArray[$sArrayIndex]))
						$aReportArray[$sArrayIndex] = $aDateArray;
						
					$aReportArray[$sArrayIndex][$objDB->FormatDateForDisplay($objDB->f('actionon'))] += $objDB->f('hours');
				}
				while ($objDB->next_record());
				
				$aTotalArray = $aDateArray;
				$aDisplayArray = array();
				ksort($aReportArray);
				
				$iIndex = 0;
				foreach ($aReportArray as $sGroup => $aHours)
				{
					$aDisplayArray[$iIndex] = array();
					$aDisplayArray[$iIndex][] = $sGroup;
					
					$fTotal = 0.0;
					foreach ($aHours as $sDate => $fHours)
					{
						$aTotalArray[$sDate] += $fHours;
						$aDisplayArray[$iIndex][] = $fHours;
						$fTotal += $fHours;
					}
					
					$aDisplayArray[$iIndex][] = $fTotal;
					$iIndex++;
				}
				
				if ($bExport)
				{
					$aDisplayArray[$iIndex] = array();
					$aDisplayArray[$iIndex][] = 'Total';
					
					$fTotal = 0.0;
					foreach ($aTotalArray as $sDate => $fHours)
					{
						$aDisplayArray[$iIndex][] = $fHours;
						$fTotal += $fHours;
					}
					
					$aDisplayArray[$iIndex][] = $fTotal;
					
					$nameArray = array_merge(array(''), array_keys($aDateArray), array('Total'));
				
					ExportArray($nameArray, $aDisplayArray);
				}
				else
				{
					$oTable = CreateObject('dcl.htmlTable');
					$oTable->addColumn('', 'string');
					foreach (array_keys($aDateArray) as $sDate)
					{
						$oTable->addColumn($sDate, 'numeric');
					}
					
					$oTable->addColumn('Total', 'numeric');
					
					$oTable->addFooter('Total');
					
					$fTotal = 0.0;
					foreach ($aTotalArray as $sDate => $fHours)
					{
						$oTable->addFooter($fHours);
						$fTotal += $fHours;
					}
					
					$oTable->addFooter($fTotal);
					
					$oTable->setData($aDisplayArray);
					$oTable->setShowRownum(true);
					$oTable->setCaption(sprintf(STR_WOST_ACTIVITYTITLE, $sReportFor, $begindate, $enddate));
					$oTable->addToolbar(menuLink('', sprintf('menuAction=reportPersonnelActivity.execute&export=1&timesheet=Y&responsible=%s&begindate=%s&enddate=%s&bytype=%d&groupby=%d&department=%d', $responsible, $begindate, $enddate, $_REQUEST['bytype'], $_REQUEST['groupby'], $_REQUEST['department'])), STR_VW_EXPORTRESULTS);
					$oTable->render();
				}
			}
			else
			{
				if ($bExport)
					commonHeader();

				trigger_error(STR_WOST_NOACTIVITY, E_USER_NOTICE);
			}
		}
	}

	function ShowReport($query, $iGroupColumn)
	{
		$bExport = (IsSet($_REQUEST['export']) && $_REQUEST['export'] == '1');

		$objP = CreateObject('dcl.dbPersonnel');
		$objS = CreateObject('dcl.dbStatuses');
		$objPr = CreateObject('dcl.dbPriorities');
		$objSe = CreateObject('dcl.dbSeverities');
		$objW = CreateObject('dcl.dbWorkorders');
		$objT = CreateObject('dcl.dbTimeCards');
		$objD = CreateObject('dcl.dbDepartments');
		$objDB = new dclDB;
		
		$aGroupOptions = array('1' => 'Project', '2' => 'Action', '3' => 'Date', '4' => 'Product');
		$groupBy = $_REQUEST['groupby'];
		if (!array_key_exists($groupBy, $aGroupOptions))
			$groupBy = '0';
		
		$oMeta =& CreateObject('dcl.DCL_MetadataDisplay');
		$responsible = 0;
		$department = 0;
		if ($_REQUEST['bytype'] == '1')
		{
			if (($responsible = DCL_Sanitize::ToInt($_REQUEST['responsible'])) === null)
			{
				trigger_error('Data sanitize failed.');
				return;
			}
				
			$sReportFor = $oMeta->GetPersonnel($responsible);
		}
		else
		{
			if (($department = DCL_Sanitize::ToInt($_REQUEST['department'])) === null)
			{
				trigger_error('Data sanitize failed.');
				return;
			}
				
			$sReportFor = $oMeta->GetDepartment($department);
		}

		if (($begindate = DCL_Sanitize::ToDate($_REQUEST['begindate'])) === null ||
			($enddate = DCL_Sanitize::ToDate($_REQUEST['enddate'])) === null
			)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if ($objDB->Query($query) != -1)
		{
			if ($objDB->next_record())
			{
				$lastJCN = 0;
				$lastSeq = 0;
				$lastGroup = $thisGroup = '<< undefined >>';
				$arrayIndex = -1;
				$count = 0;
				$totalEstHours = 0.0;
				$totalAppliedHours = 0.0;
				$totalEtcHours = 0.0;
				$totalTimeHours = 0.0;
				$oDate = new DCLDate;
				$aByDate = array();
				do
				{
					$thisJCN = $objDB->f('jcn');
					$thisSeq = $objDB->f('seq');
					if ($groupBy == '3')
					{
						$oDate->SetFromDB($objDB->f('actionon'));
						$thisGroup = $oDate->ToDisplay();
					}
					else if ($groupBy != '0')
					{
						if ($objDB->IsFieldNull('name'))
							$thisGroup = ' ';
						else
							$thisGroup = $objDB->f('name');
					}

					// Skip multiple time cards
					if ($thisJCN != $lastJCN || $thisSeq != $lastSeq || $thisGroup != $lastGroup)
					{
						$arrayIndex++;
						$objW->Load($thisJCN, $thisSeq);
						$objS->Load($objW->status);
						$objPr->Load($objW->priority);
						$objSe->Load($objW->severity);

						if ($bExport)
							$reportArray[$arrayIndex][0] = '[' . $thisJCN . '-' . $thisSeq . '] ' . $objW->summary;
						else
							$reportArray[$arrayIndex][0] = '[<a href="main.php?menuAction=boWorkorders.viewjcn&jcn=' . $thisJCN . '&seq=' . $thisSeq . '">' . $thisJCN . '-' . $thisSeq . '</a>] ' . htmlentities($objW->summary);

						if ($_REQUEST['bytype'] == '2')
							$reportArray[$arrayIndex][] = $objDB->f('short');

						$reportArray[$arrayIndex][] = $objS->name;
						$reportArray[$arrayIndex][] = $objPr->name;
						$reportArray[$arrayIndex][] = $objSe->name;
						$reportArray[$arrayIndex][] = (double)$objW->esthours;
						$reportArray[$arrayIndex][] = (double)$objW->etchours;
						$reportArray[$arrayIndex][] = (double)$objW->totalhours;
						$reportArray[$arrayIndex][] = $objW->eststarton;
						$reportArray[$arrayIndex][] = $objW->estendon;
						$reportArray[$arrayIndex][] = $objW->starton;
						$reportArray[$arrayIndex][] = $objW->closedon;
						$reportArray[$arrayIndex][] = (double)$objDB->f('hours');
						$ouHours = -($objW->esthours - $objW->totalhours);
						$diffHours = $ouHours;
						if ($diffHours < 0)
							$diffHours = -$diffHours;

						$ouPct = 0.0;
						$sign = '';
						if ($objW->esthours > 0)
						{
							$ouPct = $diffHours / $objW->esthours * 100;
							if ($objW->esthours > $objW->totalhours && $objW->esthours > 0)
								$sign = '-';
							else if ($objW->totalhours > $objW->esthours && $objW->totalhours > 0)
									$sign = '+';
						}

						$reportArray[$arrayIndex][] = sprintf('%s%0.2f (%s%0.2f%%)', $sign, abs($ouHours), $sign, abs($ouPct));

						if ($groupBy != '0')
							$reportArray[$arrayIndex][] = $thisGroup;

						$sKey = sprintf('%d-%d', $thisJCN, $thisSeq);
						if ($groupBy != '3' || !isset($aByDate[$sKey]))
						{
							$totalEstHours += (double)$objW->esthours;
							$totalAppliedHours += (double)$objW->totalhours;
							$totalEtcHours += (double)$objW->etchours;

							$aByDate[$sKey] = true;
						}

						$lastJCN = $thisJCN;
						$lastSeq = $thisSeq;
						$lastGroup = $thisGroup;
					}
					else
					{
						if ($_REQUEST['bytype'] == '2')
							$reportArray[$arrayIndex][12] += (double)$objDB->f('hours');
						else
							$reportArray[$arrayIndex][11] += (double)$objDB->f('hours');
					}

					$totalTimeHours += $objDB->f('hours');
					$count++;
				}
				while ($objDB->next_record());

				if ($bExport)
				{
					$arrayIndex++;
					$reportArray[$arrayIndex][0] = 'Totals';
	
					if ($_REQUEST['bytype'] == '2')
						$reportArray[$arrayIndex][] = '';
	
					$reportArray[$arrayIndex][] = '';
					$reportArray[$arrayIndex][] = '';
					$reportArray[$arrayIndex][] = '';
					$reportArray[$arrayIndex][] = $totalEstHours;
					$reportArray[$arrayIndex][] = $totalEtcHours;
					$reportArray[$arrayIndex][] = $totalAppliedHours;
					$reportArray[$arrayIndex][] = '';
					$reportArray[$arrayIndex][] = '';
					$reportArray[$arrayIndex][] = '';
					$reportArray[$arrayIndex][] = '';
					$reportArray[$arrayIndex][] = $totalTimeHours;
					$ouHours = -($totalEstHours - $totalAppliedHours);
					$diffHours = $ouHours;
					if ($diffHours < 0)
						$diffHours = -$diffHours;
	
					$ouPct = 0.0;
					$sign = '';
					if ($totalEstHours > 0)
					{
						$ouPct = $diffHours / $totalEstHours * 100;
						if ($totalEstHours > $totalAppliedHours && $totalEstHours > 0)
							$sign = '-';
						else if ($totalAppliedHours > $totalEstHours && $totalAppliedHours > 0)
							$sign = '+';
					}
	
					if ($bExport)
						$reportArray[$arrayIndex][] = sprintf('%s%0.2f (%s%0.2f%%)', $sign, abs($ouHours), $sign, abs($ouPct));
					else
						$reportArray[$arrayIndex][] = sprintf('%s%0.2f&nbsp;(%s%0.2f%%)', $sign, abs($ouHours), $sign, abs($ouPct));
						
					if ($groupBy != '0')
						$reportArray[$arrayIndex][] = '';

					$nameArray = array();
					$nameArray[] = STR_WOST_SUMMARY;
					if ($_REQUEST['bytype'] == '2')
						$nameArray[] = STR_CMMN_BY;
	
					$nameArray[] = STR_WO_STATUS;
					$nameArray[] = STR_WO_PRIORITY;
					$nameArray[] = STR_WO_SEVERITY;
					$nameArray[] = STR_WOST_BUDGET;
					$nameArray[] = STR_WOST_ETC;
					$nameArray[] = STR_WOST_TODATE;
					$nameArray[] = STR_WOST_ESTSTART;
					$nameArray[] = STR_WOST_ESTEND;
					$nameArray[] = STR_WOST_START;
					$nameArray[] = STR_WOST_END;
					$nameArray[] = STR_WOST_TIME;
					$nameArray[] = '+ / -';
	
					if ($groupBy != '0')
						$nameArray[] = '';
				
					ExportArray($nameArray, $reportArray);
				}
				else
				{
					$oTable = CreateObject('dcl.htmlTable');
					$oTable->addFooter('Totals');
	
					if ($_REQUEST['bytype'] == '2')
						$oTable->addFooter('');
	
					$oTable->addFooter('');
					$oTable->addFooter('');
					$oTable->addFooter('');
					$oTable->addFooter($totalEstHours);
					$oTable->addFooter($totalEtcHours);
					$oTable->addFooter($totalAppliedHours);
					$oTable->addFooter('');
					$oTable->addFooter('');
					$oTable->addFooter('');
					$oTable->addFooter('');
					$oTable->addFooter($totalTimeHours);
					$ouHours = -($totalEstHours - $totalAppliedHours);
					$diffHours = $ouHours;
					if ($diffHours < 0)
						$diffHours = -$diffHours;
	
					$ouPct = 0.0;
					$sign = '';
					if ($totalEstHours > 0)
					{
						$ouPct = $diffHours / $totalEstHours * 100;
						if ($totalEstHours > $totalAppliedHours && $totalEstHours > 0)
							$sign = '-';
						else if ($totalAppliedHours > $totalEstHours && $totalAppliedHours > 0)
							$sign = '+';
					}
	
					$oTable->addFooter(sprintf('%s%0.2f (%s%0.2f%%)', $sign, abs($ouHours), $sign, abs($ouPct)));
						
					if ($groupBy != '0')
					{
						$oTable->addFooter('');
					}

					$oTable->addColumn(STR_WOST_SUMMARY, 'html');
					if ($_REQUEST['bytype'] == '2')
						$oTable->addColumn(STR_CMMN_BY, 'string');
	
					$oTable->addColumn(STR_WO_STATUS, 'string');
					$oTable->addColumn(STR_WO_PRIORITY, 'string');
					$oTable->addColumn(STR_WO_SEVERITY, 'string');
					$oTable->addColumn(STR_WOST_BUDGET, 'numeric');
					$oTable->addColumn(STR_WOST_ETC, 'numeric');
					$oTable->addColumn(STR_WOST_TODATE, 'numeric');
					$oTable->addColumn(STR_WOST_ESTSTART, 'string');
					$oTable->addColumn(STR_WOST_ESTEND, 'string');
					$oTable->addColumn(STR_WOST_START, 'string');
					$oTable->addColumn(STR_WOST_END, 'string');
					$oTable->addColumn(STR_WOST_TIME, 'numeric');
					$oTable->addColumn('+ / -', 'numeric');
	
					if (array_key_exists($groupBy, $aGroupOptions))
						$oTable->addColumn($aGroupOptions[$groupBy], 'string');
						
					$oTable->setData($reportArray);
					$oTable->setCaption(sprintf(STR_WOST_ACTIVITYTITLE, $sReportFor, $_REQUEST['begindate'], $_REQUEST['enddate']));
					$oTable->addToolbar(menuLink('', sprintf('menuAction=reportPersonnelActivity.execute&export=1&responsible=%s&begindate=%s&enddate=%s&bytype=%d&groupby=%d&department=%d', $responsible, $begindate, $enddate, $_REQUEST['bytype'], $groupBy, $department)), STR_VW_EXPORTRESULTS);
					$oTable->addGroup($iGroupColumn);
					$oTable->setShowRownum(true);
					$oTable->render();
				}
			}
			else
			{
				if ($bExport)
					commonHeader();

				trigger_error(STR_WOST_NOACTIVITY, E_USER_NOTICE);
				$this->getparameters(false);
			}
		}
		else
		{
			if ($bExport)
				commonHeader();

			trigger_error(STR_WOST_QUERYERR, E_USER_ERROR);
		}
	}
}
?>
