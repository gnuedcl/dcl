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
LoadStringResource('tck');
LoadStringResource('menu'); // If we're framed, this isn't loaded every time
class htmlAgg
{
	var $group;
	var $sub;
	var $_aTypeInfo;

	function htmlAgg()
	{
		$this->group = IsSet($_REQUEST['group']) ? $_REQUEST['group'] : '';
		$this->sub = IsSet($_REQUEST['sub']) ? $_REQUEST['sub'] : '';

		$this->_aTypeInfo = array(
				'workorders' => array( // major group
					'__title__' => DCL_MENU_WORKORDERS,
					'account' => array( // what to aggregate
						'dcl_wo_account', // table to join
						'Organizations', // title
						'a.account_id', // major group field
						'ac.name', // minor group description
						'ac.org_id', // minor group key
						'' // Additional filter
						),
					'assigned' => array('personnel', STR_WO_RESPONSIBLE, 'w.responsible', 'a.short', 'a.id', ''),
					'reportto' => array('personnel', 'My Minions', 'w.responsible', 'a.short', 'a.id', 'a.reportto = ' . $GLOBALS['DCLID']),
					'product' => array('products', STR_WO_PRODUCT, 'w.product', 'a.name', 'a.id', ''),
					'priority' => array('priorities', STR_WO_PRIORITY, 'w.priority', 'a.name', 'a.id', ''),
					'severity' => array('severities', STR_WO_SEVERITY, 'w.severity', 'a.name', 'a.id', ''),
					'wo_type_id' => array('dcl_wo_type', STR_WO_TYPE, 'w.wo_type_id', 'a.type_name', 'a.wo_type_id', '')
				),
				'tickets' => array( // major group
					'__title__' => DCL_MENU_TICKETS,
					'account' => array( // what to aggregate
						'dcl_org', // table to join
						'Organizations', // title
						'w.account', // major group field
						'a.name', // minor group description
						'a.org_id', // minor group key
						'' // Additional filter
						),
					'assigned' => array('personnel', STR_TCK_RESPONSIBLE, 'w.responsible', 'a.short', 'a.id', ''),
					'reportto' => array('personnel', 'My Minions', 'w.responsible', 'a.short', 'a.id', 'a.reportto = ' . $GLOBALS['DCLID']),
					'product' => array('products', STR_TCK_PRODUCT, 'w.product', 'a.name', 'a.id', ''),
					'priority' => array('priorities', STR_TCK_PRIORITY, 'w.priority', 'a.name', 'a.id', ''),
					'severity' => array('severities', STR_TCK_TYPE, 'w.severity', 'a.name', 'a.id', '')
				)
			);
	}

	function GetBox()
	{
	}

	function Init()
	{
		commonHeader();
		$this->ShowAggNav();
	}

	function ShowAggNav()
	{
		global $dcl_info;

		$t = new DCL_Smarty();
		$t->assign('TXT_AGGREGATE', 'Aggregate');
		$t->assign('TXT_BY', STR_CMMN_BY);
		$t->assign('TXT_FORDATES', 'For Dates');

		$aGroups = array();
		foreach ($this->_aTypeInfo as $key => $val)
		{
			array_push($aGroups, array('key' => $key, 'desc' => $val['__title__']));
		}
		
		$t->assign('groups', $aGroups);
		$t->assign('group', isset($_REQUEST['group']) ? $_REQUEST['group'] : '');

		$aSubGroups = array();
		foreach ($this->_aTypeInfo['workorders'] as $key => $val)
		{
			if ($key == '__title__')
				continue;

			array_push($aSubGroups, array('key' => $key, 'desc' => $val[1]));
		}

		$t->assign('subgroups', $aSubGroups);
		$t->assign('subgroup', isset($_REQUEST['sub']) ? $_REQUEST['sub'] : '');

		if (isset($_REQUEST['chkLimitByDate']) && $_REQUEST['chkLimitByDate'] == 1)
		{
			$t->assign('VAL_DATEFROM', DCL_Sanitize::ToDate($_REQUEST['dateFrom']));
			$t->assign('VAL_DATETO', DCL_Sanitize::ToDate($_REQUEST['dateTo']));
			$t->assign('VAL_CHKLIMIT', ' checked');
		}
		else
		{
			$aFewDaysAgo = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
			$t->assign('VAL_DATEFROM', date($dcl_info['DCL_DATE_FORMAT'], $aFewDaysAgo));
			$t->assign('VAL_DATETO', date($dcl_info['DCL_DATE_FORMAT']));
			$t->assign('VAL_CHKLIMIT', '');
		}

		$t->Render('htmlAggNav.tpl');
	}

