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
		$obj = new htmlWatches();
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
				throw new InvalidDataException();
			}
		}
		else
		{
			$iTypeID = (int)$typeid;
		}

		if (($iWhatID1 = @DCL_Sanitize::ToInt($_REQUEST['whatid1'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$iWhatID2 = null;
		if ($typeid == 3 && ($iWhatID2 = @DCL_Sanitize::ToInt($_REQUEST['whatid2'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$obj = new dbWatches();
		$query = sprintf('SELECT * FROM watches WHERE typeid=%d AND whoid=%d AND whatid1=%d', $iTypeID, $GLOBALS['DCLID'], $iWhatID1);
		if ($iWhatID2 !== null)
			$query .= sprintf(' AND whatid2=%d', $iWhatID2);

		$objHTML = new htmlWatches();
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
		
		$obj = new dbWatches();
		$obj->InitFromGlobals();
		$obj->Add();

		$objHTML = new htmlWatches();
		$objHTML->PrintMine();
	}

	function modify()
	{
		commonHeader();
		
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['watchid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$obj = new dbWatches();
		if ($obj->Load($iID) == -1)
			return;
			
		if ($obj->whoid != $GLOBALS['DCLID'])
			throw new PermissionDeniedException();
			
		$objHTML = new htmlWatches();
		$objHTML->ShowEntryForm($obj, $this->getWatchDescription($obj));
	}

	function dbmodify()
	{
		commonHeader();
		
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['watchid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$obj = new dbWatches();
		if ($obj->Load($iID) == -1)
			return;
			
		if ($obj->whoid != $GLOBALS['DCLID'])
			throw new PermissionDeniedException();

		$obj->InitFromGlobals();
		$obj->Edit();
		
		$objHTML = new htmlWatches();
		$objHTML->PrintMine();
	}
	function delete()
	{
		commonHeader();
		
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['watchid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$obj = new dbWatches();
		if ($obj->Load($iID) == -1)
			return;
			
		if ($obj->whoid != $GLOBALS['DCLID'])
			throw new PermissionDeniedException();

		ShowDeleteYesNo('Watch', 'boWatches.dbdelete', $obj->watchid, $this->getWatchDescription($obj));
	}

	function dbdelete()
	{
		commonHeader();
		
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$obj = new dbWatches();
		if ($obj->Load($iID) != -1)
		{
			$obj->Delete();
			trigger_error(STR_BO_DELETED, E_USER_NOTICE);
		}

		$objHTML = new htmlWatches();
		$objHTML->PrintMine();
	}

	function getWatchDescription($obj)
	{
		if (!is_object($obj))
			return '';

		if ($this->oMeta == null)
			$this->oMeta = new DCL_MetadataDisplay();

		$objW = new dbWorkorders();
		$objT = new dbTickets();

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
	
	function GetWorkOrderNotificationBody(&$obj, $bIsPublic)
	{
		global $dcl_info;
		
		$t = new DCL_Smarty();
		$t->assign_by_ref('obj', $obj);

		$dbEntityTag = new EntityTagModel();
		$t->assign('VAL_TAGS', str_replace(',', ', ', $dbEntityTag->getTagsForEntity(DCL_ENTITY_WORKORDER, $obj->jcn, $obj->seq)));

		$dbEntityHotlist = new EntityHotlistModel();
		$hotlistCollection = $dbEntityHotlist->getTagsWithPriorityForEntity(DCL_ENTITY_WORKORDER, $obj->jcn, $obj->seq);
		$hotlists = '';

		foreach ($hotlistCollection as $hotlistEntry)
			$hotlists .= ($hotlists != '' ? ', ' : '') . $hotlistEntry['hotlist'] . ' #' . $hotlistEntry['priority'];

		$t->assign('VAL_HOTLISTS', $hotlists);

		$oTC = new dbTimeCards();
		$t->assign('VAL_TIMECARDS', $oTC->GetTimeCardsArray($obj->jcn, $obj->seq, $bIsPublic));
		
		if ($bIsPublic)
			return $t->ToString($dcl_info['DCL_WO_EMAIL_TEMPLATE_PUBLIC'], 'custom');
		
		return $t->ToString($dcl_info['DCL_WO_EMAIL_TEMPLATE'], 'custom');
	}
	
	// obj is a dbWorkorder object and actions is a comma delimited list of actions to send for
	function sendNotification(&$obj, $actions, $bShowNotifyMsg = true)
	{
		global $dcl_info, $g_oSession, $g_oSec;

		if (!is_object($obj) || $actions == '' || $dcl_info['DCL_SMTP_ENABLED'] != 'Y')
			return;

		$oMail = new Smtp();
		$oMail->isHtml = ($dcl_info['DCL_WO_NOTIFICATION_HTML'] == 'Y');

		$objWtch = new dbWatches();
		$query = "select distinct email_addr, whoid from personnel " . $objWtch->JoinKeyword . " dcl_contact_email ON personnel.contact_id=dcl_contact_email.contact_id AND dcl_contact_email.preferred = 'Y' ";
		$query .= $objWtch->JoinKeyword . ' watches ON id=whoid ';
		$query .= 'LEFT JOIN dcl_wo_account ON typeid = 6 AND wo_id = ' . $obj->jcn;
		$query .= ' AND dcl_wo_account.seq = ' . $obj->seq . ' AND whatid1 = account_id ';
		$query .= "where id = whoid AND actions in ($actions) and (";
		$query .= sprintf('(typeid=1 AND whatid1=%d)', $obj->product);
		
		if ($obj->IsInAProject())
		{
			$oPM = new ProjectMapModel();
			$oPM->LoadByWO($obj->jcn, $obj->seq);
			$query .= sprintf(' or (typeid=2 and whatid1=%d)', $oPM->projectid);
		}
		
		$query .= sprintf(' or (typeid=3 and whatid1=%d and whatid2 in (0,%d))', $obj->jcn, $obj->seq);
		$query .= ' or (typeid = 6 and whatid1 = account_id)';

		$query .= sprintf(') AND whoid != %d', $GLOBALS['DCLID']);
		$query .= " AND active = 'Y'";

		$arrEmail = array();
		$arrPublicEmail = array();
		$mailFrom = '';
		if ($g_oSession->Value('USEREMAIL') != '')
			$mailFrom = '<' . $g_oSession->Value('USEREMAIL') . '>';

		if ($this->oMeta == null)
			$this->oMeta = new DCL_MetadataDisplay();
			
		$bIsPublic = false;
		if ($obj->responsible != $GLOBALS['DCLID'] && $this->oMeta->GetPersonnel($obj->responsible) != '' && $this->oMeta->oPersonnel->active == 'Y')
		{
			$aContact = $this->oMeta->GetContact($this->oMeta->oPersonnel->contact_id);
			if (IsSet($aContact['email']) && !IsSet($arrEmail[$aContact['email']]) && $obj->CanView($obj, $obj->responsible, $bIsPublic))
			{
				if ($bIsPublic)
					$arrPublicEmail[$aContact['email']] = 1;
				else
					$arrEmail[$aContact['email']] = 1;
			}
		}
		
		if ($obj->createby != $GLOBALS['DCLID'] && $this->oMeta->GetPersonnel($obj->createby) != '' && $this->oMeta->oPersonnel->active == 'Y')
		{
			$aContact = $this->oMeta->GetContact($this->oMeta->oPersonnel->contact_id);
			if (IsSet($aContact['email']) && !IsSet($arrEmail[$aContact['email']]) && $obj->CanView($obj, $obj->createby, $bIsPublic))
			{
				$oPrefs = new PreferencesModel();
				if ($oPrefs->Load($obj->createby) == -1 || $oPrefs->Value('DCL_PREF_CREATED_WATCH_OPTION') == '' || strpos($actions, $oPrefs->Value('DCL_PREF_CREATED_WATCH_OPTION')) !== false)
				{
					if ($bIsPublic)
						$arrPublicEmail[$aContact['email']] = 1;
					else
						$arrEmail[$aContact['email']] = 1;
				}
			}
		}
		
		if ($objWtch->Query($query) != -1)
		{
			while ($objWtch->next_record())
			{
				if (!isset($arrEmail[$objWtch->f(0)]))
				{
					if ($obj->CanView($obj, $objWtch->f(1), $bIsPublic))
					{
						if ($bIsPublic)
							$arrPublicEmail[$objWtch->f(0)] = 1;
						else
							$arrEmail[$objWtch->f(0)] = 1;
					}
				}
			}
		}

		if ('Y' == @DCL_Sanitize::ToYN($_REQUEST['copy_me_on_notification']) && $g_oSession->Value('USEREMAIL') != '')
		{
			if (!isset($arrEmail[$g_oSession->Value('USEREMAIL')]) && !isset($arrPublicEmail[$g_oSession->Value('USEREMAIL')]))
			{
				if ($obj->CanView($obj, $GLOBALS['DCLID'], $bIsPublic))
				{
					if ($bIsPublic)
						$arrPublicEmail[$g_oSession->Value('USEREMAIL')] = 1;
					else
						$arrEmail[$g_oSession->Value('USEREMAIL')] = 1;
				}
			}
		}
		
		if (count($arrEmail) == 0 && count($arrPublicEmail) == 0)
			return;
			
		if (count($arrEmail) > 0 && count($arrPublicEmail) > 0)
		{
			foreach ($arrPublicEmail as $sEmail => $junk)
			{
				if (isset($arrEmail[$sEmail]))
					unset($arrEmail[$sEmail]);
			}
		}

		// Here we go!
		$toAddr = '';
		if ($this->oMeta->GetPriority($obj->priority) != '' && $this->oMeta->oPriority->weight == 1)
			$oMail->AddHeader('X-Priority: 1');

		$oMail->from = $mailFrom;

		$sResponsible = '';
		$sStatusName = '';
		$oMail->subject = sprintf('[%s %d-%d] [%s] [%s] %s', STR_WO_JCN, $obj->jcn, $obj->seq, $this->oMeta->GetPersonnel($obj->responsible), $this->oMeta->GetStatus($obj->status), $obj->summary);
		$oMail->body = $this->GetWorkOrderNotificationBody($obj, false);

		$oMail->to = array();
		foreach ($arrEmail as $email => $junk)
		{
			$oMail->to[] = '<' . $email . '>';
			if ($toAddr != '')
				$toAddr .= ', ';
			$toAddr .= $email;
		}
		
		if (count($oMail->to) > 0)
		{
			$bSuccess = $oMail->Send();
	
			if ($bShowNotifyMsg && $toAddr != '')
			{
				if ($bSuccess)
					trigger_error(sprintf(STR_BO_MAILSENT, $toAddr), E_USER_NOTICE);
				else
					trigger_error('Could not send email notification.', E_USER_ERROR);
			}
		}
		
		if ($obj->is_public == 'Y' && count($arrPublicEmail) > 0)
		{
			$oMail->body = $this->GetWorkOrderNotificationBody($obj, true);
	
			$oMail->to = array();
			foreach ($arrPublicEmail as $email => $junk)
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
	}

	function GetTicketNotificationBody(&$obj, $bIsPublic)
	{
		global $dcl_info;
		
		$t = new DCL_Smarty();
		$t->assign_by_ref('obj', $obj);
		
		$objTR = new TicketResolutionsModel();
		$t->assign('VAL_RESOLUTIONS', $objTR->GetResolutionsArray($obj->ticketid, $bIsPublic));
		
		if ($bIsPublic)
			return $t->ToString($dcl_info['DCL_TCK_EMAIL_TEMPLATE_PUBLIC'], 'custom');
		
		return $t->ToString($dcl_info['DCL_TCK_EMAIL_TEMPLATE'], 'custom');
	}
	
	function sendTicketNotification($obj, $actions, $bShowNotifyMsg = true)
	{
		global $dcl_info, $g_oSession;

		if ($dcl_info['DCL_SMTP_ENABLED'] != 'Y' || !is_object($obj))
			return;

		$oMail = new Smtp();
		$oMail->isHtml = ($dcl_info['DCL_TCK_NOTIFICATION_HTML'] == 'Y');

		// Got the message constructed, so send it!
		$objWtch = new dbWatches();
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
		$arrPublicEmail = array();
		$mailFrom = '';
		if ($g_oSession->Value('USEREMAIL') != '')
			$mailFrom = '<' . $g_oSession->Value('USEREMAIL') . '>';

		if ($this->oMeta == null)
			$this->oMeta = new DCL_MetadataDisplay();
			
		$bIsPublic = false;
		if ($obj->responsible != $GLOBALS['DCLID'] && $this->oMeta->GetPersonnel($obj->responsible) != '' && $this->oMeta->oPersonnel->active == 'Y')
		{
			$aContact = $this->oMeta->GetContact($this->oMeta->oPersonnel->contact_id);
			if (IsSet($aContact['email']) && !IsSet($arrEmail[$aContact['email']]) && $obj->CanView($obj, $obj->responsible, $bIsPublic))
			{
				if ($bIsPublic)
					$arrPublicEmail[$aContact['email']] = 1;
				else
					$arrEmail[$aContact['email']] = 1;
			}
		}
		
		if ($obj->createdby != $GLOBALS['DCLID'] && $this->oMeta->GetPersonnel($obj->createdby) != '' && $this->oMeta->oPersonnel->active == 'Y')
		{
			$aContact = $this->oMeta->GetContact($this->oMeta->oPersonnel->contact_id);
			if (IsSet($aContact['email']) && !IsSet($arrEmail[$aContact['email']]) && $obj->CanView($obj, $obj->createdby, $bIsPublic))
			{
				$oPrefs = new PreferencesModel();
				if ($oPrefs->Load($obj->createdby) == -1 || $oPrefs->Value('DCL_PREF_CREATED_WATCH_OPTION') == '' || strpos($actions, $oPrefs->Value('DCL_PREF_CREATED_WATCH_OPTION')) !== false)
				{
					if ($bIsPublic)
						$arrPublicEmail[$aContact['email']] = 1;
					else
						$arrEmail[$aContact['email']] = 1;
				}
			}
		}

		if ('Y' == @DCL_Sanitize::ToYN($_REQUEST['copy_me_on_notification']) && $g_oSession->Value('USEREMAIL') != '')
		{
			if (!isset($arrEmail[$g_oSession->Value('USEREMAIL')]) && !isset($arrPublicEmail[$g_oSession->Value('USEREMAIL')]))
			{
				if ($obj->CanView($obj, $GLOBALS['DCLID'], $bIsPublic))
				{
					if ($bIsPublic)
						$arrPublicEmail[$g_oSession->Value('USEREMAIL')] = 1;
					else
						$arrEmail[$g_oSession->Value('USEREMAIL')] = 1;
				}
			}
		}
		
		if ($objWtch->Query($query) != -1)
		{
			while ($objWtch->next_record())
			{
				if (!isset($arrEmail[$objWtch->f(0)]))
				{
					if ($obj->CanView($obj, $objWtch->f(1), $bIsPublic))
					{
						if ($bIsPublic)
							$arrPublicEmail[$objWtch->f(0)] = 1;
						else
							$arrEmail[$objWtch->f(0)] = 1;
					}
				}
			}
		}

		if (count($arrEmail) == 0 && count($arrPublicEmail) == 0)
			return;
			
		if (count($arrEmail) > 0 && count($arrPublicEmail) > 0)
		{
			foreach ($arrPublicEmail as $sEmail => $junk)
			{
				if (isset($arrEmail[$sEmail]))
					unset($arrEmail[$sEmail]);
			}
		}

		// Here we go!
		$toAddr = '';
		if ($this->oMeta->GetPriority($obj->priority) != '' && $this->oMeta->oPriority->weight == 1)
			$oMail->AddHeader('X-Priority: 1');

		$oMail->from = $mailFrom;
		$oMail->subject = sprintf('[%s %d] [%s] [%s] %s', STR_TCK_TICKET, $obj->ticketid, $this->oMeta->GetPersonnel($obj->responsible), $this->oMeta->GetStatus($obj->status), $obj->summary);
		$oMail->body = $this->GetTicketNotificationBody($obj, true);
		$oMail->to = array();
		foreach ($arrEmail as $email => $junk)
		{
			$oMail->to[] = '<' . $email . '>';
			if ($toAddr != '')
				$toAddr .= ', ';
			$toAddr .= $email;
		}

		if (count($oMail->to) > 0)
		{
			$bSuccess = $oMail->Send();
	
			if ($bShowNotifyMsg && $toAddr != '')
			{
				if ($bSuccess)
					trigger_error(sprintf(STR_BO_MAILSENT, $toAddr), E_USER_NOTICE);
				else
					trigger_error('Could not send email notification.', E_USER_ERROR);
			}
		}
		
		if ($obj->is_public == 'Y' && count($arrPublicEmail) > 0)
		{
			$oMail->body = $this->GetTicketNotificationBody($obj, true);
	
			$oMail->to = array();
			foreach ($arrPublicEmail as $email => $junk)
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
	}

	function showmy()
	{
		commonHeader();
		$obj = new htmlWatches();
		$obj->my(0);
	}
}
