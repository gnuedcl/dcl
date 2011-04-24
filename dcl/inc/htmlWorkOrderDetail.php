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

LoadStringResource('wo');
LoadStringResource('tc');
class htmlWorkOrderDetail
{
	function Show($jcn, $seq, $editTimeCardID = 0, $forDelete = false)
	{
		global $dcl_info, $g_oSec, $g_oSession;

		if ($jcn < 1 || $seq < 1)
			return trigger_error(sprintf(STR_WO_BADJCNSEQERR, 'ShowWorkOrderDetail'));

		if (!$forDelete && !$g_oSec->HasAnyPerm(array(DCL_ENTITY_WORKORDER => array($g_oSec->PermArray(DCL_PERM_VIEW, $jcn, $seq), $g_oSec->PermArray(DCL_PERM_VIEWACCOUNT, $jcn, $seq), $g_oSec->PermArray(DCL_PERM_VIEWSUBMITTED, $jcn, $seq)))))
			throw new PermissionDeniedException();
		else if ($forDelete && !$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_DELETE, $jcn, $seq))
			throw new PermissionDeniedException();

		$objWorkOrder = new dbWorkorders();
		if ($objWorkOrder->Load($jcn, $seq) == -1)
			return trigger_error(sprintf(STR_WO_NOTFOUNDERR, $jcn, $seq), E_USER_ERROR);

		$oMeta = new DCL_MetadataDisplay();
		$oSmarty = new DCL_Smarty();
		
		$oSmarty->assign('IS_PUBLIC', $g_oSec->IsPublicUser());

		$oSmarty->assign('VAL_FORMACTION', menuLink());
		$oSmarty->assign('VAL_SUMMARY', $objWorkOrder->summary);
		$oSmarty->assign('VAL_JCN', $objWorkOrder->jcn);
		$oSmarty->assign('VAL_SEQ', $objWorkOrder->seq);
		$oSmarty->assign('VAL_PUBLIC', $objWorkOrder->is_public == 'Y' ? STR_CMMN_YES : STR_CMMN_NO);
		$oSmarty->assign('VAL_DEADLINEON', $objWorkOrder->deadlineon);

		$oSmarty->assign('VAL_REPORTED_VERSION', $oMeta->GetProductVersion($objWorkOrder->reported_version_id));
		$oSmarty->assign('VAL_TARGETED_VERSION', $oMeta->GetProductVersion($objWorkOrder->targeted_version_id));
		$oSmarty->assign('VAL_FIXED_VERSION', $oMeta->GetProductVersion($objWorkOrder->fixed_version_id));
		$oSmarty->assign('VAL_ESTSTARTON', $objWorkOrder->eststarton);
		$oSmarty->assign('VAL_STARTON', $objWorkOrder->starton);
		$oSmarty->assign('VAL_ESTENDON', $objWorkOrder->estendon);
		$oSmarty->assign('VAL_ESTHOURS', $objWorkOrder->esthours);
		$oSmarty->assign('VAL_TOTALHOURS', $objWorkOrder->totalhours);
		$oSmarty->assign('VAL_ETCHOURS', $objWorkOrder->etchours);
		$oSmarty->assign('VAL_CREATEDON', $objWorkOrder->createdon);
		$oSmarty->assign('VAL_STATUSON', $objWorkOrder->statuson);
		$oSmarty->assign('VAL_LASTACTIONON', $objWorkOrder->lastactionon);
		$oSmarty->assign('VAL_NOTES', $objWorkOrder->notes);
		$oSmarty->assign('VAL_DESCRIPTION', $objWorkOrder->description);

		$oSmarty->assign('VAL_CREATEBY', $oMeta->GetPersonnel($objWorkOrder->createby));
		$oSmarty->assign('VAL_STATUS', $oMeta->GetStatus($objWorkOrder->status));
		$oSmarty->assign('VAL_PRODUCT', $oMeta->GetProduct($objWorkOrder->product));
		$oSmarty->assign('VAL_SETID', $oMeta->oProduct->wosetid);
		$oSmarty->assign('VAL_TYPE', $oMeta->GetWorkOrderType($objWorkOrder->wo_type_id));
		$oSmarty->assign('VAL_MODULE', $oMeta->GetModule($objWorkOrder->module_id));
		$oSmarty->assign('VAL_SOURCE', $oMeta->GetSource($objWorkOrder->entity_source_id));
		$oSmarty->assign('VAL_RESPONSIBLEID', $objWorkOrder->responsible);
		$oSmarty->assign('VAL_RESPONSIBLE', $oMeta->GetPersonnel($objWorkOrder->responsible));
		$oSmarty->assign('VAL_PRIORITY', $oMeta->GetPriority($objWorkOrder->priority));
		$oSmarty->assign('VAL_SEVERITY', $oMeta->GetSeverity($objWorkOrder->severity));
		$oSmarty->assign('VAL_TAGS', str_replace(',', ', ', $oMeta->GetTags(DCL_ENTITY_WORKORDER, $objWorkOrder->jcn, $objWorkOrder->seq)));
		$oSmarty->assign('VAL_HOTLIST', $oMeta->GetHotlistWithPriority(DCL_ENTITY_WORKORDER, $objWorkOrder->jcn, $objWorkOrder->seq));
		
		$iStatusType = $oMeta->oStatus->GetStatusType($objWorkOrder->status);
		$oSmarty->assign('VAL_STATUS_TYPE', $iStatusType);
		if ($iStatusType == 2)
		{
			$oSmarty->assign('VAL_CLOSEDBY', $oMeta->GetPersonnel($objWorkOrder->closedby));
			$oSmarty->assign('VAL_CLOSEDON', $objWorkOrder->closedon);
		}

