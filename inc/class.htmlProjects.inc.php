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

LoadStringResource('prj');
LoadStringResource('wo');

class htmlProjects
{
	function GetCombo($default = 0, $cbName = 'project', $reportTo = 0, $size = 0, $dontshowid = -1, $bHideClosed = false)
	{
		$whereClause = '';

		if ($bHideClosed)
		{
			$whereClause = ', statuses ';
		}

		if ($reportTo > 0)
		{
			$whereClause .= " WHERE reportto=$reportTo";
		}

		if ($dontshowid != -1)
		{
			if ($whereClause == '' || $whereClause == ', statuses ')
				$whereClause .= ' WHERE ';
			else
				$whereClause .= ' AND ';

			$whereClause .= "dcl_projects.projectid != $dontshowid";
		}

		if ($bHideClosed)
		{
			if ($whereClause == '' || $whereClause == ', statuses ')
				$whereClause .= ' WHERE ';
			else
				$whereClause .= ' AND ';

			$whereClause .= 'dcl_projects.status = statuses.id AND statuses.dcl_status_type != 2';
		}

		$oSelect = CreateObject('dcl.htmlSelect');
		$oSelect->vDefault = $default;
		$oSelect->sName = $cbName;
		$oSelect->iSize = $size;
		$oSelect->sZeroOption = STR_CMMN_SELECTONE;
		$oSelect->SetFromQuery('SELECT dcl_projects.projectid, dcl_projects.name FROM dcl_projects' . $whereClause . ' ORDER BY dcl_projects.name');

		return $oSelect->GetHTML();
	}

	function show()
	{
		global $dcl_info, $g_oSec;

		commonHeader();

		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEW))
			return PrintPermissionDenied();

		$oView = CreateObject('dcl.boView');
		$oView->numrows = 25;

		$filterStatus = @DCL_Sanitize::ToSignedInt($_REQUEST['filterStatus']);
		$filterReportto = @DCL_Sanitize::ToInt($_REQUEST['filterReportto']);

		$oView->table = 'dcl_projects';
		$oView->style = 'report';
		$oView->title = 'Projects';
		$oView->AddDef('columnhdrs', '', array(STR_CMMN_ID, STR_PRJ_LEAD, STR_PRJ_STATUS, STR_PRJ_NAME));
		$oView->AddDef('columns', '', array('projectid', 'reportto.short', 'statuses.name', 'dcl_projects.name'));
		if ($dcl_info['DCL_PROJECT_BROWSE_PARENTS_ONLY'] == 'Y')
			$oView->AddDef('filter', 'parentprojectid', 0);

		$oView->AddDef('order', '', array('dcl_projects.name'));

		if ($filterStatus !== null)
		{
			if ($filterStatus > 0)
				$oView->AddDef('filter', 'dcl_projects.status', $filterStatus);
			else if ($filterStatus == -1)
				$oView->AddDef('filter', 'statuses.dcl_status_type', '2');
			else
				$oView->AddDef('filternot', 'statuses.dcl_status_type', '2');
		}
		else
			$oView->AddDef('filternot', 'statuses.dcl_status_type', '2');

		if ($filterReportto !== null)
			$oView->AddDef('filter', 'dcl_projects.reportto', $filterReportto);

		$oHtml = CreateObject('dcl.htmlProjectsBrowse');
		$oHtml->Render($oView);
	}

	function ShowUploadFileForm($projectid)
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ATTACHFILE, $projectid))
			return PrintPermissionDenied();
			
		$t = CreateSmarty();

		$t->assign('VAL_MAXUPLOADFILESIZE', $dcl_info['DCL_MAX_UPLOAD_FILE_SIZE']);
		$t->assign('VAL_PROJECTID', $projectid);
		$t->assign('LNK_CANCEL', menuLink('', 'menuAction=boProjects.viewproject&project=' . $projectid));
		
		SmartyDisplay($t, 'htmlProjectsUpload.tpl');
	}

	function ShowDeleteAttachmentYesNo($projectid, $filename)
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_REMOVEFILE, $projectid))
			return PrintPermissionDenied();

		$t = CreateSmarty();
		$t->assign('VAL_FILENAME', $filename);
		$t->assign('VAL_PROJECTID', $projectid);
		$t->assign('TXT_DELCONFIRM', sprintf(STR_PRJ_DELCONFIRM, $filename));
		
		SmartyDisplay($t, 'htmlProjectsDeleteAttachment.tpl');
	}

	function changeLog()
	{
		global $dcl_info, $g_oSec;

		commonHeader();

		if (!$g_oSec->HasPerm(DCL_ENTITY_CHANGELOG, DCL_PERM_VIEW))
			return PrintPermissionDenied();

		if (($id = DCL_Sanitize::ToInt($_REQUEST['projectid'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEW, $id))
			return PrintPermissionDenied();

		$o = CreateObject('dcl.dbSccsXref');
		if ($o->ListChangeLog(DCL_ENTITY_PROJECT, $id) != -1)
		{
			$allRecs = array();
			while ($o->next_record())
			{
				$allRecs[] = array($o->f(0) . ': ' . $o->f(2), $o->f(1), $o->f(3), $o->f(4), $o->f(5), $o->FormatTimestampForDisplay($o->f(6)));
			}

			$oTable =& CreateObject('dcl.htmlTable');
			$oTable->setCaption("ChangeLog for Project $id");
			$oTable->addColumn('Project', 'string');
			$oTable->addColumn('Changed By', 'string');
			$oTable->addColumn('File', 'string');
			$oTable->addColumn('Version', 'string');
			$oTable->addColumn('Comments', 'string');
			$oTable->addColumn('Date', 'string');
	
			$oTable->addToolbar(menuLink('', "menuAction=boProjects.viewproject&project=$id"), 'Back');
			$oTable->addGroup(0);
			$oTable->setData($allRecs);
			$oTable->setShowRownum(true);
			$oTable->render();
		}
	}
}
?>