<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2011 Free Software Foundation
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
LoadStringResource('cfg');

class UrlTypePresenter
{
	public function Index()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_URLTYPE, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$o = new UrlTypeSqlQueryHelper();
		$o->title = sprintf('URL Types');
		$o->AddDef('columns', '', array('url_type_id', 'url_type_name'));
		$o->AddDef('columnhdrs', '', array(STR_CMMN_ID, STR_CMMN_NAME));
		$o->AddDef('order', '', 'url_type_name');

		$oDB = new UrlTypeModel();
		if ($oDB->query($o->GetSQL()) == -1)
			return;
			
		$allRecs = $oDB->FetchAllRows();

		$oTable = new TableHtmlHelper();
		$oTable->setCaption('URL Types');
		$oTable->addColumn(STR_CMMN_ID, 'numeric');
		$oTable->addColumn(STR_CMMN_NAME, 'string');

		if ($g_oSec->HasPerm(DCL_ENTITY_URLTYPE, DCL_PERM_ADD))
			$oTable->addToolbar(menuLink('', 'menuAction=UrlType.Create'), STR_CMMN_NEW);

		if ($g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW))
			$oTable->addToolbar(menuLink('', 'menuAction=SystemSetup.Index'), DCL_MENU_SYSTEMSETUP);

		if (count($allRecs) > 0 && $g_oSec->HasAnyPerm(array(DCL_ENTITY_URLTYPE => array($g_oSec->PermArray(DCL_PERM_MODIFY), $g_oSec->PermArray(DCL_PERM_DELETE)))))
		{
			$oTable->addColumn(STR_CMMN_OPTIONS, 'html');
			for ($i = 0; $i < count($allRecs); $i++)
			{
				$options = '';
				if ($g_oSec->HasPerm(DCL_ENTITY_URLTYPE, DCL_PERM_MODIFY))
					$options = '<a href="' . menuLink('', 'menuAction=UrlType.Edit&url_type_id=' . $allRecs[$i][0]) . '">' . STR_CMMN_EDIT . '</a>';

				if ($g_oSec->HasPerm(DCL_ENTITY_URLTYPE, DCL_PERM_DELETE))
				{
					if ($options != '')
						$options .= '&nbsp;|&nbsp;';

					$options .= '<a href="' . menuLink('', 'menuAction=UrlType.Delete&url_type_id=' . $allRecs[$i][0]) . '">' . STR_CMMN_DELETE . '</a>';
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
		if (!$g_oSec->HasPerm(DCL_ENTITY_URLTYPE, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$t = new SmartyHelper();

		$t->assign('TXT_FUNCTION', 'Add URL Type');
		$t->assign('menuAction', 'UrlType.Insert');

		$t->Render('htmlUrlTypeForm.tpl');
	}

	public function Edit(UrlTypeModel $model)
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_URLTYPE, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$t = new SmartyHelper();

		$t->assign('TXT_FUNCTION', 'Edit URL Type');
		$t->assign('menuAction', 'UrlType.Update');
		$t->assign('url_type_id', $model->url_type_id);
		$t->assign('VAL_NAME', $model->url_type_name);

		$t->Render('htmlUrlTypeForm.tpl');
	}

	public function Delete(UrlTypeModel $model)
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_URLTYPE, DCL_PERM_DELETE))
			throw new PermissionDeniedException();

		ShowDeleteYesNo('URL Type', 'UrlType.Destroy', $model->url_type_id, $model->url_type_name);
	}
}