		$aContact = $oMeta->GetContact($objWorkOrder->contact_id);
		$oSmarty->assign('VAL_CONTACTID', $objWorkOrder->contact_id);
		$oSmarty->assign('VAL_CONTACT', $aContact['name']);
		$oSmarty->assign('VAL_CONTACTPHONETYPE', $aContact['phonetype']);
		$oSmarty->assign('VAL_CONTACTPHONE', $aContact['phone']);
		$oSmarty->assign('VAL_CONTACTEMAILTYPE', $aContact['emailtype']);
		$oSmarty->assign('VAL_CONTACTEMAIL', $aContact['email']);
		$oSmarty->assign('VAL_WATCHTYPE', '3');

		if ($forDelete && $editTimeCardID == 0)
			$oSmarty->assign('IS_DELETE', true);

		$oTC = new TimeCardsModel();
		$oSmarty->assign('VAL_TIMECARDS', $oTC->GetTimeCardsArray($objWorkOrder->jcn, $objWorkOrder->seq));
		$oSmarty->assign('VAL_EDITTCID', $editTimeCardID);
		$oSmarty->assign('VAL_FORDELETE', $forDelete);
		
		$oTasks = new dbWorkOrderTask();
		$oSmarty->assign('VAL_TASKS', $oTasks->GetTasksForWorkOrder($objWorkOrder->jcn, $objWorkOrder->seq));

		if ($g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEWFILE))
		{
			$oAttachments = new boFile();
			$oSmarty->assign('VAL_ATTACHMENTS', $oAttachments->GetAttachments(DCL_ENTITY_WORKORDER, $objWorkOrder->jcn, $objWorkOrder->seq));
		}

		if ($g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEW))
		{
			$oProjects = new boProjects();
			$oSmarty->assign('VAL_PROJECTS', $oProjects->GetProjectPath($objWorkOrder->jcn, $objWorkOrder->seq));
		}

		$oAcct = new WorkOrderOrganizationModel();
		if ($oAcct->Load($objWorkOrder->jcn, $objWorkOrder->seq) != -1)
		{
			$aOrgs = array();
			$bHasPerm = $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEWACCOUNT) || $g_oSec->IsOrgUser();
			$bViewAll = !$g_oSec->IsOrgUser();
			if ($bHasPerm)
				$aOrgs = split(',', $g_oSession->Value('member_of_orgs'));

			$aOrgNames = array();
			$iOrgIndex = 0;
			do
			{
				$oAcct->GetRow();
				if ($bViewAll || ($bHasPerm && in_array($oAcct->account_id, $aOrgs)))
				{
					$aOrgNames[$iOrgIndex]['org_id'] = $oAcct->account_id;
					$aOrgNames[$iOrgIndex]['org_name'] = $oAcct->account_name;
					$iOrgIndex++;
				}
			}
			while ($oAcct->next_record());

			$oSmarty->assign('VAL_ORGS', $aOrgNames);
		}

		$oSmarty->assign('PERM_ACTION', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION));
		$oSmarty->assign('PERM_ASSIGN', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ASSIGN));
		$oSmarty->assign('PERM_ADDTASK', $g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK));
		$oSmarty->assign('PERM_REMOVETASK', $g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_REMOVETASK));
		$oSmarty->assign('PERM_ADD', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ADD));
		$oSmarty->assign('PERM_COPYTOWO', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_COPYTOWO));
		$oSmarty->assign('PERM_MODIFY', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_MODIFY));
		$oSmarty->assign('PERM_DELETE', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_DELETE));
		$oSmarty->assign('PERM_ATTACHFILE', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ATTACHFILE));
		$oSmarty->assign('PERM_REMOVEFILE', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_REMOVEFILE));
		$oSmarty->assign('PERM_VIEW', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEW));
		$oSmarty->assign('PERM_VIEWWIKI', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEWWIKI));
		$oSmarty->assign('PERM_VIEWCHANGELOG', $g_oSec->HasPerm(DCL_ENTITY_CHANGELOG, DCL_PERM_VIEW));
		$oSmarty->assign('PERM_VIEWORG', $g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_VIEW));
		$oSmarty->assign('PERM_VIEWCONTACT', $g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_VIEW));
		$oSmarty->assign('PERM_AUDIT', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_AUDIT));
		$oSmarty->assign('PERM_MODIFY_TC', $g_oSec->HasPerm(DCL_ENTITY_TIMECARD, DCL_PERM_MODIFY));
		$oSmarty->assign('PERM_DELETE_TC', $g_oSec->HasPerm(DCL_ENTITY_TIMECARD, DCL_PERM_DELETE));
		$oSmarty->assign('PERM_ISPUBLICUSER', $g_oSec->IsPublicUser());

		if ($g_oSec->IsPublicUser())
			$oSmarty->Render('htmlWorkordersDetailPublic.tpl');
		else
			$oSmarty->Render('htmlWorkordersDetail.tpl');
	}

	function Download()
	{
		global $dcl_info, $g_oSec;

		if (($jcn = DCL_Sanitize::ToInt($_REQUEST['jcn'])) === null ||
			($seq = DCL_Sanitize::ToInt($_REQUEST['seq'])) === null ||
			!DCL_Sanitize::IsValidFileName($_REQUEST['filename'])
			)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEW, $jcn, $seq))
			throw new PermissionDeniedException();

		$o = new boFile();
		$o->iType = DCL_ENTITY_WORKORDER;
		$o->iKey1 = $jcn;
		$o->iKey2 = $seq;
		$o->sFileName = $_REQUEST['filename'];
		$o->sRoot = $dcl_info['DCL_FILE_PATH'] . '/attachments';
		$o->Download();
	}
}
