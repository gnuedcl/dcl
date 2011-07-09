<?php
/*
 * $Id$
 *
 * Double Choco Latte - Source Configuration Management System
 * Copyright (C) 1999  Michael L. Dean & Tim R. Norman
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
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

class htmlHotlistProjectTimeline
{
    function htmlHotlistProjectTimeline()
    {
        
    }
    
    function GetCriteria()
    {
        global $dcl_info;
        
		commonHeader();
		
		if (($id = Filter::ToInt($_REQUEST['id'])) === null)
		    throw new PermissionDeniedException();
		    
		$hotlist = new HotlistModel();
		$hotlist->Load($id);

		$t = new DCL_Smarty();
		$t->assign('VAL_HOTLISTNAME', $hotlist->hotlist_desc);
		$t->assign('VAL_HOTLISTID', $id);
		$t->assign('VAL_DAYS', 7);
		$t->assign('VAL_ENDON', date($dcl_info['DCL_DATE_FORMAT']));
		$t->assign('VAL_SCOPE', true);
		$t->assign('VAL_TIMECARDS', true);
		$t->assign('VAL_CODE', true);
		$t->Render('htmlHotlistProjectTimelineForm.tpl');
    }
    
    function Render()
    {
        commonHeader();
        
		if (($id = Filter::ToInt($_REQUEST['id'])) === null)
		    throw new PermissionDeniedException();

		if (($days = Filter::ToInt($_REQUEST['days'])) === null)
		    throw new PermissionDeniedException();

		if (($endon = Filter::ToDate($_REQUEST['endon'])) === null)
		    throw new PermissionDeniedException();
		    
		$oDate = new DCLDate;
		$oDate->SetFromDisplay($endon);
		$endon = $oDate->ToDB();
		$oDate->time = mktime(0, 0, 0, date('m', $oDate->time), date('d', $oDate->time) - $days, date('Y', $oDate->time));
		$beginon = $oDate->ToDB();

		$sSQL = '';
		if (isset($_REQUEST['scope']))
		    $sSQL = $this->GetScopeSQL($id, $beginon, $endon);
		    
		if (isset($_REQUEST['timecards']))
		{
		    if ($sSQL != '')
		        $sSQL .= ' UNION ALL ';
		        
		    $sSQL .= $this->GetTimeCardSQL($id, $beginon, $endon);
		}
		
		if (isset($_REQUEST['code']))
		{
		    if ($sSQL != '')
		        $sSQL .= ' UNION ALL ';
		        
		    $sSQL .= $this->GetWorkOrderCodeSQL($id, $beginon, $endon);
		    $sSQL .= ' UNION ALL ';
		    $sSQL .= $this->GetProjectCodeSQL($id, $beginon, $endon);
		}
		
		if ($sSQL == '')
		{
		    ShowError('No options selected.', __FILE__, __LINE__, null);
		    return;
		}
		
		$sSQL .= ' ORDER BY 2 DESC';
		
		$oDB = new DbProvider();
		if ($oDB->Query($sSQL) !== -1)
		{
		    $aResults = array();
		    while ($oDB->next_record())
		    {
		        $aRecord = array();
		        $aRecord[] = $oDB->FormatDateForDisplay($oDB->f(1));
		        
		        $oDB->objTimestamp->SetFromDB($oDB->f(1));
		        $aRecord[] = $oDB->objTimestamp->ToTimeOnly();
		        
		        if ($oDB->f(0) == 4)
		        {
		            $aRecord[] = '<a href="' . menuLink('', 'menuAction=boProjects.viewproject&project=' . $id) . '">[' . $id . '] ' . htmlspecialchars($oDB->f(5)) . '</a>';
		        }
		        else
		        {
		            $aRecord[] = '<a href="' . menuLink('', 'menuAction=boWorkorders.viewjcn&jcn=' . $oDB->f(2) . '&seq=' . $oDB->f(3)) . '">[' . $oDB->f(2) . '-' . $oDB->f(3) . '] ' . htmlspecialchars($oDB->f(6)) . '</a>';
		        }
		        
		        $aRecord[] = $oDB->f(7);
		        $aRecord[] = $oDB->f(8);
		        $aRecord[] = $oDB->f(9);
		        
		        $aResults[] = $aRecord;
		    }
		    
    		$oTable = new TableHtmlHelper();
    		$oTable->setCaption('Hotlist Timeline');
    		$oTable->addColumn('Date', 'string');
    		$oTable->addColumn('Time', 'string');
    		$oTable->addColumn('Item', 'html');
    		$oTable->addColumn('Current Status', 'string');
    		$oTable->addColumn('Action By', 'string');
    		$oTable->addColumn('Action Description', 'string');
    		$oTable->addGroup(0);
    		
    		$oTable->setData($aResults);
    		$oTable->setShowRownum(true);
    		$oTable->render();
		}
    }
    
    function GetScopeSQL($projectid, $begin_dt, $end_dt)
    {
        return "select 1 AS type, A.audit_on, A.jcn, W.seq, A.projectid, P.name, W.summary, S.name, R.short,
						CASE A.audit_type WHEN 1 THEN 'Added work order to hotlist' ELSE 'Removed work order from hotlist' END AS action
			FROM dcl_entity_hotlist A
	        JOIN dcl_projects P ON P.projectid = A.projectid
	        JOIN workorders W ON W.jcn = A.jcn AND A.seq IN (0, W.seq)
	        JOIN statuses S ON W.status = S.id
	        JOIN personnel R ON R.id = A.audit_by
	        WHERE A.projectid = $projectid AND A.audit_on BETWEEN '$begin_dt' AND '$end_dt 23:59'";
    }
    
    function GetTimeCardSQL($projectid, $begin_dt, $end_dt)
    {
        return "select 2 AS type, T.inputon, T.jcn, T.seq, P.projectid, P.name, W.summary, S.name, R.short, TS.name || ', ' || A.name || ': ' || T.summary from timecards T
			JOIN projectmap M ON M.jcn = T.jcn AND M.seq IN (0, T.seq)
			JOIN dcl_projects P ON P.projectid = M.projectid
			JOIN workorders W ON W.jcn = T.jcn AND T.seq = W.seq
			JOIN statuses S ON W.status = S.id
			JOIN statuses TS ON T.status = TS.id
			JOIN actions A ON T.action = A.id
			JOIN personnel R ON R.id = T.actionby
			where M.projectid = $projectid AND T.inputon BETWEEN '$begin_dt' AND '$end_dt 23:59'";
    }
    
    function GetWorkOrderCodeSQL($projectid, $begin_dt, $end_dt)
    {
        return "select 3 AS type, SC.sccs_checkin_on, SC.dcl_entity_id, SC.dcl_entity_id2, P.projectid, P.name, W.summary, S.name, R.short, 'Checked in v' || SC.sccs_version || ' ' || SC.sccs_project_path || '/' || SC.sccs_file_name  from dcl_sccs_xref SC
			JOIN projectmap M ON M.jcn = SC.dcl_entity_id AND M.seq IN (0, SC.dcl_entity_id2)
			JOIN dcl_projects P ON P.projectid = M.projectid
			JOIN workorders W ON W.jcn = SC.dcl_entity_id AND M.seq IN (0, SC.dcl_entity_id2)
			JOIN statuses S ON W.status = S.id
			JOIN personnel R ON R.id = SC.personnel_id
			where M.projectid = $projectid AND dcl_entity_type_id = 2 AND SC.sccs_checkin_on BETWEEN '$begin_dt' AND '$end_dt 23:59'";
    }
    
    function GetProjectCodeSQL($projectid, $begin_dt, $end_dt)
    {
        return "select 4 AS type, SC.sccs_checkin_on, SC.dcl_entity_id, SC.dcl_entity_id2, P.projectid, P.name, P.name, S.name, R.short, 'Checked in v' || SC.sccs_version || ' ' || SC.sccs_project_path || '/' || SC.sccs_file_name  from dcl_sccs_xref SC
	        JOIN dcl_projects P ON P.projectid = SC.dcl_entity_id
	        JOIN statuses S ON P.status = S.id
	        JOIN personnel R ON R.id = SC.personnel_id
	        where P.projectid = $projectid AND dcl_entity_type_id = 1 AND SC.sccs_checkin_on BETWEEN '$begin_dt' AND '$end_dt 23:59'";
    }
}
