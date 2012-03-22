<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2011 Free Software Foundation
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

LoadStringResource('usr');

class PersonnelPresenter
{
	public function Index()
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$template = new SmartyHelper();
		$template->assign('VAL_LETTERS', array_merge(array('All'), range('A', 'Z')));
		$template->assign('PERM_ADD', $g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_ADD));
		$template->assign('PERM_MODIFY', $g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_MODIFY));
		$template->assign('PERM_DELETE', $g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_DELETE));
		$template->assign('PERM_SETUP', $g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW));

		$filterActive = '';
		if (IsSet($_REQUEST['filterActive']))
			$filterActive = $_REQUEST['filterActive'];

		$filterStartsWith = '';
		if (IsSet($_REQUEST['filterStartsWith']))
			$filterStartsWith = $_REQUEST['filterStartsWith'];

		$filterSearch = '';
		if (IsSet($_REQUEST['filterSearch']))
			$filterSearch = $_REQUEST['filterSearch'];

		$filterDepartment = 0;
		if (IsSet($_REQUEST['filterDepartment']))
			$filterDepartment = Filter::ToInt($_REQUEST['filterDepartment']);

		$template->assign('VAL_FILTERACTIVE', $filterActive);
		$template->assign('VAL_FILTERSTART', $filterStartsWith);
		$template->assign('VAL_FILTERSEARCH', $filterSearch);
		$template->assign('VAL_FILTERDEPT', $filterDepartment);

		$aColumnHeaders = array(STR_CMMN_ID, STR_CMMN_ACTIVE, STR_USR_LOGIN, STR_CMMN_NAME, STR_USR_DEPARTMENT, 'Phone', 'Email', 'Internet');
		$aColumns = array('id', 'active', 'short', 'dcl_contact.last_name', 'dcl_contact.first_name', 'departments.name', 'dcl_contact_phone.phone_number', 'dcl_contact_email.email_addr', 'dcl_contact_url.url_addr');

		$queryHelper = new PersonnelSqlQueryHelper();
		$model = new PersonnelModel();
		$model = new DbProvider;

		$iPage = 1;
		$queryHelper->startrow = 0;
		$queryHelper->numrows = 25;
		if (isset($_REQUEST['page']))
		{
			$iPage = (int)$_REQUEST['page'];
			if ($iPage < 1)
				$iPage = 1;

			$queryHelper->startrow = ($iPage - 1) * $queryHelper->numrows;
			if ($queryHelper->startrow < 0)
				$queryHelper->startrow = 0;
		}

		$queryHelper->AddDef('columnhdrs', '', $aColumnHeaders);
		$queryHelper->AddDef('columns', '', $aColumns);
		$queryHelper->AddDef('order', '', array('short', 'id'));

		if ($filterActive == 'Y' || $filterActive == 'N')
			$queryHelper->AddDef('filter', 'active', "'$filterActive'");

		if ($filterSearch != '')
			$queryHelper->AddDef('filterlike', 'short', $filterSearch);

		if ($filterStartsWith != '')
			$queryHelper->AddDef('filterstart', 'short', $filterStartsWith);

		if ($filterDepartment > 0)
			$queryHelper->AddDef('filter', 'department', $filterDepartment);

		if ($model->Query($queryHelper->GetSQL(true)) == -1 || !$model->next_record())
			return;

		$iRecords = (int)$model->f(0);
		$template->assign('VAL_COUNT', $iRecords);
		$template->assign('VAL_PAGE', $iPage);
		$template->assign('VAL_MAXPAGE', ceil($iRecords / $queryHelper->numrows));
		$model->FreeResult();

		if ($model->LimitQuery($queryHelper->GetSQL(), $queryHelper->startrow, $queryHelper->numrows) != -1)
		{
			$aUsers = array();
			while ($model->next_record())
				$aUsers[] = $model->Record;

			$model->FreeResult();

			$template->assign_by_ref('VAL_USERS', $aUsers);
			$template->assign('VAL_HEADERS', $aColumnHeaders);
		}

		$template->Render('PersonnelBrowse.tpl');
	}
	
	public function Detail()
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_PERSONNEL, DCL_PERM_VIEW);

		$personnelId = Filter::RequireInt($_REQUEST['id']);
		$personnelModel = new PersonnelModel();
		if ($personnelModel->Load($personnelId) == -1)
			throw new InvalidEntityException();
		
		$contactModel = new ContactModel();
		$contactModel->Load($personnelModel->contact_id);
		
		$smarty = new SmartyHelper();
		$smarty->assign_by_ref('Personnel', $personnelModel);
		$smarty->assign_by_ref('Contact', $contactModel);
		
		$smarty->Render('PersonnelDetail.tpl');
		
		$sSQL = '';
	    $sSQL = $this->GetScopeSQL($personnelId);
        $sSQL .= ' UNION ALL ';
	    $sSQL .= $this->GetTimeCardSQL($personnelId);
        $sSQL .= ' UNION ALL ';
		$sSQL .= $this->GetWorkOrderCodeSQL($personnelId);
		$sSQL .= ' UNION ALL ';
		$sSQL .= $this->GetProjectCodeSQL($personnelId);
		$sSQL .= ' ORDER BY 2 DESC';
		
		$oDB = new DbProvider();
		if ($oDB->LimitQuery($sSQL, 0, 25) !== -1)
		{
		    $aResults = array();
		    while ($oDB->next_record())
		    {
		        $aRecord = array();
		        $aRecord[] = $oDB->FormatDateForDisplay($oDB->f(1));
		        
		        $oDB->objTimestamp->SetFromDB($oDB->f(1));
		        $aRecord[] = $oDB->objTimestamp->ToTimeOnly();
		        
		        if ($oDB->f(0) == 4)
		            $aRecord[] = '<a href="' . menuLink('', 'menuAction=boProjects.viewproject&project=' . $oDB->f(4)) . '">[' . $oDB->f(4) . '] ' . htmlspecialchars($oDB->f(5)) . '</a>';
		        else
		            $aRecord[] = '<a href="' . menuLink('', 'menuAction=WorkOrder.Detail&jcn=' . $oDB->f(2) . '&seq=' . $oDB->f(3)) . '">[' . $oDB->f(2) . '-' . $oDB->f(3) . '] ' . htmlspecialchars($oDB->f(6)) . '</a>';
		        
		        $aRecord[] = $oDB->f(7);
		        $aRecord[] = $oDB->f(8);
		        $aRecord[] = $oDB->f(9);
		        
		        $aResults[] = $aRecord;
		    }
		    
    		$oTable = new TableHtmlHelper();
    		$oTable->setCaption('Recent Activity');
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

	public function Create()
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_PERSONNEL, DCL_PERM_ADD);

		$template = new SmartyHelper();

		$template->assign('IS_EDIT', false);
		$template->assign('VAL_ACTIVE', 'Y');
		$template->assign('VAL_REPORTTO', $GLOBALS['DCLID']);
		$template->assign('VAL_DEPARTMENT', 0);
		$template->assign('VAL_SHORT', '');

		$oUserRole = new UserRoleModel();
		$template->assign('Roles', $oUserRole->GetGlobalRoles());

		$template->Render('PersonnelForm.tpl');
	}

	public function Edit(PersonnelModel $model)
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$template = new SmartyHelper();
		
		$template->assign('IS_EDIT', true);
		$template->assign('VAL_PERSONNELID', $model->id);
		$template->assign('VAL_ACTIVE', $model->active);
		$template->assign('VAL_SHORT', $model->short);
		$template->assign('VAL_REPORTTO', $model->reportto);
		$template->assign('VAL_DEPARTMENT', $model->department);

		$oUserRole = new UserRoleModel();
		$template->assign('Roles', $oUserRole->GetGlobalRoles($model->id));

		$oMeta = new DisplayHelper();
		$aContact =& $oMeta->GetContact($model->contact_id);

		$template->assign('VAL_CONTACTID', $model->contact_id);
		$template->assign('VAL_CONTACTNAME', $aContact['name']);

		$template->Render('PersonnelForm.tpl');
	}

	public function Delete(PersonnelModel $model)
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		ShowDeleteYesNo('User', 'Personnel.Destroy', $model->id, $model->short);
	}

	public function EditPassword()
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PREFS, DCL_PERM_PASSWORD))
			throw new PermissionDeniedException();
		
		$oSmarty = new SmartyHelper();
		
		$oSmarty->assign('PERM_ADMIN', $g_oSec->HasPerm(DCL_ENTITY_GLOBAL, DCL_PERM_ADMIN));
		$oSmarty->assign('VAL_USERID', $GLOBALS['DCLID']);
		$oSmarty->assign('VAL_USERNAME', $GLOBALS['DCLNAME']);
		
		$oSmarty->Render('PersonnelPasswdForm.tpl');
	}
	
	private function GetScopeSQL($personnelId)
    {
        return "select 1 AS type, A.audit_on, A.jcn, W.seq, A.projectid, P.name, W.summary, S.name, R.short, CASE A.audit_type WHEN 1 THEN 'Added work order to project' ELSE 'Removed work order from project' END AS action from projectmap_audit A
	        JOIN dcl_projects P ON P.projectid = A.projectid
	        JOIN workorders W ON W.jcn = A.jcn AND A.seq IN (0, W.seq)
	        JOIN statuses S ON W.status = S.id
	        JOIN personnel R ON R.id = A.audit_by
	        where A.audit_by = $personnelId";
    }
    
    private function GetTimeCardSQL($personnelId)
    {
        return "select 2 AS type, T.inputon, T.jcn, T.seq, P.projectid, P.name, W.summary, S.name, R.short, TS.name || ', ' || A.name || ': ' || T.summary from timecards T
			JOIN projectmap M ON M.jcn = T.jcn AND M.seq IN (0, T.seq)
			JOIN dcl_projects P ON P.projectid = M.projectid
			JOIN workorders W ON W.jcn = T.jcn AND T.seq = W.seq
			JOIN statuses S ON W.status = S.id
			JOIN statuses TS ON T.status = TS.id
			JOIN actions A ON T.action = A.id
			JOIN personnel R ON R.id = T.actionby
			where T.actionby = $personnelId";
    }
    
    private function GetWorkOrderCodeSQL($personnelId)
    {
        return "select 3 AS type, SC.sccs_checkin_on, SC.dcl_entity_id, SC.dcl_entity_id2, P.projectid, P.name, W.summary, S.name, R.short, 'Checked in v' || SC.sccs_version || ' ' || SC.sccs_project_path || '/' || SC.sccs_file_name  from dcl_sccs_xref SC
			JOIN projectmap M ON M.jcn = SC.dcl_entity_id AND M.seq IN (0, SC.dcl_entity_id2)
			JOIN dcl_projects P ON P.projectid = M.projectid
			JOIN workorders W ON W.jcn = SC.dcl_entity_id AND M.seq IN (0, SC.dcl_entity_id2)
			JOIN statuses S ON W.status = S.id
			JOIN personnel R ON R.id = SC.personnel_id
			where SC.personnel_id = $personnelId";
    }
    
    private function GetProjectCodeSQL($personnelId)
    {
        return "select 4 AS type, SC.sccs_checkin_on, SC.dcl_entity_id, SC.dcl_entity_id2, P.projectid, P.name, P.name, S.name, R.short, 'Checked in v' || SC.sccs_version || ' ' || SC.sccs_project_path || '/' || SC.sccs_file_name  from dcl_sccs_xref SC
	        JOIN dcl_projects P ON P.projectid = SC.dcl_entity_id
	        JOIN statuses S ON P.status = S.id
	        JOIN personnel R ON R.id = SC.personnel_id
	        where SC.personnel_id = $personnelId";
    }
}
