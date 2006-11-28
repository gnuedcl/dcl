<?php
/*
 * $Id: class.htmlPersonnelBrowse.inc.php,v 1.1.1.1 2006/11/27 05:30:50 mdean Exp $
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

LoadStringResource('usr');
class htmlPersonnelBrowse
{
	var $oSmarty;
	var $oView;
	var $oDB;

	function htmlPersonnelBrowse()
	{
		$this->oSmarty =& CreateSmarty();
		$this->oView =& CreateObject('dcl.boView');
		$this->oDB = new dclDB;
	}

	function show()
	{
		global $dcl_info, $g_oSec;

		commonHeader();

		if (!$g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_VIEW))
			return PrintPermissionDenied();

		$this->oSmarty->assign('VAL_LETTERS', array_merge(array('All'), range('A', 'Z')));
		$this->oSmarty->assign('PERM_ADD', $g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_ADD));
		$this->oSmarty->assign('PERM_MODIFY', $g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_MODIFY));
		$this->oSmarty->assign('PERM_DELETE', $g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_DELETE));
		$this->oSmarty->assign('PERM_SETUP', $g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW));

		$filterActive = '';
		if (IsSet($_REQUEST['filterActive']))
			$filterActive = $_REQUEST['filterActive'];

		$filterStartsWith = '';
		if (IsSet($_REQUEST['filterStartsWith']))
			$filterStartsWith = $_REQUEST['filterStartsWith'];

		$filterSearch = '';
		if (IsSet($_REQUEST['filterSearch']))
			$filterSearch = $_REQUEST['filterSearch'];

		$filterDepartment = 0;
		if (IsSet($_REQUEST['filterDepartment']))
			$filterDepartment = DCL_Sanitize::ToInt($_REQUEST['filterDepartment']);

		$this->oSmarty->assign('VAL_FILTERACTIVE', $filterActive);
		$this->oSmarty->assign('VAL_FILTERSTART', $filterStartsWith);
		$this->oSmarty->assign('VAL_FILTERSEARCH', $filterSearch);
		$this->oSmarty->assign('VAL_FILTERDEPT', $filterDepartment);

		$aColumnHeaders = array(STR_CMMN_ID, STR_CMMN_ACTIVE, STR_USR_LOGIN, STR_CMMN_NAME, STR_USR_DEPARTMENT, 'Phone', 'Email', 'Internet');
		$aColumns = array('id', 'active', 'short', 'dcl_contact.last_name', 'dcl_contact.first_name', 'departments.name', 'dcl_contact_phone.phone_number', 'dcl_contact_email.email_addr', 'dcl_contact_url.url_addr');

		$iPage = 1;
		$this->oView->startrow = 0;
		$this->oView->numrows = 25;
		if (isset($_REQUEST['page']))
		{
			$iPage = (int)$_REQUEST['page'];
			if ($iPage < 1)
				$iPage = 1;

			$this->oView->startrow = ($iPage - 1) * $this->oView->numrows;
			if ($this->oView->startrow < 0)
				$this->oView->startrow = 0;
		}

		$this->oView->table = 'personnel';
		$this->oView->AddDef('columnhdrs', '', $aColumnHeaders);
		$this->oView->AddDef('columns', '', $aColumns);
		$this->oView->AddDef('order', '', array('short', 'id'));

		if ($filterActive == 'Y' || $filterActive == 'N')
			$this->oView->AddDef('filter', 'active', "'$filterActive'");

		if ($filterSearch != '')
			$this->oView->AddDef('filterlike', 'short', $filterSearch);

		if ($filterStartsWith != '')
			$this->oView->AddDef('filterstart', 'short', $filterStartsWith);

		if ($filterDepartment > 0)
			$this->oView->AddDef('filter', 'department', $filterDepartment);

		if ($this->oDB->Query($this->oView->GetSQL(true)) == -1 || !$this->oDB->next_record())
			return;

		$iRecords = (int)$this->oDB->f(0);
		$this->oSmarty->assign('VAL_COUNT', $iRecords);
		$this->oSmarty->assign('VAL_PAGE', $iPage);
		$this->oSmarty->assign('VAL_MAXPAGE', ceil($iRecords / $this->oView->numrows));
		$this->oDB->FreeResult();

		if ($this->oDB->LimitQuery($this->oView->GetSQL(), $this->oView->startrow, $this->oView->numrows) != -1)
		{
			$aUsers = array();
			while ($this->oDB->next_record())
				$aUsers[] = $this->oDB->Record;

			$this->oDB->FreeResult();

			$this->oSmarty->assign_by_ref('VAL_USERS', $aUsers);
			$this->oSmarty->assign('VAL_HEADERS', $aColumnHeaders);
		}

		SmartyDisplay($this->oSmarty, 'htmlPersonnelBrowse.tpl');
	}
}
?>
