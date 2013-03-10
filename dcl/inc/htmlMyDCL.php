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

LoadStringResource('tck');
LoadStringResource('wo');
LoadStringResource('prj');
LoadStringResource('prod');
LoadStringResource('wtch');
class htmlMyDCL
{
	var $bHasOutput;
	
	function htmlMyDCL()
	{
		$this->bHasOutput = false;
	}
	
	function show()
	{
		commonHeader();
		$this->showMy();
	}

	function showMy()
	{
		global $g_oSession;
		
		//$this->myTimesheet();
		$this->myTickets();
		$this->myTickets('createdby');
		$this->myWorkOrders();
		$this->myWorkOrders('createby');
		$this->myProjects();
	}
	
	function myTimesheet()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION))
			return;
			
		$oTS = new htmlWorkOrderTimesheet();
		$oTS->ShowEntryForm();
	}
	
	function myTickets($forField = 'responsible', $rowlimit = 5)
	{
		global $g_oSec;
		
		$objView = new boView();
		
		if ($forField == 'responsible')
			$objView->title = STR_TCK_MYTICKETS;
		else
			$objView->title = STR_TCK_MYSUBMISSIONS;
		
		$objView->style = 'report';
		$objView->table = 'tickets';
		
		if ($g_oSec->IsPublicUser())
		{
			$objView->AddDef('columns', '', array('ticketid', 'products.name', 'statuses.name', 'priorities.name', 'severities.name', 'summary'));
			$objView->AddDef('columnhdrs', '', array(STR_TCK_TICKET, STR_TCK_PRODUCT, STR_TCK_STATUS, STR_TCK_PRIORITY, STR_TCK_TYPE, STR_TCK_SUMMARY));
		}
		else
		{
			$objView->AddDef('columns', '', array('ticketid', 'products.name', 'statuses.name', 'priorities.name', 'severities.name', 'responsible.short', 'summary'));
			$objView->AddDef('columnhdrs', '', array(STR_TCK_TICKET, STR_TCK_PRODUCT, STR_TCK_STATUS, STR_TCK_PRIORITY, STR_TCK_TYPE, STR_TCK_RESPONSIBLE, STR_TCK_SUMMARY));
		}

		$objView->AddDef('filter', $forField, $GLOBALS['DCLID']);
		$objView->AddDef('filternot', 'statuses.dcl_status_type', '2');
		if ($forField == 'createdby')
			$objView->AddDef('filternot', 'responsible', $GLOBALS['DCLID']);
		$objView->AddDef('order', '', array('priorities.weight', 'severities.weight', 'ticketid'));

		$oTable = new TableHtmlHelper();
		foreach ($objView->columnhdrs as $sColumn)
		{
			if ($sColumn == STR_TCK_TICKET)
				$oTable->addColumn($sColumn, 'html');
			else
				$oTable->addColumn($sColumn, 'string');
		}

		$oTable->setShowRownum(false);
		$oTable->setCaption($objView->title);
		$oTable->setWidth('100%');
		
		if ($forField == 'responsible')
			$oTable->addToolbar(menuLink('', 'menuAction=htmlTickets.show&filterReportto=' . $GLOBALS['DCLID']), STR_CMMN_VIEWALL);
		else
			$oTable->addToolbar(menuLink('', 'menuAction=htmlTickets.showSubmissions'), STR_CMMN_VIEWALL);
		
		$oDB = new DbProvider;
		$oDB->LimitQuery($objView->GetSQL(), 0, 5);
		$aData = $oDB->FetchAllRows();
		for ($i = 0; $i < count($aData); $i++)
		{
			$aData[$i][0] = '<a href="' . menuLink('', 'menuAction=boTickets.view&ticketid=' . $aData[$i][0]) . '">' . $aData[$i][0] . '</a>';
		}
		
		$oTable->setData($aData);
		if (count($oTable->aData) > 0)
		{
			if ($this->bHasOutput)
				$oTable->setSpacer(true);
			else
				$this->bHasOutput = true;
				
			$oTable->render();
		}
	}

	function myWorkOrders($forField = 'responsible', $rowlimit = 5)
	{
		global $g_oSec;
		
		$objView = new boView();
		
		if ($forField == 'responsible')
			$objView->title = STR_WO_MYWO;
		else
			$objView->title = STR_WO_MYSUBMISSIONS;
		
		$objView->style = 'report';
		$objView->table = 'workorders';

		if ($g_oSec->IsPublicUser())
		{
			$objView->AddDef('columns', '', array('jcn', 'seq', 'dcl_wo_type.type_name', 'products.name', 'statuses.name', 'priorities.name', 
												'severities.name', 'deadlineon', 'summary'));
			$objView->AddDef('columnhdrs', '', array(STR_WO_JCN, STR_WO_SEQ, STR_WO_TYPE, STR_WO_PRODUCT, STR_WO_STATUS, STR_WO_PRIORITY,
												STR_WO_SEVERITY, STR_WO_DEADLINE, STR_WO_SUMMARY));
		}
		else
		{
			$objView->AddDef('columns', '', array('jcn', 'seq', 'dcl_wo_type.type_name', 'products.name', 'statuses.name', 'priorities.name', 
												'severities.name', 'responsible.short', 'deadlineon', 'summary'));
			$objView->AddDef('columnhdrs', '', array(STR_WO_JCN, STR_WO_SEQ, STR_WO_TYPE, STR_WO_PRODUCT, STR_WO_STATUS, STR_WO_PRIORITY,
												STR_WO_SEVERITY, STR_WO_RESPONSIBLE, STR_WO_DEADLINE, STR_WO_SUMMARY));
		}
		
		$objView->AddDef('filter', $forField, $GLOBALS['DCLID']);
		$objView->AddDef('filternot', 'statuses.dcl_status_type', '2');
		if ($forField == 'createby')
			$objView->AddDef('filternot', 'responsible', $GLOBALS['DCLID']);
			
		$objView->AddDef('order', '', array('priorities.weight', 'severities.weight', 'deadlineon', 'eststarton', 'jcn', 'seq'));

		$oTable = new TableHtmlHelper();
		foreach ($objView->columnhdrs as $sColumn)
		{
			if ($sColumn == STR_WO_JCN || $sColumn == STR_WO_SEQ)
				$oTable->addColumn($sColumn, 'html');
			else
				$oTable->addColumn($sColumn, 'string');
		}

		$oTable->setShowRownum(false);
		$oTable->setCaption($objView->title);
		$oTable->setWidth('100%');
		
		if ($forField == 'responsible')
			$oTable->addToolbar(menuLink('', 'menuAction=WorkOrder.SearchMy'), STR_CMMN_VIEWALL);
		else
			$oTable->addToolbar(menuLink('', 'menuAction=WorkOrder.SearchMyCreated'), STR_CMMN_VIEWALL);
		
		$oDB = new DbProvider;
		$oDB->LimitQuery($objView->GetSQL(), 0, 5);
		$aData = $oDB->FetchAllRows();
		for ($i = 0; $i < count($aData); $i++)
		{
			$iID = $aData[$i][0];
			$iSeq = $aData[$i][1];
			$aData[$i][0] = '<a href="' . menuLink('', 'menuAction=WorkOrder.Detail&jcn=' . $iID . '&seq=' . $iSeq) . '">' . $iID . '</a>';
			$aData[$i][1] = '<a href="' . menuLink('', 'menuAction=WorkOrder.Detail&jcn=' . $iID . '&seq=' . $iSeq) . '">' . $iSeq . '</a>';
		}
		
		$oTable->setData($aData);
		if (count($oTable->aData) > 0)
		{
			if ($this->bHasOutput)
				$oTable->setSpacer(true);
			else
				$this->bHasOutput = true;
				
			$oTable->render();
		}
	}
	
	function myProjects()
	{
		$oView = new boView();
		$oView->numrows = 5;

		$oView->table = 'dcl_projects';
		$oView->style = 'report';
		$oView->title = STR_PRJ_MYPRJ;
		$oView->AddDef('columnhdrs', '', array(STR_CMMN_ID, STR_PRJ_LEAD, STR_PRJ_STATUS, STR_PRJ_NAME));
		$oView->AddDef('columns', '', array('projectid', 'reportto.short', 'statuses.name', 'dcl_projects.name'));

		$oView->AddDef('filternot', 'statuses.dcl_status_type', '2');
		$oView->AddDef('filter', 'dcl_projects.reportto', $GLOBALS['DCLID']);

		$oView->AddDef('order', '', array('dcl_projects.name'));

		$oTable = new TableHtmlHelper();
		foreach ($oView->columnhdrs as $sColumn)
		{
			if ($sColumn == STR_PRJ_NAME)
				$oTable->addColumn($sColumn, 'html');
			else
				$oTable->addColumn($sColumn, 'string');
		}

		$oTable->setShowRownum(false);
		$oTable->setCaption($oView->title);
		$oTable->setWidth('100%');
		$oTable->addToolbar(menuLink('', 'menuAction=Project.Index&filterReportto=' . $GLOBALS['DCLID']), STR_CMMN_VIEWALL);

		$oDB = new DbProvider;
		$oDB->LimitQuery($oView->GetSQL(), 0, 5);
		$aData = $oDB->FetchAllRows();
		for ($i = 0; $i < count($aData); $i++)
		{
			$aData[$i][3] = '<a href="' . menuLink('', 'menuAction=Project.Detail&id=' . $aData[$i][0]) . '">' . $aData[$i][3] . '</a>';
		}
		
		$oTable->setData($aData);
		if (count($oTable->aData) > 0)
		{
			if ($this->bHasOutput)
				$oTable->setSpacer(true);
			else
				$this->bHasOutput = true;
				
			$oTable->render();
		}
	}
}
