<?php
/*
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
		global $g_oSession;

		if ($needHdr == true)
			commonHeader();

		$objPersonnel = new PersonnelHtmlHelper();
		$oDept = new DepartmentHtmlHelper();
		$statusHelper = new StatusHtmlHelper();

		$oDBPersonnel = new PersonnelModel();
		if ($oDBPersonnel->Load(DCLID) == -1)
			return;

		$t = new SmartyHelper();
		$oSelect = new SelectHtmlHelper();
		
		$iDept = $oDBPersonnel->department;
		if (isset($_REQUEST['department']))
			$iDept = (int)$_REQUEST['department'];
		else if ($g_oSession->IsRegistered('personnel_activity_department'))
			$iDept = (int)$g_oSession->Value('personnel_activity_department');
			
		$iUser = DCLID;
		if (isset($_REQUEST['responsible']))
			$iUser = (int)$_REQUEST['responsible'];
		else if ($g_oSession->IsRegistered('personnel_activity_responsible'))
			$iUser = (int)$g_oSession->Value('personnel_activity_responsible');
		
		$aStatuses = array();
		if (isset($_REQUEST['status']))
			$aStatuses = $_REQUEST['status'];
		
		$t->assign('CMB_RESPONSIBLE', $objPersonnel->Select($iUser, 'responsible', 'lastfirst', 0, false));
		$t->assign('CMB_DEPARTMENTS', $oDept->Select($iDept, 'department', 'name', 0, false, true));
		$t->assign('CMB_STATUSES', $statusHelper->Select($aStatuses, 'status', 'name', 8, false));

		// By department or responsible
		$oSelect->FirstOption = '';
		$oSelect->Id = 'bytype';
		$oSelect->Options = array(array('1', 'By Responsible'), array('2', 'By Department'));
		if (isset($_REQUEST['bytype']))
			$oSelect->DefaultValue = $_REQUEST['bytype'];
		else if ($g_oSession->IsRegistered('personnel_activity_bytype'))
			$oSelect->DefaultValue = $g_oSession->Value('personnel_activity_bytype');
		
		$t->assign('CMB_BYTYPE', $oSelect->GetHTML());
		
		$oDate = new DateHelper();
		$oDate->time = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		
		$dtOneWeekAgo = $oDate->time - 7 * 86400;
		$dtYesterday = $oDate->time - 86400;

		// Optional group by
		$oSelect->FirstOption = 'None';
		$oSelect->Id = 'groupby';
		$oSelect->OnChange = '';
		$oSelect->Options = array(array('1', 'Project'), array('2', 'Action'), array('3', 'Date'), array('4', 'Product'), array('5', 'Action By'));
		if (isset($_REQUEST['groupby']))
			$oSelect->DefaultValue = $_REQUEST['groupby'];
		else if ($g_oSession->IsRegistered('personnel_activity_groupby'))
			$oSelect->DefaultValue = $g_oSession->Value('personnel_activity_groupby');
		
		$t->assign('CMB_GROUPBY', $oSelect->GetHTML());

		$begindate = @Filter::ToDate($_REQUEST['begindate']);
		if ($begindate !== null)
		{
			$t->assign('VAL_BEGINDATE', $begindate);
		}
		else if ($g_oSession->IsRegistered('personnel_activity_begindate'))
		{
			$t->assign('VAL_BEGINDATE', $g_oSession->Value('personnel_activity_begindate'));
		}
		else
		{
			$oDate->time = $dtOneWeekAgo;
			$t->assign('VAL_BEGINDATE', $oDate->ToDisplay());
		}

		$enddate = @Filter::ToDate($_REQUEST['enddate']);
		if ($enddate !== null)
		{
			$t->assign('VAL_ENDDATE', $enddate);
		}
		else if ($g_oSession->IsRegistered('personnel_activity_enddate'))
		{
			$t->assign('VAL_ENDDATE', $g_oSession->Value('personnel_activity_enddate'));
		}
		else
		{
			$oDate->time = $dtYesterday;
			$t->assign('VAL_ENDDATE', $oDate->ToDisplay());
		}

		if (isset($_REQUEST['begindate']) && isset($_REQUEST['enddate']))
			$t->assign('VAL_TIMESHEET', isset($_REQUEST['timesheet']) ? $_REQUEST['timesheet'] : 'N');
		else if ($g_oSession->IsRegistered('personnel_activity_timesheet'))
			$t->assign('VAL_TIMESHEET', $g_oSession->Value('personnel_activity_timesheet'));
			
		$t->Render('PersonnelActivity.tpl');
	}

	function execute()
	{
		global $g_oSession;
		
		$bExport = (IsSet($_REQUEST['export']) && $_REQUEST['export'] == '1');

		if (!$bExport)
			commonHeader();

		$begindate = @Filter::ToDate($_REQUEST['begindate']);
		$enddate = @Filter::ToDate($_REQUEST['enddate']);
		if ($begindate === null || $enddate === null)
		{
			if ($bExport)
				commonHeader();

			ShowError(STR_WOST_DATEERR);
			$this->GetParameters(false);
			return;
		}
		
		$g_oSession->Register('personnel_activity_begindate', $begindate);
		$g_oSession->Register('personnel_activity_enddate', $enddate);
		$g_oSession->Register('personnel_activity_bytype', $_REQUEST['bytype']);
		$g_oSession->Register('personnel_activity_groupby', $_REQUEST['groupby']);
		$g_oSession->Register('personnel_activity_responsible', $_REQUEST['responsible']);
		$g_oSession->Register('personnel_activity_department', $_REQUEST['department']);
		$g_oSession->Register('personnel_activity_timesheet', isset($_REQUEST['timesheet']) ? $_REQUEST['timesheet'] : 'N');
		$g_oSession->Edit();
		
		$bTimesheet = isset($_REQUEST['timesheet']) && $_REQUEST['timesheet'] == 'Y';
		if ($bTimesheet && $_REQUEST['groupby'] != '1' && $_REQUEST['groupby'] != '2' && $_REQUEST['groupby'] != '4' && $_REQUEST['groupby'] != '5')
		{
			if ($bExport)
				commonHeader();

			ShowError('Timesheet report must by grouped by project, action, action by, or product.');
			$this->GetParameters(false);
			return;
		}
		
		if ($_REQUEST['groupby'] == '5' && $_REQUEST['bytype'] != '2')
		{
			ShowError('Grouping by Action By must use report by department.');
			$this->GetParameters(false);
			return;
		}
		
		$aStatuses = @Filter::ToIntArray($_REQUEST['status']);

		$objDB = new DbProvider;
		
		$sReportFor = '';
		$sCols = 'timecards.jcn, timecards.seq, timecards.hours';
		if ($_REQUEST['bytype'] == '2' || $_REQUEST['groupby'] == '5')
		{
			$sCols .= ', personnel.short';
			
			if ($bTimesheet && $_REQUEST['groupby'] == '5')
				$sCols .= ' AS name';
		}

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
		if ($_REQUEST['groupby'] == '0' || $_REQUEST['groupby'] == '3' || $_REQUEST['groupby'] == '5')
		{
			// None (0) or date (3) or action by (5)
			if ($_REQUEST['bytype'] == '1')
			{
				if (($responsible = Filter::ToInt($_REQUEST['responsible'])) === null)
				{
					throw new InvalidDataException();
				}
				
				$query .= ' where actionby=' . $responsible;
				$query .= ' and actionon between ' . $objDB->DisplayToSQL($begindate) . ' and ' . $objDB->DisplayToSQL($enddate);
			}
			else
			{
				if (($department = Filter::ToInt($_REQUEST['department'])) === null)
				{
					throw new InvalidDataException();
				}
				
				$query .= $objDB->JoinKeyword . ' personnel on actionby = personnel.id ';
				$query .= 'where personnel.department=' . $department;
				$query .= ' and actionon between ' . $objDB->DisplayToSQL($begindate) . ' and ' . $objDB->DisplayToSQL($enddate);
			}
			
			if (count($aStatuses) > 0)
			{
				$query .= ' and status in (' . join(',', $aStatuses) . ')';
			}

			if ($_REQUEST['groupby'] == '0')
			{
				$query .= ' order by jcn, seq';
			}
			else if ($_REQUEST['groupby'] == '5')
			{
				$query .= ' order by personnel.short, jcn, seq';
				$iGroupColumn = 2;
			}
			else
			{
				$query .= ' order by actionon, jcn, seq';
				$iGroupColumn = 13;
				if ($_REQUEST['bytype'] != '1')
					$iGroupColumn++;
					
				if ($_REQUEST['groupby'] != '1')
					$iGroupColumn++;
			}
		}
		else if ($_REQUEST['groupby'] == '1')
		{
			// projects
			if ($_REQUEST['bytype'] == '1')
			{
				if (($responsible = Filter::ToInt($_REQUEST['responsible'])) === null)
				{
					throw new InvalidDataException();
				}
				
				$query .= 'left join projectmap on timecards.jcn = projectmap.jcn and projectmap.seq in (timecards.seq, 0) ';
				$query .= 'left join dcl_projects on dcl_projects.projectid = projectmap.projectid ';
				$query .= ' where timecards.actionby=' . $responsible;
				$query .= ' and timecards.actionon between ' . $objDB->DisplayToSQL($begindate) . ' and ' . $objDB->DisplayToSQL($enddate);
				$iGroupColumn = 13;
			}
			else
			{
				if (($department = Filter::ToInt($_REQUEST['department'])) === null)
				{
					throw new InvalidDataException();
				}
				
				$query .= $objDB->JoinKeyword . ' personnel on actionby = personnel.id ';
				$query .= 'left join projectmap on timecards.jcn = projectmap.jcn and projectmap.seq in (timecards.seq, 0) ';
				$query .= 'left join dcl_projects on dcl_projects.projectid = projectmap.projectid ';
				$query .= 'where personnel.department=' . $department;
				$query .= ' and actionon between ' . $objDB->DisplayToSQL($begindate) . ' and ' . $objDB->DisplayToSQL($enddate);
				$iGroupColumn = 14;
			}

			if (count($aStatuses) > 0)
			{
				$query .= ' and status in (' . join(',', $aStatuses) . ')';
			}

			$query .= ' order by dcl_projects.name, timecards.jcn, timecards.seq';
		}
		else if ($_REQUEST['groupby'] == '2')
		{
			// actions
			if ($_REQUEST['bytype'] == '1')
			{
				if (($responsible = Filter::ToInt($_REQUEST['responsible'])) === null)
				{
					throw new InvalidDataException();
				}
				
				$query .= $objDB->JoinKeyword . ' actions on timecards.action = actions.id ';
				$query .= ' where timecards.actionby=' . $responsible;
				$query .= ' and timecards.actionon between ' . $objDB->DisplayToSQL($begindate) . ' and ' . $objDB->DisplayToSQL($enddate);
				$iGroupColumn = 14;
			}
			else
			{
				if (($department = Filter::ToInt($_REQUEST['department'])) === null)
				{
					throw new InvalidDataException();
				}
				
				$query .= $objDB->JoinKeyword . ' personnel on actionby = personnel.id ';
				$query .= $objDB->JoinKeyword . ' actions on timecards.action = actions.id ';
				$query .= 'where personnel.department=' . $department;
				$query .= ' and actionon between ' . $objDB->DisplayToSQL($begindate) . ' and ' . $objDB->DisplayToSQL($enddate);
				$iGroupColumn = 15;
			}

			if (count($aStatuses) > 0)
			{
				$query .= ' and status in (' . join(',', $aStatuses) . ')';
			}

			$query .= ' order by actions.name, timecards.jcn, timecards.seq';
		}
		else
		{
			// product
			if ($_REQUEST['bytype'] == '1')
			{
				if (($responsible = Filter::ToInt($_REQUEST['responsible'])) === null)
				{
					throw new InvalidDataException();
				}
				
				$query .= $objDB->JoinKeyword . ' workorders on timecards.jcn = workorders.jcn and timecards.seq = workorders.seq ';
				$query .= $objDB->JoinKeyword . ' products on workorders.product = products.id ';
				$query .= ' where timecards.actionby=' . $responsible;
				$query .= ' and timecards.actionon between ' . $objDB->DisplayToSQL($begindate) . ' and ' . $objDB->DisplayToSQL($enddate);
				$iGroupColumn = 14;
			}
			else
			{
				if (($department = Filter::ToInt($_REQUEST['department'])) === null)
				{
					throw new InvalidDataException();
				}
				
				$query .= $objDB->JoinKeyword . ' personnel on actionby = personnel.id ';
				$query .= $objDB->JoinKeyword . ' workorders on timecards.jcn = workorders.jcn and timecards.seq = workorders.seq ';
				$query .= $objDB->JoinKeyword . ' products on workorders.product = products.id ';
				$query .= 'where personnel.department=' . $department;
				$query .= ' and actionon between ' . $objDB->DisplayToSQL($begindate) . ' and ' . $objDB->DisplayToSQL($enddate);
				$iGroupColumn = 15;
			}

			if (count($aStatuses) > 0)
			{
				$query .= ' and status in (' . join(',', $aStatuses) . ')';
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

		$responsible = '';
		$oMeta = new DisplayHelper();
		if ($_REQUEST['bytype'] == '1')
		{
			if (($responsible = Filter::ToInt($_REQUEST['responsible'])) === null)
			{
				throw new InvalidDataException();
			}
				
			$sReportFor = $oMeta->GetPersonnel($responsible);
		}
		else
		{
			if (($department = Filter::ToInt($_REQUEST['department'])) === null)
			{
				throw new InvalidDataException();
			}
				
			$sReportFor = $oMeta->GetDepartment($department);
		}

		if (($begindate = Filter::ToDate($_REQUEST['begindate'])) === null ||
			($enddate = Filter::ToDate($_REQUEST['enddate'])) === null
			)
		{
			throw new InvalidDataException();
		}
		
		$oBeginDate = new DateHelper();
		$oBeginDate->SetFromDisplay($begindate);
		$oEndDate = new DateHelper();
		$oEndDate->SetFromDisplay($enddate);
		
		$aDateArray = array();
		for ($iTime = $oBeginDate->time; $iTime <= $oEndDate->time; $iTime += 86400)
		{
			$oBeginDate->time = $iTime;
			$aDateArray[$oBeginDate->ToDisplay()] = 0.0;
		}
		
		$aReportArray = array();
		$objDB = new DbProvider();
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
					$oTable = new TableHtmlHelper();
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

				ShowInfo(STR_WOST_NOACTIVITY);
				$this->getparameters(false);
			}
		}
	}

	function ShowReport($query, $iGroupColumn)
	{
		$bExport = (IsSet($_REQUEST['export']) && $_REQUEST['export'] == '1');

		$objS = new StatusModel();
		$objPr = new PriorityModel();
		$objSe = new SeverityModel();
		$objW = new WorkOrderModel();
		$oPM = new ProjectMapModel();
		$objDB = new DbProvider;
		
		$aGroupOptions = array('1' => 'Project', '2' => 'Action', '3' => 'Date', '4' => 'Product', '5' => 'by');
		$groupBy = $_REQUEST['groupby'];
		if (!array_key_exists($groupBy, $aGroupOptions))
			$groupBy = '0';
		
		$oMeta = new DisplayHelper();
		$responsible = 0;
		$department = 0;
		if ($_REQUEST['bytype'] == '1')
		{
			if (($responsible = Filter::ToInt($_REQUEST['responsible'])) === null)
			{
				throw new InvalidDataException();
			}
				
			$sReportFor = $oMeta->GetPersonnel($responsible);
		}
		else
		{
			if (($department = Filter::ToInt($_REQUEST['department'])) === null)
			{
				throw new InvalidDataException();
			}
				
			$sReportFor = $oMeta->GetDepartment($department);
		}

		if (($begindate = Filter::ToDate($_REQUEST['begindate'])) === null ||
			($enddate = Filter::ToDate($_REQUEST['enddate'])) === null
			)
		{
			throw new InvalidDataException();
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
				$subEstHours = 0.0;
				$subAppliedHours = 0.0;
				$subEtcHours = 0.0;
				$subTimeHours = 0.0;
				$totalEstHours = 0.0;
				$totalAppliedHours = 0.0;
				$totalEtcHours = 0.0;
				$totalTimeHours = 0.0;
				$oDate = new DateHelper;
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
					else if ($groupBy == '5')
					{
						$thisGroup = $objDB->f('short');
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
						if ($groupBy != '0' && $thisGroup != $lastGroup && $lastGroup != '<< undefined >>')
						{
							// Subtotals
							$arrayIndex++;
							if ($bExport)
								$reportArray[$arrayIndex][0] = 'Subtotal for ' . $lastGroup;
							else
								$reportArray[$arrayIndex][0] = '<b>Subtotal for ' . $lastGroup . '</b>';
							
							if ($groupBy != '1')
								$reportArray[$arrayIndex][] = '';
														
							if ($_REQUEST['bytype'] == '2')
							{
								if ($groupBy == '5')
									$reportArray[$arrayIndex][] = $lastGroup;
								else
									$reportArray[$arrayIndex][] = '';
							}
			
							$reportArray[$arrayIndex][] = '';
							$reportArray[$arrayIndex][] = '';
							$reportArray[$arrayIndex][] = '';
							$reportArray[$arrayIndex][] = $subEstHours;
							$reportArray[$arrayIndex][] = $subEtcHours;
							$reportArray[$arrayIndex][] = $subAppliedHours;
							$reportArray[$arrayIndex][] = '';
							$reportArray[$arrayIndex][] = '';
							$reportArray[$arrayIndex][] = '';
							$reportArray[$arrayIndex][] = '';
							$reportArray[$arrayIndex][] = $subTimeHours;
							$ouHours = -($subEstHours - $subAppliedHours);
							$diffHours = $ouHours;
							if ($diffHours < 0)
								$diffHours = -$diffHours;
			
							$ouPct = 0.0;
							$sign = '';
							if ($subEstHours > 0)
							{
								$ouPct = $diffHours / $subEstHours * 100;
								if ($subEstHours > $subAppliedHours && $subEstHours > 0)
									$sign = '-';
								else if ($subAppliedHours > $subEstHours && $subAppliedHours > 0)
									$sign = '+';
							}
			
							if ($bExport)
								$reportArray[$arrayIndex][] = sprintf('%s%0.2f (%s%0.2f%%)', $sign, abs($ouHours), $sign, abs($ouPct));
							else
								$reportArray[$arrayIndex][] = sprintf('%s%0.2f&nbsp;(%s%0.2f%%)', $sign, abs($ouHours), $sign, abs($ouPct));
								
							if ($groupBy != '0' && ($_REQUEST['bytype'] != '2' || $groupBy != '5'))
								$reportArray[$arrayIndex][] = $lastGroup;

							$subEstHours = 0.0;
							$subAppliedHours = 0.0;
							$subEtcHours = 0.0;
							$subTimeHours = 0.0;
						}
						
						$arrayIndex++;
						$objW->LoadByIdSeq($thisJCN, $thisSeq);
						$objS->Load($objW->status);
						$objPr->Load($objW->priority);
						$objSe->Load($objW->severity);

						if ($bExport)
							$reportArray[$arrayIndex][0] = '[' . $thisJCN . '-' . $thisSeq . '] ' . $objW->summary;
						else
							$reportArray[$arrayIndex][0] = '[<a href="main.php?menuAction=WorkOrder.Detail&jcn=' . $thisJCN . '&seq=' . $thisSeq . '">' . $thisJCN . '-' . $thisSeq . '</a>] ' . htmlspecialchars($objW->summary, ENT_QUOTES, 'UTF-8');

						if ($groupBy != '1')
						{
							if ($oPM->LoadByWO($thisJCN, $thisSeq) != -1)
							{
								if ($bExport)
									$reportArray[$arrayIndex][] = '[' . $oPM->projectid . '] ' . $oMeta->GetProject($oPM->projectid);
								else
									$reportArray[$arrayIndex][] = '[<a href="main.php?menuAction=Project.Detail&id=' . $oPM->projectid . '">' . $oPM->projectid . '</a>] ' . htmlspecialchars($oMeta->GetProject($oPM->projectid), ENT_QUOTES, 'UTF-8');
							}
							else 
							{
								$reportArray[$arrayIndex][] = '';
							}
						}
						
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

						if ($groupBy != '0' && ($_REQUEST['bytype'] != '2' || $groupBy != '5'))
							$reportArray[$arrayIndex][] = $thisGroup;

						$sKey = sprintf('%d-%d', $thisJCN, $thisSeq);
						if ($groupBy != '3' || !isset($aByDate[$sKey]))
						{
							$subEstHours += (double)$objW->esthours;
							$subAppliedHours += (double)$objW->totalhours;
							$subEtcHours += (double)$objW->etchours;
							
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
						$iOrdinal = 11;
						if ($groupBy != '1')
							$iOrdinal++;
							
						if ($_REQUEST['bytype'] == '2')
							$iOrdinal++;
							
						$reportArray[$arrayIndex][$iOrdinal] += (double)$objDB->f('hours');
					}

					$subTimeHours += $objDB->f('hours');
					$totalTimeHours += $objDB->f('hours');
					$count++;
				}
				while ($objDB->next_record());

				// Subtotals
				$arrayIndex++;
				if ($bExport)
					$reportArray[$arrayIndex][0] = 'Subtotal for ' . $lastGroup;
				else
					$reportArray[$arrayIndex][0] = '<b>Subtotal for ' . $lastGroup . '</b>';
				
				if ($groupBy != '1')
					$reportArray[$arrayIndex][] = '';
								
				if ($_REQUEST['bytype'] == '2')
				{
					if ($groupBy == '5')
						$reportArray[$arrayIndex][] = $lastGroup;
					else
						$reportArray[$arrayIndex][] = '';
				}

				$reportArray[$arrayIndex][] = '';
				$reportArray[$arrayIndex][] = '';
				$reportArray[$arrayIndex][] = '';
				$reportArray[$arrayIndex][] = $subEstHours;
				$reportArray[$arrayIndex][] = $subEtcHours;
				$reportArray[$arrayIndex][] = $subAppliedHours;
				$reportArray[$arrayIndex][] = '';
				$reportArray[$arrayIndex][] = '';
				$reportArray[$arrayIndex][] = '';
				$reportArray[$arrayIndex][] = '';
				$reportArray[$arrayIndex][] = $subTimeHours;
				$ouHours = -($subEstHours - $subAppliedHours);
				$diffHours = $ouHours;
				if ($diffHours < 0)
					$diffHours = -$diffHours;

				$ouPct = 0.0;
				$sign = '';
				if ($subEstHours > 0)
				{
					$ouPct = $diffHours / $subEstHours * 100;
					if ($subEstHours > $subAppliedHours && $subEstHours > 0)
						$sign = '-';
					else if ($subAppliedHours > $subEstHours && $subAppliedHours > 0)
						$sign = '+';
				}

				if ($bExport)
					$reportArray[$arrayIndex][] = sprintf('%s%0.2f (%s%0.2f%%)', $sign, abs($ouHours), $sign, abs($ouPct));
				else
					$reportArray[$arrayIndex][] = sprintf('%s%0.2f&nbsp;(%s%0.2f%%)', $sign, abs($ouHours), $sign, abs($ouPct));
					
				if ($groupBy != '0' && ($_REQUEST['bytype'] != '2' || $groupBy != '5'))
					$reportArray[$arrayIndex][] = $lastGroup;

				$subEstHours = 0.0;
				$subAppliedHours = 0.0;
				$subEtcHours = 0.0;
				$subTimeHours = 0.0;

				if ($bExport)
				{
					$arrayIndex++;
					$reportArray[$arrayIndex][0] = 'Totals';
					
					if ($groupBy != '1')
						$reportArray[$arrayIndex][] = '';
						
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
					
					if ($groupBy != '1')
						$nameArray[] = STR_WO_PROJECT;
						
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
	
					if ($groupBy != '0' && ($_REQUEST['bytype'] != '2' || $groupBy != '5'))
						$nameArray[] = '';
				
					ExportArray($nameArray, $reportArray);
				}
				else
				{
					$oTable = new TableHtmlHelper();
					$oTable->addFooter('Totals');
	
					if ($groupBy != '1')
						$oTable->addFooter('');
	
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
						
					if ($groupBy != '0' && ($_REQUEST['bytype'] != '2' || $groupBy != '5'))
					{
						$oTable->addFooter('');
					}

					$oTable->addColumn(STR_WOST_SUMMARY, 'html');
					if ($groupBy != '1')
						$oTable->addColumn(STR_WO_PROJECT, 'html');
					
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
					$oTable->addColumn('+ / -', 'html');
	
					if (array_key_exists($groupBy, $aGroupOptions) && ($_REQUEST['bytype'] != '2' || $groupBy != '5'))
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

				ShowInfo(STR_WOST_NOACTIVITY);
				$this->getparameters(false);
			}
		}
		else
		{
			if ($bExport)
				commonHeader();

			ShowError(STR_WOST_QUERYERR);
		}
	}
}
