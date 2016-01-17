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

class htmlMetrics
{
	function show()
	{
		commonHeader();

		$db = new DbProvider();
		$db->query("select a.id, a.short, count(*) from personnel a join dcl_sccs_xref b on a.id = b.personnel_id group by a.id, a.short order by 3 desc");
		$aRecords = $db->FetchAllRows();
		
		$oTable = new TableHtmlHelper();
		$oTable->setCaption('ChangeLog Entries');
		$oTable->addColumn(STR_CMMN_ID, 'numeric');
		$oTable->addColumn('User', 'html');
		$oTable->addColumn('Commits', 'numeric');

		for ($i = 0; $i < count($aRecords); $i++)
		{
			$aRecords[$i][1] = '<a href="' . menuLink('', 'menuAction=htmlChangeLog.ShowRepositoryCommits&personnel_id=' . $aRecords[$i][0]) . '">' . $aRecords[$i][1] . '</a>';
		}

		$oTable->setData($aRecords);
		$oTable->setShowRownum(true);
		$oTable->sTemplate = 'TableView.tpl';
		$oTable->render();
	}
}
