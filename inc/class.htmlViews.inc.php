<?php
/*
 * $Id: class.htmlViews.inc.php,v 1.1.1.1 2006/11/27 05:30:50 mdean Exp $
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

LoadStringResource('vw');

class htmlViews
{
	function GetCombo($default = 0, $cbName = 'viewid', $size = 0, $includePublic = true, $forTable = '')
	{
		$objDB = CreateObject('dcl.dbViews');
		$objDB->cacheEnabled = false;

		$query = 'SELECT viewid,name FROM views WHERE ';

		if ($includePublic)
			$query .= '(whoid=' . $GLOBALS['DCLID'] . ' OR ispublic=\'Y\')';
		else
			$query .= 'whoid=' . $GLOBALS['DCLID'];

		if ($forTable != '')
			$query .= " AND tablename='$forTable'";

		$query .= ' ORDER BY name';
		$objDB->Query($query);

		$retVal = '<select name="' . $cbName;
		if ($size > 1)
			$retVal .= '[]" multiple size="' . $size;

		$retVal .= '">';

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
		global $g_oSec;

		commonHeader();

		if (!$g_oSec->HasPerm(DCL_ENTITY_SAVEDSEARCH, DCL_PERM_VIEW))
			return PrintPermissionDenied();

		$objDB = CreateObject('dcl.dbViews');

		$objDB->Query('SELECT viewid,whoid,ispublic,name,tablename FROM views WHERE whoid=' . $GLOBALS['DCLID'] . " OR ispublic='Y' ORDER BY $orderBy");
		$allRecs = $objDB->FetchAllRows();

		$oTable =& CreateObject('dcl.htmlTable');
		$oTable->setCaption(sprintf(STR_VW_TITLE, $orderBy));
		$oTable->addColumn(STR_VW_ID, 'numeric');
		$oTable->addColumn(STR_VW_OWNER, 'string');
		$oTable->addColumn(STR_VW_PUBLIC, 'string');
		$oTable->addColumn(STR_VW_NAME, 'html');
		$oTable->addColumn(STR_VW_TABLE, 'string');
		$oTable->addColumn(STR_CMMN_OPTIONS, 'html');

		$objDBP = CreateObject('dcl.dbPersonnel');

		for ($i = 0; $i < count($allRecs); $i++)
		{
			$allRecs[$i][3] = sprintf('<a href="%s">%s</a>',
					menuLink('', sprintf('menuAction=boViews.exec&viewid=%d', $allRecs[$i][0])),
					$allRecs[$i][3]);

			$options = '';
			if ($allRecs[$i][4] == 'workorders' )
				$options .= '<a href="' . menuLink('', 'menuAction=htmlWOSearches.ShowView&id=' . $allRecs[$i][0]) . '">' . STR_VW_SETUP . '</a>';
			else if ($allRecs[$i][4] == 'tickets' )
				$options .= '<a href="' . menuLink('', 'menuAction=htmlTicketSearches.ShowView&id=' . $allRecs[$i][0]) . '">' . STR_VW_SETUP . '</a>';

			if ($allRecs[$i][1] == $GLOBALS['DCLID'] || $g_oSec->HasPerm(DCL_ENTITY_GLOBAL, DCL_PERM_ADMIN))
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

		if (!$g_oSec->HasPerm(DCL_ENTITY_SAVEDSEARCH, DCL_PERM_ADD))
			return PrintPermissionDenied();

		$Template = CreateTemplate(array('hForm' => 'htmlViewForm.tpl'));

		$Template->set_var('TXT_TITLE', STR_VW_ADDVIEW);
		$Template->set_var('TXT_PUBLIC', STR_VW_PUBLIC);
		$Template->set_var('TXT_NAME', STR_VW_NAME);
		$Template->set_var('BTN_SAVE', STR_CMMN_SAVE);
		$Template->set_var('BTN_RESET', STR_CMMN_RESET);
		$Template->set_var('TXT_HIGHLIGHTEDNOTE', STR_CMMN_HIGHLIGHTEDNOTE);
		$Template->set_var('VAL_FORMACTION', menuLink());
		$Template->set_var('BTN_CANCEL', STR_CMMN_CANCEL);

		$Template->set_var('VAL_DCLID', $GLOBALS['DCLID']);
		$Template->set_var('VAL_TABLENAME', $_REQUEST['vt']);

		// Add the URL pieces
		$viewUrl = '';
		$objView = CreateObject("dcl.boView");
		$o = CreateObject('dcl.dbPersonnel');
		while (list($key, $val) = each($objView->urlpieces))
		{
			if (IsSet($_REQUEST[$val]))
				$viewUrl .= sprintf('<input type="hidden" name="%s" value="%s">', $val, htmlspecialchars($o->GPCStripSlashes($_REQUEST[$val])));
		}

		$Template->set_var('VAL_VIEWURL', $viewUrl);
		$Template->set_var('CMB_ISPUBLIC', GetYesNoCombo("N", "ispublic", 0, false));

		$Template->pparse('out', 'hForm');
	}
}
?>
