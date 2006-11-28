<?php
/*
 * $Id: class.htmlProjectSelector.inc.php,v 1.1.1.1 2006/11/27 05:30:45 mdean Exp $
 *
 * Double Choco Latte - Source Configuration Management System
 * Copyright (C) 1999  Michael L. Dean & Tim R. Norman
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
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

LoadStringResource('prj');
class htmlProjectSelector
{
	var $oSmarty;
	var $bMultiSelect;
	var $oView;
	var $oDB;
	
	function htmlProjectSelector()
	{
		$this->bMultiSelect = false;
		$this->oSmarty =& CreateSmarty();
		$this->oView =& CreateObject('dcl.boView');
		$this->oDB = new dclDB;
	}
	
	function show()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEW))
			return PrintPermissionDenied();
		
		if (isset($_REQUEST['multiple']) && $_REQUEST['multiple'] == 'true')
			$this->oSmarty->assign('VAL_MULTIPLE', 'true');
		else
			$this->oSmarty->assign('VAL_MULTIPLE', 'false');
			
		if (isset($_REQUEST['filterID']) && $_REQUEST['filterID'] != '')
			$this->oSmarty->assign('VAL_FILTERID', $_REQUEST['filterID']);

		SmartyDisplay($this->oSmarty, 'htmlProjectSelector.tpl');
	}
	
	function showControlFrame()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEW))
			return PrintPermissionDenied();
			
		if (isset($_REQUEST['multiple']) && $_REQUEST['multiple'] == 'true')
			$this->oSmarty->assign('VAL_MULTIPLE', 'true');
		else
			$this->oSmarty->assign('VAL_MULTIPLE', 'false');
		
		$filterStatus = @DCL_Sanitize::ToSignedInt($_REQUEST['filterStatus']);
		if ($filterStatus === null)
			$filterStatus = -1;
			
		$this->oSmarty->assign('VAL_FILTERSTATUS', $filterStatus);

		$this->oSmarty->assign('VAL_LETTERS', array_merge(array('All'), range('A', 'Z')));
		SmartyDisplay($this->oSmarty, 'htmlProjectSelectorControl.tpl');
		exit();
	}
	
	function showBrowseFrame()
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_VIEW))
			return PrintPermissionDenied();

		$filterStatus = @DCL_Sanitize::ToSignedInt($_REQUEST['filterStatus']);
		if ($filterStatus === null)
			$filterStatus = -1;
		
		$filterStartsWith = '';
		if (IsSet($_REQUEST['filterStartsWith']))
			$filterStartsWith = $_REQUEST['filterStartsWith'];
		
		$filterSearch = '';
		if (IsSet($_REQUEST['filterSearch']))
			$filterSearch = $_REQUEST['filterSearch'];
			
		$filterID = '';
		if (isset($_REQUEST['filterID']))
		{
			if (ereg('^[0-9]+([,][0-9]+)*$', $_REQUEST['filterID']))
				$filterID = explode(',', $_REQUEST['filterID']);
		}
		
		$aColumnHeaders = array(STR_CMMN_ID, STR_PRJ_LEAD, STR_PRJ_STATUS, STR_PRJ_NAME);
		$aColumns = array('projectid', 'reportto.short', 'statuses.name', 'dcl_projects.name');

		$iPage = 1;
		$this->oView->startrow = 0;
		$this->oView->numrows = 15;
		if (isset($_REQUEST['page']))
		{
			$iPage = (int)$_REQUEST['page'];
			if ($iPage < 1)
				$iPage = 1;
			
			$this->oView->startrow = ($iPage - 1) * $this->oView->numrows;
			if ($this->oView->startrow < 0)
				$this->oView->startrow = 0;
		}
		
		$this->oView->table = 'dcl_projects';
		$this->oView->AddDef('columnhdrs', '', $aColumnHeaders);
		$this->oView->AddDef('columns', '', $aColumns);
		$this->oView->AddDef('order', '', array('name'));

		if ($filterStatus !== null)
		{
			if ($filterStatus > 0)
				$this->oView->AddDef('filter', 'dcl_projects.status', $filterStatus);
			else if ($filterStatus == -2)
				$this->oView->AddDef('filter', 'statuses.dcl_status_type', '2');
			else
				$this->oView->AddDef('filternot', 'statuses.dcl_status_type', '2');
		}
		else
			$this->oView->AddDef('filternot', 'statuses.dcl_status_type', '2');
			
		if ($filterSearch != '')
			$this->oView->AddDef('filterlike', 'name', $filterSearch);
			
		if ($filterStartsWith != '')
			$this->oView->AddDef('filterstart', 'name', $filterStartsWith);
			
		if (is_array($filterID))
			$this->oView->AddDef('filter', 'projectid', $filterID);

		if ($this->oDB->Query($this->oView->GetSQL(true)) == -1 || !$this->oDB->next_record())
			exit();
		
		$iRecords = (int)$this->oDB->f(0);
		$this->oSmarty->assign('VAL_COUNT', $iRecords);
		$this->oSmarty->assign('VAL_PAGE', $iPage);
		$this->oSmarty->assign('VAL_MAXPAGE', ceil($iRecords / $this->oView->numrows));
		$this->oDB->FreeResult();

		if ($this->oDB->LimitQuery($this->oView->GetSQL(), $this->oView->startrow, $this->oView->numrows) != -1)
		{
			$aProjects = array();
			while ($this->oDB->next_record())
			{
				$aRecord = $this->oDB->Record;
				$aRecord['status'] = $this->oDB->f(2);
				$aRecord['reportto'] = $this->oDB->f(1);
				array_push($aProjects, $aRecord);
			}
				
			$this->oDB->FreeResult();
			
			$this->oSmarty->assign_by_ref('VAL_PROJECTS', $aProjects);
			$this->oSmarty->assign('VAL_HEADERS', $aColumnHeaders);
			$this->oSmarty->assign('VAL_FILTERID', $filterID);
			$this->oSmarty->assign('VAL_MULTISELECT', (isset($_REQUEST['multiple']) && $_REQUEST['multiple'] == 'true'));
			
 			SmartyDisplay($this->oSmarty, 'htmlProjectSelectorBrowse.tpl');
		}
		
		exit();
	}
}
?>