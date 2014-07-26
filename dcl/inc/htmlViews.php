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

LoadStringResource('vw');

class htmlViews
{
	function GetCombo($default = 0, $cbName = 'viewid', $size = 0, $includePublic = true, $forTable = '')
	{
		$objDB = new SavedSearchesModel();
		$objDB->cacheEnabled = false;

		$query = 'SELECT viewid,name FROM views WHERE ';

		if ($includePublic)
			$query .= '(whoid=' . DCLID . ' OR ispublic=\'Y\')';
		else
			$query .= 'whoid=' . DCLID;

		if ($forTable != '')
			$query .= " AND tablename='$forTable'";

		$query .= ' ORDER BY name';
		$objDB->Query($query);

		$retVal = '<select class="form-control" name="' . $cbName;
		if ($size > 1)
			$retVal .= '[]" multiple size="' . $size;

		$retVal .= '" id="' . $cbName . '">';

		if ($size < 2)
			$retVal .= '<option value="0">' . STR_CMMN_SELECTONE . '</option>';

		while ($objDB->next_record())
		{
			$retVal .= sprintf('<option value="%s"%s>%s</option>',
								$objDB->f(0),
								$objDB->f(0) == $default ? ' selected' : '',
								$objDB->f(1));
		}

		$retVal .= '</select>';

		return $retVal;
	}

	function PrintAll($orderBy = 'name')
	{
		global $g_oSec, $g_oSession;

		commonHeader();

		if (!$g_oSec->HasPerm(DCL_ENTITY_SAVEDSEARCH, DCL_PERM_VIEW))
			throw new PermissionDeniedException();
			
		if ($g_oSession->IsInWorkspace())
		{
			ShowWarning('You are currently in a workspace.  It is possible for results to be mutually exclusive if a search contains a product filter.  If you do not see the results you expect, switch to "No Workspace" or another workspace that has the products contained in the search.');
		}

		$objDB = new SavedSearchesModel();

		$objDB->Query('SELECT viewid,whoid,ispublic,name,tablename FROM views WHERE whoid=' . DCLID . " OR ispublic='Y' ORDER BY $orderBy");
		$allRecs = $objDB->FetchAllRows();

		$oTable = new TableHtmlHelper();
		$oTable->setCaption(sprintf(STR_VW_TITLE, $orderBy));
		$oTable->addColumn(STR_VW_ID, 'numeric');
		$oTable->addColumn(STR_VW_OWNER, 'string');
		$oTable->addColumn(STR_VW_PUBLIC, 'string');
		$oTable->addColumn(STR_VW_NAME, 'html');
		$oTable->addColumn(STR_VW_TABLE, 'string');
		$oTable->addColumn(STR_CMMN_OPTIONS, 'html');

		$objDBP = new PersonnelModel();

		for ($i = 0; $i < count($allRecs); $i++)
		{
			$allRecs[$i][3] = sprintf('<a href="%s">%s</a>',
					menuLink('', sprintf('menuAction=boViews.exec&viewid=%d', $allRecs[$i][0])),
					$allRecs[$i][3]);

			$options = '';
			if ($allRecs[$i][4] == 'workorders' )
				$options .= '<a href="' . menuLink('', 'menuAction=WorkOrder.LoadCriteria&id=' . $allRecs[$i][0]) . '">' . STR_VW_SETUP . '</a>';
			else if ($allRecs[$i][4] == 'tickets' )
				$options .= '<a href="' . menuLink('', 'menuAction=htmlTicketSearches.ShowView&id=' . $allRecs[$i][0]) . '">' . STR_VW_SETUP . '</a>';

			if ($allRecs[$i][1] == DCLID || $g_oSec->HasPerm(DCL_ENTITY_GLOBAL, DCL_PERM_ADMIN))
			{
				if ($options != '')
					$options .= '&nbsp;|&nbsp;';

				$options .= '<a href="' . menuLink('', 'menuAction=boViews.delete&id=' . $allRecs[$i][0]) . '">' . STR_CMMN_DELETE . '</a>';
			}

			if ($options == '')
				$options = '&nbsp;';

			$objDBP->Load($allRecs[$i][1]);
			$allRecs[$i][1] = $objDBP->short;
			$allRecs[$i][] = $options;
		}

		$oTable->setData($allRecs);
		$oTable->setShowRownum(true);
		$oTable->render();
	}

	function ShowEntryForm()
	{
		global $dcl_info, $g_oSec;

		RequirePermission(DCL_ENTITY_SAVEDSEARCH, DCL_PERM_ADD);

		$template = new SmartyHelper();

		$template->assign('VAL_FORMACTION', menuLink());

		$template->assign('VAL_DCLID', DCLID);
		$template->assign('VAL_TABLENAME', $_REQUEST['vt']);

		// Add the URL pieces
		$viewUrl = '';
		$objView = new boView();
		$o = new PersonnelModel();
		while (list($key, $val) = each($objView->urlpieces))
		{
			if (IsSet($_REQUEST[$val]))
				$viewUrl .= sprintf('<input type="hidden" name="%s" value="%s">', $val, htmlspecialchars($o->GPCStripSlashes($_REQUEST[$val]), ENT_QUOTES, 'UTF-8'));
		}

		$template->assign('VAL_VIEWURL', $viewUrl);
		$template->assign('CMB_ISPUBLIC', GetYesNoCombo("N", "ispublic", 0, false));

		$template->Render('ViewForm.tpl');
	}
}