	function ShowReport()
	{
		$this->Init();

		$oDB = new WorkOrderModel();
		$oDB->Query($this->_GetSQL());
		$aDetail = array();
		$i = -1;
		$last_id = -1;
		while ($oDB->next_record())
		{
			$id = $oDB->f(1);
			if ($id < 1)
				continue;

			if ($id != $last_id)
			{
				$aDetail[++$i] = array('0', '0', $oDB->f(2));
				$last_id = $id;
			}

			$link = 'menuAction=htmlAgg.Search&group=' . $this->group . '&sub=' . $this->sub . '&col=' . (string)($oDB->f(3) - 1) . '&item=' . $oDB->f(1);
			if (isset($_REQUEST['chkLimitByDate']) && $_REQUEST['chkLimitByDate'] == 1)
			{
				$link .= '&dateFrom=' . DCL_Sanitize::ToDate($_REQUEST['dateFrom']);
				$link .= '&dateTo=' . DCL_Sanitize::ToDate($_REQUEST['dateTo']);
				$link .= '&chkLimitByDate=1';
			}

			$aDetail[$i][$oDB->f(3) - 1] = '<a href="' . menuLink('', $link) . '">' .  $oDB->f(0) . '</a>';
		}

		if (count($aDetail) < 1)
		{
			trigger_error('No Matches for Your Filter', E_USER_NOTICE);
		}
		else
		{
			$oTable = new TableHtmlHelper();
			$oTable->addColumn('Open', 'html');
			$oTable->addColumn('Completed', 'html');
			$oTable->addColumn($this->_aTypeInfo[$this->group][$this->sub][1], 'string');
			$oTable->setData($aDetail);
			$oTable->render();
		}
	}

