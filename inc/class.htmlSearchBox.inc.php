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
LoadStringResource('tck');

class htmlSearchBox
{
	var $oView;

	function htmlSearchBox()
	{
		$this->oView = CreateObject('dcl.boView');
		$this->oView->style = 'report';
	}

	function submitSearch()
	{
		commonHeader();

		$this->oView->table = $_REQUEST['which'];
		$search_text = trim($_REQUEST['search_text']);
		switch ($_REQUEST['which'])
		{
			case 'workorders':
				if (ereg('^([0-9]+[-]?[0-9]*)+([,][0-9]+[-]?[0-9]*)+$', $search_text))
					$this->listWorkOrders($search_text);
				else if (ereg('^([0-9]+)[-]?([0-9]*)$', $search_text, $reg))
					$this->findWorkOrders($reg[1], $reg[2]);
				else if (ereg('^([0-9]+)$', $search_text, $reg))
					$this->findWorkOrders($reg[1], 0);
				else
					$this->searchWorkOrders($search_text);
				break;
			case 'dcl_projects':
				if (ereg('^([0-9]+)$', $search_text, $reg))
					$this->findProject($reg[1], 0);
				else
					$this->searchProjects($search_text);
				break;
			case 'tickets':
				if (ereg('^([0-9]+)$', $search_text, $reg))
					$this->findTicket($reg[1], 0);
				else
					$this->searchTickets($search_text);
				break;
			default:
				trigger_error('Error');
				break;
		}
	}

	function listWorkOrders($sWorkOrders)
	{
		commonHeader();

		$aWorkOrders = array();
		$aList = explode(',', $sWorkOrders);
		foreach ($aList as $sWorkOrder)
		{
			if ($sWorkOrder == '')
				continue;

			$aWoSeq = explode('-', $sWorkOrder);
			if (!isset($aWorkOrders[$aWoSeq[0]]))
				$aWorkOrders[$aWoSeq[0]] = array();

			if (count($aWoSeq) > 1)
				$aWorkOrders[$aWoSeq[0]][] = $aWoSeq[1];
		}

		$sWhere = '';
		$oView =& CreateObject('dcl.boExplicitView');
		foreach ($aWorkOrders as $wo_id => $aSeq)
		{
			if ($sWhere != '')
				$sWhere .= ' OR ';

			$sWhere .= '(jcn = ' . $wo_id;
			if (count($aSeq) > 1 || (count($aSeq) == 1 && $aSeq[0] != 0))
			{
				if (count($aSeq) == 1)
					$sWhere .= ' AND seq = ' . $aSeq[0];
				else
					$sWhere .= ' AND seq IN (' . implode(',', $aSeq) . ')';
			}

			$sWhere .= ')';
		}

		$oView->sql = 'SELECT jcn, seq, a.short, p.name, s.name, t.type_name, eststarton, deadlineon, etchours, totalhours, summary FROM workorders, statuses s, products p, personnel a, dcl_wo_type t WHERE responsible = a.id AND status = s.id AND product = p.id AND (' . $sWhere . ') AND t.wo_type_id = workorders.wo_type_id ORDER BY jcn, seq';
		$oView->title = STR_WO_RESULTSTITLE;

		$oView->AddDef('columns', '',
			array('jcn', 'seq', 'responsible.short', 'products.name', 'statuses.name', 'dcl_wo_type.type_name', 'eststarton', 'deadlineon',
				'etchours', 'totalhours', 'summary'));

		$oView->AddDef('columnhdrs', '',
			array(STR_WO_JCN, STR_WO_SEQ, STR_WO_RESPONSIBLE, STR_WO_PRODUCT,
				STR_WO_STATUS, STR_WO_TYPE, STR_WO_ESTSTART, STR_WO_DEADLINE, STR_WO_ETCHOURS, STR_WO_ACTHOURS, STR_WO_SUMMARY));

		$objHV = CreateViewObject($this->oView->table);
		$objHV->Render($oView);
	}

