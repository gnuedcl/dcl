<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2014 Free Software Foundation
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

LoadStringResource('sec');
class boSecAudit
{
	var $objDB;
	
	function __construct()
	{
	}
	
	function Show($needHdr = true)
	{
	
		global $g_oSec;
		
		if ($needHdr == true)
			commonHeader();
			
		if (!$g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();
			
		$obj = new htmlSecAudit();
		$obj->Show();
	
	}
	
	function ShowResults()
	{
		global $g_oSec;
		commonHeader();
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();
			
		$begindate = @Filter::ToDateTime($_REQUEST['begindate'] . ' 00:00:00.00');
		$enddate = @Filter::ToDateTime($_REQUEST['enddate'] . ' 23:59:59.99');
		$responsible = Filter::ToInt($_REQUEST['responsible']);
		
		if ($begindate === null || $enddate === null)
		{
			commonHeader();

			ShowError(STR_SEC_DATEERR);
			$this->Show(false);
			return;
		}
		
		$objDBPer = new PersonnelModel();
		$objDBSA = new SecurityAuditModel();
		

		
		$objDB = new DbProvider;
		
		$sCols = 'SA.id, '. $objDBSA->ConvertTimestamp('SA.actionon', 'actionon') . ', SA.actiontxt, SA.actionparam';
		$sCols .= ', ' . $objDBPer->SelectAllColumns('Pers.');
		
		
		$sQuery = "SELECT $sCols
			FROM $objDBSA->TableName SA INNER JOIN $objDBPer->TableName Pers on 
			SA.id = pers.id 
			WHERE SA.actionon BETWEEN " . $objDBSA->DisplayToSQL($begindate) . ' and ' . $objDBSA->DisplayToSQL($enddate);
		
		if ($responsible == 0)
			$respname=STR_SEC_ALLUSERS;
		else
		{
			$objDBPer->Load($responsible);
			$respname=$objDBPer->short;
			$sQuery .= ' AND SA.id=' . $responsible;

		}
		$sQuery .= ' ORDER BY SA.actionon';
			
		$reportAr = null;
			
		if ($objDB->Query($sQuery) != -1)
		{
			if ($objDB->next_record())
			{
				$idx = -1;
				do
				{
					$idx++;
					
					$reportAr[$idx][] = $objDB->f('short');
					$reportAr[$idx][] = $objDB->FormatTimeStampForDisplay($objDB->f('actionon'));
					$reportAr[$idx][] = $objDB->f('actiontxt');
					$reportAr[$idx][] = $objDB->f('actionparam');
					
				}	
				while ($objDB->next_record());
			}
			else
			{
				ShowInfo(STR_SEC_RPTNODATA);
				$this->Show(false);
				return;
			}
		
		}
		
		
		$obj = new htmlSecAudit();
		if (!$obj->Render($reportAr, $begindate, $enddate, $respname))
		{
			ShowError(STR_SEC_RPTERROR);
			$this->Show(false);
			return;
		}
	
	}
}
