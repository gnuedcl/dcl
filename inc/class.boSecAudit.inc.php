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
LoadStringResource('sec');
class boSecAudit
{
	var $objDB;
	
	function boSecAudit()
	{
	}
	
	function Show($needHdr = true)
	{
	
		global $g_oSec;
		
		if ($needHdr == true)
			commonHeader();
			
		if (!$g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_MODIFY))
			return PrintPermissionDenied();
			
		$obj =& CreateObject('dcl.htmlSecAudit');
		$obj->Show();
	
	}
	
	function ShowResults()
	{
		global $g_oSec;
		commonHeader();
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_MODIFY))
			return PrintPermissionDenied();
			
		$begindate = @DCL_Sanitize::ToDateTime($_REQUEST['begindate'] . ' 00:00:00.00');
		$enddate = @DCL_Sanitize::ToDateTime($_REQUEST['enddate'] . ' 23:59:59.99');
		$responsible = DCL_Sanitize::ToInt($_REQUEST['responsible']);
		
		if ($begindate === null || $enddate === null)
		{
			commonHeader();

			trigger_error(STR_SEC_DATEERR, E_USER_ERROR);
			$this->Show(false);
			return;
		}
		
		$objDBPer =& CreateObject('dcl.dbPersonnel');
		$objDBSA =& CreateObject('dcl.dbSecAudit');
		

		
		$objDB = new DCLDb;
		
		$sCols = 'SA.id, '. $objDBSA->ConvertTimestamp('SA.actionon', 'SAActionOn') . ', SA.actiontxt, SA.actionparam';
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
					$reportAr[$idx][] = $objDB->f('SAActionOn');
					$reportAr[$idx][] = $objDB->f('actiontxt');
					$reportAr[$idx][] = $objDB->f('actionparam');
					
				}	
				while ($objDB->next_record());
			}
			else
			{
				trigger_error(STR_SEC_RPTNODATA, E_USER_ERROR);
				$this->Show(false);
				return;
			}
		
		}
		
		
		$obj =& CreateObject('dcl.htmlSecAudit');
		if (!$obj->Render($reportAr, $begindate, $enddate, $respname))
		{
			trigger_error(STR_SEC_RPTERROR, E_USER_ERROR);
			$this->Show(false);
			return;
		}
	
	}
}
?>
