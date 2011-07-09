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
			throw new PermissionDeniedException();

		$obj = new htmlViews();
		$obj->ShowEntryForm();
		print('<p>');
		$obj->PrintAll();
	}

	function dbadd()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_SAVEDSEARCH, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$objDB = new SavedSearchesModel();
		$objDB->InitFromGlobals();

		$objView = new boView();
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
		if (($iID = @Filter::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$obj = new SavedSearchesModel();
		if ($obj->Load($iID) == -1)
			return;
			
		if (!$g_oSec->HasPerm(DCL_ENTITY_SAVEDSEARCH, DCL_PERM_DELETE, $iID))
		{
			// Users can delete their own saved searches
			if ($obj->whoid != $GLOBALS['DCLID'])
				throw new PermissionDeniedException();
		}

		if ($obj->whoid == $GLOBALS['DCLID'] || $g_oSec->HasPerm(DCL_ENTITY_GLOBAL, DCL_PERM_ADMIN))
			ShowDeleteYesNo(STR_CMMN_VIEW, 'boViews.dbdelete', $obj->viewid, $obj->name, false);
		else
			throw new PermissionDeniedException();
	}

	function dbdelete()
	{
		global $g_oSec;
		
		commonHeader();
		if (($iID = @Filter::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$obj = new SavedSearchesModel();
		if ($obj->Load($iID) == -1)
			return;

		if (!$g_oSec->HasPerm(DCL_ENTITY_SAVEDSEARCH, DCL_PERM_DELETE, $iID))
		{
			// Users can delete their own saved searches
			if ($obj->whoid != $GLOBALS['DCLID'])
				throw new PermissionDeniedException();
		}
			
		if ($obj->whoid == $GLOBALS['DCLID'] || $g_oSec->HasPerm(DCL_ENTITY_GLOBAL, DCL_PERM_ADMIN))
		{
			$obj->Delete();
			print(STR_BO_DELETED);
		}
		else
			throw new PermissionDeniedException();

		$objHTML = new htmlViews();
		$objHTML->PrintAll();
	}

	function exec()
	{
		commonHeader();
		if (($iID = @Filter::ToInt($_REQUEST['viewid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$objDB = new SavedSearchesModel();
		if ($objDB->Load($iID) == -1)
			return;

		if ($objDB->ispublic == 'N' && $objDB->whoid != $GLOBALS['DCLID'])
			throw new PermissionDeniedException();

		$objView = new boView();
		$objView->SetFromURLString($objDB->viewurl);

		if (IsSet($_REQUEST['btnNav']) && IsSet($_REQUEST['startrow']) && IsSet($_REQUEST['numrows']))
		{
			$startrow = @Filter::ToInt($_REQUEST['startrow']);
			$numrows = @Filter::ToInt($_REQUEST['numrows']);
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
		$objView = new boView();
		$objView->SetFromURL();

		if ((IsSet($_REQUEST['btnNav']) || IsSet($_REQUEST['jumptopage'])) && IsSet($_REQUEST['startrow']) && IsSet($_REQUEST['numrows']))
		{
			$startrow = @Filter::ToInt($_REQUEST['startrow']);
			$numrows = @Filter::ToInt($_REQUEST['numrows']);
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
				$iPage = @Filter::ToInt($_REQUEST['jumptopage']);
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

		$objView = new boView();
		$objView->SetFromURL();

		// Make object, run query, and (for now) blindly dump data.  The first
		// record will contain column headings.  Any tabs within data will be replaced
		// by spaces since our fields our tab delimited.
		$obj = new DbProvider;
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
		$metadata = new DCL_MetadataDisplay();
		$workOrderOrg = new WorkOrderOrganizationModel();

		// Now for the records
		while ($obj->next_record())
		{
			$record = '';
			for ($i = 0; $i < count($objView->columns); $i++)
			{
				$fieldName = $obj->GetFieldName($i);
				if ($i > 0)
					$record .= phpTab;

				$fieldValue = $obj->f($i);
				if ($objView->table == 'tickets')
				{
					if ($fieldName == 'seconds')
					{
						$fieldValue = $obj->GetHoursText();
					}
					else if ($fieldName == 'tag_desc')
					{
						$fieldValue = '';
						$tags = $metadata->GetTags(DCL_ENTITY_TICKET, $obj->f('ticketid'));
						if (strlen($tags) > 0)
							$fieldValue = str_replace(',', ', ', $tags);
					}
				}
				else if ($objView->table == 'workorders')
				{
					if ($fieldName == 'hotlist_tag')
					{
						$fieldValue = '';
						$hotlists = $metadata->GetHotlistWithPriority(DCL_ENTITY_WORKORDER, $obj->f('jcn'), $obj->f('seq'));
						if (is_array($hotlists) && count($hotlists) > 0)
						{
							foreach ($hotlists as $hotlistTag)
							{
								if ($fieldValue != '')
									$fieldValue .= ', ';
								
								$fieldValue .= $hotlistTag['hotlist'] . ' #' . ($hotlistTag['priority'] == 999999 ? '?' : $hotlistTag['priority']);
							}
						}
					}
					else if ($fieldName == 'tag_desc')
					{
						$fieldValue = '';
						$tags = $metadata->GetTags(DCL_ENTITY_WORKORDER, $obj->f('jcn'), $obj->f('seq'));
						if (strlen($tags) > 0)
							$fieldValue = str_replace(',', ', ', $tags);
					}
					else if ($objView->columns[$i] == 'dcl_org.name')
					{
						$fieldValue = '';
						if ($obj->f('_num_accounts_') > 0)
						{
							if ($workOrderOrg->Load($obj->f('jcn'), $obj->f('seq')) != -1)
							{
								do
								{
									if ($fieldValue != '')
										$fieldValue .= ', ';

									$workOrderOrg->GetRow();
									$fieldValue .= $workOrderOrg->account_name;
								} while ($workOrderOrg->next_record());
							}

						}
					}
				}

				$record .= $this->CleanDataForExport($fieldValue);
			}

			echo $record . phpCrLf;
		}

		exit; // Don't output footer
	}

	function CleanDataForExport($data)
	{
		$data = str_replace(phpTab, ' ', $data);
		$data = str_replace("\r", ' ', $data);
		$data = str_replace("\n", ' ', $data);

		return $data;
	}
}
