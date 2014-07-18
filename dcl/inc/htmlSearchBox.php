<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2014 Free Software Foundation
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

class htmlSearchBox
{
	var $oView;

	function submitSearch()
	{
		commonHeader();

		$search_text = trim($_REQUEST['search_text']);
		switch ($_REQUEST['which'])
		{
			case 'workorders':
			case 'openworkorders':
		        $this->oView = new WorkOrderSqlQueryHelper();
			    if (preg_match('/^([0-9]+[-]?[0-9]*)+([,][0-9]+[-]?[0-9]*)+$/', $search_text))
					$this->findWorkOrders($search_text);
				else if (preg_match('/^([0-9]+)[-]?([0-9]*)$/', $search_text, $reg))
					$this->findWorkOrders($search_text);
				else if (preg_match('/^([0-9]+)$/', $search_text, $reg))
					$this->findWorkOrders($search_text);
				else
					$this->searchWorkOrders($search_text);
				break;
			case 'dcl_projects':
			case 'opendcl_projects':
				$this->oView = new ProjectSqlQueryHelper();
			    if (preg_match('/^([0-9]+)$/', $search_text, $reg))
					$this->findProject($reg[1], 0);
				else
					$this->searchProjects($search_text);
				break;
			case 'tickets':
			case 'opentickets':
				$this->oView = new TicketSqlQueryHelper();
			    if (preg_match('/^([0-9]+)$/', $search_text, $reg))
					$this->findTicket($reg[1], 0);
				else
					$this->searchTickets($search_text);
				break;
			case 'tags':
			    $this->searchTags($search_text);
			    break;
			case 'hotlists':
				$this->searchHotlists($search_text);
				break;
			default:
				throw new InvalidArgumentException();
				break;
		}
	}

	function findWorkOrders($sWorkOrders)
	{
		global $g_oSec;

		commonHeader();
		if ($sWorkOrders == '' || $sWorkOrders < 1)
			throw new InvalidArgumentException();

		if (strpos($sWorkOrders, '-') > 0 && strpos($sWorkOrders, ',') === false)
		{
			list($woid, $seq) = explode('-', $sWorkOrders);
			if ($seq > 0)
			{
				$workOrderModel = new WorkOrderModel();
				if ($workOrderModel->LoadByIdSeq($woid, $seq) == -1)
					throw new InvalidEntityException();
				
				$workOrderPresenter = new WorkOrderPresenter();
				$workOrderPresenter->Detail($workOrderModel);
				return;
			}
		}

		$this->oView->title = STR_WO_RESULTSTITLE;

		$aList = explode(',', $sWorkOrders);
		foreach ($aList as $sWorkOrder)
		{
			$this->oView->AddDef('filter', 'jcn', $sWorkOrder);
		}

		if ($g_oSec->IsPublicUser())
		{
			$this->oView->AddDef('filter', 'is_public', "'Y'");
			$this->oView->AddDef('filter', 'products.is_public', "'Y'");
		}

		$this->oView->AddDef('columns', '',
			array('jcn', 'seq', 'responsible.short', 'products.name', 'dcl_product_module.module_name', 'statuses.name', 'eststarton', 'deadlineon',
				'etchours', 'totalhours', 'dcl_tag.tag_desc', 'summary'));

		$this->oView->AddDef('order', '', array('jcn', 'seq'));

		$this->oView->AddDef('columnhdrs', '',
			array(STR_WO_JCN, STR_WO_SEQ, STR_WO_RESPONSIBLE, STR_WO_PRODUCT, STR_CMMN_MODULE,
				STR_WO_STATUS, STR_WO_ESTSTART, STR_WO_DEADLINE, STR_WO_ETCHOURS, STR_WO_ACTHOURS, STR_CMMN_TAGS, STR_WO_SUMMARY));

		$presenter = new WorkOrderPresenter();
		$presenter->DisplayView($this->oView);
	}

	function searchWorkOrders($searchText)
	{
		global $g_oSec;
		
		commonHeader();
		if ($g_oSec->IsPublicUser() && !$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH))
		{
			ShowError('You must provide a work order ID and sequence.');
			return;
		}

		$this->oView->title = STR_WO_RESULTSTITLE;

