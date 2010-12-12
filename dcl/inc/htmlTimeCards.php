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

LoadStringResource('tc');
LoadStringResource('wo');

class htmlTimeCards
{
	function ShowBatchWO()
	{
		global $dcl_info;

		$objWO = new dbWorkorders();
		$query = 'select a.jcn, a.seq, b.short, c.name, e.name, a.summary from workorders a ' . $objWO->JoinKeyword . ' personnel b on a.responsible = b.id ';
		$query .= $objWO->JoinKeyword . ' statuses c on a.status = c.id left join projectmap d on a.jcn = d.jcn and (a.seq = d.seq or d.seq = 0) ';
		$query .= 'left join dcl_projects e on d.projectid = e.projectid ';
		$query .= 'where (';

		$bFirst = true;
		foreach ($_REQUEST['selected'] as $jcnseq)
		{
			list($jcn, $seq) = explode('.', $jcnseq);
			if (DCL_Sanitize::ToInt($jcn) === null || DCL_Sanitize::ToInt($seq) === null)
				continue;
				
			if ($bFirst)
				$bFirst = false;
			else
				$query .= ' or ';

			$query .= "(a.jcn=$jcn and a.seq=$seq)";
		}

		if ($bFirst)
			return;
			
		$query .= ')';
		if ($objWO->Query($query) != -1)
		{
			$oTable = new TableHtmlHelper();
			$oTable->sCaption = 'Selected Work Orders';
			$oTable->addColumn(STR_WO_JCN, 'numeric');
			$oTable->addColumn(STR_WO_SEQ, 'numeric');
			$oTable->addColumn(STR_WO_RESPONSIBLE, 'string');
			$oTable->addColumn(STR_WO_STATUS, 'string');
			$oTable->addColumn(STR_WO_PROJECT, 'string');
			$oTable->addColumn(STR_WO_SUMMARY, 'string');
			$oTable->setShowRownum(true);
			$oTable->setData($objWO->FetchAllRows());
			$oTable->render();
		}
	}

	function GetTimeCards($jcn, $seq, $editID = 0, $forDelete = false)
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_TIMECARD, DCL_PERM_VIEW, (int)$jcn, (int)$seq))
			return '';

		$retVal = '';

		$objTimeCard = new dbTimeCards();
		if ($objTimeCard->GetTimeCards($jcn, $seq) != -1)
		{
			$objPersonnel = new PersonnelModel();
			$objStatus = new StatusModel();
			$objAction = new ActionModel();

			$oMeta =& new DCL_MetadataDisplay();

			$oSmarty = new DCL_Smarty();
			$oSmarty->assign('PERM_MODIFY', $g_oSec->HasPerm(DCL_ENTITY_TIMECARD, DCL_PERM_MODIFY));
			$oSmarty->assign('PERM_DELETE', $g_oSec->HasPerm(DCL_ENTITY_TIMECARD, DCL_PERM_DELETE));
			$oSmarty->assign('IS_DELETE', $forDelete);

			while ($objTimeCard->next_record())
			{
				$objTimeCard->GetRow();
				if (!$forDelete && $editID == $objTimeCard->id)
				{
					$retVal .= '<tr><th align="left" colspan="2">';
					$oTCF = new htmlTimeCardForm();
					$retVal .= $oTCF->GetForm($objTimeCard->jcn, $objTimeCard->seq, $objTimeCard);
					$retVal .= '</th></tr>';
				}
				else
				{
					$oSmarty->assign('VAL_ACTIONBY', $oMeta->GetPersonnel($objTimeCard->actionby));
					$oSmarty->assign('VAL_ACTIONON', $objTimeCard->actionon);
					$oSmarty->assign('VAL_SUMMARY', $objTimeCard->summary);
					$oSmarty->assign('VAL_STATUS', $oMeta->GetStatus($objTimeCard->status));
					$oSmarty->assign('VAL_REVISION', $objTimeCard->revision);
					$oSmarty->assign('VAL_ACTION', $oMeta->GetAction($objTimeCard->action));
					$oSmarty->assign('VAL_HOURS', $objTimeCard->hours);
					$oSmarty->assign('VAL_DESCRIPTION', $objTimeCard->description);
					$oSmarty->assign('VAL_INPUTON', $objTimeCard->inputon);
					$oSmarty->assign('VAL_PUBLIC', $objTimeCard->is_public == 'Y' ? STR_CMMN_YES : STR_CMMN_NO);
					$oSmarty->assign('VAL_TIMECARDID', $objTimeCard->id);

					if ($objTimeCard->reassign_from_id > 0)
						$oSmarty->assign('VAL_REASSIGNFROM', $oMeta->GetPersonnel($objTimeCard->reassign_from_id));
					else
						$oSmarty->assign('VAL_REASSIGNFROM', '');

					if ($objTimeCard->reassign_to_id > 0)
						$oSmarty->assign('VAL_REASSIGNTO', $oMeta->GetPersonnel($objTimeCard->reassign_to_id));
					else
						$oSmarty->assign('VAL_REASSIGNTO', '');

					$retVal .= $oSmarty->ToString('htmlTimeCardDetail.tpl');
				}
			}
		}

		return $retVal;
	}
}
