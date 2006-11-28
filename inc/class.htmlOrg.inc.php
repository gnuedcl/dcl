<?php
/*
 * $Id: class.htmlOrg.inc.php,v 1.1.1.1 2006/11/27 05:30:48 mdean Exp $
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

class htmlOrg
{
	function GetCombo($default = 0, $cbName = 'org_id', $size = 0, $activeOnly = true)
	{
		global $g_oSec, $g_oSession;
		
		$oDB = CreateObject('dcl.dbOrg');
		$oDB->cacheEnabled = false;

		$orderBy = 'name';

		$query = "SELECT org_id, name FROM dcl_org ";
		
		$sWhere = '';
		if ($activeOnly)
			$sWhere .= "WHERE active='Y' ";
			
		if ($g_oSec->IsOrgUser())
		{
			if ($sWhere != '')
				$sWhere .= ' AND ';
			else
				$sWhere .= 'WHERE ';
				
			$sWhere .= 'org_id IN (' . $g_oSession->Value('member_of_orgs') . ')';
		}

		$query .= $sWhere . " ORDER BY $orderBy";

		$oDB->Query($query);

		$oSelect = CreateObject('dcl.htmlSelect');
		$oSelect->vDefault = $default;
		$oSelect->sName = $cbName;
		$oSelect->iSize = $size;
		$oSelect->sZeroOption = STR_CMMN_SELECTONE;
		$oSelect->bCastToInt = true;

		while ($oDB->next_record())
			$oSelect->AddOption($oDB->f(0), $oDB->f(1));

		return $oSelect->GetHTML();
	}

	function show($orderBy = 'short')
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_VIEW))
			return PrintPermissionDenied();

		$oView = CreateObject('dcl.boView');
		$oView->startrow = 0;
		$oView->numrows = 25;

		$filterActive = '';
		if (IsSet($_REQUEST['filterActive']))
			$filterActive = $_REQUEST['filterActive'];

		$oView->table = 'dcl_org';
		$oView->title = 'Browse Organizations';
		$oView->AddDef('columnhdrs', '', array('ID', 'Active', 'Name', 'Phone', 'Email', 'Internet'));

		$oView->AddDef('columns', '', array('org_id', 'active', 'name', 'dcl_org_phone.phone_number', 'dcl_org_email.email_addr', 'dcl_org_url.url_addr'));

		$oView->AddDef('order', '', array('name'));

		if ($filterActive == 'Y' || $filterActive == 'N')
			$oView->AddDef('filter', 'active', "'$filterActive'");

		$oHtml = CreateObject('dcl.htmlOrgBrowse');
		$oHtml->Render($oView);
	}
	
	function viewWorkOrders()
	{
		commonHeader();
		
		if (($id = DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$oMeta =& CreateObject('dcl.DCL_MetadataDisplay');
		$aOrg = $oMeta->GetOrganization($id);
		
		$objView = CreateObject('dcl.boView');
		$objView->title = sprintf('%s Work Orders', $aOrg['name']);
		$objView->style = 'report';
		$objView->table = 'workorders';
		$objView->AddDef('columns', '', array('jcn', 'seq', 'priorities.name', 'severities.name', 'responsible.short', 'deadlineon', 'summary'));
		$objView->AddDef('columnhdrs', '', array(
				STR_WO_STATUS,
				STR_WO_JCN,
				STR_WO_SEQ,
				STR_WO_PRIORITY,
				STR_WO_SEVERITY,
				STR_WO_RESPONSIBLE,
				STR_WO_DEADLINE,
				STR_WO_SUMMARY));

		$objView->AddDef('filter', 'dcl_wo_account.account_id', $id);
		$objView->AddDef('filter', 'dcl_status_type.dcl_status_type_id', array(1, 3));
		$objView->AddDef('order', '', array('jcn', 'seq'));
		$objView->AddDef('groups', '', array('statuses.name'));

		$objHV = CreateViewObject($objView->table);
		$objHV->bShowPager = false;
		$objHV->Render($objView);
	}
	
	function viewTickets()
	{
		commonHeader();
		
		if (($id = DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}

		$oMeta =& CreateObject('dcl.DCL_MetadataDisplay');
		$aOrg = $oMeta->GetOrganization($id);
		
		$objView = CreateObject('dcl.boView');
		$objView->title = sprintf('%s Tickets', $aOrg['name']);
		$objView->style = 'report';
		$objView->table = 'tickets';
		$objView->AddDef('columns', '', array('ticketid', 'statuses.name', 'priorities.name', 'severities.name', 'responsible.short', 'summary'));
		$objView->AddDef('columnhdrs', '', array(
				STR_TCK_STATUS,
				STR_TCK_TICKET . '#',
				STR_TCK_STATUS,
				STR_TCK_PRIORITY,
				STR_TCK_TYPE,
				STR_TCK_RESPONSIBLE,
				STR_TCK_SUMMARY));

		$objView->AddDef('filter', 'account', $id);
		$objView->AddDef('filter', 'dcl_status_type.dcl_status_type_id', array(1, 3));
		$objView->AddDef('order', '', array('ticketid'));
		$objView->AddDef('groups', '', array('statuses.name'));

		$objHV = CreateViewObject($objView->table);
		$objHV->bShowPager = false;
		$objHV->Render($objView);
	}
}
?>
