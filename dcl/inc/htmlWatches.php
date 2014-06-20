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

LoadStringResource('wtch');

class htmlWatches
{
	var $objW;
	var $objT;
	var $oMeta;

	function htmlWatches()
	{
		$this->oMeta = new DisplayHelper();
		$this->objW = new WorkOrderModel();
		$this->objT = new TicketsModel();
	}

	function GetCombo($default = 1, $id = 'actions')
	{
		$obj = new WatchesModel();

		$retVal = "<select id=\"$id\" name=\"$id\">";
		while (list($key, $val) = each($obj->arrActions))
		{
			$retVal .= "<option value=\"$key\"";
			if ($key == $default)
				$retVal .= ' selected';
			$retVal .= ">$val</option>";
		}

		$retVal .= '</select>';

		return $retVal;
	}

	function GetObjectDescription($type, $key1, $key2 = 0)
	{
		switch($type)
		{
			case 1:
			case 4:
				return $this->oMeta->GetProduct($key1);
			case 2:
				return sprintf('(%d) %s', $key1, $this->oMeta->GetProject($key1));
			case 3:
				if ($key2 > 0)
				{
					$this->objW->Load($key1, $key2);
					return '(' . $key1 . '-' . $key2 . ') ' . $this->objW->summary;
				}

				return '(' . $key1 . ') ' . STR_WTCH_ALLSEQ;
			case 5:
				$this->objT->Load($key1);
				return '(' . $key1 . ') ' . $this->objT->summary;
			case 6:
			case 7:
				$aOrg = $this->oMeta->GetOrganization($key1);
				return $aOrg['name'];
		}

		return STR_WTCH_INVALIDITEM;
	}

	function PrintMine()
	{
		global $dcl_info;
		
		$obj = new WatchesModel();

		$obj->Query(sprintf('SELECT * FROM watches WHERE whoid=%d ORDER BY typeid,watchid', DCLID));
		if (!$obj->next_record())
		{
			ShowInfo(STR_WTCH_YOUHAVENONE);
			return;
		}

		$aData = array();
		do // next_record already called
		{
			$aRecord = array();
			
			$obj->GetRow();
			array_push($aRecord, $obj->arrTypeid[$obj->typeid]);

			list($summary, $link) = each($this->GetMyViewLinkAndDescription($obj));
			
			array_push($aRecord, $summary);
			array_push($aRecord, $obj->arrActions[$obj->actions]);

			$options = sprintf('<a href="%s">%s</a>', $link, STR_CMMN_VIEW);
			$options .= '&nbsp;|&nbsp;';
			$options .= sprintf('<a href="%s">%s</a>', menuLink('', 'menuAction=boWatches.modify&watchid=' . $obj->watchid), STR_CMMN_EDIT);
			$options .= '&nbsp;|&nbsp;';
			$options .= sprintf('<a href="%s">%s</a>', menuLink('', 'menuAction=boWatches.delete&watchid=' . $obj->watchid), STR_CMMN_DELETE);

			array_push($aRecord, $options);
			array_push($aData, $aRecord);
		}
		while ($obj->next_record());

		$oTable = new TableHtmlHelper();
		$oTable->setCaption(STR_WTCH_MYWTCH);
		$oTable->setShowRownum(true);
		$oTable->setData($aData);
		$oTable->addColumn(STR_WTCH_TYPE, 'string');
		$oTable->addColumn(STR_WTCH_SUMMARY, 'string');
		$oTable->addColumn(STR_WTCH_ACTIONS, 'string');
		$oTable->addColumn(STR_CMMN_OPTIONS, 'html');
		$oTable->addGroup(0);
		$oTable->render();
	}

	function ShowEntryForm($obj = '', $desc = '')
	{
		global $dcl_info, $g_oSec;

		$isEdit = is_object($obj);
		
		$t = new SmartyHelper();
		$t->assign('IS_EDIT', $isEdit);

		if ($isEdit)
		{
			$t->assign('TXT_TITLE', sprintf(STR_WTCH_EDIT, $obj->arrTypeid[$obj->typeid]));
			$t->assign('VAL_DESC', $this->GetObjectDescription($obj->typeid, $obj->whatid1, $obj->whatid2));
			$t->assign('VAL_WATCHID', $obj->watchid);
			$t->assign('VAL_TYPEID', $obj->typeid);
			$t->assign('VAL_WHATID1', $obj->whatid1);
			$t->assign('VAL_WHATID2', $obj->whatid2);
			$t->assign('VAL_WHOID', $obj->whoid);
			$t->assign('CMB_ACTIONS', $this->GetCombo($obj->actions));
		}
		else
		{
			if (($typeid = Filter::ToInt($_REQUEST['typeid'])) === null ||
				($whatid1 = Filter::ToInt($_REQUEST['whatid1'])) === null
				)
			{
				throw new InvalidDataException();
			}
			
			if (!isset($_REQUEST['whatid2']) || ($whatid2 = Filter::ToInt($_REQUEST['whatid2'])) === null)
				$whatid2 = 0;
			
			$objW = new WatchesModel();
			$t->assign('TXT_TITLE', sprintf(STR_WTCH_ADD, $objW->arrTypeid[$typeid]));
			$t->assign('VAL_DESC', $this->GetObjectDescription($typeid, $whatid1, $whatid2));
			$t->assign('VAL_TYPEID', $typeid);
			$t->assign('VAL_WHATID1', $whatid1);
			$t->assign('VAL_WHOID', DCLID);
			$t->assign('CMB_ACTIONS', $this->GetCombo());
			$t->assign('VAL_WHATID2', $whatid2);
		}

		$t->Render('WatchForm.tpl');
	}

	function GetMyViewLinkAndDescription($obj)
	{
		$summary = $this->GetObjectDescription($obj->typeid, $obj->whatid1, $obj->whatid2);
		$link = '';
		switch ($obj->typeid)
		{
			case 1:
			case 4:
				if ($obj->typeid == 1)
					$which = 'workorders';
				else
					$which = 'tickets';

				$link = menuLink('','menuAction=Product.Detail&id=' . $obj->whatid1. '&which=' .$which);

				break;
			case 2:
				$link = menuLink('', 'menuAction=Project.Detail&id=' . $obj->whatid1);
				break;
			case 3:
				if ($obj->whatid2 > 0)
					$link = menuLink('', 'menuAction=WorkOrder.Detail&jcn=' . $obj->whatid1 . '&seq=' . $obj->whatid2);
				else
					$link = menuLink('', 'menuAction=htmlSearchBox.submitSearch&which=workorders&search_text=' . $obj->whatid1);

				break;
			case 5:
				$link = menuLink('', 'menuAction=boTickets.view&ticketid=' . $obj->whatid1);
				break;
			case 6:
			case 7:
				$link = menuLink('', 'menuAction=Organization.Detail&org_id=' . $obj->whatid1);
				break;
			default:
				$summary = STR_WTCH_INVALIDITEM;
				break;
		}

		return array($summary => $link);
	}
}
