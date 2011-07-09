<?php
/*
 * $Id$
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

class htmlContactSelector
{
	var $oSmarty;
	var $bMultiSelect;
	var $oView;
	var $oDB;

	function htmlContactSelector()
	{
		$this->bMultiSelect = false;
		$this->oSmarty = new DCL_Smarty();
		$this->oView = new boView();
		$this->oDB = new DbProvider;
	}

	function show()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		if (isset($_REQUEST['multiple']) && $_REQUEST['multiple'] == 'true')
			$this->oSmarty->assign('VAL_MULTIPLE', 'true');
		else
			$this->oSmarty->assign('VAL_MULTIPLE', 'false');

		if (isset($_REQUEST['filterID']) && $_REQUEST['filterID'] != '')
			$this->oSmarty->assign('VAL_FILTERID', $_REQUEST['filterID']);
			
		if (isset($_REQUEST['filterActive']) && ($_REQUEST['filterActive'] == 'Y' || $_REQUEST['filterActive'] == 'N' || $_REQUEST['filterActive'] == 'S'))
			$this->oSmarty->assign('VAL_FILTERACTIVE', $_REQUEST['filterActive']);
		else
			$this->oSmarty->assign('VAL_FILTERACTIVE', '');

		$this->oSmarty->Render('htmlContactSelector.tpl');
	}

	function showControlFrame()
	{
		global $g_oSec;
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		if (isset($_REQUEST['multiple']) && $_REQUEST['multiple'] == 'true')
			$this->oSmarty->assign('VAL_MULTIPLE', 'true');
		else
			$this->oSmarty->assign('VAL_MULTIPLE', 'false');

		if (isset($_REQUEST['filterActive']) && ($_REQUEST['filterActive'] == 'Y' || $_REQUEST['filterActive'] == 'N' || $_REQUEST['filterActive'] == 'S'))
			$this->oSmarty->assign('VAL_FILTERACTIVE', $_REQUEST['filterActive']);
		else
			$this->oSmarty->assign('VAL_FILTERACTIVE', '');

		$this->oSmarty->assign('PERM_ADD', $g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_ADD));
		$this->oSmarty->assign('VAL_LETTERS', array_merge(array('All'), range('A', 'Z')));

		$this->oSmarty->Render('htmlContactSelectorControl.tpl');
		
		exit();
	}

	function showBrowseFrame()
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_VIEW))
			throw new PermissionDeniedException();

		$filterActive = '';
		if (IsSet($_REQUEST['filterActive']))
			$filterActive = $_REQUEST['filterActive'];

		$filterStartsWith = '';
		if (IsSet($_REQUEST['filterStartsWith']))
			$filterStartsWith = $_REQUEST['filterStartsWith'];

		$filterSearch = '';
		if (IsSet($_REQUEST['filterSearch']))
			$filterSearch = $_REQUEST['filterSearch'];

		$filterID = '';
		if (isset($_REQUEST['filterID']))
		{
			if (preg_match('/^[0-9]+([,][0-9]+)*$/', $_REQUEST['filterID']))
				$filterID = explode(',', $_REQUEST['filterID']);
		}

		$aColumnHeaders = array(STR_CMMN_ID, STR_CMMN_NAME, 'Organization', 'Phone', 'Email');
		$aColumns = array('contact_id', 'last_name', 'first_name');

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

		$this->oView->table = 'dcl_contact';
		$this->oView->AddDef('columnhdrs', '', $aColumnHeaders);
		$this->oView->AddDef('columns', '', $aColumns);
		$this->oView->AddDef('order', '', array('last_name', 'first_name', 'contact_id'));

		if ($filterActive == 'Y' || $filterActive == 'N')
			$this->oView->AddDef('filter', 'active', "'$filterActive'");

		if ($filterSearch != '')
		{
			if (strpos($filterSearch, ',') === false)
			{
				$this->oView->AddDef('filterlike', 'last_name', $filterSearch);
			}
			else
			{
				$this->oView->logiclike = 'AND';
				$aCriteria = split(',', $filterSearch);
				if (count($aCriteria) > 2)
				{
					$this->oView->AddDef('filterlike', 'last_name', trim($aCriteria[0]));
					array_shift($aCriteria);
					$this->oView->AddDef('filterlike', 'first_name', trim(join(',', $aCriteria)));
				}
				else
				{
					$this->oView->AddDef('filterlike', 'last_name', trim($aCriteria[0]));
					$this->oView->AddDef('filterlike', 'first_name', trim($aCriteria[1]));
				}
			}
		}

		if ($filterStartsWith != '')
			$this->oView->AddDef('filterstart', 'last_name', $filterStartsWith);

		if (is_array($filterID))
			$this->oView->AddDef('filter', 'contact_id', $filterID);

		if (isset($_REQUEST['updateTop']) && $_REQUEST['updateTop'] == 'true' && is_array($filterID))
		{
			$this->oSmarty->assign('updateTop', 'true');
			$this->oSmarty->assign('filterID', join(',', $filterID));
		}

		if ($this->oDB->Query($this->oView->GetSQL(true)) == -1 || !$this->oDB->next_record())
			exit();

		$iRecords = (int)$this->oDB->f(0);
		$this->oSmarty->assign('VAL_COUNT', $iRecords);
		$this->oSmarty->assign('VAL_PAGE', $iPage);
		$this->oSmarty->assign('VAL_MAXPAGE', ceil($iRecords / $this->oView->numrows));
		$this->oDB->FreeResult();

		if ($this->oDB->LimitQuery($this->oView->GetSQL(), $this->oView->startrow, $this->oView->numrows) != -1)
		{
			$oMetadata = new DisplayHelper();
			$aContacts = array();
			while ($this->oDB->next_record())
			{
				$aContact = $oMetadata->GetContact($this->oDB->f('contact_id'));
				$aRow = array('contact_id' => $this->oDB->f('contact_id'), 'last_name' => $this->oDB->f('last_name'),
								'first_name' => $this->oDB->f('first_name'), 'email_addr' => $aContact['email'], 'phone_number' => $aContact['phone'],
								'org_name' => $aContact['org_name'], 'org_id' => $aContact['org_id']);
				
				$aContacts[] = $aRow;
			}

			$this->oDB->FreeResult();

			$this->oSmarty->assign_by_ref('VAL_CONTACTS', $aContacts);
			$this->oSmarty->assign('VAL_HEADERS', $aColumnHeaders);
			$this->oSmarty->assign('VAL_MULTISELECT', (isset($_REQUEST['multiple']) && $_REQUEST['multiple'] == 'true'));

 			$this->oSmarty->Render('htmlContactSelectorBrowse.tpl');
		}

		exit();
	}
}
