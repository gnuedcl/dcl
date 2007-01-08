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

class reportAccountActivity
{
	function reportAccountActivity()
	{
	}

	function getParams()
	{
		global $dcl_info;

		commonHeader();

		$oSmarty =& CreateSmarty();

		if (($account_id = DCL_Sanitize::ToInt($_REQUEST['account_id'])) === null)
		{
			trigger_error('Data sanitize failed.', E_USER_ERROR);
			return;
		}

		$oSmarty->assign('account_id', $account_id);
		$oSmarty->assign('VAL_FORMACTION', menuLink());

		$iThisWeek = mktime(0, 0, 0, date('m'), date('d') - 6, date('Y'));
		$oSmarty->assign('VAL_BEGINDATE', date($dcl_info['DCL_DATE_FORMAT'], $iThisWeek));
		$oSmarty->assign('VAL_ENDDATE', date($dcl_info['DCL_DATE_FORMAT']));

		$oSource =& CreateObject('dcl.htmlEntitySource');
		$oSmarty->assign('CMB_SOURCE', $oSource->GetCombo(0, 'entity_source_id', 8));

		SmartyDisplay($oSmarty, 'htmlAccountActivity.tpl');
	}

	function showActivity()
	{
		$oSmarty =& CreateSmarty();
		$oDB =& CreateObject('dcl.dbWorkorders');

		if (($account_id = DCL_Sanitize::ToInt($_REQUEST['account_id'])) === null)
		{
			trigger_error('Data sanitize failed.', E_USER_ERROR);
			return;
		}

		if (($date_begin = DCL_Sanitize::ToDate($_REQUEST['date_begin'])) === null ||
			($date_end = DCL_Sanitize::ToDate($_REQUEST['date_end'])) === null)
		{
			trigger_error('Data sanitize failed.', E_USER_ERROR);
			return;
		}

		$sql = 'SELECT w.jcn, w.seq, p.name as product, t.type_name, s.name as status, s.dcl_status_type, w.createdon, w.closedon, w.description FROM workorders w ';
		$sql .= 'JOIN statuses s ON w.status = s.id ';
		$sql .= 'JOIN dcl_wo_account a ON w.jcn = a.wo_id AND w.seq = a.seq ';
		$sql .= 'JOIN products p ON w.product = p.id ';
		$sql .= 'JOIN dcl_wo_type t ON w.wo_type_id = t.wo_type_id ';
		$sql .= "WHERE (s.dcl_status_type = 1 OR (s.dcl_status_type = 2 AND w.closedon BETWEEN ";
		$sql .= $oDB->DisplayToSQL($date_begin . ' 00:00:00');
		$sql .= ' AND ';
		$sql .= $oDB->DisplayToSQL($date_end . ' 23:59:59');
		$sql .= ")) AND a.account_id = $account_id ";

		if (isset($_REQUEST['is_public']) && $_REQUEST['is_public'] == 'Y')
			$sql .= " AND w.is_public = 'Y' ";

		if (isset($_REQUEST['entity_source_id']))
		{
			if (($a_entity_source_id = DCL_Sanitize::ToIntArray($_REQUEST['entity_source_id'])) !== null)
			{
				if (count($a_entity_source_id) > 1)
					$sql .= ' AND w.entity_source_id IN (' . join(',', $a_entity_source_id) . ')';
				else if (count($a_entity_source_id) == 1)
					$sql .= ' AND w.entity_source_id = ' . $a_entity_source_id[0];
			}
		}

		$sql .= ' ORDER BY s.name, w.closedon, w.createdon, w.jcn, w.seq';

		$oMeta =& CreateObject('dcl.DCL_MetadataDisplay');
		$oOrg = $oMeta->GetOrganization($account_id);

		$oSmarty->assign('VAL_ACCOUNTNAME', $oOrg['name']);
		$oSmarty->assign('VAL_DATEBEGIN', $date_begin);
		$oSmarty->assign('VAL_DATEEND', $date_end);

		$sLastStatus = '';
		$aWO = array();
		if ($oDB->Query($sql) == -1)
			exit;

		while ($oDB->next_record())
		{
			if ($oDB->f('status') != $sLastStatus)
			{
				$aWO[$oDB->f('status')] = array();
				$sLastStatus = $oDB->f('status');
			}

			$oDB->Record[2] = preg_replace('/[^\x20-\x7f]/', '', $oDB->Record[2]);
			$oDB->Record[3] = preg_replace('/[^\x20-\x7f]/', '', $oDB->Record[3]);
			$oDB->Record[6] = $oDB->FormatDateForDisplay($oDB->Record[6]);
			$oDB->Record[7] = $oDB->FormatDateForDisplay($oDB->Record[7]);
			$oDB->Record[8] = preg_replace('/[^\x20-\x7f]/', '', $oDB->Record[8]);
			$oDB->Record['product'] = $oDB->Record[2];
			$oDB->Record['type_name'] = $oDB->Record[3];
			$oDB->Record['createdon'] = $oDB->Record[6];
			$oDB->Record['closedon'] = $oDB->Record[7];
			$oDB->Record['description'] = $oDB->Record[8];
			$aWO[$oDB->f('status')][] = $oDB->Record;
		}

		$oSmarty->assign('VAL_HEADERS', array('Date', 'WO#', 'Product', 'Type', 'Description'));
		$oSmarty->assign('VAL_WO', $aWO);

		header('Content-Type: application/msword', true);
		SmartyDisplay($oSmarty, 'wpmlAccountActivity.tpl');

		exit;
	}
}
?>
