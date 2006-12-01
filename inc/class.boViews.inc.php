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

LoadStringResource('bo');
class boViews
{
	function add()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_SAVEDSEARCH, DCL_PERM_ADD))
			return PrintPermissionDenied();

		$obj =& CreateObject('dcl.htmlViews');
		$obj->ShowEntryForm();
		print('<p>');
		$obj->PrintAll();
	}

	function dbadd()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_SAVEDSEARCH, DCL_PERM_ADD))
			return PrintPermissionDenied();

		$objDB =& CreateObject('dcl.dbViews');
		$objDB->InitFromGlobals();

		$objView =& CreateObject('dcl.boView');
		$objView->SetFromURL();
		
		$objDB->viewurl = $objView->GetURL();
		$objDB->Add();

		$objH =& CreateViewObject($objDB->tablename);
		$objH->Render($objView);
	}

	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$obj =& CreateObject('dcl.dbViews');
		if ($obj->Load($iID) == -1)
			return;
			
		if (!$g_oSec->HasPerm(DCL_ENTITY_SAVEDSEARCH, DCL_PERM_DELETE, $iID))
		{
			// Users can delete their own saved searches
			if ($obj->whoid != $GLOBALS['DCLID'])
				return PrintPermissionDenied();
		}

		if ($obj->whoid == $GLOBALS['DCLID'] || $g_oSec->HasPerm(DCL_ENTITY_GLOBAL, DCL_PERM_ADMIN))
			ShowDeleteYesNo(STR_CMMN_VIEW, 'boViews.dbdelete', $obj->viewid, $obj->name, false);
		else
			PrintPermissionDenied();
	}

	function dbdelete()
	{
		global $g_oSec;
		
		commonHeader();
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['id'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$obj =& CreateObject('dcl.dbViews');
		if ($obj->Load($iID) == -1)
			return;

		if (!$g_oSec->HasPerm(DCL_ENTITY_SAVEDSEARCH, DCL_PERM_DELETE, $iID))
		{
			// Users can delete their own saved searches
			if ($obj->whoid != $GLOBALS['DCLID'])
				return PrintPermissionDenied();
		}
			
		if ($obj->whoid == $GLOBALS['DCLID'] || $g_oSec->HasPerm(DCL_ENTITY_GLOBAL, DCL_PERM_ADMIN))
		{
			$obj->Delete();
			print(STR_BO_DELETED);
		}
		else
			PrintPermissionDenied();

		$objHTML =& CreateObject('dcl.htmlViews');
		$objHTML->PrintAll();
	}

	function exec()
	{
		commonHeader();
		if (($iID = @DCL_Sanitize::ToInt($_REQUEST['viewid'])) === null)
		{
			trigger_error('Data sanitize failed.');
			return;
		}
		
		$objDB =& CreateObject('dcl.dbViews');
		if ($objDB->Load($iID) == -1)
			return;

		if ($objDB->ispublic == 'N' && $objDB->whoid != $GLOBALS['DCLID'])
			return PrintPermissionDenied();

		$objView =& CreateObject('dcl.boView');
		$objView->SetFromURLString($objDB->viewurl);

		if (IsSet($_REQUEST['btnNav']) && IsSet($_REQUEST['startrow']) && IsSet($_REQUEST['numrows']))
		{
			$startrow = @DCL_Sanitize::ToInt($_REQUEST['startrow']);
			$numrows = @DCL_Sanitize::ToInt($_REQUEST['numrows']);
			if ($startrow === null)
				$startrow = 0;
				
			if ($numrows === null)
				$numrows = 25;
				
			if ($_REQUEST['btnNav'] == '<<')
			{
				$objView->startrow = $startrow - $numrows;
				if ($objView->startrow < 0)
					$objView->startrow = 0;
					
				$objView->numrows = $numrows;
			}
			else
			{
				$objView->startrow = $startrow + $numrows;
				$objView->numrows = $numrows;
			}
		}
		else
		{
			$objView->numrows = 25;
			$objView->startrow = 0;
		}

		$objH =& CreateViewObject($objView->table);
		$objH->Render($objView);
	}

	function page()
	{
		commonHeader();
		$objView = CreateObject('dcl.boView');
		$objView->SetFromURL();

		if ((IsSet($_REQUEST['btnNav']) || IsSet($_REQUEST['jumptopage'])) && IsSet($_REQUEST['startrow']) && IsSet($_REQUEST['numrows']))
		{
			$startrow = @DCL_Sanitize::ToInt($_REQUEST['startrow']);
			$numrows = @DCL_Sanitize::ToInt($_REQUEST['numrows']);
			if ($startrow === null)
				$startrow = 0;
				
			if ($numrows === null)
				$numrows = 25;
				
			if ($_REQUEST['btnNav'] == '<<')
				$objView->startrow = $startrow - $numrows;
			else if ($_REQUEST['btnNav'] == '>>')
				$objView->startrow = $startrow + $numrows;
			else
			{
				$iPage = @DCL_Sanitize::ToInt($_REQUEST['jumptopage']);
				if ($iPage === null || $iPage < 1)
					$iPage = 1;

				$objView->startrow = ($iPage - 1) * $numrows;
			}

			if ($objView->startrow < 0)
				$objView->startrow = 0;

			$objView->numrows = $numrows;
		}
		else
		{
			$objView->numrows = 25;
			$objView->startrow = 0;
		}

		$objH =& CreateViewObject($objView->table);
		$objH->Render($objView);
	}

	function export()
	{
		// Silent function to export tab delimited file and force browser to 
		// force the user to save the file.
		header('Content-Type: application/binary; name=dclexport.txt');
		header('Content-Disposition: attachment; filename=dclexport.txt');

		$objView =& CreateObject('dcl.boView');
		$objView->SetFromURL();

		// Make object, run query, and (for now) blindly dump data.  The first
		// record will contain column headings.  Any tabs within data will be replaced
		// by spaces since our fields our tab delimited.
		$obj = new dclDB;
		$obj->Query($objView->GetSQL());

		$record = '';
		if (count($objView->columnhdrs) > 0)
		{
			foreach ($objView->columnhdrs as $val)
			{
				$val = str_replace(phpTab, ' ', $val);
				if ($record != '')
					$record .= phpTab;

				$record .= $val;
			}
		}

		// Output field headings
		echo $record . phpCrLf;

		// Now for the records
		while ($obj->next_record())
		{
			$record = '';
			for ($i = 0; $i < $obj->NumFields(); $i++)
			{
				if ($i > 0)
					$record .= phpTab;


				if ($objView->table == 'tickets' && $obj->GetFieldName($i) == 'seconds')
				{
					$record .= str_replace(phpTab, ' ', $obj->GetHoursText());
				}
				else
				{
					$sData = str_replace(phpTab, ' ', $obj->f($i));
					$sData = str_replace("\r", ' ', $sData);
					$sData = str_replace("\n", ' ', $sData);
					$record .= $sData;
				}
			}

			echo $record . phpCrLf;
		}

		exit; // Don't output footer
	}
}
?>
