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

class htmlWorkOrderForm
{
	var $oSmarty;
	var $eState;

	function htmlWorkOrderForm()
	{
		$this->oSmarty = new DCL_Smarty();
		$this->eState = DCL_FORM_ADD;
	}

	function Show($jcn = 0, $oSource = '')
	{
		global $dcl_info, $g_oSec, $dcl_preferences, $g_oSession;

		$isEdit = false;
		$isTicket = false;
		$isCopy = false;
		if (is_object($oSource))
		{
			$isEdit = is_a($oSource, 'WorkOrderModel') && $oSource->jcn > 0;
			$isTicket = !$isEdit && is_a($oSource, 'TicketsModel');
			$isCopy = !$isEdit && !$isTicket && is_a($oSource, 'WorkOrderModel') && $oSource->jcn == 0;
		}
		
		if ($isEdit)
		{
			if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_MODIFY, $oSource->jcn, $oSource->seq))
				throw new PermissionDeniedException();
		}
		else if ($isTicket)
		{
			if (!$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_COPYTOWO, $oSource->ticketid))
				throw new PermissionDeniedException();
		}
		else if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ADD))
		{
			throw new PermissionDeniedException();
		}

		$objJS = new AttributeSetJsHelper();
		if (!$isEdit)
			$objJS->bActiveOnly = true;

		$objJS->bPriorities = true;
		$objJS->bSeverities = true;
		$objJS->bModules = true;
		$objJS->DisplayAttributeScript();

		$title = '';
		if ($isTicket)
		{
			$title = sprintf(STR_WO_TICKET, $oSource->ticketid);
		}
		elseif ($isEdit)
		{
			$title = sprintf(STR_WO_EDITWO, $oSource->jcn, $oSource->seq);
		}
		elseif ($isCopy)
		{
			$title = 'Copy Work Order';
			if ($jcn > 0)
				$title .= " as Sequence of $jcn";
		}
		elseif ($jcn == 0)
		{
			$title = STR_WO_ADDWO;
		}
		else
		{
			if ($jcn > 0)
				$title = sprintf(STR_WO_ADDSEQJCN, $jcn);
			else
				$title = STR_WO_ADDSEQ;
		}

		$this->oSmarty->assign('TXT_TITLE', $title);
		$this->oSmarty->assign('IS_EDIT', $isEdit);

		if ($isEdit)
		{
			$this->oSmarty->assign('VAL_MENUACTION', 'boWorkorders.dbmodifyjcn');
			$this->oSmarty->assign('VAL_WOID', $oSource->jcn);
			$this->oSmarty->assign('VAL_SEQ', $oSource->seq);
		}
		else
		{
			$this->oSmarty->assign('VAL_MENUACTION', 'boWorkorders.dbnewjcn');

			if ($isTicket)
				$this->oSmarty->assign('VAL_TICKETID', $oSource->ticketid);

			if ($jcn > 0)
				$this->oSmarty->assign('VAL_WOID', $jcn);
		}

		$this->oSmarty->assign('VAL_JSDATEFORMAT', GetJSDateFormat());
		$this->oSmarty->assign('VAL_FORMACTION', menuLink());

		$this->oSmarty->assign('VAL_MULTIORG', $dcl_info['DCL_WO_SECONDARY_ACCOUNTS_ENABLED'] == 'Y');
		$this->oSmarty->assign('VAL_AUTODATE', $dcl_info['DCL_AUTO_DATE'] == 'Y');
		$this->oSmarty->assign('PERM_ADDTASK', $g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK, IsSet($_REQUEST['projectid']) ? (int)$_REQUEST['projectid'] : 0));
		$this->oSmarty->assign('PERM_ACTION', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION));
		$this->oSmarty->assign('PERM_ASSIGNWO', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ASSIGN));
		$this->oSmarty->assign('PERM_ATTACHFILE', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ATTACHFILE) && $dcl_info['DCL_MAX_UPLOAD_FILE_SIZE'] > 0 && !$isEdit);
		$this->oSmarty->assign('VAL_MAXUPLOADFILESIZE', $dcl_info['DCL_MAX_UPLOAD_FILE_SIZE']);
		$this->oSmarty->assign('PERM_ISPUBLICUSER', $g_oSec->IsPublicUser());
		$this->oSmarty->assign('VAL_NOTIFYDEFAULT', isset($dcl_preferences['DCL_PREF_NOTIFY_DEFAULT']) ? $dcl_preferences['DCL_PREF_NOTIFY_DEFAULT'] : 'N');

		$oMeta = new DCL_MetadataDisplay();
		if ($isEdit || $isTicket || $isCopy)
		{
			$oProduct = new ProductModel();
			$oProduct->Query('SELECT wosetid FROM products WHERE id=' . ($isTicket ? $oSource->product : $oSource->product));
			if ($oProduct->next_record())
				$this->oSmarty->assign('VAL_SETID', $oProduct->f(0));

			if ($isEdit || $isCopy)
			{
				$this->oSmarty->assign('VAL_SOURCE', $oSource->entity_source_id);
				$this->oSmarty->assign('VAL_PRODUCT', $oSource->product);
				$this->oSmarty->assign('VAL_MODULE', $oSource->module_id);
				$this->oSmarty->assign('VAL_TYPE', $oSource->wo_type_id);

				$this->oSmarty->assign('VAL_DEADLINEON', $oSource->deadlineon);
				$this->oSmarty->assign('VAL_ESTSTARTON', $oSource->eststarton);
				$this->oSmarty->assign('VAL_ESTENDON', $oSource->estendon);
				$this->oSmarty->assign('VAL_ESTHOURS', $oSource->esthours);
				$this->oSmarty->assign('VAL_SEVERITY', $oSource->severity);
				$this->oSmarty->assign('VAL_PRIORITY', $oSource->priority);
				$this->oSmarty->assign('VAL_CONTACTS', $oSource->contact_id);
			}
			else
			{
				$this->oSmarty->assign('VAL_SOURCE', $oSource->entity_source_id);
				$this->oSmarty->assign('VAL_PRODUCT', $oSource->product);
				$this->oSmarty->assign('VAL_MODULE', $oSource->module_id);
				
				$this->oSmarty->assign('VAL_TYPE', 0);
				$this->oSmarty->assign('VAL_SEVERITY', 0);
				$this->oSmarty->assign('VAL_PRIORITY', 0);
				$this->oSmarty->assign('VAL_CONTACTS', $oSource->contact_id);
			}
			
			$oTag = new EntityTagModel();
			if ($isTicket)
				$this->oSmarty->assign('VAL_TAGS', $oTag->getTagsForEntity(DCL_ENTITY_TICKET, $oSource->ticketid));
			else
				$this->oSmarty->assign('VAL_TAGS', $oTag->getTagsForEntity(DCL_ENTITY_WORKORDER, $oSource->jcn, $oSource->seq));
			
			$oHotlist = new EntityHotlistModel();
			if ($isTicket)
				$this->oSmarty->assign('VAL_HOTLIST', $oHotlist->getTagsForEntity(DCL_ENTITY_TICKET, $oSource->ticketid));
			else
				$this->oSmarty->assign('VAL_HOTLIST', $oHotlist->getTagsForEntity(DCL_ENTITY_WORKORDER, $oSource->jcn, $oSource->seq));
		}

		if (!$isEdit && !$isCopy)
		{
			if ($dcl_info['DCL_AUTO_DATE'] == 'Y')
			{
				$this->oSmarty->assign('VAL_DEADLINEON', date($dcl_info['DCL_DATE_FORMAT']));
				$this->oSmarty->assign('VAL_ESTSTARTON', date($dcl_info['DCL_DATE_FORMAT']));
				$this->oSmarty->assign('VAL_ESTENDON', date($dcl_info['DCL_DATE_FORMAT']));
			}
			else
			{
				$this->oSmarty->assign('VAL_DEADLINEON', '');
				$this->oSmarty->assign('VAL_ESTSTARTON', '');
				$this->oSmarty->assign('VAL_ESTENDON', '');
			}

			$this->oSmarty->assign('VAL_ESTHOURS', '');

			// If not editing, display project options (if any)
			$objPM = new ProjectMapModel();
			if (($jcn > 0 && $objPM->LoadByWO($jcn, 0) != -1) || ($g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK) && IsSet($_REQUEST['projectid'])))
			{
				$oProject = new ProjectsModel();

				if ($objPM->projectid > 0)
					$oProject->Load($objPM->projectid);
				else
					$oProject->Load((int)$_REQUEST['projectid']);

				$this->oSmarty->assign('VAL_PROJECT', $oProject->name);
				$this->oSmarty->assign('VAL_PROJECTS', (int)$_REQUEST['projectid']);
				$this->oSmarty->assign('TXT_WILLBEPARTOFPROJECT', sprintf(STR_WO_WILLBEPARTOFPROJECT, $oProject->name));
			}
		}
		elseif ($isCopy)
		{
			$objPM = new ProjectMapModel();
			$bAllSequencesInSameProject = ($jcn > 0 && $objPM->LoadByWO($jcn, 0) != -1);
			if ($bAllSequencesInSameProject || ($g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK) && IsSet($_REQUEST['projectid'])))
			{
				$oProject = new ProjectsModel();

				if ($objPM->projectid > 0)
					$oProject->Load($objPM->projectid);
				else
					$oProject->Load((int)$_REQUEST['projectid']);

				$this->oSmarty->assign('VAL_PROJECT', $oProject->name);
				$this->oSmarty->assign('VAL_PROJECTS', (int)$_REQUEST['projectid']);
				
				if ($bAllSequencesInSameProject)
					$this->oSmarty->assign('TXT_WILLBEPARTOFPROJECT', sprintf(STR_WO_WILLBEPARTOFPROJECT, $oProject->name));
			}
		}

		$this->oSmarty->assign('VAL_HIDEPROJECT', $isEdit);
		if ($isEdit || $isCopy)
		{
			$this->oSmarty->assign('VAL_REPORTED_VERSION', $oSource->reported_version_id);
			$this->oSmarty->assign('VAL_TARGETED_VERSION', $isCopy ? 0 : $oSource->targeted_version_id);
			$this->oSmarty->assign('VAL_FIXED_VERSION', $isCopy ? 0 : $oSource->fixed_version_id);
			$this->oSmarty->assign('VAL_SUMMARY', $oSource->summary);
			$this->oSmarty->assign('VAL_NOTES', $oSource->notes);
			$this->oSmarty->assign('VAL_DESCRIPTION', $oSource->description);
			$this->oSmarty->assign('VAL_ISPUBLIC', $oSource->is_public);
			$this->oSmarty->assign('VAL_RESPONSIBLE', $oSource->responsible);
			
			$this->oSmarty->assign('VAL_CONTACTID', $oSource->contact_id);
			if ($oSource->contact_id != '' && $oSource->contact_id > 0)
			{
				$aContact =& $oMeta->GetContact($oSource->contact_id);
				if (is_array($aContact) && count($aContact) > 1)
					$this->oSmarty->assign('VAL_CONTACTNAME', $aContact['name']);
				else
					$this->oSmarty->assign('VAL_CONTACTNAME', 'Unknown');
			}

			if ($oSource->responsible == $GLOBALS['DCLID'])
			{
				$this->oSmarty->assign('VAL_RESPONSIBLENAME', $GLOBALS['DCLNAME']);
			}
			else
			{
				$oPersonnel = new PersonnelModel();
				if ($oPersonnel->Load($oSource->responsible) == -1)
					$this->oSmarty->assign('VAL_RESPONSIBLENAME', $oPersonnel->short);
				else
					$this->oSmarty->assign('VAL_RESPONSIBLENAME', 'Unknown');
			}
		}
		elseif ($isTicket)
		{
			$this->oSmarty->assign('VAL_REPORTED_VERSION', '');
			$this->oSmarty->assign('VAL_SUMMARY', $oSource->summary);
			$this->oSmarty->assign('VAL_DESCRIPTION', $oSource->issue);

			$this->oSmarty->assign('VAL_CONTACTID', $oSource->contact_id);
			if ($oSource->contact_id != '' && $oSource->contact_id > 0)
			{
				$aContact =& $oMeta->GetContact($oSource->contact_id);
				if (is_array($aContact) && count($aContact) > 1)
					$this->oSmarty->assign('VAL_CONTACTNAME', $aContact['name']);
				else
					$this->oSmarty->assign('VAL_CONTACTNAME', 'Unknown');
			}

			$notes = 'Copied from ticket dcl://tickets/' . $oSource->ticketid;

			$this->oSmarty->assign('VAL_NOTES', $notes);
			$this->oSmarty->assign('VAL_ISPUBLIC', $oSource->is_public);
			$this->oSmarty->assign('VAL_RESPONSIBLE', $GLOBALS['DCLID']);
			$this->oSmarty->assign('VAL_RESPONSIBLENAME', $GLOBALS['DCLNAME']);
		}
		else
		{
			$this->oSmarty->assign('VAL_REPORTED_VERSION', '');
			$this->oSmarty->assign('VAL_CONTACTID', '');
			$this->oSmarty->assign('VAL_SUMMARY', '');
			$this->oSmarty->assign('VAL_NOTES', '');
			$this->oSmarty->assign('VAL_DESCRIPTION', '');
			$this->oSmarty->assign('VAL_ISPUBLIC', 'N');
			$this->oSmarty->assign('VAL_RESPONSIBLE', $GLOBALS['DCLID']);
			$this->oSmarty->assign('VAL_RESPONSIBLENAME', $GLOBALS['DCLNAME']);
			
			if (($iContactID = @DCL_Sanitize::ToInt($_REQUEST['contact_id'])) !== null && $iContactID > 0)
			{
				$aContact =& $oMeta->GetContact($iContactID);
				if (is_array($aContact) && count($aContact) > 1)
				{
					$this->oSmarty->assign('VAL_CONTACTID', $iContactID);
					$this->oSmarty->assign('VAL_CONTACTNAME', $aContact['name']);
				}
			}
		}

		$sAssignedContactID = @$this->oSmarty->get_template_vars('VAL_CONTACTID');
		if ($sAssignedContactID != '')
		{
			$oContact = new ContactModel();
			if ($oContact->Load(array('contact_id' => $sAssignedContactID)) != -1)
				$this->oSmarty->assign('VAL_CONTACT', sprintf('%s, %s', $oContact->last_name, $oContact->first_name));
		}

		$aOrgID = array();
		$aOrgName = array();
		if ($isEdit || $isTicket || $isCopy)
		{
			$oOrgs = new boOrg();

			if ($isEdit || $isCopy)
				$oOrgs->ListSelectedByWorkOrder($oSource->jcn, $oSource->seq);
			else
				$oOrgs->ListSelectedByTicket($oSource->ticketid);

			while ($oOrgs->oDB->next_record())
			{
				$aOrgID[] = $oOrgs->oDB->f(0);
				$aOrgName[] = $oOrgs->oDB->f(1);
			}
		}
		else
		{
			$iOrgID = @DCL_Sanitize::ToInt($_REQUEST['org_id']);
			if ($iOrgID === null && $g_oSession->Value('member_of_orgs') != '')
				$iOrgID = array_shift(split(',', $g_oSession->Value('member_of_orgs')));
			
			if ($iOrgID !== null && $iOrgID > 0)
			{
				$aOrg =& $oMeta->GetOrganization($iOrgID);
				if (is_array($aOrg) && count($aOrg) > 0)
				{
					$aOrgID[] = $iOrgID;
					$aOrgName[] = $aOrg['name'];
				}
			}
		}

		if (count($aOrgID) > 0)
		{
			$this->oSmarty->assign_by_ref('VAL_ORGID', $aOrgID);
			$this->oSmarty->assign_by_ref('VAL_ORGNAME', $aOrgName);
		}
		else
		{
			$this->oSmarty->assign('VAL_ORGID', '');
			$this->oSmarty->assign('VAL_ORGNAME', '');
		}

		$this->oSmarty->Render('htmlWorkOrderForm.tpl');
	}
}
