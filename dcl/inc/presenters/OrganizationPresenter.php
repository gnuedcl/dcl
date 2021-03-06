<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2011 Free Software Foundation
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

class OrganizationPresenter
{
	public function Create()
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_ADD);
		
		$smartyHelper = new SmartyHelper();
		
		if (isset($_REQUEST['return_to']))
		{
			$smartyHelper->assign('return_to', $_REQUEST['return_to']);
			$smartyHelper->assign('URL_BACK', menuLink('', $_REQUEST['return_to']));
		}
		else
			$smartyHelper->assign('URL_BACK', menuLink('', 'menuAction=htmlOrgBrowse.show&filterActive=Y'));
			
		if (isset($_REQUEST['hideMenu']))
			$smartyHelper->assign('hideMenu', $_REQUEST['hideMenu']);

		$smartyHelper->assign('TXT_FUNCTION', 'Add New Organization');
		$smartyHelper->assign('VAL_MENUACTION', 'Organization.Insert');

		$addressTypeHtmlHelper = new AddressTypeHtmlHelper();
		$smartyHelper->assign('CMB_ADDRTYPE', $addressTypeHtmlHelper->Select());

		$emailTypeHtmlHelper = new EmailTypeHtmlHelper();
		$smartyHelper->assign('CMB_EMAILTYPE', $emailTypeHtmlHelper->Select());

		$phoneTypeHtmlHelper = new PhoneTypeHtmlHelper();
		$smartyHelper->assign('CMB_PHONETYPE', $phoneTypeHtmlHelper->Select());

		$urlTypeHtmlHelper = new UrlTypeHtmlHelper();
		$smartyHelper->assign('CMB_URLTYPE', $urlTypeHtmlHelper->Select());

		$organizationTypeModel = new OrganizationTypeModel();
		$smartyHelper->assign('orgTypes', $organizationTypeModel->GetTypes());

		$smartyHelper->Render('NewOrgForm.tpl');
	}

	public function Edit(OrganizationModel $model)
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_MODIFY);

		$smartyHelper = new SmartyHelper();
		
		if (isset($_REQUEST['return_to']))
		{
			$smartyHelper->assign('return_to', $_REQUEST['return_to']);
			$smartyHelper->assign('URL_BACK', menuLink('', $_REQUEST['return_to']));
		}
		else
			$smartyHelper->assign('URL_BACK', menuLink('', 'menuAction=htmlOrgBrowse.show&filterActive=Y'));
			
		if (isset($_REQUEST['hideMenu']))
			$smartyHelper->assign('hideMenu', $_REQUEST['hideMenu']);

		$smartyHelper->assign('VAL_MENUACTION', 'Organization.Update');
		$smartyHelper->assign('VAL_NAME', $model->name);
		$smartyHelper->assign('VAL_ORGID', $model->org_id);
		$smartyHelper->assign('VAL_ACTIVE', $model->active);
		$smartyHelper->assign('TXT_FUNCTION', 'Edit Organization');

		$oOrgType = new OrganizationTypeModel();
		$smartyHelper->assign('orgTypes', $oOrgType->GetTypes($model->org_id));

		$smartyHelper->Render('OrgForm.tpl');
	}

	public function Delete(OrganizationModel $model)
	{
		commonHeader();
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_DELETE);

		ShowDeleteYesNo('Organization', 'Organization.Destroy', $model->org_id, $model->name);
	}
	
	public function Detail(OrganizationModel $model)
	{
		global $dcl_info, $g_oSec, $g_oSession;

		commonHeader();
		LoadStringResource('wo');
		LoadStringResource('tck');

		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_VIEW, $model->org_id);
		if ($g_oSec->IsOrgUser() && !in_array($model->org_id, explode(',', $g_oSession->Value('member_of_orgs'))))
			throw new PermissionDeniedException();

		$t = self::GetSmartyHelperWithToolbar($model);

		// Get aliases for this org
		$oOrgAlias = new OrganizationAliasModel();
		$oOrgAlias->ListByOrg($model->org_id);
		$aAliases = array();
		while ($oOrgAlias->next_record())
		{
			$aAliases[] = $oOrgAlias->Record;
		}

		$t->assignByRef('OrgAlias', $aAliases);
		$oOrgAlias->FreeResult();

		// Get types for this org
		$oOrgType = new OrganizationTypeModel();
		$oOrgType->ListByOrg($model->org_id);
		$aTypes = array();
		while ($oOrgType->next_record())
		{
			$aTypes[] = $oOrgType->Record;
		}

		$t->assignByRef('OrgType', $aTypes);
		$oOrgType->FreeResult();

		// Get products for this org
		$oOrgProduct = new OrganizationProductModel();
		$oOrgProduct->ListByOrg($model->org_id);
		$aProducts = array();
		while ($oOrgProduct->next_record())
		{
			$aProducts[] = $oOrgProduct->Record;
		}

		$t->assignByRef('OrgProduct', $aProducts);
		$oOrgProduct->FreeResult();

		// Get addresses
		$oOrgAddress = new OrganizationAddressModel();
		$oOrgAddress->ListByOrg($model->org_id);
		$aAddresses = array();
		while ($oOrgAddress->next_record())
		{
			$aAddresses[] = $oOrgAddress->Record;
		}

		$t->assignByRef('OrgAddress', $aAddresses);
		$oOrgAddress->FreeResult();

		// Get phone numbers
		$oOrgPhone = new OrganizationPhoneModel();
		$oOrgPhone->ListByOrg($model->org_id);
		$aPhoneNumbers = array();
		while ($oOrgPhone->next_record())
		{
			$aPhoneNumbers[] = $oOrgPhone->Record;
		}

		$t->assignByRef('OrgPhone', $aPhoneNumbers);
		$oOrgPhone->FreeResult();

		// Get e-mail addresses
		$oOrgEmail = new OrganizationEmailModel();
		$oOrgEmail->ListByOrg($model->org_id);
		$aEmails = array();
		while ($oOrgEmail->next_record())
		{
			$aEmails[] = $oOrgEmail->Record;
		}

		$t->assignByRef('OrgEmail', $aEmails);
		$oOrgEmail->FreeResult();

		// Get environments
		$envOrgModel = new EnvironmentOrgModel();
		$envOrgModel->ListByOrg($model->org_id);
		$environments = array();
		while ($envOrgModel->next_record())
		{
			$beginDt = new DateTime($envOrgModel->Record['begin_dt']);
			$envOrgModel->Record['begin_dt'] = $beginDt->format($dcl_info['DCL_DATE_FORMAT'] . ' H:i');

			if ($envOrgModel->Record['end_dt'] != '')
			{
				$endDt = new DateTime($envOrgModel->Record['end_dt']);
				$envOrgModel->Record['end_dt'] = $endDt->format($dcl_info['DCL_DATE_FORMAT'] . ' H:i');
			}

			$environments[] = $envOrgModel->Record;
		}

		$t->assignByRef('OrgEnvironment', $environments);
		$envOrgModel->FreeResult();

		// Get outage SLA
		$orgOutageModel = new OrganizationOutageSlaModel();
		if ($orgOutageModel->Load($model->org_id, false) !== -1)
		{
			$t->assignByRef('OrgOutage', $orgOutageModel);
		}

		// Get measurement SLAs
		$orgMeasureSlaModel = new OrganizationMeasurementSlaModel();
		if ($orgMeasureSlaModel->ListByOrganization($model->org_id) != -1)
		{
			$measureSla = array();
			while ($orgMeasureSlaModel->next_record())
			{
				$measureSla[] = array(
					'typeId' => $orgMeasureSlaModel->f('measurement_type_id'),
					'type' => $orgMeasureSlaModel->f('measurement_name'),
					'min' => $orgMeasureSlaModel->f('min_valid_value'),
					'max' => $orgMeasureSlaModel->f('max_valid_value'),
					'sla' => $orgMeasureSlaModel->f('measurement_sla'),
					'warn' => $orgMeasureSlaModel->f('measurement_sla_warn'),
					'trimPct' => $orgMeasureSlaModel->f('sla_trim_pct'),
					'slaIsTrim' => $orgMeasureSlaModel->f('sla_is_trim_based'),
					'schedule' => $orgMeasureSlaModel->f('schedule_name'),
					'unit' => $orgMeasureSlaModel->f('unit_abbr')
				);
			}

			$t->assignByRef('OrgMeasureSla', $measureSla);
		}

		// Get URLs
		$oOrgURL = new OrganizationUrlModel();
		$oOrgURL->ListByOrg($model->org_id);
		$aURL = array();
		while ($oOrgURL->next_record())
		{
			$aURL[] = $oOrgURL->Record;
		}

		$t->assignByRef('OrgURL', $aURL);
		$oOrgURL->FreeResult();
		
		// Get main contacts
		$oOrgContacts = new OrganizationModel();
		$oOrgContacts->ListMainContacts($model->org_id);
		$aContacts = array();
		$oMetadata = new DisplayHelper();
		$oContactType = new ContactTypeModel();
		while ($oOrgContacts->next_record())
		{
			$aContact = $oMetadata->GetContact($oOrgContacts->f('contact_id'));
			$aRow = array('contact_id' => $oOrgContacts->f('contact_id'), 'name' => $aContact['name'], 'phone' => $aContact['phone'], 'email' => $aContact['email'], 'url' => $aContact['url']);
			
			$oContactType->ListByContact($oOrgContacts->f('contact_id'));
			$aContactTypes = array();
			while ($oContactType->next_record())
			{
				$aContactTypes[] = $oContactType->f('contact_type_name');
			}

			$aRow['type'] = join(', ', $aContactTypes);
			$oContactType->FreeResult();
			
			$aContacts[] = $aRow;
		}
		
		$t->assignByRef('OrgContacts', $aContacts);
		$oOrgContacts->FreeResult();

		// Get last 10 tickets
		$oViewTicket = new TicketSqlQueryHelper();
		$oViewTicket->title = 'Last 10 Tickets';
		$oViewTicket->AddDef('columns', '', array('ticketid', 'products.name', 'statuses.name', 'priorities.name', 'severities.name', 'responsible.short', 'summary'));
		$oViewTicket->AddDef('columnhdrs', '', array(
				STR_TCK_TICKET . '#',
				STR_TCK_PRODUCT,
				STR_TCK_STATUS,
				STR_TCK_PRIORITY,
				STR_TCK_TYPE,
				STR_TCK_RESPONSIBLE,
				STR_TCK_SUMMARY));

		$oViewTicket->AddDef('filter', 'account', $model->org_id);
		$oViewTicket->AddDef('order', '', array('createdon DESC'));
		$oViewTicket->numrows = 10;
		
		$oTickets = new TicketsModel();
		$oTickets->LimitQuery($oViewTicket->GetSQL(), 0, $oViewTicket->numrows);
		$aTickets = array();
		while ($oTickets->next_record())
		{
			$aData = array();
			for ($i = 0; $i < count($oViewTicket->columns); $i++)
				array_push($aData, $oTickets->f($i));
				
			$aData['ticketid'] = $oTickets->f('ticketid');
			array_push($aTickets, $aData);
		}

		$t->assignByRef('ViewTicket', $oViewTicket);
		$t->assignByRef('Tickets', $aTickets);
		$oTickets->FreeResult();

		// Get last 10 work orders
		$oViewWO = new WorkOrderSqlQueryHelper();
		$oViewWO->title = 'Last 10 Work Orders';
		$oViewWO->AddDef('columns', '', array('jcn', 'seq', 'products.name', 'statuses.name', 'priorities.name', 'severities.name', 'responsible.short', 'deadlineon', 'summary'));
		$oViewWO->AddDef('columnhdrs', '', array(
				STR_WO_JCN,
				STR_WO_SEQ,
				STR_WO_PRODUCT,
				STR_WO_STATUS,
				STR_WO_PRIORITY,
				STR_WO_SEVERITY,
				STR_WO_RESPONSIBLE,
				STR_WO_DEADLINE,
				STR_WO_SUMMARY));

				
		$oViewWO->AddDef('filter', 'dcl_wo_account.account_id', $model->org_id);
		$oViewWO->AddDef('order', '', array('createdon DESC'));
		$oViewWO->numrows = 10;
		
		$oWO = new WorkOrderModel();
		$oWO->LimitQuery($oViewWO->GetSQL(), 0, $oViewWO->numrows);
		$aWO = array();
		while ($oWO->next_record())
		{
			$aData = array();
			for ($i = 0; $i < count($oViewWO->columns); $i++)
				array_push($aData, $oWO->f($i));
				
			$aData['jcn'] = $oWO->f('jcn');
			$aData['seq'] = $oWO->f('seq');

			array_push($aWO, $aData);
		}

		$t->assignByRef('ViewWorkOrder', $oViewWO);
		$t->assignByRef('WorkOrders', $aWO);
		$oWO->FreeResult();

		$t->Render('OrgDetail.tpl');
	}

	public function Measurement(OrganizationModel $model)
	{
		global $dcl_info;

		commonHeader();

		$t = self::GetSmartyHelperWithToolbar($model);

		$viewData = new stdClass();
		$endDateTime = new DateTime();
		$viewData->EndDate = $endDateTime->format($dcl_info['DCL_DATE_FORMAT']);

		$startDateTime = new DateTime($viewData->EndDate);
		$startDateTime->modify('-30 days');
		$viewData->BeginDate = $startDateTime->format($dcl_info['DCL_DATE_FORMAT']);

		// Select the most common measurement to start with
		$measurementModel = new OrganizationMeasurementModel();
		$sql = sprintf("SELECT measurement_type_id, COUNT(*) FROM dcl_org_measurement WHERE org_id = %d AND measurement_ts BETWEEN %s AND %s GROUP BY measurement_type_id ORDER BY 2 DESC",
			$model->org_id,
			$measurementModel->Quote($measurementModel->ArrangeDateForInsert($viewData->BeginDate)),
			$measurementModel->Quote($measurementModel->ArrangeDateForInsert($viewData->EndDate)));

		if ($measurementModel->LimitQuery($sql, 0, 1) != -1 && $measurementModel->next_record())
			$viewData->MeasurementType = $measurementModel->f(0);

		$t->registerObject('ViewData', $viewData);

		$t->Render('OrganizationMeasurement.tpl');
	}

	public function Outage(OrganizationModel $model)
	{
		global $dcl_info;

		commonHeader();

		$t = self::GetSmartyHelperWithToolbar($model);

		$viewData = new stdClass();
		$endDateTime = new DateTime();
		$viewData->EndDate = $endDateTime->format($dcl_info['DCL_DATE_FORMAT']);

		$startDateTime = new DateTime($viewData->EndDate);
		$startDateTime->modify('-30 days');
		$viewData->BeginDate = $startDateTime->format($dcl_info['DCL_DATE_FORMAT']);

		$t->registerObject('ViewData', $viewData);

		$t->Render('OrganizationOutage.tpl');
	}

	public function SlaReport(OrganizationModel $model)
	{
		global $dcl_info;

		commonHeader();

		$t = self::GetSmartyHelperWithToolbar($model);

		$viewData = new stdClass();
		$endDateTime = new DateTime();
		$viewData->EndDate = $endDateTime->format($dcl_info['DCL_DATE_FORMAT']);

		$startDateTime = new DateTime($viewData->EndDate);
		$startDateTime->modify('-30 days');
		$viewData->BeginDate = $startDateTime->format($dcl_info['DCL_DATE_FORMAT']);

		// Select the most common measurement to start with
		$measurementModel = new OrganizationMeasurementModel();
		$sql = sprintf("SELECT measurement_type_id, COUNT(*) FROM dcl_org_measurement WHERE org_id = %d AND measurement_ts BETWEEN %s AND %s GROUP BY measurement_type_id ORDER BY 2 DESC",
			$model->org_id,
			$measurementModel->Quote($measurementModel->ArrangeDateForInsert($viewData->BeginDate)),
			$measurementModel->Quote($measurementModel->ArrangeDateForInsert($viewData->EndDate)));

		if ($measurementModel->LimitQuery($sql, 0, 1) != -1 && $measurementModel->next_record())
			$viewData->MeasurementType = $measurementModel->f(0);

		$t->registerObject('ViewData', $viewData);

		$t->Render('OrganizationSlaReport.tpl');
	}

	private static function GetSmartyHelperWithToolbar(OrganizationModel $model)
	{
		$t = new SmartyHelper();
		$t->registerObject('Org', $model);

		$t->assign('PERM_MODIFY', HasPermission(DCL_ENTITY_ORG, DCL_PERM_MODIFY));
		$t->assign('PERM_DELETE', HasPermission(DCL_ENTITY_ORG, DCL_PERM_DELETE));
		$t->assign('PERM_VIEW', HasPermission(DCL_ENTITY_ORG, DCL_PERM_VIEW));
		$t->assign('PERM_WIKI', HasPermission(DCL_ENTITY_ORG, DCL_PERM_VIEWWIKI));

		$t->assign('PERM_VIEW_CONTACT', HasPermission(DCL_ENTITY_CONTACT, DCL_PERM_VIEW));
		$t->assign('PERM_VIEW_WORKORDER', HasPermission(DCL_ENTITY_WORKORDER, DCL_PERM_VIEW));
		$t->assign('PERM_VIEW_TICKET', HasPermission(DCL_ENTITY_TICKET, DCL_PERM_VIEW));
		$t->assign('PERM_ADD_WORKORDER', HasPermission(DCL_ENTITY_WORKORDER, DCL_PERM_ADD));
		$t->assign('PERM_ADD_TICKET', HasPermission(DCL_ENTITY_TICKET, DCL_PERM_ADD));

		$t->assign('PERM_VIEW_MEASUREMENT', HasPermission(DCL_ENTITY_ORGMEASUREMENT, DCL_PERM_VIEW));
		$t->assign('PERM_VIEW_OUTAGE', HasPermission(DCL_ENTITY_OUTAGE, DCL_PERM_VIEW));

		return $t;
	}
}
