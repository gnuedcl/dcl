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

LoadStringResource('bo');
LoadStringResource('wo');
LoadStringResource('tc');
LoadStringResource('tck');

class boWatches
{
	var $oMeta;
	
	function boWatches()
	{
		$this->oMeta = null;
	}
	
	function showall()
	{
		commonHeader();
		$obj =& CreateObject('dcl.htmlWatches');
		$obj->PrintMine();
	}

	function addWorkorder()
	{
		$this->add(1);
	}

	function addTicket()
	{
		$this->add(4);
	}

	function add($typeid = null)
	{
		commonHeader();
		
		if ($typeid === null)
		{
			if (($iTypeID = @DCL_Sanitize::ToInt($_REQUEST['typeid'])) === null)
			{
				trigger_error('Data sanitize failed.');
				return;
			}
		}
		else
		{
			$iTypeID = (int)$typeid;
		}

		if (($iWhatID1 = @DCL_Sanitize::ToInt($_REQUEST['whatid1'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$iWhatID2 = null;
		if ($typeid == 3 && ($iWhatID2 = @DCL_Sanitize::ToInt($_REQUEST['whatid2'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$obj =& CreateObject('dcl.dbWatches');
		$query = sprintf('SELECT * FROM watches WHERE typeid=%d AND whoid=%d AND whatid1=%d', $iTypeID, $GLOBALS['DCLID'], $iWhatID1);
		if ($iWhatID2 !== null)
			$query .= sprintf(' AND whatid2=%d', $iWhatID2);

		$objHTML =& CreateObject('dcl.htmlWatches');
		if ($obj->Query($query) != -1)
		{
			if ($obj->next_record())
			{
				// He said they've already got one!
				$obj->GetRow();
				$objHTML->ShowEntryForm($obj, $this->getWatchDescription($obj));
				print('<p>');
				$objHTML->PrintMine();
				return;
			}
		}

		$objHTML->ShowEntryForm();
		print('<p>');
		$objHTML->PrintMine();
	}

	function dbadd()
	{
		commonHeader();
		
		$obj =& CreateObject('dcl.dbWatches');
		$obj->InitFromGlobals();
		$obj->Add();

		$objHTML =& CreateObject('dcl.htmlWatches');
		$objHTML->PrintMine();
	}

	function modify()
	{
		commonHeader();
		
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['watchid'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$obj =& CreateObject('dcl.dbWatches');
		if ($obj->Load($iID) == -1)
			return;
			
		if ($obj->whoid != $GLOBALS['DCLID'])
			return PrintPermissionDenied();
			
		$objHTML =& CreateObject('dcl.htmlWatches');
		$objHTML->ShowEntryForm($obj, $this->getWatchDescription($obj));
	}

	function dbmodify()
	{
		commonHeader();
		
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['watchid'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$obj =& CreateObject('dcl.dbWatches');
		if ($obj->Load($iID) == -1)
			return;
			
		if ($obj->whoid != $GLOBALS['DCLID'])
			return PrintPermissionDenied();

		$obj->InitFromGlobals();
		$obj->Edit();
		
		$objHTML =& CreateObject('dcl.htmlWatches');
		$objHTML->PrintMine();
	}
	function delete()
	{
		commonHeader();
		
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['watchid'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$obj =& CreateObject('dcl.dbWatches');
		if ($obj->Load($iID) == -1)
			return;
			
		if ($obj->whoid != $GLOBALS['DCLID'])
			return PrintPermissionDenied();

		ShowDeleteYesNo('Watch', 'boWatches.dbdelete', $obj->watchid, $this->getWatchDescription($obj));
	}

	function dbdelete()
	{
		commonHeader();
		
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$obj =& CreateObject('dcl.dbWatches');
		if ($obj->Load($iID) != -1)
		{
			$obj->Delete();
			trigger_error(STR_BO_DELETED, E_USER_NOTICE);
		}

		$objHTML =& CreateObject('dcl.htmlWatches');
		$objHTML->PrintMine();
	}

	function getWatchDescription($obj)
	{
		if (!is_object($obj))
			return '';

		if ($this->oMeta == null)
			$this->oMeta =& CreateObject('dcl.DCL_MetadataDisplay');

		$objW =& CreateObject('dcl.dbWorkorders');
		$objT =& CreateObject('dcl.dbTickets');

		$summary = '';
		switch ($obj->typeid)
		{
			case 1:
			case 4:
				$summary = $this->oMeta->GetProduct($obj->whatid1);
				break;
			case 2:
				$summary = $this->oMeta->GetProject($obj->whatid1);
				break;
			case 3:
				if ($obj->whatid2 > 0)
				{
					$objW->Load($obj->whatid1, $obj->whatid2);
					$summary = sprintf('[%d-%d] %s', $obj->whatid1, $obj->whatid2, $objW->summary);
				}
				else
					$summary = sprintf(STR_BO_WTCHALLSEQ, $obj->whatid1);
				break;
			case 5:
				$objT->Load($obj->whatid1);
				$summary = sprintf('[%d] %s', $obj->whatid1, $objT->summary);
				break;
			default:
				$summary = STR_BO_WTCHSUMMARYERR;
				break;
		}

		return sprintf('(%s %s) %s', $obj->arrTypeid[$obj->typeid], $obj->arrActions[$obj->actions], $summary);
	}
	
	function GetWorkOrderNotificationBody(&$obj, $bIsHTML = true, &$sResponsible, &$sStatusName)
	{
		global $dcl_info, $g_oSession;
		
		$t =& CreateSmarty();
		$t->assign_by_ref('obj', $obj);

		$oTC =& CreateObject('dcl.dbTimeCards');
		$t->assign('VAL_TIMECARDS', $oTC->GetTimeCardsArray($obj->jcn, $obj->seq));
		
		return SmartyFetch($t, $dcl_info['DCL_WO_EMAIL_TEMPLATE'], 'custom');
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$t =& CreateObject('dcl.DCLTemplate');
		$t->set_file(array('hForm' => DCL_ROOT . 'templates/custom/' . $dcl_info['DCL_WO_EMAIL_TEMPLATE']));
		$t->set_block('hForm', 'timecards', 'hTimecards');
		$t->set_var('hTimecards');

		$t->set_var('URL_DETAIL', $dcl_info['DCL_ROOT'] . 'main.php?menuAction=boWorkorders.viewjcn&jcn=' . $obj->jcn . '&seq=' . $obj->seq);

		$t->set_var('TXT_RESPONSIBLE', STR_WO_RESPONSIBLE);
		$t->set_var('TXT_PRIORITY', STR_WO_PRIORITY);
		$t->set_var('TXT_SEVERITY', STR_WO_SEVERITY);
		$t->set_var('TXT_DEADLINE', STR_WO_DEADLINE);
		$t->set_var('TXT_PRODUCT', STR_WO_PRODUCT);
		$t->set_var('TXT_VERSION', STR_WO_REVISION);
		$t->set_var('TXT_ESTSTART', STR_WO_ESTSTART);
		$t->set_var('TXT_ACTSTART', STR_WO_START);
		$t->set_var('TXT_ESTEND', STR_WO_ESTEND);
		$t->set_var('TXT_ACTEND', STR_WO_END);
		$t->set_var('TXT_ESTHOURS', STR_WO_ESTHOURS);
		$t->set_var('TXT_ACTHOURS', STR_WO_ACTHOURS);
		$t->set_var('TXT_ETCHOURS', STR_WO_ETCHOURS);
		$t->set_var('TXT_OPENEDBY', STR_WO_OPENBY);
		$t->set_var('TXT_CLOSEDBY', STR_WO_CLOSEBY);
		$t->set_var('TXT_STATUS', STR_WO_STATUS);
		$t->set_var('TXT_LASTACTION', STR_WO_LASTACTION);
		$t->set_var('TXT_ACCOUNT', STR_WO_ACCOUNT);
		$t->set_var('TXT_CONTACT', STR_WO_CONTACT);
		$t->set_var('TXT_CONTACTPHONE', STR_WO_CONTACTPHONE);
		$t->set_var('TXT_NOTES', STR_WO_NOTES);
		$t->set_var('TXT_DESCRIPTION', STR_WO_DESCRIPTION);
		$t->set_var('TXT_MODULE', STR_CMMN_MODULE);
		$t->set_var('TXT_PROJECT', STR_WO_PROJECT);
		$t->set_var('TXT_TYPE', STR_WO_TYPE);

		$t->set_var('VAL_WOID', $obj->jcn);
		$t->set_var('VAL_SEQ', $obj->seq);
		$t->set_var('VAL_DEADLINE', $obj->deadlineon);
		$t->set_var('VAL_ESTSTART', $obj->eststarton);
		$t->set_var('VAL_ACTSTART', $obj->starton);
		$t->set_var('VAL_ESTEND', $obj->estendon);
		$t->set_var('VAL_ACTEND', $obj->closedon);
		$t->set_var('VAL_ESTHOURS', $obj->esthours);
		$t->set_var('VAL_ACTHOURS', $obj->totalhours);
		$t->set_var('VAL_ETCHOURS', $obj->etchours);
		$t->set_var('VAL_OPENEDON', $obj->createdon);
		$t->set_var('VAL_STATUSON', $obj->statuson);
		$t->set_var('VAL_LASTACTION', $obj->lastactionon);
		$t->set_var('VAL_CLOSEDON', $obj->closedon);

		if ($this->oMeta == null)
			$this->oMeta =& CreateObject('dcl.DCL_MetadataDisplay');
			
		$aContact = $this->oMeta->GetContact($obj->contact_id);
		if ($bIsHTML)
		{
			$t->set_var('VAL_SUMMARY', htmlspecialchars($obj->summary));
			$t->set_var('VAL_VERSION', htmlspecialchars($obj->revision));
			$t->set_var('VAL_CONTACT', htmlspecialchars($aContact['name']));
			$t->set_var('VAL_CONTACTPHONE', htmlspecialchars($aContact['phone']));
			$t->set_var('VAL_NOTES', nl2br(htmlspecialchars($obj->notes)));
			$t->set_var('VAL_DESCRIPTION', nl2br(htmlspecialchars($obj->description)));
		}
		else
		{
			$t->set_var('VAL_SUMMARY', $obj->summary);
			$t->set_var('VAL_VERSION', $obj->revision);
			$t->set_var('VAL_CONTACT', $aContact['name']);
			$t->set_var('VAL_CONTACTPHONE', $aContact['phone']);
			$t->set_var('VAL_NOTES', $obj->notes);
			$t->set_var('VAL_DESCRIPTION', $obj->description);
		}

		$t->set_var('VAL_OPENEDBY', $this->oMeta->GetPersonnel($obj->createby));
		$t->set_var('VAL_TYPE', $this->oMeta->GetWorkOrderType($obj->wo_type_id));
		$t->set_var('VAL_PRIORITY', $this->oMeta->GetPriority($obj->priority));
		$t->set_var('VAL_SEVERITY', $this->oMeta->GetSeverity($obj->severity));
		$t->set_var('VAL_PRODUCT', $this->oMeta->GetProduct($obj->product));
		$t->set_var('VAL_MODULE', $this->oMeta->GetModule($obj->module_id));

		$sResponsible = $this->oMeta->GetPersonnel($obj->responsible);
		$t->set_var('VAL_RESPONSIBLE', $sResponsible);

		$sStatusName = $this->oMeta->GetStatus($obj->status);
		$t->set_var('VAL_STATUS', $sStatusName);

		if ($this->oMeta->oStatus->GetStatusType($obj->status) == 2)
			$t->set_var('VAL_CLOSEDBY', $this->oMeta->GetPersonnel($obj->closedby));
		else
			$t->set_var('VAL_CLOSEDBY', '');

		$objAccount =& CreateObject('dcl.dbWorkOrderAccount');
		if ($objAccount->Load($obj->jcn, $obj->seq) != -1)
		{
			$sAccounts = '';
			do
			{
				$objAccount->GetRow();
				$sAccounts .= $objAccount->account_name . '; ';
			}
			while ($objAccount->next_record());

			$t->set_var('VAL_ACCOUNT', $sAccounts);
		}
		else
			$t->set_var('VAL_ACCOUNT', '');

		$projectID = -1;
		if ($obj->IsInAProject())
		{
			$oPM =& CreateObject('dcl.dbProjectmap');
			$oPM->LoadByWO($obj->jcn, $obj->seq);

			$t->set_var('VAL_PROJECT', $this->oMeta->GetProject($oPM->projectid));
			$projectID = $oPM->projectid;
		}
		else
			$t->set_var('VAL_PROJECT', '');

		$oTC =& CreateObject('dcl.dbTimeCards');
		$oSmarty->assign('VAL_TIMECARDS', $oTC->GetTimeCardsArray($obj->jcn, $obj->seq));
		
		// Slap on the time cards!
		$objTimeCard =& CreateObject('dcl.dbTimeCards');
		if ($objTimeCard->GetTimeCards($obj->jcn, $obj->seq) != -1)
		{
			$t->set_var('TXT_TCSTATUS', STR_TC_STATUS);
			$t->set_var('TXT_TCVERSION', STR_TC_VERSION);
			$t->set_var('TXT_TCACTION', STR_TC_ACTION);
			$t->set_var('TXT_TCHOURS', STR_TC_HOURS);
			$t->set_var('TXT_TCDESCRIPTION', STR_TC_DESCRIPTION);
			$t->set_var('TXT_TCREASSIGN', STR_CMMN_REASSIGN);
			$t->set_var('TXT_TCTO', STR_CMMN_TO);

			$objAction =& CreateObject('dcl.dbActions');
			while ($objTimeCard->next_record())
			{
				$objTimeCard->GetRow();

				$t->set_var('VAL_TCACTIONON', $objTimeCard->actionon);
				$t->set_var('VAL_TCSUMMARY', $objTimeCard->summary);
				$t->set_var('VAL_TCVERSION', $objTimeCard->revision);
				$t->set_var('VAL_TCHOURS', $objTimeCard->hours);
				$t->set_var('VAL_TCDESCRIPTION', $objTimeCard->description);

				$t->set_var('VAL_TCACTIONBY', $this->oMeta->GetPersonnel($objTimeCard->actionby));
				$t->set_var('VAL_TCSTATUS', $this->oMeta->GetStatus($objTimeCard->status));
				$t->set_var('VAL_TCACTION', $this->oMeta->GetAction($objTimeCard->action));

				if ($objTimeCard->reassign_from_id > 0)
					$t->set_var('VAL_TCREASSIGN', $this->oMeta->GetPersonnel($objTimeCard->reassign_from_id));
				else
					$t->set_var('VAL_TCREASSIGN', '');

				if ($objTimeCard->reassign_to_id > 0)
					$t->set_var('VAL_TCTO', $this->oMeta->GetPersonnel($objTimeCard->reassign_to_id));
				else
					$t->set_var('VAL_TCTO', '');

				$t->parse('hTimecards', 'timecards', true);
			}
		}
		
		return $t->parse('out', 'hForm');
	}

	// obj is a dbWorkorder object and actions is a comma delimited list of actions to send for
	function sendNotification(&$obj, $actions, $bShowNotifyMsg = true)
	{
		global $dcl_info, $g_oSession;

		if (!is_object($obj) || $actions == '' || $dcl_info['DCL_SMTP_ENABLED'] != 'Y')
			return;

		$oMail =& CreateObject('dcl.boSMTP');
		$oMail->isHtml = ($dcl_info['DCL_WO_NOTIFICATION_HTML'] == 'Y');

		$objWtch =& CreateObject('dcl.dbWatches');
		$query = "select distinct email_addr from personnel " . $objWtch->JoinKeyword . " dcl_contact_email ON personnel.contact_id=dcl_contact_email.contact_id AND dcl_contact_email.preferred = 'Y' ";
		$query .= $objWtch->JoinKeyword . ' watches ON id=whoid ';
		$query .= 'LEFT JOIN dcl_wo_account ON typeid = 6 AND wo_id = ' . $obj->jcn;
		$query .= ' AND dcl_wo_account.seq = ' . $obj->seq . ' AND whatid1 = account_id ';;
		$query .= "where id = whoid AND actions in ($actions) and (";
		$query .= sprintf('(typeid=1 AND whatid1=%d)', $obj->product);
		
		if ($obj->IsInAProject())
		{
			$oPM =& CreateObject('dcl.dbProjectmap');
			$oPM->LoadByWO($obj->jcn, $obj->seq);
			$query .= sprintf(' or (typeid=2 and whatid1=%d)', $oPM->projectid);
		}
		
		$query .= sprintf(' or (typeid=3 and whatid1=%d and whatid2 in (0,%d))', $obj->jcn, $obj->seq);
		$query .= ' or (typeid = 6 and whatid1 = account_id)';

		$query .= sprintf(') AND whoid != %d', $GLOBALS['DCLID']);
		$query .= " AND active = 'Y'";

		$arrEmail = array();
		$mailFrom = '';
		if ($g_oSession->Value('USEREMAIL') != '')
			$mailFrom = '<' . $g_oSession->Value('USEREMAIL') . '>';

		if ($this->oMeta == null)
			$this->oMeta =& CreateObject('dcl.DCL_MetadataDisplay');
			
		if ($obj->responsible != $GLOBALS['DCLID'] && $this->oMeta->GetPersonnel($obj->responsible) != '' && $this->oMeta->oPersonnel->active == 'Y')
		{
			$aContact = $this->oMeta->GetContact($this->oMeta->oPersonnel->contact_id);
			if (IsSet($aContact['email']) && !IsSet($arrEmail[$aContact['email']]))
				$arrEmail[$aContact['email']] = 1;
		}
		
		if ($obj->createby != $GLOBALS['DCLID'] && $this->oMeta->GetPersonnel($obj->createby) != '' && $this->oMeta->oPersonnel->active == 'Y')
		{
			$aContact = $this->oMeta->GetContact($this->oMeta->oPersonnel->contact_id);
			if (IsSet($aContact['email']) && !IsSet($arrEmail[$aContact['email']]))
				$arrEmail[$aContact['email']] = 1;
		}
		
		if ($objWtch->Query($query) != -1)
		{
			while ($objWtch->next_record())
			{
				$arrEmail[$objWtch->f(0)] = 1;
			}
		}

		if ('Y' == @DCL_Sanitize::ToYN($_REQUEST['copy_me_on_notification']) && $g_oSession->Value('USEREMAIL') != '')
		{
			$arrEmail[$g_oSession->Value('USEREMAIL')] = 1;
		}
		
		if (count($arrEmail) == 0)
			return;

		// Here we go!
		$toAddr = '';
		if ($this->oMeta->GetPriority($obj->priority) != '' && $this->oMeta->oPriority->weight == 1)
			$oMail->AddHeader('X-Priority: 1');

		$oMail->from = $mailFrom;

		$sResponsible = '';
		$sStatusName = '';
		$oMail->body = $this->GetWorkOrderNotificationBody($obj, $oMail->isHtml, $sResponsible, $sStatusName);
		$oMail->subject = sprintf('[%s %d-%d] [%s] [%s] %s', STR_WO_JCN, $obj->jcn, $obj->seq, $this->oMeta->GetPersonnel($obj->responsible), $this->oMeta->GetStatus($obj->status), $obj->summary);

		$oMail->to = array();
		while (list($email, $junk) = each($arrEmail))
		{
			$oMail->to[] = '<' . $email . '>';
			if ($toAddr != '')
				$toAddr .= ', ';
			$toAddr .= $email;
		}
		
		if (count($oMail->to) < 1)
			return;

		$bSuccess = $oMail->Send();

		if ($bShowNotifyMsg && $toAddr != '')
		{
			if ($bSuccess)
				trigger_error(sprintf(STR_BO_MAILSENT, $toAddr), E_USER_NOTICE);
			else
				trigger_error('Could not send email notification.', E_USER_ERROR);
		}
	}

	function sendTicketNotification($obj, $actions)
	{
		global $dcl_info, $g_oSession;

		if ($dcl_info['DCL_SMTP_ENABLED'] != 'Y' || !is_object($obj))
			return;

		$oMail =& CreateObject('dcl.boSMTP');
		$oMail->isHtml = ($dcl_info['DCL_TCK_NOTIFICATION_HTML'] == 'Y');

		$t =& CreateObject('dcl.DCLTemplate');
		$t->set_file(array('hForm' => DCL_ROOT . 'templates/custom/' . $dcl_info['DCL_TCK_EMAIL_TEMPLATE']));
		$t->set_block('hForm', 'resolutions', 'hResolutions');
		$t->set_var('hResolutions');

		$t->set_var('URL_DETAIL', $dcl_info['DCL_ROOT'] . 'main.php?menuAction=boTickets.view&ticketid=' . $obj->ticketid);

		$t->set_var('TXT_TICKET', STR_TCK_TICKET);
		$t->set_var('TXT_OPENEDBY', STR_TCK_OPENEDBY);
		$t->set_var('TXT_CLOSEDBY', STR_TCK_CLOSEDBY);
		$t->set_var('TXT_CLOSEDON', STR_TCK_CLOSEDON);
		$t->set_var('TXT_LASTACTION', STR_TCK_LASTACTIONON);
		$t->set_var('TXT_RESPONSIBLE', STR_TCK_RESPONSIBLE);
		$t->set_var('TXT_STATUS', STR_TCK_STATUS);
		$t->set_var('TXT_PRIORITY', STR_TCK_PRIORITY);
		$t->set_var('TXT_TYPE', STR_TCK_TYPE);
		$t->set_var('TXT_PRODUCT', STR_TCK_PRODUCT);
		$t->set_var('TXT_MODULE', STR_CMMN_MODULE);
		$t->set_var('TXT_VERSION', STR_TCK_VERSION);
		$t->set_var('TXT_ACCOUNT', STR_TCK_ACCOUNT);
		$t->set_var('TXT_CONTACT', STR_TCK_CONTACT);
		$t->set_var('TXT_CONTACTPHONE', STR_TCK_CONTACTPHONE);
		$t->set_var('TXT_ISSUE', STR_TCK_ISSUE);
		$t->set_var('TXT_RESOLUTION', STR_TCK_RESOLUTION);
		$t->set_var('TXT_TIME', STR_TCK_APPROXTIME);
		
		if ($this->oMeta == null)
			$this->oMeta =& CreateObject('dcl.DCL_MetadataDisplay');
			
		$aContact = $this->oMeta->GetContact($obj->contact_id);

		if ($oMail->isHtml)
		{
			$t->set_var('VAL_VERSION', htmlspecialchars($obj->version));
			$t->set_var('VAL_CONTACT', htmlspecialchars($aContact['name']));
			$t->set_var('VAL_CONTACTPHONE', htmlspecialchars($aContact['phone']));
			$t->set_var('VAL_ISSUE', nl2br(htmlspecialchars($obj->issue)));
			$t->set_var('VAL_SUMMARY', htmlspecialchars($obj->summary));
		}
		else
		{
			$t->set_var('VAL_VERSION', $obj->version);
			$t->set_var('VAL_CONTACT', $obj->contact);
			$t->set_var('VAL_CONTACTPHONE', $obj->contactphone);
			$t->set_var('VAL_ISSUE', $obj->issue);
			$t->set_var('VAL_SUMMARY', $obj->summary);
		}

		$t->set_var('VAL_TICKETID', $obj->ticketid);
		$t->set_var('VAL_STATUSON', $obj->statuson);
		$t->set_var('VAL_OPENEDON', $obj->createdon);
		$t->set_var('VAL_CLOSEDON', $obj->closedon);
		$t->set_var('VAL_LASTACTION', $obj->lastactionon);
		$t->set_var('VAL_TIME', $obj->GetHoursText());

		if ($this->oMeta == null)
			$this->oMeta =& CreateObject('dcl.DCL_MetadataDisplay');
			
		$t->set_var('VAL_OPENEDBY', $this->oMeta->GetPersonnel($obj->createdby));
		$t->set_var('VAL_CLOSEDBY', $this->oMeta->GetPersonnel($obj->closedby));
		$t->set_var('VAL_PRIORITY', $this->oMeta->GetPriority($obj->priority));
		$t->set_var('VAL_TYPE', $this->oMeta->GetSeverity($obj->type));
		$t->set_var('VAL_PRODUCT', $this->oMeta->GetProduct($obj->product));
		$t->set_var('VAL_MODULE', $this->oMeta->GetModule($obj->module_id));

		$sResponsible = $this->oMeta->GetPersonnel($obj->responsible);
		$t->set_var('VAL_RESPONSIBLE', $sResponsible);

		$sStatusName = $this->oMeta->GetStatus($obj->status);
		$t->set_var('VAL_STATUS', $sStatusName);

		$aOrg = $this->oMeta->GetOrganization($obj->account);
		$t->set_var('VAL_ACCOUNT', $aOrg['name']);

		// Add the resolutions
		$objT =& CreateObject('dcl.dbTicketresolutions');
		if ($objT->GetResolutions($obj->ticketid) != -1)
		{
			while ($objT->next_record())
			{
				$objT->GetRow();

				$t->set_var('VAL_LOGGEDBY', $this->oMeta->GetPersonnel($objT->loggedby));
				$t->set_var('VAL_LOGGEDON', $objT->loggedon);
				$t->set_var('VAL_RESSTATUS', $this->oMeta->GetStatus($objT->status));
				$t->set_var('VAL_RESTIME', $objT->GetHoursText());

				if ($oMail->isHtml)
					$t->set_var('VAL_RESOLUTION', nl2br(htmlspecialchars($objT->resolution)));
				else
					$t->set_var('VAL_RESOLUTION', $objT->resolution);

				$t->parse('hResolutions', 'resolutions', true);
			}
		}

		// Got the message constructed, so send it!
		$objWtch =& CreateObject('dcl.dbWatches');
		$query = "select distinct email_addr from personnel " . $objWtch->JoinKeyword . " dcl_contact_email ON personnel.contact_id=dcl_contact_email.contact_id AND dcl_contact_email.preferred = 'Y' ";
		$query .= $objWtch->JoinKeyword . ' watches ON id=whoid ';
		$query .= "where id = whoid AND actions in ($actions) and (";
		$query .= sprintf('(typeid=4 AND whatid1=%d)', $obj->product);
		$query .= sprintf(' or (typeid=5 and whatid1=%d)', $obj->ticketid);
		
		if ($obj->account > 0)
			$query .= " or (typeid = 7 and whatid1 = {$obj->account})";

		$query .= sprintf(') AND whoid != %d', $GLOBALS['DCLID']);
		$query .= " AND active = 'Y'";
		
		$arrEmail = array();
		$mailFrom = '';
		if ($g_oSession->Value('USEREMAIL') != '')
			$mailFrom = '<' . $g_oSession->Value('USEREMAIL') . '>';

		if ($this->oMeta == null)
			$this->oMeta =& CreateObject('dcl.DCL_MetadataDisplay');
			
		if ($obj->responsible != $GLOBALS['DCLID'] && $this->oMeta->GetPersonnel($obj->responsible) != '' && $this->oMeta->oPersonnel->active == 'Y')
		{
			$aContact = $this->oMeta->GetContact($this->oMeta->oPersonnel->contact_id);
			if (IsSet($aContact['email']) && !IsSet($arrEmail[$aContact['email']]))
				$arrEmail[$aContact['email']] = 1;
		}
		
		if ($obj->createdby != $GLOBALS['DCLID'] && $this->oMeta->GetPersonnel($obj->createdby) != '' && $this->oMeta->oPersonnel->active == 'Y')
		{
			$aContact = $this->oMeta->GetContact($this->oMeta->oPersonnel->contact_id);
			if (IsSet($aContact['email']) && !IsSet($arrEmail[$aContact['email']]))
				$arrEmail[$aContact['email']] = 1;
		}

		if ($objWtch->Query($query) != -1)
		{
			while ($objWtch->next_record())
			{
				$arrEmail[$objWtch->f(0)] = 1;
			}
		}
		// Here we go!
		$toAddr = '';
		if ($this->oMeta->oPriority->weight == 1)
			$oMail->AddHeader('X-Priority: 1');

		$oMail->from = $mailFrom;
		$oMail->subject = sprintf('[%s %d] [%s] [%s] %s', STR_TCK_TICKET, $obj->ticketid, $sResponsible, $sStatusName, $obj->summary);
		$oMail->body = $t->parse('out', 'hForm');
		$oMail->to = array();
		while (list($email, $junk) = each($arrEmail))
		{
			$oMail->to[] = '<' . $email . '>';
			if ($toAddr != '')
				$toAddr .= ', ';
			$toAddr .= $email;
		}

		if ('Y' == @DCL_Sanitize::ToYN($_REQUEST['copy_me_on_notification']) && $g_oSession->Value('USEREMAIL') != '')
		{
			$oMail->to[] = '<' . $g_oSession->Value('USEREMAIL') . '>';
			if ($toAddr != '')
				$toAddr .= ', ';
			$toAddr .= $g_oSession->Value('USEREMAIL');
		}
		
		if (count($oMail->to) < 1)
			return;

		$bSuccess = $oMail->Send();

		if ($bShowNotifyMsg && $toAddr != '')
		{
			if ($bSuccess)
				trigger_error(sprintf(STR_BO_MAILSENT, $toAddr), E_USER_NOTICE);
			else
				trigger_error('Could not send email notification.', E_USER_ERROR);
		}
	}

	function showmy()
	{
		commonHeader();
		$obj =& CreateObject('dcl.htmlWatches');
		$obj->my(0);
	}
}
?>
