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

LoadStringResource('tck');

class htmlTickets
{
	function PrintReassignForm($obj)
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ASSIGN, $obj->ticketid))
			return PrintPermissionDenied();

		$objProduct = new dbProducts();
		$objHTMLPersonnel = new htmlPersonnel();
		$objHTMLPriorities = new htmlPriorities();
		$objHTMLSeverities = new htmlSeverities();

		$objProduct->Query('SELECT tcksetid FROM products WHERE id=' . $obj->product);
		$objProduct->next_record();
		$setid = $objProduct->f(0);
		
		$t = CreateSmarty();
		$t->assign('TXT_TITLE', sprintf(STR_TCK_REASSIGNTICKET, $obj->ticketid));
		$t->assign('VAL_TICKETID', $obj->ticketid);
		$t->assign('CMB_RESPONSIBLE', $objHTMLPersonnel->GetCombo($obj->responsible, 'responsible', 'lastfirst', 0, true, DCL_ENTITY_TICKET));
		$t->assign('CMB_PRIORITY', $objHTMLPriorities->GetCombo($obj->priority, 'priority', 'name', 0, false, $setid));
		$t->assign('CMB_TYPE', $objHTMLSeverities->GetCombo($obj->type, 'type', 'name', 0, false, $setid));
		
		SmartyDisplay($t, 'htmlTicketReassignForm.tpl');
	}

	function ShowUploadFileForm($obj)
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ATTACHFILE, $obj->ticketid))
			return PrintPermissionDenied();

		$t = CreateSmarty();

		$t->assign('VAL_MAXUPLOADFILESIZE', $dcl_info['DCL_MAX_UPLOAD_FILE_SIZE']);
		$t->assign('VAL_TICKETID', $obj->ticketid);
		$t->assign('LNK_CANCEL', menuLink('', 'menuAction=boTickets.view&ticketid=' . $obj->ticketid));
		
		SmartyDisplay($t, 'htmlTicketAddAttachment.tpl');
	}

	function ShowDeleteAttachmentYesNo($ticketid, $filename)
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_REMOVEFILE, $ticketid))
			return PrintPermissionDenied();

		$t = CreateSmarty();
		$t->assign('VAL_FILENAME', $filename);
		$t->assign('VAL_TICKETID', $ticketid);
		$t->assign('TXT_DELCONFIRM', sprintf(STR_TCK_CONFIRMDELATT, $filename));
		
		SmartyDisplay($t, 'htmlTicketDelAttachment.tpl');
	}

	function DisplayGraphForm()
	{
		global $dcl_info;

		$t =& CreateSmarty();

		$t->assign('CMB_DAYS', '<select id="days" name="days"><option value="7">7 ' . STR_WO_DAYS . '</option><option value="14">14 ' . STR_WO_DAYS . '</option></select>');
		$t->assign('VAL_TODAY', date($dcl_info['DCL_DATE_FORMAT']));

		$o = new htmlProducts();
		$t->assign('CMB_PRODUCTS', $o->GetCombo(0, 'product', 'name', 0, 0, false));
		
		SmartyDisplay($t, 'htmlTicketGraph.tpl');
	}

	function showSubmissions()
	{
		commonHeader();
		$this->showmy('createdby', STR_TCK_MYSUBMISSIONS, STR_TCK_NOSUBMISSIONS);
	}

	function showmy($forField, $title, $noneMsg)
	{
		global $dcl_info, $g_oSec;

		$obj = new dbTickets();

		$objView = new boView();
		$objView->title = $title;
		$objView->style = 'report';
		$objView->table = 'tickets';
		$objView->AddDef('columns', '', array('ticketid', 'priorities.name', 'severities.name', 'responsible.short', 'dcl_tag.tag_desc', 'summary'));
		$objView->AddDef('columnhdrs', '', array(
				STR_TCK_TICKET,
				STR_TCK_PRIORITY,
				STR_TCK_TYPE,
				STR_TCK_RESPONSIBLE,
				STR_CMMN_TAGS,
				STR_TCK_SUMMARY));

		$objView->AddDef('filter', $forField, $GLOBALS['DCLID']);
		$objView->AddDef('filternot', 'statuses.dcl_status_type', '2');
		if ($forField == 'createdby')
			$objView->AddDef('filternot', 'responsible', $GLOBALS['DCLID']);
		$objView->AddDef('order', '', array('priorities.weight', 'severities.weight', 'ticketid'));

		$objHV = CreateViewObject($objView->table);
		$objHV->Render($objView);
	}

	function show()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasAnyPerm(array(DCL_ENTITY_TICKET => array($g_oSec->PermArray(DCL_PERM_VIEW), $g_oSec->PermArray(DCL_PERM_VIEWSUBMITTED), $g_oSec->PermArray(DCL_PERM_VIEWACCOUNT)))))
			return PrintPermissionDenied();

		$oView = new boView();
		if ((IsSet($_REQUEST['btnNav']) || IsSet($_REQUEST['jumptopage'])) && IsSet($_REQUEST['startrow']) && IsSet($_REQUEST['numrows']))
		{
			if (IsSet($_REQUEST['btnNav']) && $_REQUEST['btnNav'] == '<<')
				$oView->startrow = (int)$_REQUEST['startrow'] - (int)$_REQUEST['numrows'];
			else if (IsSet($_REQUEST['btnNav']) && $_REQUEST['btnNav'] == '>>')
				$oView->startrow = (int)$_REQUEST['startrow'] + (int)$_REQUEST['numrows'];
			else
			{
				$iPage = (int)$_REQUEST['jumptopage'];
				if ($iPage < 1)
					$iPage = 1;

				$oView->startrow = ($iPage - 1) * (int)$_REQUEST['numrows'];
			}

			if ($oView->startrow < 0)
				$oView->startrow = 0;

			$oView->numrows = (int)$_REQUEST['numrows'];
		}
		else
		{
			$oView->numrows = 25;
			$oView->startrow = 0;
		}

		$oView->table = 'tickets';
		$oView->style = 'report';
		$oView->title = STR_TCK_BROWSETCK;
		$oView->AddDef('order', '', array('priorities.weight', 'severities.weight', 'ticketid'));

		if ($g_oSec->IsPublicUser())
		{
			$oView->AddDef('columns', '', array('ticketid', 'products.name', 'statuses.name', 'priorities.name', 'severities.name', 'dcl_tag.tag_desc', 'summary'));
			$oView->AddDef('columnhdrs', '', array(STR_TCK_TICKET, STR_TCK_PRODUCT, STR_TCK_STATUS, STR_TCK_PRIORITY, STR_TCK_TYPE, STR_CMMN_TAGS, STR_TCK_SUMMARY));
		}
		else
		{
			$oView->AddDef('columns', '', array('ticketid', 'products.name', 'statuses.name', 'priorities.name', 'severities.name', 'responsible.short', 'dcl_tag.tag_desc', 'summary'));
			$oView->AddDef('columnhdrs', '', array(STR_TCK_TICKET, STR_TCK_PRODUCT, STR_TCK_STATUS, STR_TCK_PRIORITY, STR_TCK_TYPE, STR_TCK_RESPONSIBLE, STR_CMMN_TAGS, STR_TCK_SUMMARY));
		}

		$filterStatus = '-1';
		$filterReportto = '0';
		$filterProduct = '0';
		$filterType = '0';
		if (IsSet($_REQUEST['filterStatus']))
			$filterStatus = $_REQUEST['filterStatus'];
		if (IsSet($_REQUEST['filterReportto']))
			$filterReportto = $_REQUEST['filterReportto'];
		if (IsSet($_REQUEST['filterProduct']))
			$filterProduct = $_REQUEST['filterProduct'];
		if (IsSet($_REQUEST['filterType']))
			$filterType = $_REQUEST['filterType'];

		if ($filterStatus != '0')
		{
			if ($filterStatus == '-1')
				$oView->AddDef('filternot', 'statuses.dcl_status_type', '2');
			else if ($filterStatus == '-2')
				$oView->AddDef('filter', 'statuses.dcl_status_type', '2');
			else
				$oView->AddDef('filter', 'status', $filterStatus);
		}

		if ($filterReportto != '0')
			$oView->AddDef('filter', 'responsible', $filterReportto);

		if ($filterProduct != '0')
			$oView->AddDef('filter', 'product', $filterProduct);

		if ($filterType != '0')
			$oView->AddDef('filter', 'type', $filterType);

		$oHtml = new htmlTicketBrowse();
		$oHtml->Render($oView);
	}
}
