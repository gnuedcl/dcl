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

class htmlMetrics
{
	var $oDB;

	function htmlMetrics()
	{
		$this->oDB = new dclDB;
	}

	function show()
	{
		global $dcl_info;

		commonHeader();

		$this->oDB->query("select a.id, a.short, count(*) from personnel a join dcl_sccs_xref b on a.id = b.personnel_id group by a.id, a.short order by 3 desc");
		$aRecords = $this->oDB->FetchAllRows();
		
		$oTable = new htmlTable();
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
		$oTable->render();

		$this->oDB->FreeResult();

		$aTables = array(
			'Personnel' => 'personnel',
			'Organizations' => 'dcl_org',
			'Contacts' => 'dcl_contact',
			'Work Orders' => 'workorders',
			'Time Cards' => 'timecards',
			'Tickets' => 'tickets',
			'Ticket Resolutions' => 'ticketresolutions',
			'Projects' => 'dcl_projects',
			'Products' => 'products',
			'ChangeLog' => 'dcl_sccs_xref'
		);

		$oTable = new htmlTable();
		$oTable->setCaption('Table Record Counts');
		$oTable->addColumn('Table', 'string');
		$oTable->addColumn('Records', 'numeric');
		$oTable->setShowRownum(true);

		foreach ($aTables as $sName => $sTable)
		{
			$this->oDB->query("select '$sName', count(*) from $sTable");
			if ($this->oDB->next_record())
				$oTable->addRow(array($this->oDB->f(0), $this->oDB->f(1)));
				
			$this->oDB->FreeResult();
		}
		
		$oTable->render();
	}
}