	function findWorkOrders($woid, $seq)
	{
		global $g_oSec;

		commonHeader();
		if ($woid == '' || $woid < 1)
		{
			trigger_error(STR_WO_NEEDJCNERR);
			return;
		}

		if ($seq > 0)
		{
			$obj = CreateObject('dcl.htmlWorkOrderDetail');
			$obj->Show($woid, $seq);
			return;
		}

		$this->oView->title = STR_WO_RESULTSTITLE;

		$this->oView->AddDef('filter', 'jcn', $woid);
		if ($seq > 0)
			$this->oView->AddDef('filter', 'seq', $seq);

		if ($g_oSec->IsPublicUser())
		{
			$this->oView->AddDef('filter', 'is_public', "'Y'");
			$this->oView->AddDef('filter', 'products.is_public', "'Y'");
		}

		$this->oView->AddDef('columns', '',
			array('jcn', 'seq', 'responsible.short', 'products.name', 'statuses.name', 'eststarton', 'deadlineon',
				'etchours', 'totalhours', 'summary'));

		$this->oView->AddDef('order', '', array('jcn', 'seq'));

		$this->oView->AddDef('columnhdrs', '',
			array(STR_WO_JCN, STR_WO_SEQ, STR_WO_RESPONSIBLE, STR_WO_PRODUCT,
				STR_WO_STATUS, STR_WO_ESTSTART, STR_WO_DEADLINE, STR_WO_ETCHOURS, STR_WO_ACTHOURS, STR_WO_SUMMARY));

		$objHV = CreateViewObject($this->oView->table);
		$objHV->Render($this->oView);
	}

	function searchWorkOrders($searchText)
	{
		global $g_oSec;
		
		commonHeader();
		if ($g_oSec->IsPublicUser() && !$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH))
		{
			ShowInfo('You must provide a work order ID and sequence.', __FILE__, __LINE__, null);
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

		$this->oView->AddDef('columns', '',
			array('jcn', 'seq', 'responsible.short', 'products.name', 'statuses.name', 'eststarton', 'deadlineon',
				'etchours', 'totalhours', 'summary'));

		$this->oView->AddDef('order', '', array('jcn', 'seq'));

		$this->oView->AddDef('columnhdrs', '',
			array(STR_WO_JCN, STR_WO_SEQ, STR_WO_RESPONSIBLE, STR_WO_PRODUCT,
				STR_WO_STATUS, STR_WO_ESTSTART, STR_WO_DEADLINE, STR_WO_ETCHOURS, STR_WO_ACTHOURS, STR_WO_SUMMARY));

		$objHV = CreateViewObject($this->oView->table);
		$objHV->Render($this->oView);
	}

	function findTicket($ticketid)
	{
		commonHeader();

		$obj = CreateObject('dcl.dbTickets');
		if ($obj->Load($ticketid) != -1)
		{
			$objHT = CreateObject('dcl.htmlTicketDetail');
			$objHT->Show($obj);
		}
	}

	function searchTickets($searchText)
	{
		global $g_oSec;
		
		commonHeader();
		if ($g_oSec->IsPublicUser() && !$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_SEARCH))
		{
			ShowInfo('You must provide a ticket ID.', __FILE__, __LINE__, null);
			return;
		}

		$this->oView->title = STR_TCK_TICKETSEARCHRESULTS;
		$this->oView->table = 'tickets';

		$this->oView->AddDef('filterlike', 'issue', $searchText);
		$this->oView->AddDef('filterlike', 'summary', $searchText);

		if ($g_oSec->IsPublicUser())
		{
			$this->oView->AddDef('filter', 'is_public', "'Y'");
			$this->oView->AddDef('filter', 'products.is_public', "'Y'");
		}

		$this->oView->AddDef('columns', '',
			array('ticketid', 'responsible.short', 'product', 'account', 'status', 'dcl_contact.last_name', 'dcl_contact.first_name', 'dcl_contact_phone.phone_number', 'summary'));

		$this->oView->AddDef('order', '', array('ticketid'));

		$this->oView->AddDef('columnhdrs', '',
			array(STR_TCK_TICKET, STR_TCK_RESPONSIBLE, STR_TCK_PRODUCT,
				STR_TCK_ACCOUNT, STR_TCK_STATUS, 'Last Name', 'First Name', STR_TCK_CONTACTPHONE, STR_TCK_SUMMARY));

		$objHV = CreateViewObject($this->oView->table);
		$objHV->Render($this->oView);
	}

	function findProject($projectid)
	{
		commonHeader();

		$obj = CreateObject('dcl.htmlProjectsdetail');
		$obj->show($projectid, 0, 0);
	}

	function searchProjects($searchText)
	{
		commonHeader();
	}
}
?>