		$this->oView->AddDef('filterlike', 'description', $searchText);
		$this->oView->AddDef('filterlike', 'summary', $searchText);
		$this->oView->AddDef('filterlike', 'notes', $searchText);

		if ($g_oSec->IsPublicUser())
		{
			$this->oView->AddDef('filter', 'is_public', "'Y'");
			$this->oView->AddDef('filter', 'products.is_public', "'Y'");
		}
		
		if ($_REQUEST['which'] == 'openworkorders')
		    $this->oView->AddDef('filternot', 'statuses.dcl_status_type', '2');

		$this->oView->AddDef('columns', '',
			array('jcn', 'seq', 'responsible.short', 'products.name', 'dcl_product_module.module_name', 'statuses.name', 'eststarton', 'deadlineon',
				'etchours', 'totalhours', 'dcl_tag.tag_desc', 'summary'));

		$this->oView->AddDef('order', '', array('jcn', 'seq'));

		$this->oView->AddDef('columnhdrs', '',
			array(STR_WO_JCN, STR_WO_SEQ, STR_WO_RESPONSIBLE, STR_WO_PRODUCT, STR_CMMN_MODULE,
				STR_WO_STATUS, STR_WO_ESTSTART, STR_WO_DEADLINE, STR_WO_ETCHOURS, STR_WO_ACTHOURS, STR_CMMN_TAGS, STR_WO_SUMMARY));

		$presenter = new WorkOrderPresenter();
		$presenter->DisplayView($this->oView);
	}

	function findTicket($ticketid)
	{
		commonHeader();

		$obj = new TicketsModel();
		if ($obj->Load($ticketid) != -1)
		{
			$objHT = new htmlTicketDetail();
			$objHT->Show($obj);
		}
	}

	function searchTickets($searchText)
	{
		global $g_oSec;
		
		commonHeader();
		if ($g_oSec->IsPublicUser() && !$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_SEARCH))
		{
			ShowError('You must provide a ticket ID.');
			return;
		}

		$this->oView->title = STR_TCK_TICKETSEARCHRESULTS;

		$this->oView->AddDef('filterlike', 'issue', $searchText);
		$this->oView->AddDef('filterlike', 'summary', $searchText);

		if ($g_oSec->IsPublicUser())
		{
			$this->oView->AddDef('filter', 'is_public', "'Y'");
			$this->oView->AddDef('filter', 'products.is_public', "'Y'");
		}

		if ($_REQUEST['which'] == 'opentickets')
		    $this->oView->AddDef('filternot', 'statuses.dcl_status_type', '2');

		$this->oView->AddDef('columns', '',
			array('ticketid', 'responsible.short', 'products.name', 'dcl_product_module.module_name', 'dcl_org.name', 'statuses.name', 'dcl_contact.last_name', 'dcl_contact.first_name', 'dcl_contact_phone.phone_number', 'dcl_tag.tag_desc', 'summary'));

		$this->oView->AddDef('order', '', array('ticketid'));

		$this->oView->AddDef('columnhdrs', '',
			array(STR_TCK_TICKET, STR_TCK_RESPONSIBLE, STR_TCK_PRODUCT, STR_CMMN_MODULE,
				STR_TCK_ACCOUNT, STR_TCK_STATUS, 'Last Name', 'First Name', STR_TCK_CONTACTPHONE, STR_CMMN_TAGS, STR_TCK_SUMMARY));

		$objHV = new htmlTicketResults();
		$objHV->Render($this->oView);
	}

	function findProject($projectid)
	{
		commonHeader();

		$obj = new ProjectDetailPresenter();
		$obj->show($projectid, 0, 0);
	}

	function searchProjects($searchText)
	{
		commonHeader();
		
		$presenter = new ProjectPresenter();
		$_REQUEST['filterName'] = $searchText;
		$presenter->Index();
	}
	
	function searchTags($searchText)
	{
	    commonHeader();
	    
	    $obj = new htmlTags();
	    $_REQUEST['tag'] = $searchText;
	    $obj->browse();
	}
	
	function searchHotlists($searchText)
	{
		commonHeader();
		
		$obj = new HotlistController();
		$_REQUEST['tag'] = $searchText;
		$obj->Browse();
	}
}
