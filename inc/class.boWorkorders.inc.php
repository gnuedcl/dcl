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

class boWorkorders
{
	var $oMetaData;
	
	function boWorkorders()
	{
		$this->oMetaData = null;
	}
	
	function newjcn()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ADD))
			return PrintPermissionDenied();

		$obj =& CreateObject('dcl.htmlWorkOrderForm');
		$obj->Show();
	}

	function newseq()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ADD))
			return PrintPermissionDenied();

		if (!IsSet($_REQUEST['jcn']))
		{
			$iID = 0;
		}
		else if (($iID = @DCL_Sanitize::ToInt($_REQUEST['jcn'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$obj =& CreateObject('dcl.htmlWorkOrderForm');
		$obj->Show($iID);
	}

	function copy()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ADD))
		{
			PrintPermissionDenied();
			return;
		}
		
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['jcn'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (($iSeq = @DCL_Sanitize::ToInt($_REQUEST['seq'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$bSequence = isset($_REQUEST['copyseq']) && $_REQUEST['copyseq'] == 'true';
		
		$oWO =& CreateObject('dcl.dbWorkorders');
		$oWO->Load($iID, $iSeq);
		
		$oProject =& CreateObject('dcl.dbProjectmap');
		if ($oProject->LoadByWO($iID, $iSeq) != -1)
			$_REQUEST['projectid'] = $oProject->projectid;
			
		$oWO->jcn = 0;
		$oWO->seq = 0;

		$obj =& CreateObject('dcl.htmlWorkOrderForm');
		$obj->Show($bSequence ? $iID : 0, $oWO);
	}

	function modifyjcn()
	{
		global $g_oSec;
		
		commonHeader();
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['jcn'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (($iSeq = @DCL_Sanitize::ToInt($_REQUEST['seq'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_MODIFY, $iID, $iSeq))
			return PrintPermissionDenied();

		$oWO =& CreateObject('dcl.dbWorkorders');
		$oWO->Load($iID, $iSeq);

		$obj =& CreateObject('dcl.htmlWorkOrderForm');
		$obj->Show($iID, $oWO);
	}

	function dbnewjcn()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ADD))
			return PrintPermissionDenied();

		$objWorkorder =& CreateObject('dcl.dbWorkorders');

		// If we're creating a seq, be sure the jcn exists
		$iID = 0;
		if (IsSet($_REQUEST['jcn']) && $_REQUEST['jcn'] != '')
		{
			if (($iID = @DCL_Sanitize::ToInt($_REQUEST['jcn'])) === null)
			{
				trigger_error('Data sanitize failed.');
				return;
			}
			
			$objWorkorder->Query('SELECT jcn FROM workorders where jcn=' . $iID);
			if (!$objWorkorder->next_record())
			{
				trigger_error(sprintf(STR_BO_NOJCNFORSEQWARNING, $iID));
				$iID = 0;
			}
		}

		$objWorkorder->InitFromGlobals();
		$objWorkorder->etchours = $objWorkorder->esthours;
		$objWorkorder->createby = $GLOBALS['DCLID'];
		$objWorkorder->is_public = ((isset($_REQUEST['is_public']) && $_REQUEST['is_public'] == 'Y') || $g_oSec->IsPublicUser() ? 'Y' : 'N');
		$objWorkorder->Add();

		// multiple accounts?
		if ($dcl_info['DCL_WO_SECONDARY_ACCOUNTS_ENABLED'] == 'Y')
		{
			if (IsSet($_REQUEST['secaccounts']) && $_REQUEST['secaccounts'] != '')
			{
				$aAccounts = explode(',', $_REQUEST['secaccounts']);
				if (count($aAccounts) > 0)
				{
					$oWOA =& CreateObject('dcl.dbWorkOrderAccount');
					$oWOA->wo_id = $objWorkorder->jcn;
					$oWOA->seq = $objWorkorder->seq;

					for ($i = 0; $i < count($aAccounts); $i++)
					{
						if (($iOrgID = DCL_Sanitize::ToInt($aAccounts[$i])) !== null && $iOrgID > 0)
						{
							$oWOA->account_id = $iOrgID;
							$oWOA->Add();
						}
					}
				}
			}
		}
		else if (IsSet($_REQUEST['secaccounts']))
		{
			if (($iOrgID = @DCL_Sanitize::ToInt($_REQUEST['secaccounts'])) !== null && $iOrgID > 0)
			{
				$oWOA =& CreateObject('dcl.dbWorkOrderAccount');
				$oWOA->wo_id = $objWorkorder->jcn;
				$oWOA->seq = $objWorkorder->seq;
				$oWOA->account_id = $iOrgID;
				$oWOA->Add();
			}
		}
		
		if (isset($_REQUEST['tags']))
		{
			$oTag =& CreateObject('dcl.dbEntityTag');
			$oTag->serialize(DCL_ENTITY_WORKORDER, $objWorkorder->jcn, $objWorkorder->seq, $_REQUEST['tags']);
		}

		// add to a project?
		if (IsSet($_REQUEST['projectid']))
		{
			if (($iProjID = @DCL_Sanitize::ToInt($_REQUEST['projectid'])) !== null && $iProjID > 0)
			{
				if ($g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK, $iProjID))
				{
					$objPM =& CreateObject('dcl.dbProjectmap');
					$objPM->projectid = $iProjID;
					$objPM->jcn = $objWorkorder->jcn;
		
					if (IsSet($_REQUEST['addall']) && $_REQUEST['addall'] == '1')
					{
						$objPM->Execute('DELETE FROM projectmap WHERE jcn=' . $objWorkorder->jcn);
						$objPM->seq = 0;
					}
					else
						$objPM->seq = $objWorkorder->seq;
		
					$objPM->Add();
				}
			}
		}

		// upload a file attachment?
		if (($sFileName = DCL_Sanitize::ToFileName('userfile')) !== null)
		{
			$o =& CreateObject('dcl.boFile');
			$o->iType = DCL_ENTITY_WORKORDER;
			$o->iKey1 = $objWorkorder->jcn;
			$o->iKey2 = $objWorkorder->seq;
			$o->sFileName = DCL_Sanitize::ToActualFileName('userfile');
			$o->sTempFileName = $sFileName;
			$o->sRoot = $dcl_info['DCL_FILE_PATH'] . '/attachments';
			$o->Upload();
		}

		// copied from ticket?
		if (IsSet($_REQUEST['ticketid']))
		{
			if (($iTicketID = @DCL_Sanitize::ToInt($_REQUEST['ticketid'])) !== null && $iTicketID > 0)
			{
				$oTR =& CreateObject('dcl.dbTicketresolutions');
				$oTR->ticketid = $iTicketID;
				$oTR->loggedby = $GLOBALS['DCLID'];
				$oTR->loggedon = date($dcl_info['DCL_TIMESTAMP_FORMAT']);
				$oTR->startedon = date($dcl_info['DCL_TIMESTAMP_FORMAT']);
				$oTR->is_public = $objWorkorder->is_public;
				$oTR->resolution = sprintf('Copied to dcl://workorders/%d-%d', $objWorkorder->jcn, $objWorkorder->seq);
	
				$oTck =& CreateObject('dcl.dbTickets');
				$oTck->Load($oTR->ticketid);
				$oTck->lastactionon = date($dcl_info['DCL_TIMESTAMP_FORMAT']);
				$oTR->status = $oTck->status;
	
				$oTR->BeginTransaction();
				$oTR->Add();
				$oTck->Edit();
				$oTR->EndTransaction();
			}
		}

		// Reload work order to update fields now that we have it all stored
		$objWorkorder->Load($objWorkorder->jcn, $objWorkorder->seq);

		$objWtch =& CreateObject('dcl.boWatches');
		$objWtch->sendNotification($objWorkorder, '4,1');

		if (EvaluateReturnTo())
			return;

		$objWO =& CreateObject('dcl.htmlWorkOrderDetail');
		$objWO->Show($objWorkorder->jcn, $objWorkorder->seq);
	}

	function dbmodifyjcn()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['jcn'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (($iSeq = @DCL_Sanitize::ToInt($_REQUEST['seq'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_MODIFY, $iID, $iSeq))
			return PrintPermissionDenied();

		$objWorkorder =& CreateObject('dcl.dbWorkorders');
		if ($objWorkorder->Load($iID, $iSeq) == -1)
			return;

		$aFields = array('product', 'module_id', 'wo_type_id', 'deadlineon', 'eststarton', 'estendon', 'esthours', 'priority', 'severity',
						'contact_id', 'summary', 'notes', 'description', 'responsible', 'reported_version_id', 'is_public', 'entity_source_id', 'targeted_version_id', 'fixed_version_id');

		$bModified = false;
		foreach ($aFields as $sField)
		{
			if ($sField == 'is_public')
			{
				$sValue = (isset($_REQUEST['is_public']) && $_REQUEST['is_public'] == 'Y' ? 'Y' : 'N');
			}
			else
			{
				if (!IsSet($_REQUEST[$sField]))
					continue;

				$sValue = $_REQUEST[$sField];
			}

			$sType = $GLOBALS['phpgw_baseline'][$objWorkorder->TableName]['fd'][$sField]['type'];
			if ($sType == 'text' || $sType == 'varchar' || $sType == 'char')
				$sValue = $objWorkorder->GPCStripSlashes($sValue);
			else if ($sType == 'int')
				$sValue = DCL_Sanitize::ToInt($sValue);
			else if ($sType == 'float')
				$sValue = DCL_Sanitize::ToDecimal($sValue);

			if ($objWorkorder->$sField != $sValue)
			{
				$bModified = true;
				$objWorkorder->$sField = $sValue;
			}
		}

		if ($bModified)
			$objWorkorder->Edit();

		$oWOA =& CreateObject('dcl.dbWorkOrderAccount');
		if (IsSet($_REQUEST['secaccounts']))
		{
			$aAccounts = @DCL_Sanitize::ToIntArray($_REQUEST['secaccounts']);
			if ($aAccounts === null)
				$aAccounts = array();
				
			$oWOA->DeleteByWorkOrder($objWorkorder->jcn, $objWorkorder->seq, join(',', $aAccounts));
			
			// Add the new ones
			if (count($aAccounts) > 0)
			{
				$oWOA->wo_id = $objWorkorder->jcn;
				$oWOA->seq = $objWorkorder->seq;

				for ($i = 0; $i < count($aAccounts); $i++)
				{
					if ($aAccounts[$i] > 0)
					{
						$oWOA->account_id = $aAccounts[$i];
						$oWOA->Add();
						if ($dcl_info['DCL_WO_SECONDARY_ACCOUNTS_ENABLED'] != 'Y')
							break;
					}
				}
			}
		}
		else
			$oWOA->DeleteByWorkOrder($objWorkorder->jcn, $objWorkorder->seq);

		if (isset($_REQUEST['tags']))
		{
			$oTag =& CreateObject('dcl.dbEntityTag');
			$oTag->serialize(DCL_ENTITY_WORKORDER, $objWorkorder->jcn, $objWorkorder->seq, $_REQUEST['tags']);
		}
		
		$objWtch =& CreateObject('dcl.boWatches');
		$objWtch->sendNotification($objWorkorder, '4');

		if (EvaluateReturnTo())
			return;

		$objWO =& CreateObject('dcl.htmlWorkOrderDetail');
		$objWO->Show($objWorkorder->jcn, $objWorkorder->seq);
	}

	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['jcn'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (($iSeq = @DCL_Sanitize::ToInt($_REQUEST['seq'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_DELETE, $iID, $iSeq))
			return PrintPermissionDenied();

		$oWO =& CreateObject('dcl.dbWorkorders');
		if ($oWO->Load($iID, $iSeq) == -1)
			return;

		ShowDeleteYesNo('Delete Work Order [' . $iID . '-' . $iSeq . ']', 'boWorkorders.dbdelete', $iID, $oWO->summary, false, 'jcn', $iSeq, 'seq');
	}

	function dbdelete()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['jcn'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (($iSeq = @DCL_Sanitize::ToInt($_REQUEST['seq'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_DELETE, $iID, $iSeq))
			return PrintPermissionDenied();

		// Remove from projects
		$objPM =& CreateObject('dcl.boProjects');
		$objPM->dbunmap($iID, $iSeq, true);

		// Remove secondary accounts
		if ($dcl_info['DCL_WO_SECONDARY_ACCOUNTS_ENABLED'] == 'Y')
		{
			$oWOA = CreateObject('dcl.dbWorkOrderAccount');
			$oWOA->DeleteByWorkOrder($iID, $iSeq);
		}

		// Remove the work order entry - also does time cards
		$obj =& CreateObject('dcl.dbWorkorders');
		$obj->jcn = $iID;
		$obj->seq = $iSeq;

		$obj->Delete();

		// Remove account references
		$oWOA =& CreateObject('dcl.dbWorkOrderAccount');
		$oWOA->DeleteByWorkOrder($obj->jcn, $obj->seq);

		// Remove all attachments
		$attachPath = $dcl_info['DCL_FILE_PATH'] . '/attachments/wo/' . substr($iID, -1) . '/' . $iID . '/' . $iSeq . '/';
		if ($hDir = @opendir($attachPath))
		{
			while ($fileName = @readdir($hDir))
			{
				if (is_file($attachPath . $fileName) && is_readable($attachPath . $fileName))
					unlink($attachPath . $fileName);
			}

			@closedir($hDir);
		}
		
		// Remove tasks
		$oTasks =& CreateObject('dcl.dbWorkOrderTask');
		$oTasks->DeleteByWorkOrder($iID, $iSeq);
		
		// Remove tags
		$oTag =& CreateObject('dcl.dbEntityTag');
		$oTag->deleteByEntity(DCL_ENTITY_WORKORDER, $iID, $iSeq);

		trigger_error(sprintf(STR_BO_WORKORDERDELETED, $iID, $iSeq), E_USER_NOTICE);

		$objMy =& CreateObject('dcl.htmlMyDCL');
		$objMy->showMy();
	}

	function viewjcn()
	{
		global $g_oSec;
		
		commonHeader();
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['jcn'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$iSeq = @DCL_Sanitize::ToInt($_REQUEST['seq']);
		if ($iSeq !== null && $iSeq > 0)
		{
			if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEW, $iID, $iSeq))
				return PrintPermissionDenied();
				
			$obj =& CreateObject('dcl.htmlWorkOrderDetail');
			$obj->Show($iID, $iSeq);
			return;
		}

		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEW, $iID))
			return PrintPermissionDenied();

		$objView =& CreateObject('dcl.boView');
		$objView->style = 'report';
		$objView->title = STR_WO_RESULTSTITLE;

		$objView->AddDef('filter', 'jcn', $iID);

		$objView->AddDef('columns', '',
			array('jcn', 'seq', 'responsible.short', 'products.name', 'statuses.name', 'eststarton', 'deadlineon',
				'etchours', 'totalhours', 'summary'));

		$objView->AddDef('order', '', array('jcn', 'seq'));

		$objView->AddDef('columnhdrs', '',
			array(STR_WO_JCN, STR_WO_SEQ, STR_WO_RESPONSIBLE, STR_WO_PRODUCT,
				STR_WO_STATUS, STR_WO_ESTSTART, STR_WO_DEADLINE, STR_WO_ETCHOURS, STR_WO_ACTHOURS, STR_WO_SUMMARY));

		$objHV =& CreateObject('dcl.htmlWorkOrderResults');
		$objHV->Render($objView);
	}

	function dbsearch()
	{
		global $g_oSec, $g_oSession;

		commonHeader();

		$personnel = isset($_REQUEST['personnel']) && is_array($_REQUEST['personnel']) ? $_REQUEST['personnel'] : array();
		$status = @$_REQUEST['status'];
		$is_public = @$_REQUEST['is_public'];
		$createdon = @$_REQUEST['createdon'];
		$closedon = @$_REQUEST['closedon'];
		$statuson = @$_REQUEST['statuson'];
		$lastactionon = @$_REQUEST['lastactionon'];
		$deadlineon = @$_REQUEST['deadlineon'];
		$eststarton = @$_REQUEST['eststarton'];
		$estendon = @$_REQUEST['estendon'];
		$starton = @$_REQUEST['starton'];
		$module_id = isset($_REQUEST['module_id']) && is_array($_REQUEST['module_id']) ? $_REQUEST['module_id'] : array();
		$searchText = $_REQUEST['searchText'];
		$tags = $_REQUEST['tags'];
		$columns = $_REQUEST['columns'];
		$groups = $_REQUEST['groups'];
		$order = $_REQUEST['order'];
		$columnhdrs = $_REQUEST['columnhdrs'];

		$account = @DCL_Sanitize::ToIntArray($_REQUEST['account']);
		$entity_source_id = @DCL_Sanitize::ToIntArray($_REQUEST['entity_source_id']);
		$severity = @DCL_Sanitize::ToIntArray($_REQUEST['severity']);
		$priority = @DCL_Sanitize::ToIntArray($_REQUEST['priority']);
		$dcl_status_type = @DCL_Sanitize::ToIntArray($_REQUEST['dcl_status_type']);
		$product = @DCL_Sanitize::ToIntArray($_REQUEST['product']);
		$department = @DCL_Sanitize::ToIntArray($_REQUEST['department']);
		$project = @DCL_Sanitize::ToIntArray($_REQUEST['project']);
		$wo_type_id = @DCL_Sanitize::ToIntArray($_REQUEST['wo_type_id']);

		$dateFrom = DCL_Sanitize::ToDate($_REQUEST['dateFrom']);
		$dateTo = DCL_Sanitize::ToDate($_REQUEST['dateTo']);

		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH))
			return PrintPermissionDenied();

		$objView = CreateObject('dcl.boView');
		$objView->table = 'workorders';

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

		if (count($personnel) > 0 || count($department) > 0)
		{
			$fieldList = array('responsible', 'createby', 'closedby');
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

		$fieldList = array('priority', 'severity', 'wo_type_id', 'entity_source_id');
		foreach($fieldList as $field)
		{
			if (count($$field) > 0)
				$objView->AddDef('filter', $field, $$field);
		}
		
		if (trim($tags) != '')
			$objView->AddDef('filter', 'dcl_tag.tag_desc', $tags);

		if (count($is_public) > 0)
		{
			foreach ($is_public as $publicValue)
			{
				if ($publicValue == 'Y' || $publicValue == 'N')
					$objView->AddDef('filter', 'is_public', "'" . $publicValue . "'");
			}
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

			$objView->AddDef('filter', 'module_id', $module);
		}

		$g_oSession->Unregister('showBM');
		if (count($product) > 0)
		{
			$objView->AddDef('filter', 'product', $product);

			// Adds BuildManager to drop down menu only if user selects a product
			if (count($product) == 1)
			{
				$g_oSession->Register('showBM', 1);
			}
		}

		$g_oSession->Edit();

		if (($dcl_status_type = DCL_Sanitize::ToIntArray($dcl_status_type)) === null)
			$dcl_status_type = array();
			
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

		if (count($account) > 0)
			$objView->AddDef('filter', 'dcl_wo_account.account_id', $account);

		// already sanitized this one above
		if (count($dcl_status_type) > 0)
			$objView->AddDef('filter', 'statuses.dcl_status_type', $dcl_status_type);

		if (count($project) > 0)
			$objView->AddDef('filter', 'dcl_projects.projectid', $project);

		if ($dateFrom != '' || $dateTo != '')
		{
			$fieldList = array('createdon', 'closedon', 'statuson', 'lastactionon', 'deadlineon',
					'eststarton', 'estendon', 'starton');

			foreach ($fieldList as $field)
			{
				if ($$field == '1')
					$objView->AddDef('filterdate', $field, array($dateFrom, $dateTo));
			}
		}

		if ($searchText != '')
		{
			$fieldList = array('summary', 'notes', 'description');
			foreach ($fieldList as $field)
			{
				if ($_REQUEST[$field] == '1')
					$objView->AddDef('filterlike', $field, $searchText);
			}
		}

		if (count($columns) > 0)
			$objView->AddDef('columns', '', $columns);

		if (count($groups) > 0)
		{
			foreach ($groups as $groupField)
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
			foreach ($order as $orderField)
			{
				if ($orderField == 'priorities.name')
					$order[$key] = 'priorities.weight';
				else if ($orderField == 'severities.name')
					$order[$key] = 'severities.weight';
			}

			$objView->AddDef('order', '', $order);
		}
		else
			$objView->AddDef('order', '', array('jcn', 'seq'));

		$objView->style = 'report';

		if ($_REQUEST['title'] != '')
			$objView->title = GPCStripSlashes($_REQUEST['title']);
		else
			$objView->title = STR_WO_RESULTSTITLE;

		$obj =& CreateObject('dcl.htmlWorkOrderResults');
		$obj->Render($objView);
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

		$obj =& CreateObject('dcl.htmlWorkorders');
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

		if (($iDays = @DCL_Sanitize::ToInt($_REQUEST['days'])) === null ||
			($dateFrom = @DCL_Sanitize::ToDate($_REQUEST['dateFrom'])) === null
			)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		$iProduct = 0;
		if (($iProduct = @DCL_Sanitize::ToInt($_REQUEST['product'])) === null)
		    $iProduct = 0;
		
		$objG =& CreateObject('dcl.boGraph');
		$obj =& CreateObject('dcl.dbWorkorders');
		
		$beginDate = new DCLTimestamp;
		$endDate = new DCLTimestamp;
		$testDate = new DCLDate;
		$testTS = new DCLTimestamp;

		$endDate->SetFromDisplay($dateFrom . ' 23:59:59');
		$beginDate->SetFromDisplay($dateFrom . ' 00:00:00');
		$beginDate->time -= (($iDays - 1) * 86400);
		$query = 'SELECT ' . $obj->ConvertTimestamp('createdon', 'createdon') . ', ' . $obj->ConvertTimestamp('closedon', 'closedon') . ' FROM workorders WHERE ';
		
		if ($iProduct > 0)
			$query .= 'product = ' . $iProduct . ' AND ';

		$query .= '(createdon between ' . $obj->DisplayToSQL($beginDate->ToDisplay());
		$query .= ' AND ' . $obj->DisplayToSQL($endDate->ToDisplay());
		$query .= ') OR (closedon between ' . $obj->DisplayToSQL($beginDate->ToDisplay());
		$query .= ' AND ' . $obj->DisplayToSQL($endDate->ToDisplay()) . ')';
		$obj->Query($query);

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
			$iTime = 0;
			for ($y = 0; $y < 2; $y++)
			{
				if ($y == 0)
				{
					$testTS->SetFromDB($obj->f($y));
					$iTime = $testTS->time;
				}
				else
				{
					$testDate->SetFromDB($obj->f($y));
					$iTime = $testDate->time;
				}

				$j = $iDays - 1;
				while ($j >= 0)
				{
					if ($iTime >= $daysBack[$j])
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

		$objG->title = STR_BO_WOGRAPHTITLE;
		if ($iProduct > 0)
		{
			$oDB =& CreateObject('dcl.dbProducts');
			if ($oDB->Load($iProduct) != -1)
				$objG->title .= ' ' . $oDB->name;
		}

		$objG->caption_y = STR_BO_WOGRAPHCAPTIONY;
		$objG->caption_x = STR_BO_GRAPHCAPTIONX;
		$objG->num_lines_y = 15;
		$objG->num_lines_x = $iDays;
		$objG->colors = array('red', 'blue');


		print('<center>');
		echo '<img border="0" src="', menuLink('', 'menuAction=boGraph.Show&' . $objG->ToURL()), '">';
		print('</center>');
	}

	function reassign()
	{
		global $g_oSec;
		
		commonHeader();
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['jcn'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (($iSeq = @DCL_Sanitize::ToInt($_REQUEST['seq'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ASSIGN, $iID, $iSeq))
			return PrintPermissionDenied();

		$objWO =& CreateObject('dcl.htmlWorkorders');
		$objWO->PrintReassignForm();

		$obj =& CreateObject('dcl.htmlWorkOrderDetail');
		$obj->Show($iID, $iSeq);
	}

	function dbreassign()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (
				($iID = @DCL_Sanitize::ToInt($_REQUEST['jcn'])) === null ||
				($iSeq = @DCL_Sanitize::ToInt($_REQUEST['seq'])) === null ||
				($iResponsible = @DCL_Sanitize::ToInt($_REQUEST['responsible'])) === null ||
				($fEstHours = @DCL_Sanitize::ToDecimal($_REQUEST['esthours'])) === null ||
				($fEtcHours = @DCL_Sanitize::ToDecimal($_REQUEST['etchours'])) === null ||
				($iSeverity = @DCL_Sanitize::ToInt($_REQUEST['severity'])) === null ||
				($iPriority = @DCL_Sanitize::ToInt($_REQUEST['priority'])) === null ||
				($deadlineon = @DCL_Sanitize::ToDate($_REQUEST['deadlineon'])) === null ||
				($eststarton = @DCL_Sanitize::ToDate($_REQUEST['eststarton'])) === null ||
				($estendon = @DCL_Sanitize::ToDate($_REQUEST['estendon'])) === null
			)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ASSIGN, $iID, $iSeq))
			return PrintPermissionDenied();

		$objWO =& CreateObject('dcl.dbWorkorders');
		if ($objWO->Load($iID, $iSeq) == -1)
			return;
		
		if ($objWO->responsible != $iResponsible ||
				$objWO->deadlineon != $deadlineon ||
				$objWO->eststarton != $eststarton ||
				$objWO->estendon != $estendon ||
				$objWO->esthours != $fEstHours ||
				$objWO->etchours != $fEtcHours ||
				$objWO->priority != $iPriority ||
				$objWO->status == $dcl_info['DCL_DEF_STATUS_UNASSIGN_WO'] ||
				$objWO->severity != $iSeverity)
		{
			$objWO->responsible = $iResponsible;
			$objWO->deadlineon = $deadlineon;
			$objWO->eststarton = $eststarton;
			$objWO->estendon = $estendon;
			$objWO->esthours = $fEstHours;

			$oStatus =& CreateObject("dcl.dbStatuses");
			if ($oStatus->GetStatusType($objWO->status) != 2)
			{
				$objWO->etchours = $fEtcHours;
				if ($objWO->status == $dcl_info['DCL_DEF_STATUS_UNASSIGN_WO'])
				{
					$objWO->status = $dcl_info['DCL_DEF_STATUS_ASSIGN_WO'];
					$objWO->statuson = $objWO->GetDateSQL();
				}
			}
			else
				$objWO->etchours = 0.0;

			$objWO->priority = $iPriority;
			$objWO->severity = $iSeverity;
			$objWO->Edit();

			$objWtch =& CreateObject('dcl.boWatches');
			$objWtch->sendNotification($objWO, '4');
		}

		$objHTMLWO =& CreateObject('dcl.htmlWorkOrderDetail');
		$objHTMLWO->Show($iID, $iSeq);
	}

	function batchassign()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ASSIGN))
			return PrintPermissionDenied();

		$objWO =& CreateObject('dcl.htmlWorkorders');
		$objWO->PrintReassignForm();

		$obj =& CreateObject('dcl.htmlTimeCards');
		$obj->ShowBatchWO();
	}

	function dbbatchassign()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ASSIGN))
			return PrintPermissionDenied();

		if (IsSet($_REQUEST['selected']) && is_array($_REQUEST['selected']) && count($_REQUEST['selected']) > 0)
		{
			$objWtch =& CreateObject('dcl.boWatches');
			$objWO =& CreateObject('dcl.dbWorkorders');
			$bNeedBreak = false;

			if (($iResponsible = @DCL_Sanitize::ToInt($_REQUEST['responsible'])) === null)
			{
				trigger_error('Data sanitize failed.');
				return;
			}

			if (($iPriority = @DCL_Sanitize::ToInt($_REQUEST['priority'])) === null)
				$iPriority = 0;

			if (($iSeverity = @DCL_Sanitize::ToInt($_REQUEST['severity'])) === null)
				$iSeverity = 0;
			
			foreach ($_REQUEST['selected'] as $val)
			{
				list($jcn, $seq) = explode('.', $val);
				if (($jcn = DCL_Sanitize::ToInt($jcn)) === null ||
					($seq = DCL_Sanitize::ToInt($seq)) === null)
				{
					trigger_error('Data sanitize failed.');
					return;
				}

				if ($objWO->Load($jcn, $seq) == -1)
					continue;
					
				if ($objWO->responsible != $iResponsible ||
						($iPriority > 0 && $objWO->priority != $iPriority) ||
						($iSeverity > 0 && $objWO->severity != $iSeverity))
				{
					$objWO->responsible = $iResponsible;
					
					if ($iPriority > 0)
						$objWO->priority = $iPriority;
						
					if ($iSeverity > 0)
						$objWO->severity = $iSeverity;
						
					$objWO->Edit();

					$objWtch->sendNotification($objWO, '4,1');
				}
			}
		}

		if (EvaluateReturnTo())
			return;

		$objView =& CreateObject('dcl.boView');
		$objView->SetFromURL();
		
		$objH =& CreateObject('dcl.htmlWorkOrderResults');
		$objH->Render($objView);
	}

	function upload()
	{
		global $g_oSec;
		
		commonHeader();
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['jcn'])) === null ||
			($iSeq = @DCL_Sanitize::ToInt($_REQUEST['seq'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ATTACHFILE, $iID, $iSeq))
			return PrintPermissionDenied();

		$obj =& CreateObject('dcl.htmlWorkorders');
		$obj->ShowUploadFileForm($iID, $iSeq);

		$objWO =& CreateObject('dcl.htmlWorkOrderDetail');
		$objWO->Show($iID, $iSeq);
	}

	function doupload()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['jcn'])) === null ||
			($iSeq = @DCL_Sanitize::ToInt($_REQUEST['seq'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ATTACHFILE, $iID, $iSeq))
			return PrintPermissionDenied();
		
		$oWO =& CreateObject('dcl.dbWorkorders');
		if ($oWO->Load($iID, $iSeq) == -1)
			return;

		if (($sFileName = DCL_Sanitize::ToFileName('userfile')) === null)
			return PrintPermissionDenied();

		$o = CreateObject('dcl.boFile');
		$o->iType = DCL_ENTITY_WORKORDER;
		$o->iKey1 = $iID;
		$o->iKey2 = $iSeq;
		$o->sFileName = DCL_Sanitize::ToActualFileName('userfile');
		$o->sTempFileName = $sFileName;
		$o->sRoot = $dcl_info['DCL_FILE_PATH'] . '/attachments';
		$o->Upload();
		
		$obj = CreateObject('dcl.htmlWorkOrderDetail');
		$obj->Show($iID, $iSeq);
	}

	function deleteattachment()
	{
		global $g_oSec;
		
		commonHeader();
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['jcn'])) === null ||
			($iSeq = @DCL_Sanitize::ToInt($_REQUEST['seq'])) === null ||
			!@DCL_Sanitize::IsValidFileName($_REQUEST['filename']))
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_REMOVEFILE, $iID, $iSeq))
			return PrintPermissionDenied();

		$objH =& CreateObject('dcl.htmlWorkorders');
		$objH->ShowDeleteAttachmentYesNo($iID, $iSeq, $_REQUEST['filename']);

		$obj =& CreateObject('dcl.htmlWorkOrderDetail');
		$obj->Show($iID, $iSeq);
	}

	function dodeleteattachment()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['jcn'])) === null ||
			($iSeq = @DCL_Sanitize::ToInt($_REQUEST['seq'])) === null ||
			!@DCL_Sanitize::IsValidFileName($_REQUEST['filename']))
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_REMOVEFILE, $iID, $iSeq))
			return PrintPermissionDenied();

		$attachPath = $dcl_info['DCL_FILE_PATH'] . '/attachments/wo/' . substr($iID, -1) . '/' . $iID . '/' . $iSeq . '/';
		if (is_file($attachPath . $_REQUEST['filename']) && is_readable($attachPath . $_REQUEST['filename']))
			unlink($attachPath . $_REQUEST['filename']);

		$obj =& CreateObject('dcl.htmlWorkOrderDetail');
		$obj->Show($iID, $iSeq);
	}

	// THANKS: Urmet Janes
	function csvupload()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_IMPORT))
			return PrintPermissionDenied();

		$objHTMLWO =& CreateObject('dcl.htmlWorkorders');
		$objHTMLWO->ShowCSVUploadDialog();
	}

	function verifyID($field, $id)
	{
		if ($this->oMetaData === null)
			$this->oMetaData =& CreateObject('dcl.DCL_MetadataDisplay');

		$oVal = null;
		switch ($field)
		{
			case 'account':
				$aRetVal = $this->oMetaData->GetOrganization($id);
				$oVal = $aRetVal['name'];
				break;
			case 'contact_id':
				$aRetVal = $this->oMetaData->GetContact($id);
				$oVal = $aRetVal['name'];
				break;
			case 'product':
				$oVal = $this->oMetaData->GetProduct($id);
				break;
			case 'wo_type_id':
				$oVal = $this->oMetaData->GetWorkOrderType($id);
				break;
			case 'priority':
				$oVal = $this->oMetaData->GetPriority($id);
				break;
			case 'severity':
				$oVal = $this->oMetaData->GetSeverity($id);
				break;
			case 'responsible':
				$oVal = $this->oMetaData->GetPersonnel($id);
				break;
			case 'project':
				$oVal = $this->oMetaData->GetProject($id);
				break;
			case 'entity_source_id':
				$oVal = $this->oMetaData->GetSource($id);
				break;
			default:
				$oVal = 1;
		}

		return ($oVal !== null && $oVal != '');
	}

	// THANKS: Urmet Janes
	function docsvupload()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_IMPORT))
			return PrintPermissionDenied();

		if (($sTempFileName = DCL_Sanitize::ToFileName('userfile')) === null)
			return PrintPermissionDenied();
		
		// Open the file as text - let PHP take care of line
		// delimiter differences
		$hFile = fopen($sTempFileName, 'r');
		if(!$hFile)
		{
			trigger_error(STR_BO_CSVUPLOADERR);
			return;
		}

		// Get the line containing field names
		$newjcns = array();
		$line = 1;
		$fields = fgetcsv($hFile, 1000);

		// Define a useful function for mapping a short name to ID
		// It is really ineffective to instantiate a new object for
		// each field!
		function findID($obj, $table, $value, $pk = 'id', $fd = 'short', $fd2 = '', $val2 = '')
		{
			$sSQL = "SELECT $pk FROM $table WHERE $fd = " . $obj->Quote($value);
			if ($fd2 != '' && $val2 != '')
				$sSQL .= " AND $fd2 = $val2";

			$obj->Query($sSQL);
			if($obj->next_record())
				return $obj->f(0);
			else
				return -1;
		}
		
		$objWorkorder =& CreateObject('dcl.dbWorkorders');
		$objTemp =& CreateObject('dcl.dbWorkorders');
		$objProjectmap =& CreateObject('dcl.dbProjectmap');
		$objWtch =& CreateObject('dcl.boWatches');

		while($data = fgetcsv($hFile, 1000))
		{
			$line++;
			$projectid = -1;
			$module_id = -1;
			$objWorkorder->Clear();

			while (list($i, $val) = each($data))
			{
				if (!is_numeric($val))
				{
					// we may need to convert smth
					switch ($fields[$i])
					{
						case 'product':
							$new_val = findID($objTemp, 'products', $val);
							break;
						case 'module_id':
							$module_id = $val;
							continue;
							break;
						case 'account':
							$new_val = findID($objTemp, 'accounts', $val);
							break;
						case 'wo_type_id':
							$new_val = findID($objTemp, 'dcl_wo_type', $val, 'wo_type_id', 'type_name');
							break;
						case 'entity_source_id':
							$new_val = findID($objTemp, 'dcl_entity_source', $val, 'entity_source_id', 'entity_source_name');
							break;
						case 'priority':
							$new_val = findID($objTemp, 'priorities', $val);
							break;
						case 'severity':
							$new_val = findID($objTemp, 'severities', $val);
							break;
						case 'responsible':
							$new_val = findID($objTemp, 'personnel', $val);
							break;
						case 'project':
							$new_val = findID($objTemp, 'dcl_projects', $val, 'projectid', 'name');
							$projectid = $new_val;
							break;
						default:
							$new_val = $val;
					}

					if ($new_val == -1)
					{
						// An error on mapping
						trigger_error(sprintf(STR_BO_CSVMAPERR, $fields[$i], $line), E_USER_ERROR);
						continue 2;       // On to next line in the file
					}

					$val = $new_val;
				}
				else if ($fields[$i] == 'module_id')
				{
					$module_id = $val;
				}
				else
				{
					if (!$this->verifyID($fields[$i], $val))
					{
						// An error on mapping
						trigger_error(sprintf(STR_BO_CSVMAPERR, $fields[$i], $line), E_USER_ERROR);
						continue 2;       // On to next line in the file
					}
				}

				if ($fields[$i] != 'project' && $fields[$i] != 'module_id')
				{
					// This will ignore nonexisting members
					// Only works in PHP4 because Clear() initializes each field!
					if (isset($objWorkorder->$fields[$i]))
						$objWorkorder->$fields[$i] = $val;
				}
			}

			// Lookup module if specified
			if ($module_id != -1)
			{
				if (is_numeric($module_id))
				{
					// just verify this module exists for this product
					if ($objTemp->ExecuteScalar("SELECT COUNT(*) FROM dcl_product_module WHERE product_module_id = $module_id AND product_id = " . $objWorkorder->product) > 0)
						$objWorkorder->module_id = $module_id;
				}
				else
					$objWorkorder->module_id = findID($objTemp, 'dcl_product_module', $module_id, 'product_module_id', 'module_name', 'product_id', $objWorkorder->product);
			}

			$objWorkorder->createby = $GLOBALS['DCLID'];
			$objWorkorder->Add();

			if ($objWorkorder->jcn > 0)
			{
				if ($projectid > 0)
				{
					// Project specified, so try to add it
					$objProjectmap->projectid = $projectid;
					$objProjectmap->jcn = $objWorkorder->jcn;
					$objProjectmap->seq = $objWorkorder->seq;
					$objProjectmap->Add();
				}

				// Add it to our new work order collection
				$newjcns[] = $objWorkorder->jcn;

				// Send notification
				$objWtch->sendNotification($objWorkorder, '4,1');
			}
		}

		if (count($newjcns) > 0)
		{
			// Display imported work orders
			$objView =& CreateObject('dcl.boView');
			$objView->style = 'report';
			$objView->title = 'Work Order CSV Upload Results';
			$objView->AddDef('filter', 'jcn', $newjcns);
			$objView->AddDef('order', 'jcn');
	
			$objView->AddDef('columns', '',
				array('jcn', 'seq', 'responsible.short', 'products.name', 'statuses.name', 'eststarton', 'deadlineon',
					'etchours', 'totalhours', 'summary'));
	
			$objView->AddDef('columnhdrs', '',
				array(STR_WO_JCN, STR_WO_SEQ, STR_WO_RESPONSIBLE, STR_WO_PRODUCT,
					STR_WO_STATUS, STR_WO_ESTSTART, STR_WO_DEADLINE, STR_WO_ETCHOURS, STR_WO_ACTHOURS, STR_WO_SUMMARY));
	
			$objHV =& CreateObject('dcl.htmlWorkOrderResults');
			$objHV->Render($objView);
		}
	}

	function showmy()
	{
		commonHeader();
		
		$obj =& CreateObject('dcl.htmlWorkorders');
		$objDB =& CreateObject('dcl.dbWorkorders');
		
		if ($_REQUEST['which'] == 'responsible')
			$obj->showmy($objDB, 'responsible', STR_WO_MYWO, STR_WO_NOOPEN, 0);
		else
			$obj->showmy($objDB, 'createby', STR_WO_MYSUBMISSIONS, STR_WO_NOSUBMISSIONS, 0);
	}

	function batchdetail()
	{
		global $g_oSec;
		
		commonHeader();

		if (IsSet($_REQUEST['selected']) && is_array($_REQUEST['selected']) && count($_REQUEST['selected']) > 0)
		{
			$obj =& CreateObject('dcl.htmlWorkOrderDetail');
			$objWorkorder =& CreateObject('dcl.dbWorkorders');
			$bNeedBreak = false;
			
			foreach ($_REQUEST['selected'] as $val)
			{
				if ($bNeedBreak)
					print('<p style="page-break-after: always;">');

				list($jcn, $seq) = explode('.', $val);
				if (($jcn = DCL_Sanitize::ToInt($jcn)) === null ||
					($seq = DCL_Sanitize::ToInt($seq)) === null)
				{
					trigger_error('Data sanitize failed.');
					return;
				}

				if ($g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEW, $jcn, $seq))
				{
					if ($objWorkorder->Load($jcn, $seq) == -1)
						continue;
					
					$obj->Show($jcn, $seq);
					$bNeedBreak = true;
				}
			}
		}
		else
		{
			$objView =& CreateObject('dcl.boView');
			$objView->SetFromURL();
			
			$objH =& CreateObject('dcl.htmlWorkOrderResults');
			$objH->Render($objView);
		}
	}
}
?>
