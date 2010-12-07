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

class htmlWindowList
{
	var $vDefault;
	var $t;
	var $oDB;

	function htmlWindowList()
	{
		$this->oDB = null;
	}

	function GetHTML($part = 'all')
	{
		global $dcl_info;

		if (($wo_id = DCL_Sanitize::ToInt($_REQUEST['wo_id'])) === null ||
			($seq = DCL_Sanitize::ToInt($_REQUEST['seq'])) === null
			)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		if ($part == 'top')
		{
			$this->t->set_var('TXT_TITLE', 'Work Order ' . $wo_id . '-' . $seq . ' Organizations');
			$this->t->set_var('TXT_OK', STR_CMMN_OK);
			$this->t->set_var('TXT_PRINT', STR_CMMN_PRINT);
		}
		else if ($part == 'main')
		{
			$this->t->set_block('hForm', 'sel', 'hSel');
			$this->t->set_var('hSel', '');
			switch ($_REQUEST['what'])
			{
				case 'dcl_wo_account.wo_id':
					$this->ListFromDb('dcl_wo_account', 'name', 'wo_id = ' . $wo_id . ' And seq = ' . $seq, 'name');
					break;
				default:
					throw new PermissionDeniedException();
			}
		}
		else
		{
			$this->t = CreateTemplate(array('hForm' => 'htmlWindowList.tpl'));

			$this->t->set_var('TXT_TITLE', 'Work Order ' . $wo_id . '-' . $seq . ' Organizations');
			$this->t->set_var('TXT_OK', STR_CMMN_OK);

			switch ($_REQUEST['what'])
			{
				case 'dcl_wo_account.wo_id':
					$this->ListFromDb('dcl_wo_account', 'name', 'wo_id = ' . $wo_id . ' And seq = ' . $seq, 'name');
					break;
				default:
					throw new PermissionDeniedException();
			}

			return $this->t->parse('out', 'hForm');
		}
	}

	function Render()
	{
		echo $this->GetHTML();
	}

	function ListFromDb($table, $valField, $filter = '', $order = '')
	{
		if ($this->oDB == NULL)
		{
			$this->oDB = new dclDB;
		}

		$sql = "select $valField From $table";
		if ($table == 'dcl_wo_account')
			$sql .= ', dcl_org';

		if ($filter != '')
		{
			$sql .= ' Where ' . $filter . ' And org_id = account_id';
		}
		else
			$sql .= ' Where org_id = account_id';

		if ($order == '')
			$sql .= " Order By $valField";
		else
			$sql .= ' Order By ' . $order;

		$oDB = new dbWorkorders();
		$oDB->Query($sql);
		while ($oDB->next_record())
		{
			$this->t->set_var('VAL_TEXT', $oDB->f(0));
			$this->t->parse('hSel', 'sel', true);
		}
	}

	function FrameRender()
	{
		if (($wo_id = DCL_Sanitize::ToInt($_REQUEST['wo_id'])) === null ||
			($seq = DCL_Sanitize::ToInt($_REQUEST['seq'])) === null
			)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$this->t = CreateTemplate(array('hForm' => 'htmlWindowListFrame.tpl'));
		$this->t->set_var('LNK_TOP', menuLink('', 'menuAction=htmlWindowList.Top&what=' . $_REQUEST['what'] . '&wo_id=' . $wo_id . '&seq=' . $seq));
		$this->t->set_var('LNK_MAIN', menuLink('', 'menuAction=htmlWindowList.Main&what=' . $_REQUEST['what'] . '&wo_id=' . $wo_id . '&seq=' . $seq));
		$this->t->pparse('out', 'hForm');
		exit;
	}

	function Top()
	{
		$this->t = CreateTemplate(array('hForm' => 'htmlWindowListTop.tpl'));
		$this->GetHTML('top');
		$this->t->pparse('out', 'hForm');
		exit;
	}

	function Main()
	{
		$this->t = CreateTemplate(array('hForm' => 'htmlWindowListMain.tpl'));
		$this->GetHTML('main');
		$this->t->pparse('out', 'hForm');
		exit;
	}
}
