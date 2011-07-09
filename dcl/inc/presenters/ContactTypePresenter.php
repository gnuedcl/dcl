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

LoadStringResource('admin');
class ContactTypePresenter
{
	function Index()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACTTYPE, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$o = new ContactTypeSqlQueryHelper();
		$o->title = STR_ADMIN_CONTACTTYPES;
		$o->AddDef('columns', '', array('contact_type_id', 'contact_type_name', 'contact_type_is_main'));
		$o->AddDef('columnhdrs', '', array(STR_CMMN_ID, STR_CMMN_NAME, 'Main'));
		$o->AddDef('order', '', 'contact_type_name');

		$oDB = new ContactTypeModel();
		if ($oDB->query($o->GetSQL()) == -1)
			return;
			
		$allRecs = $oDB->FetchAllRows();

		$oTable = new TableHtmlHelper();
		$oTable->setCaption(STR_ADMIN_CONTACTTYPES);
		$oTable->addColumn(STR_CMMN_ID, 'numeric');
		$oTable->addColumn(STR_CMMN_NAME, 'string');
		$oTable->addColumn('Main', 'string');

		if ($g_oSec->HasPerm(DCL_ENTITY_CONTACTTYPE, DCL_PERM_ADD))
			$oTable->addToolbar(menuLink('', 'menuAction=ContactType.Create'), STR_CMMN_NEW);

		if ($g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW))
			$oTable->addToolbar(menuLink('', 'menuAction=SystemSetup.Index'), DCL_MENU_SYSTEMSETUP);

		if (count($allRecs) > 0 && $g_oSec->HasAnyPerm(array(DCL_ENTITY_CONTACTTYPE => array($g_oSec->PermArray(DCL_PERM_MODIFY), $g_oSec->PermArray(DCL_PERM_DELETE)))))
		{
			$oTable->addColumn(STR_CMMN_OPTIONS, 'html');
			for ($i = 0; $i < count($allRecs); $i++)
			{
				$options = '';
				if ($g_oSec->HasPerm(DCL_ENTITY_CONTACTTYPE, DCL_PERM_MODIFY))
					$options = '<a href="' . menuLink('', 'menuAction=ContactType.Edit&contact_type_id=' . $allRecs[$i][0]) . '">' . STR_CMMN_EDIT . '</a>';

				if ($g_oSec->HasPerm(DCL_ENTITY_CONTACTTYPE, DCL_PERM_DELETE))
				{
					if ($options != '')
						$options .= '&nbsp;|&nbsp;';

					$options .= '<a href="' . menuLink('', 'menuAction=ContactType.Delete&contact_type_id=' . $allRecs[$i][0]) . '">' . STR_CMMN_DELETE . '</a>';
				}

				$allRecs[$i][] = $options;
			}
		}
		
		$oTable->setData($allRecs);
		$oTable->setShowRownum(true);
		$oTable->render();
	}

	public function Create()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACTTYPE, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$t = new SmartyHelper();

		$t->assign('TXT_FUNCTION', 'Add Contact Type');
		$t->assign('menuAction', 'ContactType.Insert');

		$t->Render('htmlContactTypeForm.tpl');
	}

	public function Edit(ContactTypeModel $model)
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACTTYPE, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$t = new SmartyHelper();

		$t->assign('TXT_FUNCTION', 'Edit Contact Type');
		$t->assign('menuAction', 'ContactType.Update');
		$t->assign('contact_type_id', $model->contact_type_id);
		$t->assign('VAL_MAIN', $model->contact_type_is_main);
		$t->assign('VAL_NAME', $model->contact_type_name);

		$t->Render('htmlContactTypeForm.tpl');
	}

	public function Delete(ContactTypeModel $model)
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACTTYPE, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		ShowDeleteYesNo(STR_ADMIN_CONTACTTYPES, 'ContactType.Destroy', $model->contact_type_id, $model->contact_type_name);
	}
}
