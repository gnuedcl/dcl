<?php
/*
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
LoadStringResource('tck');

class htmlTicketSearches
{
	function ShowView()
	{
		commonHeader();

		if (($id = Filter::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$oDB = new SavedSearchesModel();
		if ($oDB->Load(array('viewid' => $id)) != -1)
		{
			$oView = new boView();
			$oView->SetFromURLString($oDB->viewurl);
			$this->Show($oView);
		}
	}

	function ShowRequest()
	{
		commonHeader();

		$oView = new boView();
		$oView->SetFromURL();
		$this->Show($oView);
	}

	function Show($oView = '')
	{
		global $dcl_info, $g_oSec, $g_oSession;

		commonHeader();

		$bView = is_object($oView);

		$objJS = new AttributeSetJsHelper();
		$objJS->bModules = true;
		$objJS->bStatusTypes = true;
		$objJS->bDepartments = !$g_oSec->IsPublicUser();
		$objJS->DisplayAttributeScript();

		$oSelect = new SelectHtmlHelper();
		$objStatuses = new StatusHtmlHelper();
		$objModules = new htmlProductModules();

		$oDBP = new PersonnelModel();
		$oDBP->Load(DCLID);
		
		$t = new SmartyHelper();
		
		if ($bView)
			$t->assign('VAL_REPORTTITLE', $oView->title);
		else
			$t->assign('VAL_REPORTTITLE', '');

		$t->assign('VAL_DEPARTMENT', $oDBP->department);
		$t->assign('VAL_ID', DCLID);

		$aDefault = array();
		$aDefault['product'] = array();
		$aDefault['module_id'] = array();
		$aDefault['priority'] = array();
		$aDefault['type'] = array();
		$aDefault['account'] = array();
		$aDefault['status'] = array();
		$aDefault['statuses.dcl_status_type'] = array();
		$aDefault['department'] = array();
		$aDefault['personnel'] = array();
		$aDefault['entity_source_id'] = array();
		$aDefault['is_public'] = array();
		$sPersonnelKey = '';
		$sStatusKey = '';
		$sModuleKey = '';

		if ($bView)
		{
			reset($oView->filter);
			while (list($field, $values) = each($oView->filter))
			{
				if (mb_substr($field, 1) == '.department')
				{
					if ($field[0] == 'a')
						$t->assign('CHK_RESPONSIBLE', ' checked');
					else if ($field[0] == 'b')
						$t->assign('CHK_CREATEDBY', ' checked');
					else if ($field[0] == 'c')
						$t->assign('CHK_CLOSEDBY', ' checked');

					$field = 'department';
					$sPersonnelKey = '';
				}
				else if ($field == 'responsible' || $field == 'createdby' || $field == 'closedby')
				{
					$t->assign('CHK_' . mb_strtoupper($field), ' checked');
					$field = 'personnel';
					$sPersonnelKey = '';
				}

				if (array_key_exists($field, $aDefault))
					$aDefault[$field] = $values;
			}

			if (is_array($aDefault['personnel']) && count($aDefault['personnel']) > 0)
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

			if (is_array($aDefault['status']) && count($aDefault['status']) > 0)
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

			if (is_array($aDefault['module_id']) && count($aDefault['module_id']) > 0)
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
			$aDefault['type'] = 0;
			$aDefault['account'] = 0;
			$aDefault['status'] = 0;
			$aDefault['project'] = 0;
			$aDefault['entity_source_id'] = 0;
			$aDefault['statuses.dcl_status_type'] = 1;
			$aDefault['department'] = array($oDBP->department);
			$aDefault['personnel'] = DCLID;
			$aDefault['is_public'] = '';
			$sPersonnelKey = sprintf('%d,%d', $oDBP->department, DCLID);
			$sStatusKey = '';
			$sModuleKey = '';

			if ($g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ACTION))
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
		$t->assign('VAL_PRODUCT', $aDefault['product']);
		$t->assign('VAL_MODULE', $aDefault['module_id']);
		$t->assign('VAL_PRIORITY', $aDefault['priority']);
		$t->assign('VAL_TYPE', $aDefault['type']);

		$t->assign('VAL_SELECTPERSONNELKEY', $sPersonnelKey);
		$t->assign('VAL_SELECTSTATUSKEY', $sStatusKey);
		$t->assign('VAL_SELECTMODULEKEY', $sModuleKey);
		$t->assign('CMB_STATUS', $objStatuses->Select($aDefault['status'], 'status', 'name', 8));
		$t->assign('IS_PUBLIC', $g_oSec->IsPublicUser());
		if (!$g_oSec->IsPublicUser())
		{
			$t->assign('CMB_PUBLIC', GetYesNoCombo($aDefault['is_public'], 'is_public', 3));
		}

		if ($g_oSec->IsOrgUser())
			$oSelect->SetOptionsFromDb('dcl_org', 'org_id', 'name', 'org_id IN (' . $g_oSession->Value('member_of_orgs') . ')', 'name');
		else
			$oSelect->SetOptionsFromDb('dcl_org', 'org_id', 'name', '', 'name');
		
		$oSelect->Size = 8;
		$oSelect->DefaultValue = $aDefault['account'];
		$oSelect->Id = 'account';
		$t->assign('CMB_ACCOUNTS', $oSelect->GetHTML());

		$oSource = new EntitySourceHtmlHelper();
		$t->assign('CMB_SOURCE', $oSource->Select($aDefault['entity_source_id'], 'entity_source_id', 8, false));

		// Modules only show for selected products
		$oSelect->Options = array();
		$oSelect->Id = 'module_id';
		$oSelect->Size = 8;
		$t->assign('CMB_MODULES', $oSelect->GetHTML());

		// Empty status is for selecting status type, then filtering status if desired
		$oSelect->Id = 'status';
		$oSelect->Size = 8;
		$t->assign('CMB_STATUSESEMPTY', $oSelect->GetHTML());

		// Status Types
		$oSelect->Id = 'dcl_status_type';
		$oSelect->Size = 8;
		$oSelect->DefaultValue = $aDefault['statuses.dcl_status_type'];
		$oSelect->SetOptionsFromDb('dcl_status_type', 'dcl_status_type_id', 'dcl_status_type_name', '', 'dcl_status_type_id');
		$t->assign('CMB_STATUSTYPES', $oSelect->GetHTML());
		if ($g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ACTION))
		{
			$t->assign('CHK_SECLVLWO', ' CHECKED');
			$t->assign('CHK_NOSECLVLWO', '');
		}
		else
		{
			$t->assign('CHK_SECLVLWO', '');
			$t->assign('CHK_NOSECLVLWO', ' CHECKED');
		}

		$t->assign('VAL_SEARCHTEXT', '');
		if ($bView && count($oView->filterlike) > 0)
		{
			$searchText = '';
			foreach ($oView->filterlike as $field => $values)
				$searchText = $values[0];

			$t->assign('VAL_SEARCHTEXT', $searchText);
		}

		if (isset($oView->filter['dcl_tag.tag_desc']) && is_array($oView->filter['dcl_tag.tag_desc']) && count($oView->filter['dcl_tag.tag_desc']) > 0)
			$t->assign('VAL_TAGS', join(',', $oView->filter['dcl_tag.tag_desc']));

		$aDateChecks = array('createdon', 'closedon', 'statuson', 'lastactionon');

		for ($i = 0; $i < count($aDateChecks); $i++)
			$t->assign('CHK_' . mb_strtoupper($aDateChecks[$i]), '');

		if ($bView)
		{
			$t->assign('VAL_DATEFROM', '');
			$t->assign('VAL_DATETO', '');
			if (count($oView->filterdate) > 0)
			{
				$fromDate = '';
				$toDate = '';
				reset($oView->filterdate);
				while (list($field, $values) = each($oView->filterdate))
				{
					$t->assign('CHK_' . mb_strtoupper($field), ' CHECKED');
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
				'ticketid' => STR_TCK_TICKET . '#',
				'responsible.short' => STR_TCK_RESPONSIBLE,
				'products.name' => STR_TCK_PRODUCT,
				'dcl_org.name' => STR_TCK_ACCOUNT,
				'statuses.name' => STR_TCK_STATUS,
				'summary' => STR_TCK_SUMMARY,
				'dcl_product_module.module_name' => STR_CMMN_MODULE,
				'dcl_tag.tag_desc' => STR_CMMN_TAGS,
				'version' => STR_TCK_VERSION,
				'createdby.short' => STR_TCK_OPENEDBY,
				'createdon' => STR_TCK_OPENEDON,
				'closedby.short' => STR_TCK_CLOSEDBY,
				'closedon' => STR_TCK_CLOSEDON,
				'statuson' => STR_TCK_STATUSON,
				'lastactionon' => STR_TCK_LASTACTIONON,
				'priorities.name' => STR_TCK_PRIORITY,
				'severities.name' => STR_TCK_TYPE,
				'dcl_contact.last_name' => 'Contact Last Name',
				'dcl_contact.first_name' => 'Contact First Name',
				'dcl_contact_phone.phone_number' => STR_TCK_CONTACTPHONE,
				'dcl_contact_email.email_addr' => STR_TCK_CONTACTEMAIL,
				'issue' => STR_TCK_ISSUE,
				'seconds' => STR_TCK_APPROXTIME,
				'dcl_status_type.dcl_status_type_name' => STR_CMMN_STATUSTYPE,
				'dcl_entity_source.entity_source_name' => 'Source',
				'is_public' => 'Public'
			);

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
					$colName = 'createdby.short';

				$aShow[$colName] = $aCols[$colName];
			}

			foreach ($oView->groups as $colName)
			{
				if ($colName == 'a.short')
					$colName = 'responsible.short';
				else if ($colName == 'b.short')
					$colName = 'closedby.short';
				else if ($colName == 'c.short')
					$colName = 'createdby.short';

				$aGroup[$colName] = $aCols[$colName];
			}
		}
		else
		{
			$aShow = array(
					'ticketid' => STR_TCK_TICKET . '#',
					'responsible.short' => STR_TCK_RESPONSIBLE,
					'products.name' => STR_TCK_PRODUCT,
					'dcl_org.name' => STR_TCK_ACCOUNT,
					'statuses.name' => STR_TCK_STATUS,
					'summary' => STR_TCK_SUMMARY
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
					$sText = $aShow['priorities.name'];
				else if ($val == 'severities.weight')
					$sText = $aShow['severities.name'];
				else
					$sText = $aShow[$val];

				array_push($aOrder, array($val => $sText));
			}
			
			$t->assign('VAL_SORT');
		}

		$t->Render('TicketSearch.tpl');
	}
}
