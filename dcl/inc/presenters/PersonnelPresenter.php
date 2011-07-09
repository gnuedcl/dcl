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

LoadStringResource('usr');

class PersonnelPresenter
{
	public function Index()
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$template = new DCL_Smarty();
		$template->assign('VAL_LETTERS', array_merge(array('All'), range('A', 'Z')));
		$template->assign('PERM_ADD', $g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_ADD));
		$template->assign('PERM_MODIFY', $g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_MODIFY));
		$template->assign('PERM_DELETE', $g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_DELETE));
		$template->assign('PERM_SETUP', $g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW));

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
			$filterDepartment = Filter::ToInt($_REQUEST['filterDepartment']);

		$template->assign('VAL_FILTERACTIVE', $filterActive);
		$template->assign('VAL_FILTERSTART', $filterStartsWith);
		$template->assign('VAL_FILTERSEARCH', $filterSearch);
		$template->assign('VAL_FILTERDEPT', $filterDepartment);

		$aColumnHeaders = array(STR_CMMN_ID, STR_CMMN_ACTIVE, STR_USR_LOGIN, STR_CMMN_NAME, STR_USR_DEPARTMENT, 'Phone', 'Email', 'Internet');
		$aColumns = array('id', 'active', 'short', 'dcl_contact.last_name', 'dcl_contact.first_name', 'departments.name', 'dcl_contact_phone.phone_number', 'dcl_contact_email.email_addr', 'dcl_contact_url.url_addr');

		$queryHelper = new PersonnelSqlQueryHelper();
		$model = new PersonnelModel();
		$model = new DbProvider;

		$iPage = 1;
		$queryHelper->startrow = 0;
		$queryHelper->numrows = 25;
		if (isset($_REQUEST['page']))
		{
			$iPage = (int)$_REQUEST['page'];
			if ($iPage < 1)
				$iPage = 1;

			$queryHelper->startrow = ($iPage - 1) * $queryHelper->numrows;
			if ($queryHelper->startrow < 0)
				$queryHelper->startrow = 0;
		}

		$queryHelper->AddDef('columnhdrs', '', $aColumnHeaders);
		$queryHelper->AddDef('columns', '', $aColumns);
		$queryHelper->AddDef('order', '', array('short', 'id'));

		if ($filterActive == 'Y' || $filterActive == 'N')
			$queryHelper->AddDef('filter', 'active', "'$filterActive'");

		if ($filterSearch != '')
			$queryHelper->AddDef('filterlike', 'short', $filterSearch);

		if ($filterStartsWith != '')
			$queryHelper->AddDef('filterstart', 'short', $filterStartsWith);

		if ($filterDepartment > 0)
			$queryHelper->AddDef('filter', 'department', $filterDepartment);

		if ($model->Query($queryHelper->GetSQL(true)) == -1 || !$model->next_record())
			return;

		$iRecords = (int)$model->f(0);
		$template->assign('VAL_COUNT', $iRecords);
		$template->assign('VAL_PAGE', $iPage);
		$template->assign('VAL_MAXPAGE', ceil($iRecords / $queryHelper->numrows));
		$model->FreeResult();

		if ($model->LimitQuery($queryHelper->GetSQL(), $queryHelper->startrow, $queryHelper->numrows) != -1)
		{
			$aUsers = array();
			while ($model->next_record())
				$aUsers[] = $model->Record;

			$model->FreeResult();

			$template->assign_by_ref('VAL_USERS', $aUsers);
			$template->assign('VAL_HEADERS', $aColumnHeaders);
		}

		$template->Render('htmlPersonnelBrowse.tpl');
	}

	public function Create()
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$template = new DCL_Smarty();

		$template->assign('IS_EDIT', false);
		$template->assign('VAL_ACTIVE', 'Y');
		$template->assign('VAL_REPORTTO', $GLOBALS['DCLID']);
		$template->assign('VAL_DEPARTMENT', 0);
		$template->assign('VAL_SHORT', '');

		$oUserRole = new UserRoleModel();
		$template->assign('Roles', $oUserRole->GetGlobalRoles());

		$template->Render('htmlPersonnelForm.tpl');
	}

	public function Edit(PersonnelModel $model)
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$template = new DCL_Smarty();
		
		$template->assign('IS_EDIT', true);
		$template->assign('VAL_PERSONNELID', $model->id);
		$template->assign('VAL_ACTIVE', $model->active);
		$template->assign('VAL_SHORT', $model->short);
		$template->assign('VAL_REPORTTO', $model->reportto);
		$template->assign('VAL_DEPARTMENT', $model->department);

		$oUserRole = new UserRoleModel();
		$template->assign('Roles', $oUserRole->GetGlobalRoles($model->id));

		$oMeta = new DCL_MetadataDisplay();
		$aContact =& $oMeta->GetContact($model->contact_id);

		$template->assign('VAL_CONTACTID', $model->contact_id);
		$template->assign('VAL_CONTACTNAME', $aContact['name']);

		$template->Render('htmlPersonnelForm.tpl');
	}

	public function Delete(PersonnelModel $model)
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PERSONNEL, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		ShowDeleteYesNo('User', 'Personnel.Destroy', $model->id, $model->short);
	}

	public function EditPassword()
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PREFS, DCL_PERM_PASSWORD))
			throw new PermissionDeniedException();
		
		$oSmarty = new DCL_Smarty();
		
		$oSmarty->assign('PERM_ADMIN', $g_oSec->HasPerm(DCL_ENTITY_GLOBAL, DCL_PERM_ADMIN));
		$oSmarty->assign('VAL_USERID', $GLOBALS['DCLID']);
		$oSmarty->assign('VAL_USERNAME', $GLOBALS['DCLNAME']);
		
		$oSmarty->Render('htmlPersonnelPasswdForm.tpl');
	}
}
