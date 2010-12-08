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
LoadStringResource('tck');

class boTickets
{
	function add()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$obj = new htmlTicketForm();
		$obj->Show();
	}

	function dbadd()
	{
		global $dcl_info, $g_oSec, $g_oSession;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$obj = new dbTickets();
		$obj->InitFromGlobals();
		$obj->createdby = $GLOBALS['DCLID'];
		$obj->createdon = DCL_NOW;

		// If responsible is set, InitFromGlobals would have fetched it above
		// If not set, get the ticket lead for the product
		if (!IsSet($_REQUEST['responsible']))
		{
			$objProduct = new dbProducts();
			if ($objProduct->Load($obj->product) == -1)
				return;
				
			$obj->responsible = $objProduct->ticketsto;
		}

		$obj->statuson = date($dcl_info['DCL_TIMESTAMP_FORMAT']);

		if (IsSet($_REQUEST['resolution']) && $_REQUEST['resolution'] != '')
			$obj->lastactionon = date($dcl_info['DCL_TIMESTAMP_FORMAT']);

		$oStatus = new StatusModel();
		if ($oStatus->GetStatusType($obj->status) == 2)
		{
			$obj->closedby = $GLOBALS['DCLID'];
			$obj->closedon = date($dcl_info['DCL_TIMESTAMP_FORMAT']);
		}

		$obj->is_public = ((isset($_REQUEST['is_public']) && $_REQUEST['is_public'] == 'Y') || $g_oSec->IsPublicUser() ? 'Y' : 'N');
		$obj->seconds = 0;
		
		// if public user, set contact ID automatically
		if ($g_oSec->IsPublicUser())
		{
			$obj->contact_id = $g_oSession->Value('contact_id');
			
			$dbContact = new dbContact();
			$aOrg = $dbContact->GetFirstOrg($obj->contact_id);
			$obj->account = $aOrg['org_id'];
		}
		
		$obj->Add();

		// Tags
		if (isset($_REQUEST['tags']))
		{
			$oTag = new dbEntityTag();
			$oTag->serialize(DCL_ENTITY_TICKET, $obj->ticketid, 0, $_REQUEST['tags']);
		}

		// upload a file attachment?
		if (($sFileName = @DCL_Sanitize::ToFileName('userfile')) !== null)
		{
			$o = new boFile();
			$o->iType = DCL_ENTITY_TICKET;
			$o->iKey1 = $obj->ticketid;
			$o->sFileName = DCL_Sanitize::ToActualFileName('userfile');
			$o->sTempFileName = $sFileName;
			$o->sRoot = $dcl_info['DCL_FILE_PATH'] . '/attachments';
			$o->Upload();
		}

		if (IsSet($_REQUEST['resolution']) && $_REQUEST['resolution'] != '')
		{
			$objR = new dbTicketresolutions();
			$objR->InitFromGlobals();
			$objR->loggedby = $GLOBALS['DCLID'];
			$objR->loggedon = date($dcl_info['DCL_TIMESTAMP_FORMAT']);
			$objR->ticketid = $obj->ticketid;
			$objR->is_public = ((isset($_REQUEST['is_public']) && $_REQUEST['is_public'] == 'Y') || $g_oSec->IsPublicUser() ? 'Y' : 'N');
			$objR->Add();

			$start = new DCLTimestamp;
			$start->SetFromDisplay($objR->startedon);

			$end = new DCLTimestamp;
			$end->SetFromDisplay($objR->loggedon);

			$obj->seconds += ($end->time - $start->time);
			$obj->Edit();

			$oTR = new boTicketresolutions();
			$oTR->oDB =& $objR;
			$oTR->sendCustomerResponseEmail($obj);
		}

		$notify = '4,1';
		if ($oStatus->GetStatusType($obj->status) == 2)
			$notify .= ',2,3';

		// Reload the ticket now that we have all of the fields updated
		$obj->Load($obj->ticketid);

		$objWatch = new boWatches();
		$objWatch->sendTicketNotification($obj, $notify);

		$objH = new htmlTicketDetail();
		$objH->Show($obj);
	}

	// THANKS: Michael Brader
	function copyToWO()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_COPYTOWO))
			throw new PermissionDeniedException();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['ticketid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$oTicket = new dbTickets();
		if ($oTicket->Load($iID) == -1)
			return;

		$objHWO = new htmlWorkOrderForm();
		$objHWO->Show(0, $oTicket);
	}

	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['ticketid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$obj = new dbTickets();
		if ($obj->Load($iID) == -1)
			return;

		if ($obj->is_public == 'N' && $g_oSec->IsPublicUser())
		{
			trigger_error('Cannot modify private item.', E_USER_ERROR);
			return;
		}

		$objF = new htmlTicketForm();
		$objF->Show($obj);
	}

	function dbmodify()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['ticketid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$obj = new dbTickets();
		if ($obj->Load($iID) == -1)
			return;
			
		if ($obj->is_public == 'N' && $g_oSec->IsPublicUser())
		{
			trigger_error('Cannot modify private item.', E_USER_ERROR);
			return;
		}

		$bHasChanges = false;
		$aFields = array('product', 'module_id', 'account', 'priority', 'type', 'contact_id', 'entity_source_id');
		foreach ($aFields as $sField)
		{
			if (!IsSet($_REQUEST[$sField]) || ($_REQUEST[$sField] == '' && $sField == 'entity_source_id'))
				continue;
			
			if (($iID = @DCL_Sanitize::ToInt($_REQUEST[$sField])) === null)
			{
				throw new InvalidDataException();
			}
		
			if ($obj->$sField != $iID)
			{
				$bHasChanges = true;
				$obj->$sField = $iID;
			}
		}

		$aFields = array('summary', 'version', 'issue');
		foreach ($aFields as $sField)
		{
			$sValue = @$obj->GPCStripSlashes($_REQUEST[$sField]);
			if ($obj->$sField != $sValue)
			{
				$bHasChanges = true;
				$obj->$sField = $sValue;
			}
		}
		
		$sIsPublic = ((isset($_REQUEST['is_public']) && $_REQUEST['is_public'] == 'Y') || $g_oSec->IsPublicUser() ? 'Y' : 'N');
		if ($sIsPublic != $obj->is_public)
		{
			$bHasChanges = true;
			$obj->is_public = $sIsPublic;
		}

		$oldResponsible = $obj->responsible;
		if (!IsSet($_REQUEST['responsible']))
		{
			$objProduct = new dbProducts();
			$objProduct->Load($obj->product);
			
			if ($objProduct->ticketsto != $obj->responsible)
			{
				$bHasChanges = true;
				$obj->responsible = $objProduct->ticketsto;
			}
		}
		else
		{
			if (($iID = @DCL_Sanitize::ToInt($_REQUEST['responsible'])) === null)
			{
				throw new InvalidDataException();
			}
		
			if ($obj->responsible != $iID)
			{
				$bHasChanges = true;
				$obj->responsible = $iID;
			}
		}

		if (isset($_REQUEST['tags']))
		{
			$oTag = new dbEntityTag();
			$oTag->serialize(DCL_ENTITY_TICKET, $obj->ticketid, 0, $_REQUEST['tags']);
		}
		
		if ($bHasChanges)
		{
			$obj->Edit();

			$objWtch = new boWatches();
			$objWtch->sendTicketNotification($obj, '4');
		}

		$objH = new htmlTicketDetail();
		$objH->Show($obj);
	}

	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['ticketid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$obj = new dbTickets();
		if ($obj->Load($iID) == -1)
			return;

		if ($obj->is_public == 'N' && $g_oSec->IsPublicUser())
		{
			trigger_error('Cannot access private item.', E_USER_ERROR);
			return;
		}

		ShowDeleteYesNo('Delete Ticket [' . $iID . ']', 'boTickets.dbdelete', $iID, $obj->summary, false, 'ticketid');
	}

	function dbdelete()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['ticketid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$obj = new dbTickets();
		if ($obj->Load($iID) == -1)
			return;

		if ($obj->is_public == 'N' && $g_oSec->IsPublicUser())
		{
			trigger_error('Cannot access private item.', E_USER_ERROR);
			return;
		}

		$obj->Delete();

		// Remove tags
		$oTag = new dbEntityTag();
		$oTag->deleteByEntity(DCL_ENTITY_TICKET, $iID, 0);

		// Remove all attachments
		$attachPath = $dcl_info['DCL_FILE_PATH'] . '/attachments/tck/' . substr($iID, -1) . '/' . $iID . '/';
		if (is_dir($attachPath) && $hDir = opendir($attachPath))
		{
			while ($fileName = readdir($hDir))
			{
				if (is_file($attachPath . $fileName) && is_readable($attachPath . $fileName))
					unlink($attachPath . $fileName);
			}

			closedir($hDir);
		}

		trigger_error(sprintf(STR_BO_TICKETDELETED, $iID), E_USER_NOTICE);

		$objMy = new htmlMyDCL();
		$objMy->showMy();
	}

	function view()
	{
		global $g_oSec, $ticketid;

		commonHeader();
		if (!$g_oSec->HasAnyPerm(array(DCL_ENTITY_TICKET => array($g_oSec->PermArray(DCL_PERM_VIEW, $ticketid), $g_oSec->PermArray(DCL_PERM_VIEWACCOUNT, $ticketid), $g_oSec->PermArray(DCL_PERM_VIEWSUBMITTED, $ticketid)))))
			throw new PermissionDeniedException();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['ticketid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$objTicket = new dbTickets();
		if ($objTicket->Load($iID) != -1)
		{
			$objHT = new htmlTicketDetail();
			$objHT->Show($objTicket);
		}
		else
		{
			trigger_error(sprintf(STR_TCK_COULDNOTFIND, $iID));

			if ($g_oSec->IsPublicUser())
				$objMy = new htmlPublicMyDCL();
			else
				$objMy = new htmlMyDCL();

			$objMy->showMy();
		}
	}

	function graph()
	{
		commonHeader();

		// GD is required, so short-circuit if not installed
		if (!extension_loaded('gd'))
		{
			trigger_error(STR_BO_GRAPHNEEDSGD);
			return;
		}

		$obj = new htmlTickets();
		$obj->DisplayGraphForm();
	}

	function showgraph()
	{
		commonHeader();

		// GD is required, so short-circuit if not installed
		if (!extension_loaded('gd'))
		{
			trigger_error(STR_BO_GRAPHNEEDSGD);
			return;
		}

		$objG = new LineGraphImageHelper();
		$obj = new dbTickets();
		$beginDate = new DCLTimestamp;
		$endDate = new DCLTimestamp;
		$testDate = new DCLTimestamp;

		if (($iDays = @DCL_Sanitize::ToInt($_REQUEST['days'])) === null ||
			($dateFrom = @DCL_Sanitize::ToDate($_REQUEST['dateFrom'])) === null
			)
		{
			throw new InvalidDataException();
		}
		
		$endDate->SetFromDisplay($dateFrom . ' 23:59:59');
		$beginDate->SetFromDisplay($dateFrom . ' 00:00:00');
		$beginDate->time -= (($iDays - 1) * 86400);

		$product_id = 0;
		if (($product_id = @DCL_Sanitize::ToInt($_REQUEST['product'])) === null)
			$product_id = 0;
		
		if ($obj->LoadDatesByRange($beginDate->ToDisplay(), $endDate->ToDisplay(), $product_id) == -1)
			return;

		$objG->data[0] = array(); // Open
		$objG->data[1] = array(); // Closed

		$daysBack = array();
		$testDate->time = $beginDate->time;
		for ($i = 0; $i < $iDays; $i++)
		{
			$daysBack[$i] = $testDate->time;
			// Set the relevant object properties while we're at it
			$objG->line_captions_x[$i] = date('m/d', $testDate->time);
			$objG->data[0][$i] = 0;
			$objG->data[1][$i] = 0;

			$testDate->time += 86400;
		}

		while ($obj->next_record())
		{
			for ($y = 0; $y < 2; $y++)
			{
				$testDate->SetFromDB($obj->f($y));
				$j = $iDays - 1;
				while ($j >= 0)
				{
					if ($testDate->time >= $daysBack[$j])
					{
						if (!IsSet($objG->data[$y][$j]))
							$objG->data[$y][$j] = 0;
						$objG->data[$y][$j]++;
						break;
					}

					$j--;
				}
			}
		}

		$objG->title = STR_BO_GRAPHTITLE;
		if ($product_id > 0)
		{
			$oDB = new dbProducts();
			if ($oDB->Load($product_id) != -1)
				$objG->title .= ' ' . $oDB->name;
		}

		$objG->caption_y = STR_BO_GRAPHCAPTIONY;
		$objG->caption_x = STR_BO_GRAPHCAPTIONX;
		$objG->num_lines_y = 15;
		$objG->num_lines_x = $iDays;
		$objG->colors = array('red', 'blue');

		print('<center>');
		echo '<img border="0" src="', menuLink('', 'menuAction=LineGraphImageHelper.Show&' . $objG->ToURL()), '">';
		print('</center>');
	}

	function reassign()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ASSIGN))
			throw new PermissionDeniedException();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['ticketid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$objTicket = new dbTickets();
		if ($objTicket->Load($iID) == -1)
			return;

		$obj = new htmlTickets();
		$obj->PrintReassignForm($objTicket);

		$objHT = new htmlTicketDetail();
		$objHT->Show($objTicket);
	}

	function dbreassign()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ASSIGN))
			throw new PermissionDeniedException();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['ticketid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (($responsible = @DCL_Sanitize::ToInt($_REQUEST['responsible'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (($priority = @DCL_Sanitize::ToInt($_REQUEST['priority'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (($type = @DCL_Sanitize::ToInt($_REQUEST['type'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$obj = new dbTickets();
		if ($obj->Load($iID) == -1)
			return;
		
		if ($obj->responsible != $responsible ||
				$obj->priority != $priority ||
				$obj->type != $type)
		{
			$obj->responsible = $responsible;
			$obj->priority = $priority;
			$obj->type = $type;
			$obj->Edit();

			$objWtch = new boWatches();
			$objWtch->sendTicketNotification($obj, '4');
		}

		$objHT = new htmlTicketDetail();
		$objHT->Show($obj);
	}

	function upload()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ATTACHFILE))
			throw new PermissionDeniedException();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['ticketid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$objTicket = new dbTickets();
		if ($objTicket->Load($iID) == -1)
			return;

		$obj = new htmlTickets();
		$obj->ShowUploadFileForm($objTicket);

		$objD = new htmlTicketDetail();
		$objD->Show($objTicket);
	}

	function doupload()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ATTACHFILE))
			throw new PermissionDeniedException();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['ticketid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$objTicket = new dbTickets();
		if ($objTicket->Load($iID) == -1)
			return;

		if (($sFileName = DCL_Sanitize::ToFileName('userfile')) === null)
			throw new PermissionDeniedException();

		$o = new boFile();
		$o->iType = DCL_ENTITY_TICKET;
		$o->iKey1 = $iID;
		$o->sFileName = DCL_Sanitize::ToActualFileName('userfile');
		$o->sTempFileName = $sFileName;
		$o->sRoot = $dcl_info['DCL_FILE_PATH'] . '/attachments';
		$o->Upload();

		$obj = new htmlTicketDetail();
		$obj->Show($objTicket);
	}

	function deleteattachment()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_REMOVEFILE))
			throw new PermissionDeniedException();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['ticketid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$objTicket = new dbTickets();
		if ($objTicket->Load($iID) == -1)
			return;
			
		if (!@DCL_Sanitize::IsValidFileName($_REQUEST['filename']))
		{
			throw new InvalidDataException();
		}

		$obj = new htmlTickets();
		$obj->ShowDeleteAttachmentYesNo($iID, $_REQUEST['filename']);

		$objD = new htmlTicketDetail();
		$objD->Show($objTicket);
	}

	function dodeleteattachment()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_REMOVEFILE))
			throw new PermissionDeniedException();

		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['ticketid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!@DCL_Sanitize::IsValidFileName($_REQUEST['filename']))
		{
			throw new InvalidDataException();
		}

		$objTicket = new dbTickets();
		if ($objTicket->Load($iID) == -1)
			return;

		$attachPath = $dcl_info['DCL_FILE_PATH'] . '/attachments/tck/' . substr($iID, -1) . '/' . $iID . '/';
		if (is_file($attachPath . $_REQUEST['filename']) && is_readable($attachPath . $_REQUEST['filename']))
			unlink($attachPath . $_REQUEST['filename']);

		$obj = new htmlTicketDetail();
		$obj->Show($objTicket);
	}

	function dbsearch()
	{
		commonHeader();

		$personnel = isset($_REQUEST['personnel']) && is_array($_REQUEST['personnel']) ? $_REQUEST['personnel'] : array();
		$type = @DCL_Sanitize::ToIntArray($_REQUEST['type']);
		$priority = @DCL_Sanitize::ToIntArray($_REQUEST['priority']);
		$status = @$_REQUEST['status'];
		$account = @DCL_Sanitize::ToIntArray($_REQUEST['account']);
		$is_public = @$_REQUEST['is_public'];
		$entity_source_id = @DCL_Sanitize::ToIntArray($_REQUEST['entity_source_id']);
		$createdon = @$_REQUEST['createdon'];
		$closedon = @$_REQUEST['closedon'];
		$statuson = @$_REQUEST['statuson'];
		$lastactionon = @$_REQUEST['lastactionon'];
		$module_id = isset($_REQUEST['module_id']) && is_array($_REQUEST['module_id']) ? $_REQUEST['module_id'] : array();
		$tags = $_REQUEST['tags'];
		$searchText = $_REQUEST['searchText'];
		$columns = $_REQUEST['columns'];
		$groups = $_REQUEST['groups'];
		$order = $_REQUEST['order'];
		$columnhdrs = $_REQUEST['columnhdrs'];

		$dcl_status_type = @DCL_Sanitize::ToIntArray($_REQUEST['dcl_status_type']);
		$product = @DCL_Sanitize::ToIntArray($_REQUEST['product']);
		$department = @DCL_Sanitize::ToIntArray($_REQUEST['department']);

		$dateFrom = DCL_Sanitize::ToDate($_REQUEST['dateFrom']);
		$dateTo = DCL_Sanitize::ToDate($_REQUEST['dateTo']);

		$oDB = new dclDB;

		if (strlen($columnhdrs) > 0)
			$columnhdrs = explode(',', $columnhdrs);
		else
			$columnhdrs = array();

		if (strlen($columns) > 0)
			$columns = explode(',', $columns);
		else
			$columns = array();

		if (strlen($groups) > 0)
			$groups = explode(',', $groups);
		else
			$groups = array();

		if (strlen($order) > 0)
			$order = explode(',', $order);
		else
			$order = array();

		$objView = new boView();
		$objView->table = 'tickets';

		if (count($personnel) > 0 || count($department) > 0)
		{
			$fieldList = array('responsible', 'createdby', 'closedby');
			$bStrippedDepartments = false;
			$pers_sel = array();
			foreach ($fieldList as $field)
			{
				if (!isset($_REQUEST[$field]) || $_REQUEST[$field] != '1')
					continue;
					
				if (count($personnel) > 0)
				{
					if (!$bStrippedDepartments)
					{
						$bStrippedDepartments = true;

						// Have actual personnel?  If so, only set personnel for their associated departments instead of the department
						// then unset the department from the array
						foreach ($personnel as $encoded_pers)
						{
							list($dpt_id, $pers_id) = explode(',', $encoded_pers);
							$pers_sel[count($pers_sel)] = $pers_id;
							if (count($department) > 0 && in_array($dpt_id, $department))
							{
								foreach ($department as $key => $department_id)
								{
									if ($department_id == $dpt_id)
									{
										unset($department[$key]);
										break;
									}
								}
							}
						}
					}

					$pers_sel = DCL_Sanitize::ToIntArray($pers_sel);
					if (count($pers_sel) > 0)
						$objView->AddDef('filter', $field, $pers_sel);
				}

				if (count($department) > 0)
					$objView->AddDef('filter', $field . '.department', $department);
			}
		}

		$fieldList = array('priority', 'type', 'account', 'entity_source_id');
		while (list($key, $field) = each($fieldList))
		{
			$$field = DCL_Sanitize::ToIntArray($$field);
			if (count($$field) > 0)
				$objView->AddDef('filter', $field, $$field);
		}

		if (trim($tags) != '')
			$objView->AddDef('filter', 'dcl_tag.tag_desc', $tags);

		if (count($is_public) > 0)
		{
			foreach ($is_public as $publicValue)
				$objView->AddDef('filter', 'is_public', $oDB->Quote(DCL_Sanitize::ToYN($publicValue)));
		}

		if (count($module_id) > 0)
		{
			// Have modules?  If so, only set module IDs for their associated products instead of the product ID
			// then unset the product id from the array
			$module = array();
			foreach ($module_id as $encoded_mod)
			{
				list($mod_prod_id, $mod_id) = explode(',', $encoded_mod);
				$module[count($module)] = $mod_id;
				if (count($product) > 0 && in_array($mod_prod_id, $product))
				{
					foreach ($product as $key => $product_id)
					{
						if ($product_id == $mod_prod_id)
						{
							unset($product[$key]);
							break;
						}
					}
				}
			}

			$module = DCL_Sanitize::ToIntArray($module);
			if (count($module) > 0)
				$objView->AddDef('filter', 'module_id', $module);
		}

		if (count($product) > 0)
			$objView->AddDef('filter', 'product', $product);

		if (count($status) > 0)
		{
			// Have statuses?  If so, only set status IDs for their associated types instead of the status type ID
			// then unset the status type id from the array
			$statuses = array();
			foreach ($status as $encoded_status)
			{
				list($type_id, $status_id) = explode(',', $encoded_status);
				if (($type_id = DCL_Sanitize::ToInt($type_id)) !== null && ($status_id = DCL_Sanitize::ToInt($status_id)) !== null)
				{
					$statuses[count($statuses)] = $status_id;
					if (count($dcl_status_type) > 0 && in_array($type_id, $dcl_status_type))
					{
						foreach ($dcl_status_type as $key => $status_type_id)
						{
							if ($status_type_id == $type_id)
							{
								unset($dcl_status_type[$key]);
								break;
							}
						}
					}
				}
			}

			$objView->AddDef('filter', 'status', $statuses);
		}

		if (count($dcl_status_type) > 0)
			$objView->AddDef('filter', 'statuses.dcl_status_type', $dcl_status_type);
			
		if ($dateFrom !== null || $dateTo !== null)
		{
			if ($dateFrom !== null)
				$dateFrom .= ' 00:00:00';
			else
			    $dateFrom = '';

			if ($dateTo !== null)
				$dateTo .= ' 23:59:59';
			else
			    $dateTo = '';

			$fieldList = array('createdon', 'closedon', 'statuson', 'lastactionon');

			foreach ($fieldList as $field)
			{
				if ($$field == '1')
					$objView->AddDef('filterdate', $field, array($dateFrom, $dateTo));
			}
		}

		if ($searchText != '')
		{
			$objView->AddDef('filterlike', 'issue', $searchText);
			$objView->AddDef('filterlike', 'summary', $searchText);
		}

		if (count($columns) > 0)
			$objView->AddDef('columns', '', $columns);
		else
			$objView->AddDef('columns', '',
				array('ticketid', 'responsible', 'product', 'account', 'status', 'contact', 'contactphone', 'summary'));

		if (count($groups) > 0)
		{
			foreach ($groups as $key => $groupField)
			{
				if ($groupField == 'priorities.name')
					$groups[$key] = 'priorities.weight';
				else if ($groupField == 'severities.name')
					$groups[$key] = 'severities.weight';
			}

			$objView->AddDef('groups', '', $groups);
		}

		if (count($columnhdrs) > 0)
			$objView->AddDef('columnhdrs', '', $columnhdrs);

		if (count($order) > 0)
		{
			foreach ($order as $key => $orderField)
			{
				if ($orderField == 'priorities.name')
					$order[$key] = 'priorities.weight';
				else if ($orderField == 'severities.name')
					$order[$key] = 'severities.weight';
			}

			$objView->AddDef('order', '', $order);
		}
		else
			$objView->AddDef('order', '', array('ticketid'));


		$objView->style = 'report';

		if (IsSet($_REQUEST['title']) && $_REQUEST['title'] != '')
			$objView->title = $oDB->GPCStripSlashes($_REQUEST['title']);
		else
			$objView->title = STR_TCK_TICKETSEARCHRESULTS;

		$obj = new htmlTicketResults();
		$obj->Render($objView);
	}

	function showmy()
	{
		commonHeader();
		$obj = new htmlTickets();
		$obj->my(0);
	}
}
