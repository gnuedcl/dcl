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
		$this->oSmarty =& CreateSmarty();
		$this->eState = DCL_FORM_ADD;
	}

	function Show($jcn = 0, $objWO = '', $objTck = '')
	{
		global $dcl_info, $g_oSec, $dcl_preferences;

		$isEdit = is_object($objWO);
		$isTicket = is_object($objTck);

		if ($isEdit && !$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_MODIFY, $objWO->jcn, $objWO->seq))
			return PrintPermissionDenied();
		else if ($isTicket && !$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_COPYTOWO, $objTck->ticketid))
			return PrintPermissionDenied();
		else if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ADD))
			return PrintPermissionDenied();

		$objJS = CreateObject('dcl.jsAttributesets');
		if (!$isEdit)
			$objJS->bActiveOnly = true;

		$objJS->bPriorities = true;
		$objJS->bSeverities = true;
		$objJS->bModules = true;
		$objJS->DisplayAttributeScript();

		$title = '';
		if ($isTicket)
		{
			$title = sprintf(STR_WO_TICKET, $objTck->ticketid);
			$obj = $objTck;
		}
		elseif ($isEdit)
		{
			$title = sprintf(STR_WO_EDITWO, $objWO->jcn, $objWO->seq);
			$obj = $objWO;
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

		if ($isEdit)
		{
			$this->oSmarty->assign('VAL_MENUACTION', 'boWorkorders.dbmodifyjcn');
			$this->oSmarty->assign('VAL_WOID', $obj->jcn);
			$this->oSmarty->assign('VAL_SEQ', $obj->seq);
		}
		else
		{
			$this->oSmarty->assign('VAL_MENUACTION', 'boWorkorders.dbnewjcn');

			if ($isTicket)
				$this->oSmarty->assign('VAL_TICKETID', $objTck->ticketid);

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

		$oMeta =& CreateObject('dcl.DCL_MetadataDisplay');
		if ($isEdit || $isTicket)
		{
			$oProduct =& CreateObject('dcl.dbProducts');
			$oProduct->Query('SELECT wosetid FROM products WHERE id=' . ($isTicket ? $objTck->product : $objWO->product));
			if ($oProduct->next_record())
				$this->oSmarty->assign('VAL_SETID', $oProduct->f(0));

			if ($isEdit)
			{
				$this->oSmarty->assign('VAL_SOURCE', $objWO->entity_source_id);
				$this->oSmarty->assign('VAL_PRODUCT', $objWO->product);
				$this->oSmarty->assign('VAL_MODULE', $objWO->module_id);
				$this->oSmarty->assign('VAL_TYPE', $objWO->wo_type_id);

				$this->oSmarty->assign('VAL_DEADLINEON', $objWO->deadlineon);
				$this->oSmarty->assign('VAL_ESTSTARTON', $objWO->eststarton);
				$this->oSmarty->assign('VAL_ESTENDON', $objWO->estendon);
				$this->oSmarty->assign('VAL_ESTHOURS', $objWO->esthours);
				$this->oSmarty->assign('VAL_SEVERITY', $objWO->severity);
				$this->oSmarty->assign('VAL_PRIORITY', $objWO->priority);
				$this->oSmarty->assign('VAL_CONTACTS', $objWO->contact_id);
			}
			else
			{
				$this->oSmarty->assign('VAL_SOURCE', $objTck->entity_source_id);
				$this->oSmarty->assign('VAL_PRODUCT', $objTck->product);
				$this->oSmarty->assign('VAL_MODULE', $objTck->module_id);
				
				$this->oSmarty->assign('VAL_TYPE', 0);
				$this->oSmarty->assign('VAL_SEVERITY', 0);
				$this->oSmarty->assign('VAL_PRIORITY', 0);
				$this->oSmarty->assign('VAL_CONTACTS', $objTck->contact_id);
			}
			
			$oTag =& CreateObject('dcl.dbEntityTag');
			if ($isTicket)
				$this->oSmarty->assign('VAL_TAGS', $oTag->getTagsForEntity(DCL_ENTITY_TICKET, $objTck->ticketid));
			else
				$this->oSmarty->assign('VAL_TAGS', $oTag->getTagsForEntity(DCL_ENTITY_WORKORDER, $objWO->jcn, $objWO->seq));
		}

		if (!$isEdit)
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
			$objPM = CreateObject('dcl.dbProjectmap');
			if (($jcn > 0 && $objPM->LoadByWO($jcn, 0) != -1) || ($g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK) && IsSet($_REQUEST['projectid'])))
			{
				$objDBPrj = CreateObject('dcl.dbProjects');

				if ($objPM->projectid > 0)
					$objDBPrj->Load($objPM->projectid);
				else
					$objDBPrj->Load((int)$_REQUEST['projectid']);

				$this->oSmarty->assign('VAL_PROJECT', $objDBPrj->name);
				$this->oSmarty->assign('VAL_PROJECTS', (int)$_REQUEST['projectid']);
				$this->oSmarty->assign('TXT_WILLBEPARTOFPROJECT', sprintf(STR_WO_WILLBEPARTOFPROJECT, $objDBPrj->name));
			}
		}

		$this->oSmarty->assign('VAL_HIDEPROJECT', $isEdit);
		if ($isEdit)
		{
			$this->oSmarty->assign('VAL_REVISION', $objWO->revision);
			$this->oSmarty->assign('VAL_SUMMARY', $objWO->summary);
			$this->oSmarty->assign('VAL_NOTES', $objWO->notes);
			$this->oSmarty->assign('VAL_DESCRIPTION', $objWO->description);
			$this->oSmarty->assign('VAL_ISPUBLIC', $objWO->is_public);
			$this->oSmarty->assign('VAL_RESPONSIBLE', $objWO->responsible);
			
			$this->oSmarty->assign('VAL_CONTACTID', $objWO->contact_id);
			if ($objWO->contact_id != '' && $objWO->contact_id > 0)
			{
				$aContact =& $oMeta->GetContact($objWO->contact_id);
				if (is_array($aContact) && count($aContact) > 1)
					$this->oSmarty->assign('VAL_CONTACTNAME', $aContact['name']);
				else
					$this->oSmarty->assign('VAL_CONTACTNAME', 'Unknown');
			}

			if ($objWO->responsible == $GLOBALS['DCLID'])
			{
				$this->oSmarty->assign('VAL_RESPONSIBLENAME', $GLOBALS['DCLNAME']);
			}
			else
			{
				$oPersonnel =& CreateObject('dcl.dbPersonnel');
				if ($oPersonnel->Load($objWO->responsible) == -1)
					$this->oSmarty->assign('VAL_RESPONSIBLENAME', $oPersonnel->short);
				else
					$this->oSmarty->assign('VAL_RESPONSIBLENAME', 'Unknown');
			}
		}
		elseif ($isTicket)
		{
			$this->oSmarty->assign('VAL_REVISION', '');
			$this->oSmarty->assign('VAL_SUMMARY', $objTck->summary);
			$this->oSmarty->assign('VAL_DESCRIPTION', $objTck->issue);

			$this->oSmarty->assign('VAL_CONTACTID', $objTck->contact_id);
			if ($objTck->contact_id != '' && $objTck->contact_id > 0)
			{
				$aContact =& $oMeta->GetContact($objTck->contact_id);
				if (is_array($aContact) && count($aContact) > 1)
					$this->oSmarty->assign('VAL_CONTACTNAME', $aContact['name']);
				else
					$this->oSmarty->assign('VAL_CONTACTNAME', 'Unknown');
			}

			$notes = 'Copied from ticket dcl://tickets/' . $objTck->ticketid;

			$this->oSmarty->assign('VAL_NOTES', $notes);
			$this->oSmarty->assign('VAL_ISPUBLIC', $objTck->is_public);
			$this->oSmarty->assign('VAL_RESPONSIBLE', $GLOBALS['DCLID']);
			$this->oSmarty->assign('VAL_RESPONSIBLENAME', $GLOBALS['DCLNAME']);
		}
		else
		{
			$this->oSmarty->assign('VAL_REVISION', '');
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
			$oContact =& CreateObject('dcl.dbContact');
			if ($oContact->Load(array('contact_id' => $sAssignedContactID)) != -1)
				$this->oSmarty->assign('VAL_CONTACT', sprintf('%s, %s', $oContact->last_name, $oContact->first_name));
		}

		$aOrgID = array();
		$aOrgName = array();
		if ($isEdit || $isTicket)
		{
			$oOrgs =& CreateObject('dcl.boOrg');

			if ($isEdit)
				$oOrgs->ListSelectedByWorkOrder($objWO->jcn, $objWO->seq);
			else
				$oOrgs->ListSelectedByTicket($objTck->ticketid);

			while ($oOrgs->oDB->next_record())
			{
				$aOrgID[] = $oOrgs->oDB->f(0);
				$aOrgName[] = $oOrgs->oDB->f(1);
			}
		}
		else
		{
			if (($iOrgID = @DCL_Sanitize::ToInt($_REQUEST['org_id'])) !== null && $iOrgID > 0)
			{
				$aOrg =& $oMeta->GetOrganization($iOrgID);
				if (is_array($aOrg) && count($aOrg) > 0)
				{
					$aOrgID[] = $iOrgID;
					$aOrgName[] = $aOrg['name'];
				}
			}
		}

		$this->oSmarty->assign_by_ref('VAL_ORGID', $aOrgID);
		$this->oSmarty->assign_by_ref('VAL_ORGNAME', $aOrgName);

		SmartyDisplay($this->oSmarty, 'htmlWorkOrderForm.tpl');
	}
}
?>