	function Search()
	{
		$this->Init();
		$aItems = $this->_aTypeInfo[$this->group][$this->sub];

		$objView = new boView();
		$objView->style = 'report';
		$objView->title = 'Aggregate Search Results';
		$objView->table = $this->group;

		if ($this->group == 'workorders')
			$objView->AddDef('order', '', array('priorities.weight', 'severities.weight', 'jcn', 'seq'));
		else if ($this->group == 'tickets')
			$objView->AddDef('order', '', array('priorities.weight', 'severities.weight', 'ticketid'));

		$objView->AddDef('filter', 'statuses.dcl_status_type', $_REQUEST['col'] + 1);

		if ($this->group == 'workorders' && $this->sub == 'account')
			$objView->AddDef('filter', 'dcl_wo_account.account_id', $_REQUEST['item']);
		else
			$objView->AddDef('filter', substr($aItems[2], 2), $_REQUEST['item']);

		if (isset($_REQUEST['dateFrom']) && isset($_REQUEST['dateTo']))
		{
			$field = $_REQUEST['col'] == 0 ? 'createdon' : 'closedon';
			$objView->AddDef('filterdate', $field, array(DCL_Sanitize::ToDate($_REQUEST['dateFrom']), DCL_Sanitize::ToDate($_REQUEST['dateTo'])));
		}

		if ($this->sub != 'product')
		{
			$objView->AddDef('groups', '', array('products.name'));

			if ($this->group == 'workorders')
			{
				$objView->AddDef('columns', '',
					array('jcn', 'seq', 'dcl_wo_type.type_name', 'responsible.short', 'statuses.name', 'eststarton', 'deadlineon',
						'etchours', 'totalhours', 'summary'));

				$objView->AddDef('columnhdrs', '',
					array('', STR_WO_JCN, STR_WO_SEQ, STR_WO_TYPE, STR_WO_RESPONSIBLE,
						STR_WO_STATUS, STR_WO_ESTSTART, STR_WO_DEADLINE, STR_WO_ETCHOURS, STR_WO_ACTHOURS, STR_WO_SUMMARY));
			}
			else
			{
				$objView->AddDef('columns', '',
					array('ticketid', 'responsible.short', 'statuses.name', 'summary'));

				$objView->AddDef('columnhdrs', '',
					array('', STR_TCK_TICKET, STR_TCK_RESPONSIBLE, STR_WO_STATUS, STR_WO_SUMMARY));
			}
		}
		else
		{
			$objView->AddDef('groups', '', array('responsible.short'));

			if ($this->group == 'workorders')
			{
				$objView->AddDef('columns', '',
					array('jcn', 'seq', 'dcl_wo_type.type_name', 'products.name', 'statuses.name', 'eststarton', 'deadlineon',
						'etchours', 'totalhours', 'summary'));

				$objView->AddDef('columnhdrs', '',
					array('', STR_WO_JCN, STR_WO_SEQ, STR_WO_TYPE, STR_WO_PRODUCT,
						STR_WO_STATUS, STR_WO_ESTSTART, STR_WO_DEADLINE, STR_WO_ETCHOURS, STR_WO_ACTHOURS, STR_WO_SUMMARY));
			}
			else
			{
				$objView->AddDef('columns', '',
					array('ticketid', 'responsible.short', 'statuses.name', 'products.name', 'summary'));

				$objView->AddDef('columnhdrs', '',
					array(STR_TCK_TICKETID, STR_TCK_RESPONSIBLE, STR_TCK_STATUS, STR_TCK_PRODUCT, STR_TCK_SUMMARY));
			}
		}

		$obj = CreateViewObject($this->group);
		$obj->Render($objView);
	}

	function _GetSQL()
	{
		$aItems = $this->_aTypeInfo[$this->group][$this->sub];
		$orderby = (IsSet($_REQUEST['order']) && $_REQUEST['order'] == 'count') ? 'count(*)' : $aItems[3];

		$sql = sprintf('select count(*), %s, %s, t.dcl_status_type_id, t.dcl_status_type_name from ', $aItems[2], $aItems[3]);
		$sql .= sprintf('%s a, %s w, dcl_status_type t, statuses s', $aItems[0], $this->group);

		if ($this->group == 'workorders' && $this->sub == 'account')
			$sql .= ', dcl_org ac';

		$sql .= sprintf(' where %s = %s ', $aItems[4], $aItems[2]);
		if ($this->group == 'workorders' && $this->sub == 'account')
			$sql .= ' AND w.jcn = a.wo_id AND w.seq = a.seq ';

		$sql .= 'and w.status = s.id and s.dcl_status_type = t.dcl_status_type_id and ';
		if (isset($_REQUEST['chkLimitByDate']) && $_REQUEST['chkLimitByDate'] == 1)
		{
			$dateFrom = DCL_Sanitize::ToDate($_REQUEST['dateFrom']);
			$dateTo = DCL_Sanitize::ToDate($_REQUEST['dateTo']);

			$oDB = new dclDB; // for sql side date formatting

			$sql .= '((t.dcl_status_type_id = 1 and w.createdon between ' . $oDB->DisplayToSQL($dateFrom) . ' and ' . $oDB->DisplayToSQL($dateTo) . ') or ';
			$sql .= '(t.dcl_status_type_id = 2 and w.closedon between ' . $oDB->DisplayToSQL($dateFrom) . ' and ' . $oDB->DisplayToSQL($dateTo) . ')) ';
		}
		else
			$sql .= 't.dcl_status_type_id != 3 ';

		if ($aItems[5] != '')
			$sql .= ' and (' . $aItems[5] . ') ';

		$sql .= sprintf('group by %s, %s, t.dcl_status_type_id, t.dcl_status_type_name order by %s', $aItems[2], $aItems[3], $orderby);

		return $sql;
	}
}
