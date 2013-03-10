<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2013 Free Software Foundation
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

class ProjectHtmlHelper
{
	function GetCombo($default = 0, $cbName = 'project', $reportTo = 0, $size = 0, $dontshowid = -1, $bHideClosed = false)
	{
		$whereClause = '';

		if ($bHideClosed)
		{
			$whereClause = ', statuses ';
		}

		if ($reportTo > 0)
		{
			$whereClause .= " WHERE reportto=$reportTo";
		}

		if ($dontshowid != -1)
		{
			if ($whereClause == '' || $whereClause == ', statuses ')
				$whereClause .= ' WHERE ';
			else
				$whereClause .= ' AND ';

			$whereClause .= "dcl_projects.projectid != $dontshowid";
		}

		if ($bHideClosed)
		{
			if ($whereClause == '' || $whereClause == ', statuses ')
				$whereClause .= ' WHERE ';
			else
				$whereClause .= ' AND ';

			$whereClause .= 'dcl_projects.status = statuses.id AND statuses.dcl_status_type != 2';
		}

		$oSelect = new SelectHtmlHelper();
		$oSelect->DefaultValue = $default;
		$oSelect->Id = $cbName;
		$oSelect->Size = $size;
		$oSelect->FirstOption = STR_CMMN_SELECTONE;
		$oSelect->SetFromQuery('SELECT dcl_projects.projectid, dcl_projects.name FROM dcl_projects' . $whereClause . ' ORDER BY dcl_projects.name');

		return $oSelect->GetHTML();
	}
}
