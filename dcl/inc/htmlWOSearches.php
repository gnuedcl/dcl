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

class htmlWOSearches
{
	function ShowView()
	{
		global $g_oSec;
		
		commonHeader();
		if (($id = DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH))
			throw new PermissionDeniedException();

		$oDB = new dbViews();
		if ($oDB->Load($id) != -1)
		{
			$oView = new boView();
			$oView->SetFromURLString($oDB->viewurl);
			$this->Show($oView);
		}
	}

	function ShowRequest()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH))
			throw new PermissionDeniedException();

		$oView = new boView();
		$oView->SetFromURL();
		$this->Show($oView);
	}

	function Show($oView = '')
	{
		global $dcl_info, $g_oSec, $g_oSession;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH))
			throw new PermissionDeniedException();

		$bView = is_object($oView);
		
		$aProtectedFields = array('notes', 'dcl_hotlist.hotlist_tag', 'is_public', 'timecards.actionby', 'timecards.summary');

		$objJS = new jsAttributesets();
		$objJS->bModules = true;
		$objJS->bStatusTypes = true;
		$objJS->bDepartments = !$g_oSec->IsPublicUser();
		$objJS->DisplayAttributeScript();

		$objPersonnel = new htmlPersonnel();
		$objProducts = new htmlProducts();
		$objSeverities = new htmlSeverities();
		$objStatuses = new htmlStatuses();
		$objProjects = new htmlProjects();
		$objModules = new htmlProductModules();
		$objType = new htmlWorkOrderType();

		$oDBP = new dbPersonnel();
		$oDBP->Load($GLOBALS['DCLID']);
		
		$t = new DCL_Smarty();
		
		if ($bView)
			$t->assign('VAL_REPORTTITLE', $oView->title);
		else
			$t->assign('VAL_REPORTTITLE', '');

		$t->assign('VAL_DEPARTMENT', $oDBP->department);
		$t->assign('VAL_ID', $GLOBALS['DCLID']);

		$aDefault = array();
		$aDefault['product'] = array();
		$aDefault['priority'] = array();
		$aDefault['severity'] = array();
		$aDefault['dcl_wo_account.account_id'] = array();
		$aDefault['status'] = array();
		$aDefault['project'] = array();
		$aDefault['wo_type_id'] = array();
		$aDefault['statuses.dcl_status_type'] = array();
		$aDefault['department'] = array();
		$aDefault['personnel'] = array();
		$aDefault['is_public'] = array();
		$aDefault['entity_source_id'] = array();
		$aDefault['module_id'] = array();
		$sPersonnelKey = '';
		$sStatusKey = '';
		$sModuleKey = '';

		if ($bView)
		{
			foreach ($oView->filter as $field => $values)
			{
				if (substr($field, 1) == '.department')
				{
					if ($field[0] == 'a')
						$t->assign('CHK_RESPONSIBLE', ' checked');
					else if ($field[0] == 'b')
						$t->assign('CHK_CREATEBY', ' checked');
					else if ($field[0] == 'c')
						$t->assign('CHK_CLOSEDBY', ' checked');

					$field = 'department';
					$sPersonnelKey = '';
				}
				else if ($field == 'responsible' || $field == 'createby' || $field == 'closedby')
				{
					$t->assign('CHK_' . strtoupper($field), ' checked');
					$field = 'personnel';
					$sPersonnelKey = '';
				}
				else if ($field == 'account')
				{
					$field = 'dcl_wo_account.account_id';
				}
				else if ($field == 'dcl_projects.projectid')
				{
					$field = 'project';
				}

				if (array_key_exists($field, $aDefault))
					$aDefault[$field] = $values;
			}

			if (isset($aDefault['personnel']) && is_array($aDefault['personnel']) && count($aDefault['personnel']) > 0)
			{
				$sPersonnel = implode(',', $aDefault['personnel']);
				$oDBP->Query("select department, id from personnel where id in ($sPersonnel)");
				while ($oDBP->next_record())
				{
					if (!in_array($oDBP->f(0), $aDefault['department']))
						$aDefault['department'][] = $oDBP->f(0);

					if ($sPersonnelKey != '')
						$sPersonnelKey .= ':';

					$sPersonnelKey .= sprintf('%d,%d', $oDBP->f(0), $oDBP->f(1));
				}
			}

			if (isset($aDefault['status']) && is_array($aDefault['status']) && count($aDefault['status']) > 0)
			{
				$sStatus = implode(',', $aDefault['status']);
				$oDBP->Query("select dcl_status_type, id from statuses where id in ($sStatus)");
				while ($oDBP->next_record())
				{
					if (!in_array($oDBP->f(0), $aDefault['statuses.dcl_status_type']))
						$aDefault['statuses.dcl_status_type'][] = $oDBP->f(0);

					if ($sStatusKey != '')
						$sStatusKey .= ':';

					$sStatusKey .= sprintf('%d,%d', $oDBP->f(0), $oDBP->f(1));
				}
			}

			if (isset($aDefault['module_id']) && is_array($aDefault['module_id']) && count($aDefault['module_id']) > 0)
			{
				$sModule = implode(',', $aDefault['module_id']);
				$oDBP->Query("select product_id, product_module_id from dcl_product_module where product_module_id in ($sModule)");
				while ($oDBP->next_record())
				{
					if (!in_array($oDBP->f(0), $aDefault['product']))
						$aDefault['product'][] = $oDBP->f(0);

					if ($sModuleKey != '')
						$sModuleKey .= ':';

					$sModuleKey .= sprintf('%d,%d', $oDBP->f(0), $oDBP->f(1));
				}
			}
		}
		else
		{
			$aDefault['product'] = 0;
			$aDefault['priority'] = 0;
			$aDefault['severity'] = 0;
			$aDefault['dcl_wo_account.account_id'] = 0;
			$aDefault['status'] = 0;
			$aDefault['project'] = 0;
			$aDefault['wo_type_id'] = 0;
			$aDefault['entity_source_id'] = 0;
			$aDefault['is_public'] = 0;
			$aDefault['statuses.dcl_status_type'] = 1;
			$aDefault['department'] = array($oDBP->department);
			$aDefault['personnel'] = $GLOBALS['DCLID'];
			$sPersonnelKey = sprintf('%d,%d', $oDBP->department, $GLOBALS['DCLID']);
			$sStatusKey = '';
			$sModuleKey = '';

			if ($GLOBALS['g_oSec']->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION))
			{
				$t->assign('CHK_RESPONSIBLE', ' checked');
				$t->assign('CHK_CREATEBY', '');
				$t->assign('CHK_CLOSEDBY', '');
			}
			else
			{
				$t->assign('CHK_CREATEBY', ' checked');
				$t->assign('CHK_RESPONSIBLE', '');
				$t->assign('CHK_CLOSEDBY', '');
			}
		}
		
		$t->assign('VAL_DEPARTMENTS', $aDefault['department']);
		$t->assign('VAL_PERSONNEL', $aDefault['personnel']);
		$t->assign('VAL_WO_TYPE', $aDefault['wo_type_id']);
		$t->assign('VAL_PRODUCT', $aDefault['product']);
		$t->assign('VAL_MODULE', $aDefault['module_id']);
		$t->assign('VAL_PRIORITY', $aDefault['priority']);
		$t->assign('VAL_SEVERITY', $aDefault['severity']);

		$t->assign('VAL_SELECTPERSONNELKEY', $sPersonnelKey);
		$t->assign('VAL_SELECTSTATUSKEY', $sStatusKey);
		$t->assign('VAL_SELECTMODULEKEY', $sModuleKey);

		$t->assign('CMB_STATUSES', $objStatuses->GetCombo($aDefault['status'], 'status', 'name', 8));

		$t->assign('IS_PUBLIC', $g_oSec->IsPublicUser());
		if (!$g_oSec->IsPublicUser())
		{
			$t->assign('CMB_PROJECTS', $objProjects->GetCombo($aDefault['project'], 'project', 'name', 8));
			$t->assign('CMB_PUBLIC', GetYesNoCombo($aDefault['is_public'], 'is_public', 2, false));
		}

		$oSelect = new htmlSelect();

		if ($g_oSec->IsOrgUser())
			$oSelect->SetOptionsFromDb('dcl_org', 'org_id', 'name', 'org_id IN (' . $g_oSession->Value('member_of_orgs') . ')', 'name');
		else
			$oSelect->SetOptionsFromDb('dcl_org', 'org_id', 'name', '', 'name');
		
		$oSelect->iSize = 8;
		$oSelect->vDefault = $aDefault['dcl_wo_account.account_id'];
		$oSelect->sName = 'account';
		$t->assign('CMB_ACCOUNTS', $oSelect->GetHTML());

		$oSource = new htmlEntitySource();
		$t->assign('CMB_SOURCE', $oSource->GetCombo($aDefault['entity_source_id'], 'entity_source_id', 8, false));

		// Empty status is for selecting status type, then filtering status if desired
		$oSelect->sName = 'status';
		$oSelect->iSize = 8;
		$t->assign('CMB_STATUSESEMPTY', $oSelect->GetHTML());

		// Status Types
		$oSelect->sName = 'dcl_status_type';
		$oSelect->iSize = 8;
		$oSelect->vDefault = $aDefault['statuses.dcl_status_type'];
		$oSelect->SetOptionsFromDb('dcl_status_type', 'dcl_status_type_id', 'dcl_status_type_name', '', 'dcl_status_type_id');
		$t->assign('CMB_STATUSTYPES', $oSelect->GetHTML());

		$t->assign('CHK_SUMMARY', '');
		$t->assign('CHK_NOTES', '');
		$t->assign('CHK_DESCRIPTION', '');
		$t->assign('VAL_SEARCHTEXT', '');
		if ($bView && count($oView->filterlike) > 0)
		{
			$searchText = '';
			foreach ($oView->filterlike as $field => $values)
			{
				if ($field == 'summary' || $field == 'notes' || $field == 'description')
				{
					$t->assign('CHK_' . strtoupper($field), ' CHECKED');
					$searchText = $values[0];
				}
			}

			$t->assign('VAL_SEARCHTEXT', $searchText);
		}
		
		if (isset($oView->filter['dcl_tag.tag_desc']) && is_array($oView->filter['dcl_tag.tag_desc']) && count($oView->filter['dcl_tag.tag_desc']) > 0)
			$t->assign('VAL_TAGS', join(',', $oView->filter['dcl_tag.tag_desc']));

		if (isset($oView->filter['dcl_hotlist.hotlist_tag']) && is_array($oView->filter['dcl_hotlist.hotlist_tag']) && count($oView->filter['dcl_hotlist.hotlist_tag']) > 0)
			$t->assign('VAL_HOTLISTS', join(',', $oView->filter['dcl_hotlist.hotlist_tag']));

		$aDateChecks = array('createdon', 'closedon', 'statuson', 'lastactionon',
							'deadlineon', 'eststarton', 'estendon', 'starton');

		for ($i = 0; $i < count($aDateChecks); $i++)
			$t->assign('CHK_' . strtoupper($aDateChecks[$i]), '');

		if ($bView)
		{
			$t->assign('VAL_DATEFROM', '');
			$t->assign('VAL_DATETO', '');
			if (count($oView->filterdate) > 0)
			{
				$fromDate = '';
				$toDate = '';

				foreach ($oView->filterdate as $field => $values)
				{
					$t->assign('CHK_' . strtoupper($field), ' CHECKED');
					$fromDate = $values[0];
					$toDate = $values[1];
				}

				$t->assign('VAL_DATEFROM', $fromDate);
				$t->assign('VAL_DATETO', $toDate);
			}
		}
		else
		{
			$aFewDaysAgo = mktime(0, 0, 0, date('m'), date('d') - 3, date('Y'));
			$t->assign('VAL_DATEFROM', date($dcl_info['DCL_DATE_FORMAT'], $aFewDaysAgo));
			$t->assign('VAL_DATETO', date($dcl_info['DCL_DATE_FORMAT']));
		}

		$aCols = array(
				'jcn' => STR_WO_JCN,
				'seq' => STR_WO_SEQ,
				'dcl_wo_type.type_name' => STR_WO_TYPE,
				'responsible.short' => STR_WO_RESPONSIBLE,
				'products.name' => STR_WO_PRODUCT,
				'statuses.name' => STR_WO_STATUS,
				'summary' => STR_WO_SUMMARY,
				'dcl_product_module.module_name' => STR_CMMN_MODULE,
				'dcl_projects.name' => STR_WO_PROJECT,
				'dcl_org.name' => STR_WO_ACCOUNT,
				'count(*):dcl_org' => '# ' . STR_WO_ACCOUNT,
				'dcl_tag.tag_desc' => STR_CMMN_TAGS,
				'dcl_hotlist.hotlist_tag' => 'Hotlists',
				'createby.short' => STR_WO_OPENBY,
				'createdon' => STR_WO_OPENEDON,
				'closedby.short' => STR_WO_CLOSEBY,
				'closedon' => STR_WO_CLOSEDON,
				'statuson' => STR_WO_STATUSON,
				'lastactionon' => STR_WO_LASTACTION,
				'deadlineon' => STR_WO_DEADLINE,
				'eststarton' => STR_WO_ESTSTART,
				'estendon' => STR_WO_ESTEND,
				'starton' => STR_WO_START,
				'esthours' => STR_WO_ESTHOURS,
				'etchours' => STR_WO_ETCHOURS,
				'totalhours' => STR_WO_ACTHOURS,
				'priorities.name' => STR_WO_PRIORITY,
				'severities.name' => STR_WO_SEVERITY,
				'reported_version_id.product_version_text' => STR_WO_REVISION,
				'targeted_version_id.product_version_text' => 'Target Version',
				'fixed_version_id.product_version_text' => 'Fixed Version',
				'dcl_contact.last_name' => 'Contact Last Name',
				'dcl_contact.first_name' => 'Contact First Name',
				'dcl_contact_phone.phone_number' => STR_WO_CONTACTPHONE,
				'notes' => STR_WO_NOTES,
				'description' => STR_WO_DESCRIPTION,
				'dcl_status_type.dcl_status_type_name' => STR_CMMN_STATUSTYPE,
				'dcl_entity_source.entity_source_name' => STR_CMMN_SOURCE,
				'is_public' => STR_CMMN_PUBLIC,
				'actionby.short' => 'Last Time Card By',
				'timecards.summary' => 'Last Time Card Summary'
			);
			
		if ($g_oSec->IsPublicUser())
		{
			foreach ($aProtectedFields as $sField)
				unset($aCols[$sField]);
		}

		if ($bView)
		{
			$aShow = array();
			$aGroup = array();

			foreach ($oView->columns as $colName)
			{
				if ($colName == 'a.short')
					$colName = 'responsible.short';
				else if ($colName == 'b.short')
					$colName = 'closedby.short';
				else if ($colName == 'c.short')
					$colName = 'createby.short';
				else if ($colName == 'g.short')
					$colName = 'actionby.short';
					
				$aShow[$colName] = $aCols[$colName];
			}

			foreach ($oView->groups as $colName)
			{
				if ($colName == 'a.short')
					$colName = 'responsible.short';
				else if ($colName == 'b.short')
					$colName = 'closedby.short';
				else if ($colName == 'c.short')
					$colName = 'createby.short';
				else if ($colName == 'g.short')
					$colName = 'actionby.short';
					
				$aGroup[$colName] = $aCols[$colName];
			}
			
			if ($g_oSec->IsPublicUser())
			{
				foreach ($aProtectedFields as $sField)
				{
					if (isset($aShow[$sField]))
						unset($aShow[$sField]);
						
					if (isset($aGroup[$sField]))
						unset($aGroup[$sField]);
				}
			}
		}
		else
		{
			$aShow = array(
					'jcn' => STR_WO_JCN,
					'seq' => STR_WO_SEQ,
					'dcl_wo_type.type_name' => STR_WO_TYPE,
					'responsible.short' => STR_WO_RESPONSIBLE,
					'products.name' => STR_WO_PRODUCT,
					'statuses.name' => STR_WO_STATUS,
					'summary' => STR_WO_SUMMARY
				);

			$aGroup = array();
		}

		array_remove_keys($aCols, $aShow);
		array_remove_keys($aCols, $aGroup);

		$t->assign('VAL_COLS', $aCols);
		$t->assign('VAL_SHOW', $aShow);
		$t->assign('VAL_GROUP', $aGroup);

		if ($bView)
		{
			$aOrder = array();
			
			foreach ($oView->order as $val)
			{
				if ($val == 'priorities.weight')
					$sText = isset($aShow['priorities.name']) ? $aShow['priorities.name'] : $aCols['priorities.name'];
				else if ($val == 'severities.weight')
					$sText = isset($aShow['severities.name']) ? $aShow['severities.name'] : $aCols['severities.name'];
				else if (IsSet($aShow[$val]))
					$sText = $aShow[$val];
				else if (isset($aCols[$val]))
					$sText = $aCols[$val];

				array_push($aOrder, array($val => $sText));
			}
			
			$t->assign('VAL_SORT');
		}

		$t->Render('htmlWorkOrderSearch.tpl');
	}

	function my()
	{
		global $dcl_info;
		
		$t = new DCL_Smarty();

		$obj = new htmlViews();
		$t->assign('CMB_VIEWS', $obj->GetCombo(0, 'viewid', 0, true, 'workorders'));
		
		$t->Render('htmlMyWorkorderSearches.tpl');
	}
}
