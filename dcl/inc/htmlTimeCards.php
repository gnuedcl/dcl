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

LoadStringResource('tc');
LoadStringResource('wo');

class htmlTimeCards
{
	function ShowBatchWO(array $aSelected, SmartyHelper $smartyHelper)
	{
		$objWO = new WorkOrderModel();
		$query = 'select a.jcn, a.seq, b.short, c.name, e.name, a.summary from workorders a ' . $objWO->JoinKeyword . ' personnel b on a.responsible = b.id ';
		$query .= $objWO->JoinKeyword . ' statuses c on a.status = c.id left join projectmap d on a.jcn = d.jcn and (a.seq = d.seq or d.seq = 0) ';
		$query .= 'left join dcl_projects e on d.projectid = e.projectid ';
		$query .= 'where (';

		$bFirst = true;
		foreach ($aSelected as $jcnseq)
		{
			list($jcn, $seq) = explode('.', $jcnseq);
			if (Filter::ToInt($jcn) === null || Filter::ToInt($seq) === null)
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
			$oTable = new TableHtmlHelper($smartyHelper);
			$oTable->setCaption('Selected Work Orders');
			$oTable->addColumn(STR_WO_JCN, 'numeric');
			$oTable->addColumn(STR_WO_SEQ, 'numeric');
			$oTable->addColumn(STR_WO_RESPONSIBLE, 'string');
			$oTable->addColumn(STR_WO_STATUS, 'string');
			$oTable->addColumn(STR_WO_PROJECT, 'string');
			$oTable->addColumn(STR_WO_SUMMARY, 'string');
			$oTable->setShowRownum(true);
			$oTable->setData($objWO->FetchAllRows());
			$oTable->embed();
		}
	}
}
