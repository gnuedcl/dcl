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
	var $iWoid;
	var $iSeq;
	var $aOrgs;
	var $aContactOrgs;
	
	function boWatches()
	{
		$this->oMeta = null;
		$this->iWoid = 0;
		$this->iSeq = 0;
		$this->aOrgs = array();
		$this->aContactOrgs = array();
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
	}
	
	function CanReceiveNotification(&$obj, $iPersonnelID)
	{
		global $dcl_info, $g_oSession, $g_oSec;
		
		$bCanReceive = true;
		$oUR =& CreateObject('dcl.dbUserRole');
		$oUR->ListPermissions(DCL_ENTITY_WORKORDER, 0, 0, array(DCL_PERM_PUBLICONLY, DCL_PERM_VIEWACCOUNT));
		while ($oUR->next_record() && $bCanReceive)
		{
			if ($oUR->f(0) == DCL_PERM_PUBLICONLY)
			{
				if ($bCanReceive)
					$bCanReceive = ($obj->is_public == 'Y');
			}
			else if ($oUR->f(0) == DCL_PERM_VIEWACCOUNT)
			{
				if ($obj->jcn != $this->iWoid || $obj->seq != $this->iSeq)
				{
					$oWOA =& CreateObject('dcl.dbWorkOrderAccount');
					if ($oWOA->Load($obj->jcn, $obj->seq) != -1)
					{
						$this->iWoid = $obj->jcn;
						$this->iSeq = $obj->seq;
						$this->aOrgs = array();
						do
						{
							array_push($this->aOrgs, $oWOA->f(2));
						} while ($oWOA->next_record());
						
						$bCanReceive = (count($this->aOrgs) > 0);
					}
					else
						$bCanReceive = false;
				}
				
				if (!$bCanReceive)
					return false;
					
				$oDB = new dclDB;
				$sSQL = "SELECT OC.org_id FROM dcl_org_contact OC JOIN personnel P ON OC.contact_id = P.contact_id WHERE P.id = $iPersonnelID";
				if ($oDB->Query($sSQL) != -1)
				{
					$this->aContactOrgs[$iPersonnelID] = array();
					while ($oDB->next_record())
					{
						array_push($this->aContactOrgs[$iPersonnelID], $oDB->f(0));
					}
					
					if (count($this->aContactOrgs[$iPersonnelID]) > 0)
						$bCanReceive = (count(array_intersect($this->aOrgs, $this->aContactOrgs[$iPersonnelID])) > 0);
					else
						$bCanReceive = false;
				}
				else
					$bCanReceive = false;
			}
		}
		
		return $bCanReceive;
	}

	// obj is a dbWorkorder object and actions is a comma delimited list of actions to send for
	function sendNotification(&$obj, $actions, $bShowNotifyMsg = true)
	{
		global $dcl_info, $g_oSession, $g_oSec;

		if (!is_object($obj) || $actions == '' || $dcl_info['DCL_SMTP_ENABLED'] != 'Y')
			return;

		$oMail =& CreateObject('dcl.boSMTP');
		$oMail->isHtml = ($dcl_info['DCL_WO_NOTIFICATION_HTML'] == 'Y');

		$objWtch =& CreateObject('dcl.dbWatches');
		$query = "select distinct email_addr, whoid from personnel " . $objWtch->JoinKeyword . " dcl_contact_email ON personnel.contact_id=dcl_contact_email.contact_id AND dcl_contact_email.preferred = 'Y' ";
		$query .= $objWtch->JoinKeyword . ' watches ON id=whoid ';
		$query .= 'LEFT JOIN dcl_wo_account ON typeid = 6 AND wo_id = ' . $obj->jcn;
		$query .= ' AND dcl_wo_account.seq = ' . $obj->seq . ' AND whatid1 = account_id ';
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
				if (!isset($arrEmail[$objWtch->f(0)]))
				{
					if ($this->CanReceiveNotification($obj, $objWtch->f(1)))
						$arrEmail[$objWtch->f(0)] = 1;
				}
			}
		}

		if ('Y' == @DCL_Sanitize::ToYN($_REQUEST['copy_me_on_notification']) && $g_oSession->Value('USEREMAIL') != '')
		{
			if (!isset($arrEmail[$g_oSession->Value('USEREMAIL')]))
			{
				if ($this->CanReceiveNotification($obj, $GLOBALS['DCLID']))
					$arrEmail[$g_oSession->Value('USEREMAIL')] = 1;
			}
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

	function CanReceiveTicketNotification(&$obj, $iPersonnelID)
	{
		global $dcl_info, $g_oSession, $g_oSec;
		
		$bCanReceive = true;
		$oUR =& CreateObject('dcl.dbUserRole');
		$oUR->ListPermissions(DCL_ENTITY_WORKORDER, 0, 0, array(DCL_PERM_PUBLICONLY, DCL_PERM_VIEWACCOUNT));
		while ($oUR->next_record() && $bCanReceive)
		{
			if ($oUR->f(0) == DCL_PERM_PUBLICONLY)
			{
				if ($bCanReceive)
					$bCanReceive = ($obj->is_public == 'Y');
			}
			else if ($oUR->f(0) == DCL_PERM_VIEWACCOUNT)
			{
				if (!isset($obj->account) || $obj->account === null || $obj->account < 1)
					return false;
					
				$oDB = new dclDB;
				$sSQL = "SELECT OC.org_id FROM dcl_org_contact OC JOIN personnel P ON OC.contact_id = P.contact_id WHERE P.id = $iPersonnelID";
				if ($oDB->Query($sSQL) != -1)
				{
					$this->aContactOrgs[$iPersonnelID] = array();
					while ($oDB->next_record())
					{
						array_push($this->aContactOrgs[$iPersonnelID], $oDB->f(0));
					}
					
					if (count($this->aContactOrgs[$iPersonnelID]) > 0)
						$bCanReceive = in_array($obj->account, $this->aContactOrgs[$iPersonnelID]);
					else
						$bCanReceive = false;
				}
				else
					$bCanReceive = false;
			}
		}
		
		return $bCanReceive;
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
		$query = "select distinct email_addr, whoid from personnel " . $objWtch->JoinKeyword . " dcl_contact_email ON personnel.contact_id=dcl_contact_email.contact_id AND dcl_contact_email.preferred = 'Y' ";
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
				if (!isset($arrEmail[$objWtch->f(0)]))
				{
					if ($this->CanReceiveTicketNotification($obj, $objWtch->f(1)))
						$arrEmail[$objWtch->f(0)] = 1;
				}
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
			if (!in_array($g_oSession->Value('USEREMAIL'), $oMail->to))
			{
				if ($this->CanReceiveTicketNotification($obj, $GLOBALS['DCLID']))
				{
					$oMail->to[] = '<' . $g_oSession->Value('USEREMAIL') . '>';
					if ($toAddr != '')
						$toAddr .= ', ';
					$toAddr .= $g_oSession->Value('USEREMAIL');
				}
			}
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
