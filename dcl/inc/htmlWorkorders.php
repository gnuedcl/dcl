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
LoadStringResource('prj');

class htmlWorkorders
{
	function PrintReassignForm()
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ASSIGN))
			throw new PermissionDeniedException();

		$bIsBatch = IsSet($_REQUEST['selected']) && is_array($_REQUEST['selected']) && count($_REQUEST['selected']) > 0;

		$objWO = new dbWorkorders();
		$objProduct = new dbProducts();
		$objHTMLPersonnel = new htmlPersonnel();
		$objHTMLPriorities = new PriorityHtmlHelper();
		$objHTMLSeverities = new htmlSeverities();

		$t = new DCL_Smarty();
		
		if (!$bIsBatch)
		{
			if (($jcn = DCL_Sanitize::ToInt($_REQUEST['jcn'])) === null ||
				($seq = DCL_Sanitize::ToInt($_REQUEST['seq'])) === null
				)
			{
				throw new InvalidDataException();
			}
			
			if ($objWO->Load($jcn, $seq) == -1)
			{
				trigger_error(sprintf(STR_WO_NOTFOUNDERR, $jcn, $seq));
				return;
			}

			$objProduct->Query('SELECT wosetid FROM products WHERE id=' . $objWO->product);
			$objProduct->next_record();
			$setid = $objProduct->f(0);
		}


		$hiddenvars = '';
		if ($bIsBatch)
		{
			$t->assign('menuAction', 'boWorkorders.dbbatchassign');
			$t->assign('selected', $_REQUEST['selected']);

			$t->assign('TXT_TITLE', STR_PRJ_BATCHASSIGN);

			$t->assign('CMB_RESPONSIBLE', $objHTMLPersonnel->GetCombo($GLOBALS['DCLID'], 'responsible', 'lastfirst', 0, true, DCL_ENTITY_WORKORDER));
			$t->assign('CMB_PRIORITY', $objHTMLPriorities->Select(0, 'priority', 'name', 0, false));
			$t->assign('CMB_SEVERITY', $objHTMLSeverities->GetCombo(0, 'severity', 'name', 0, false));

			$oView = new boView();
			$oView->SetFromURL();
			$t->assign('VAL_VIEW', $oView->GetForm());
		}
		else
		{
			$t->assign('TXT_TITLE', STR_WO_REASSIGNTITLE . ' ' . $objWO->jcn . '-' . $objWO->seq);

			$t->assign('menuAction', 'boWorkorders.dbreassign');
			$t->assign('jcn', $objWO->jcn);
			$t->assign('seq', $objWO->seq);

			$t->assign('VAL_DEADLINEON', $objWO->deadlineon);
			$t->assign('VAL_ESTSTARTON', $objWO->eststarton);
			$t->assign('VAL_ESTENDON', $objWO->estendon);
			$t->assign('VAL_ESTHOURS', $objWO->esthours);
			$t->assign('VAL_ETCHOURS', $objWO->etchours);

			$t->assign('CMB_RESPONSIBLE', $objHTMLPersonnel->GetCombo($objWO->responsible, 'responsible', 'lastfirst', 0, true, DCL_ENTITY_WORKORDER));
			$t->assign('CMB_PRIORITY', $objHTMLPriorities->Select($objWO->priority, 'priority', 'name', 0, false, $setid));
			$t->assign('CMB_SEVERITY', $objHTMLSeverities->GetCombo($objWO->severity, 'severity', 'name', 0, false, $setid));
		}

		if (IsSet($_REQUEST['return_to']))
		{
			$t->assign('return_to', $_REQUEST['return_to']);
			// FIXME: specific to projects
			if (IsSet($_REQUEST['project']))
				$t->assign('project', $_REQUEST['project']);
		}

		if ($bIsBatch)
		{
			$t->Render('htmlWorkOrderBatchAssign.tpl');
		}
		else
		{
			$t->Render('htmlWorkOrderReassign.tpl');
		}
	}

	function ShowUploadFileForm($jcn, $seq)
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ATTACHFILE, $jcn, $seq))
			throw new PermissionDeniedException();

		$t = new DCL_Smarty();
		$t->assign('VAL_MAXUPLOADFILESIZE', $dcl_info['DCL_MAX_UPLOAD_FILE_SIZE']);
		$t->assign('VAL_JCN', $jcn);
		$t->assign('VAL_SEQ', $seq);

		$t->Render('htmlWorkOrderAddAttachment.tpl');
	}

	function ShowDeleteAttachmentYesNo($jcn, $seq, $filename)
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_REMOVEFILE, $jcn, $seq))
			throw new PermissionDeniedException();

		$Template = CreateTemplate(array('hForm' => 'htmlWorkOrderDelAttachment.tpl'));
		$Template->set_var('TXT_TITLE', STR_WO_DELETEATTACHMENTTITLE);
		$Template->set_var('VAL_JCN', $jcn);
		$Template->set_var('VAL_SEQ', $seq);
		$Template->set_var('VAL_FILENAME', htmlspecialchars($filename));
		$Template->set_var('VAL_FORMACTION', menuLink());
		$Template->set_var('TXT_DELATTCONFIRM', sprintf(STR_WO_DELATTCONFIRM, htmlspecialchars($filename)));
		$Template->set_var('BTN_YES', STR_CMMN_YES);
		$Template->set_var('BTN_NO', STR_CMMN_NO);

		$Template->pparse('out', 'hForm');
	}

       // THANKS: Urmet Janes
	function ShowCSVUploadDialog()
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_IMPORT))
			throw new PermissionDeniedException();

		$t = new DCL_Smarty();
		$t->assign('VAL_MAXUPLOADFILESIZE', $dcl_info['DCL_MAX_UPLOAD_FILE_SIZE']);
		$t->Render('htmlWorkOrderCSVUpload.tpl');
	}

	function DisplayGraphForm()
	{
		global $dcl_info;
		
		$t = new DCL_Smarty();

		$t->assign('CMB_DAYS', '<select id="days" name="days"><option value="7">7 ' . STR_WO_DAYS . '</option><option value="14">14 ' . STR_WO_DAYS . '</option></select>');
		$t->assign('VAL_TODAY', date($dcl_info['DCL_DATE_FORMAT']));

		$o = new htmlProducts();
		$t->assign('CMB_PRODUCTS', $o->GetCombo(0, 'product', 'name', 0, 0, false));
		
		$t->Render('htmlWorkOrderGraph.tpl');
	}
	
	function email()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		
		if (($jcn = DCL_Sanitize::ToInt($_REQUEST['jcn'])) === null ||
			($seq = DCL_Sanitize::ToInt($_REQUEST['seq'])) === null
			)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEW, $jcn, $seq))
			throw new PermissionDeniedException();

		$oSmarty = new DCL_Smarty();
		$oNotification = new boWatches();
		$oNotification->oMeta = new DCL_MetadataDisplay();
		$oNotification->oMeta->GetWorkOrder($jcn, $seq);
		
		$oSmarty->assign('VAL_HTML', "<br/><br/>" . $oNotification->GetWorkOrderNotificationBody($oNotification->oMeta->oWorkOrder, true));
		$oSmarty->Render('htmlEmail.tpl');
	}

	function changeLog()
	{
		global $dcl_info, $g_oSec;

		commonHeader();

		if (!$g_oSec->HasPerm(DCL_ENTITY_CHANGELOG, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		if (($jcn = DCL_Sanitize::ToInt($_REQUEST['jcn'])) === null ||
			($seq = DCL_Sanitize::ToInt($_REQUEST['seq'])) === null
			)
		{
			throw new InvalidDataException();
		}

		$o = new dbSccsXref();
		if ($o->ListChangeLog(DCL_ENTITY_WORKORDER, $jcn, $seq) != -1)
		{
			$allRecs = array();
			while ($o->next_record())
			{
				$allRecs[] = array($o->f(0) . ': ' . $o->f(2), $o->f(1), $o->f(3), $o->f(4), $o->f(5), $o->FormatTimestampForDisplay($o->f(6)));
			}

			$oTable = new htmlTable();
			$oTable->setCaption("ChangeLog for Work Order $jcn-$seq");
			$oTable->addColumn('Project', 'string');
			$oTable->addColumn('Changed By', 'string');
			$oTable->addColumn('File', 'string');
			$oTable->addColumn('Version', 'string');
			$oTable->addColumn('Comments', 'string');
			$oTable->addColumn('Date', 'string');
	
			$oTable->addToolbar(menuLink('', "menuAction=htmlWorkorders.developerChecklist&jcn=$jcn&seq=$seq"), 'Developer Checklist');
			$oTable->addToolbar(menuLink('', "menuAction=boWorkorders.viewjcn&jcn=$jcn&seq=$seq"), 'Back');
			$oTable->addGroup(0);
			$oTable->setData($allRecs);
			$oTable->setShowRownum(true);
			$oTable->render();
		}
	}

	function developerChecklist()
	{
		global $dcl_info, $g_oSec;

		commonHeader();

		if (!$g_oSec->HasPerm(DCL_ENTITY_CHANGELOG, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		if (($jcn = DCL_Sanitize::ToInt($_REQUEST['jcn'])) === null ||
			($seq = DCL_Sanitize::ToInt($_REQUEST['seq'])) === null
			)
		{
			throw new InvalidDataException();
		}

		$o = new dbSccsXref();
		if ($o->ListChangeLog(DCL_ENTITY_WORKORDER, $jcn, $seq) != -1)
		{
			$allRecs = array();
			while ($o->next_record())
			{
				$allRecs[] = array($o->f(0) . ': ' . $o->f(2), $o->f(1), $o->f(3), $o->f(4), $o->f(5), $o->FormatTimestampForDisplay($o->f(6)));
			}

			$oTable = new htmlTable();
			$oTable->setCaption("ChangeLog for Work Order $jcn-$seq");
			$oTable->addColumn('Project', 'string');
			$oTable->addColumn('Changed By', 'string');
			$oTable->addColumn('File', 'string');
			$oTable->addColumn('Version', 'string');
			$oTable->addColumn('Comments', 'string');
			$oTable->addColumn('Date', 'string');

			//$oTable->addToolbar(menuLink('', "menuAction=boWorkorders.viewjcn&jcn=$jcn&seq=$seq"), 'Back');
			$oTable->addGroup(0);
			$oTable->setData($allRecs);
			$oTable->setShowRownum(true);
			$oTable->sTemplate = 'htmlDeveloperChecklist.tpl';

			$oTable->assign('VAL_DCLNAME', $GLOBALS['DCLNAME']);
			$oTable->assign('VAL_JCN', $jcn);
			$oTable->assign('VAL_SEQ', $seq);

			$oWO = new dbWorkorders();
			$oWO->Load($jcn, $seq);
			$oTable->assign('VAL_SUMMARY', $oWO->summary);

			$oProd = new dbProducts();
			$oProd->Load($oWO->product);
			$oTable->assign('VAL_PRODUCT', $oProd->name);
			
			$oMeta = new DCL_MetadataDisplay();
			if ($oWO->fixed_version_id > 0)
			{
				$oTable->assign('VAL_VERSION', $oMeta->GetProductVersion($oWO->fixed_version_id));
			}
			else if ($oWO->targeted_version_id > 0)
			{
				$oTable->assign('VAL_VERSION', $oMeta->GetProductVersion($oWO->targeted_version_id));
			}

			$oTC = new dbTimeCards();
			$oTC->LimitQuery("SELECT actionon FROM timecards WHERE status = 25 AND jcn = $jcn AND seq = $seq ORDER BY actionon DESC, id DESC", 0, 1);
			if ($oTC->next_record())
			{
				$oTable->assign('VAL_COMPILERDATE', $oTC->FormatDateForDisplay($oTC->f(0)));
			}

			$oTable->render();
		}
	}

	function showmy($obj, $forField, $title, $noneMsg, $rowlimit)
	{
		global $dcl_info, $g_oSec;

		if (!is_object($obj))
		{
			trigger_error('[htmlWorkorders::showmy] ' . STR_WO_OBJECTNOTPASSED);
			return;
		}

		$objView = new boView();
		$objView->title = $title;
		$objView->style = 'report';
		if ($g_oSec->IsPublicUser())
		{
			$objView->AddDef('columns', '', array('jcn', 'seq', 'dcl_wo_type.type_name', 'products.name', 'statuses.name', 'priorities.name', 'severities.name', 'dcl_tag.tag_desc', 'summary'));
			$objView->AddDef('order', '', array('priorities.weight', 'severities.weight', 'jcn', 'seq'));

			$objView->AddDef('columnhdrs', '', array(
					STR_WO_JCN,
					STR_WO_SEQ,
					STR_WO_TYPE,
					STR_WO_PRODUCT,
					STR_WO_STATUS,
					STR_WO_PRIORITY,
					STR_WO_SEVERITY,
					STR_CMMN_TAGS,
					STR_WO_SUMMARY));
		}
		else
		{
			$objView->AddDef('columns', '', array('jcn', 'seq', 'dcl_wo_type.type_name', 'products.name', 'statuses.name', 'priorities.name', 'severities.name', 'responsible.short', 'deadlineon', 'dcl_tag.tag_desc', 'summary'));
			$objView->AddDef('order', '', array('priorities.weight', 'severities.weight', 'deadlineon', 'eststarton', 'jcn', 'seq'));

			$objView->AddDef('columnhdrs', '', array(
					STR_WO_JCN,
					STR_WO_SEQ,
					STR_WO_TYPE,
					STR_WO_PRODUCT,
					STR_WO_STATUS,
					STR_WO_PRIORITY,
					STR_WO_SEVERITY,
					STR_WO_RESPONSIBLE,
					STR_WO_DEADLINE,
					STR_CMMN_TAGS,
					STR_WO_SUMMARY));
		}

		$objView->AddDef('filter', $forField, $GLOBALS['DCLID']);
		$objView->AddDef('filternot', 'statuses.dcl_status_type', '2');
		if ($forField == 'createby')
			$objView->AddDef('filternot', 'responsible', $GLOBALS['DCLID']);
			
		$objHV = CreateViewObject($objView->table);
		$objHV->Render($objView);
	}

	function show()
	{
		global $dcl_info, $g_oSec;

		commonHeader();

		if (!$g_oSec->HasAnyPerm(array(DCL_ENTITY_WORKORDER => array(
									array('perm' => DCL_PERM_VIEW, 'id1' => 0, 'id2' => 0),
									array('perm' => DCL_PERM_VIEWACCOUNT, 'id1' => 0, 'id2' => 0),
									array('perm' => DCL_PERM_VIEWSUBMITTED, 'id1' => 0, 'id2' => 0)))))
			throw new PermissionDeniedException();

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

		$oView->style = 'report';
		$oView->title = STR_WO_BROWSEWO;

		if ($g_oSec->IsPublicUser())
		{
			$oView->AddDef('columns', '', array('jcn', 'seq', 'dcl_wo_type.type_name', 'products.name', 'statuses.name', 'priorities.name', 'severities.name', 'dcl_tag.tag_desc', 'summary'));
			$oView->AddDef('order', '', array('priorities.weight', 'severities.weight', 'jcn', 'seq'));

			$oView->AddDef('columnhdrs', '', array(
					STR_WO_JCN,
					STR_WO_SEQ,
					STR_WO_TYPE,
					STR_WO_PRODUCT,
					STR_WO_STATUS,
					STR_WO_PRIORITY,
					STR_WO_SEVERITY,
					STR_CMMN_TAGS,
					STR_WO_SUMMARY));
		}
		else
		{
			$oView->AddDef('columns', '', array('jcn', 'seq', 'dcl_wo_type.type_name', 'products.name', 'statuses.name', 'priorities.name', 'severities.name', 'responsible.short', 'deadlineon', 'dcl_tag.tag_desc', 'summary'));
			$oView->AddDef('order', '', array('priorities.weight', 'severities.weight', 'deadlineon', 'eststarton', 'jcn', 'seq'));

			$oView->AddDef('columnhdrs', '', array(
					STR_WO_JCN,
					STR_WO_SEQ,
					STR_WO_TYPE,
					STR_WO_PRODUCT,
					STR_WO_STATUS,
					STR_WO_PRIORITY,
					STR_WO_SEVERITY,
					STR_WO_RESPONSIBLE,
					STR_WO_DEADLINE,
					STR_CMMN_TAGS,
					STR_WO_SUMMARY));
		}
		
		$filterStatus = '-1';
		$filterReportto = '0';
		$filterProduct = '0';
		$filterType = '0';
		$filterPriority = '0';
		if (IsSet($_REQUEST['filterStatus']))
			$filterStatus = $_REQUEST['filterStatus'];
		if (IsSet($_REQUEST['filterReportto']))
			$filterReportto = $_REQUEST['filterReportto'];
		if (IsSet($_REQUEST['filterProduct']))
			$filterProduct = $_REQUEST['filterProduct'];
		if (IsSet($_REQUEST['filterType']))
			$filterType = $_REQUEST['filterType'];
		if (IsSet($_REQUEST['filterPriority']))
			$filterPriority = $_REQUEST['filterPriority'];

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
			$oView->AddDef('filter', 'wo_type_id', $filterType);
			
		if ($filterPriority != '0')
			$oView->AddDef('filter', 'priority', $filterPriority);

		$oHtml = new htmlWorkOrderBrowse();
		$oHtml->sColumnTitle = STR_CMMN_OPTIONS;
		$oHtml->sPagingMenuAction = 'htmlWorkorders.show';
		$oHtml->bColumnSort = false;
		$oHtml->bShowPager = false;
		$oHtml->Render($oView);
	}
}
