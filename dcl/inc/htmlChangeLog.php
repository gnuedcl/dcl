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

class htmlChangeLog
{
	var $oDB;
	var $oPersonnel;

	function htmlChangeLog()
	{
		if (($id = DCL_Sanitize::ToInt($_REQUEST['personnel_id'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$this->oDB = new dclDB;

		$this->oPersonnel = new PersonnelModel();
		$this->oPersonnel->Load($id);
	}

	function GetLink($sHREF, $sText)
	{
		return sprintf('<a href="%s">%s</a>', menuLink('', $sHREF), $sText);
	}

	function GetNavLinks()
	{
		global $g_oSec;
		
		if (($id = @DCL_Sanitize::ToInt($_REQUEST['personnel_id'])) === null)
		{
			throw new InvalidDataException();
		}

		if (!$g_oSec->HasPerm(DCL_ENTITY_CHANGELOG, DCL_PERM_VIEW))
			throw new PermissionDeniedException();
			
		$sMethod = explode('.', $_REQUEST['menuAction']);
		$sMethod = $sMethod[1];

		$aRetVal = array();
		array_push($aRetVal, array('link' => menuLink('', 'menuAction=htmlMetrics.show'), 'title' => 'Metrics'));
		array_push($aRetVal, array('link' => menuLink('', 'menuAction=htmlChangeLog.ShowRepositoryCommits&personnel_id=' . $id), 'title' => $this->oPersonnel->short));
		if ($sMethod == 'ShowProjectCommits' || $sMethod == 'ShowFileCommits' || $sMethod == 'ShowVersionCommits')
		{
			if (($sccs_id = @DCL_Sanitize::ToInt($_REQUEST['dcl_sccs_id'])) === null)
			{
				throw new InvalidDataException();
			}

			$oRepository = new dbSccsXref();
			$sRepository = $oRepository->ExecuteScalar("select sccs_descr from dcl_sccs where dcl_sccs_id = $sccs_id");

			array_push($aRetVal, array('link' => menuLink('', 'menuAction=htmlChangeLog.ShowProjectCommits&dcl_sccs_id=' . $sccs_id . '&personnel_id=' . $id), 'title' => $sRepository));
			if ($sMethod == 'ShowFileCommits' || $sMethod == 'ShowVersionCommits')
			{
				$sccs_project_path = $_REQUEST['sccs_project_path'];
				array_push($aRetVal, array('link' => menuLink('', 'menuAction=htmlChangeLog.ShowFileCommits&dcl_sccs_id=' . $sccs_id . '&personnel_id=' . $id . '&sccs_project_path=' . rawurlencode($sccs_project_path)), 'title' => $sccs_project_path));
				if ($sMethod == 'ShowVersionCommits')
				{
					$sccs_file_name = DCL_Sanitize::IsValidFileName($_REQUEST['sccs_file_name']) ? $_REQUEST['sccs_file_name'] : '';
					array_push($aRetVal, array('link' => menuLink('', 'menuAction=htmlChangeLog.ShowVersionCommits&dcl_sccs_id=' . $sccs_id . '&personnel_id=' . $id . '&sccs_project_path=' . rawurlencode($sccs_project_path) . '&sccs_file_name=' . rawurlencode($sccs_file_name)), 'title' => $sccs_file_name));
				}
			}
		}

		return $aRetVal;
	}

	function ShowRepositoryCommits()
	{
	  	global $g_oSec;
	  
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CHANGELOG, DCL_PERM_VIEW))
			throw new PermissionDeniedException();
			
		if (($id = DCL_Sanitize::ToInt($_REQUEST['personnel_id'])) === null)
		{
			throw new InvalidDataException();
		}

		$this->oDB->query(sprintf('select r.dcl_sccs_id, r.sccs_descr, count(*) from dcl_sccs_xref c join dcl_sccs r on c.dcl_sccs_id = r.dcl_sccs_id and c.personnel_id = %d group by r.dcl_sccs_id, r.sccs_descr order by r.sccs_descr', $id));

		$aDisplayRecords = array();
		$aRecords = $this->oDB->FetchAllRows();
		for ($i = 0; $i < count($aRecords); $i++)
		{
			$aDisplayRecords[$i] = array($this->GetLink('menuAction=htmlChangeLog.ShowProjectCommits&dcl_sccs_id=' . $aRecords[$i][0] . '&personnel_id=' . $id, $aRecords[$i][1]), $aRecords[$i][2]);
		}
		
		$oTable = new TableHtmlHelper();
		$oTable->addColumn('Repository', 'html');
		$oTable->addColumn('Commits', 'numeric');
		$oTable->setData($aDisplayRecords);
		$oTable->setShowRownum(true);
		$oTable->setCaption('Repository Commits');

		$aToolbarItems = $this->GetNavLinks();
		foreach ($aToolbarItems as $aToolbarItem)
		{
			$oTable->addToolbar($aToolbarItem['link'], $aToolbarItem['title']);
		}
		
		$oTable->render();

		$this->oDB->FreeResult();
	}

	function ShowProjectCommits()
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CHANGELOG, DCL_PERM_VIEW))
			throw new PermissionDeniedException();
			
		if (($id = DCL_Sanitize::ToInt($_REQUEST['personnel_id'])) === null ||
		    ($sccs_id = DCL_Sanitize::ToInt($_REQUEST['dcl_sccs_id'])) === null
			)
		{
			throw new InvalidDataException();
		}

		$oPersonnel = new PersonnelModel();
		if ($oPersonnel->Load($id) == -1)
		    return;

		$this->oDB->query(sprintf('select sccs_project_path, count(*) from dcl_sccs_xref where personnel_id = %d and dcl_sccs_id = %d group by sccs_project_path order by sccs_project_path', $id, $sccs_id));

		$aDisplayRecords = array();
		$aRecords = $this->oDB->FetchAllRows();
		for ($i = 0; $i < count($aRecords); $i++)
		{
			$aDisplayRecords[$i] = array($this->GetLink('menuAction=htmlChangeLog.ShowFileCommits&dcl_sccs_id=' . $sccs_id . '&personnel_id=' . $id . '&sccs_project_path=' . rawurlencode($aRecords[$i][0]), $aRecords[$i][0]), $aRecords[$i][1]);
		}

		$oTable = new TableHtmlHelper();
		$oTable->addColumn('Project', 'html');
		$oTable->addColumn('Commits', 'numeric');
		$oTable->setData($aDisplayRecords);
		$oTable->setShowRownum(true);
		$oTable->setCaption('Project Commits');

		$aToolbarItems = $this->GetNavLinks();
		foreach ($aToolbarItems as $aToolbarItem)
		{
			$oTable->addToolbar($aToolbarItem['link'], $aToolbarItem['title']);
		}
		
		$oTable->render();

		$this->oDB->FreeResult();
	}

	function ShowFileCommits()
	{
		global $g_oSec, $dcl_sccs_id, $personnel_id, $sccs_project_path;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CHANGELOG, DCL_PERM_VIEW))
			throw new PermissionDeniedException();
			
		if (($id = DCL_Sanitize::ToInt($_REQUEST['personnel_id'])) === null ||
		    ($sccs_id = DCL_Sanitize::ToInt($_REQUEST['dcl_sccs_id'])) === null
			)
		{
			throw new InvalidDataException();
		}

		$sccs_project_path = $_REQUEST['sccs_project_path'];

		$oPersonnel = new PersonnelModel();
		if ($oPersonnel->Load($id) == -1)
			return;

		$this->oDB->query(sprintf("select sccs_file_name, count(*) from dcl_sccs_xref where personnel_id = %d and dcl_sccs_id = %d and sccs_project_path = %s group by sccs_file_name order by sccs_file_name", $id, $sccs_id, $this->oDB->Quote($sccs_project_path)));

		$aDisplayRecords = array();
		$aRecords = $this->oDB->FetchAllRows();
		for ($i = 0; $i < count($aRecords); $i++)
		{
			$aDisplayRecords[$i] = array($this->GetLink('menuAction=htmlChangeLog.ShowVersionCommits&dcl_sccs_id=' . $dcl_sccs_id . '&personnel_id=' . $id . '&sccs_project_path=' . rawurlencode($sccs_project_path) . '&sccs_file_name=' . rawurlencode($aRecords[$i][0]), $aRecords[$i][0]), $aRecords[$i][1]);
		}

		$oTable = new TableHtmlHelper();
		$oTable->addColumn('File', 'html');
		$oTable->addColumn('Commits', 'numeric');
		$oTable->setData($aDisplayRecords);
		$oTable->setShowRownum(true);
		$oTable->setCaption('File Commits');

		$aToolbarItems = $this->GetNavLinks();
		foreach ($aToolbarItems as $aToolbarItem)
		{
			$oTable->addToolbar($aToolbarItem['link'], $aToolbarItem['title']);
		}
		
		$oTable->render();

		$this->oDB->FreeResult();
	}

	function ShowVersionCommits()
	{
		global $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_CHANGELOG, DCL_PERM_VIEW))
			throw new PermissionDeniedException();
			
		if (($id = DCL_Sanitize::ToInt($_REQUEST['personnel_id'])) === null ||
		    ($sccs_id = DCL_Sanitize::ToInt($_REQUEST['dcl_sccs_id'])) === null ||
		    !DCL_Sanitize::IsValidFileName($_REQUEST['sccs_file_name'])
			)
		{
			throw new InvalidDataException();
		}

		$sccs_project_path = $_REQUEST['sccs_project_path'];
		$sccs_file_name = $_REQUEST['sccs_file_name'];

		$oPersonnel = new PersonnelModel();
		if ($oPersonnel->Load($id) == -1)
			return;

		$this->oDB->query(sprintf("select sccs_version, sccs_checkin_on, sccs_comments, dcl_entity_type_id, dcl_entity_id, dcl_entity_id2 from dcl_sccs_xref where personnel_id = %d and dcl_sccs_id = %d and sccs_project_path = %s and sccs_file_name = %s order by sccs_version", $id, $sccs_id, $this->oDB->Quote($sccs_project_path), $this->oDB->Quote($sccs_file_name)));

		$aRecords = $this->oDB->FetchAllRows();
		$aDisplayRecords = array();
		for ($i = 0; $i < count($aRecords); $i++)
		{
			$aDisplayRecords[$i][0] = $aRecords[$i][0];
			$aDisplayRecords[$i][1] = $this->oDB->FormatTimestampForDisplay($aRecords[$i][1]);
			$aDisplayRecords[$i][2] = $aRecords[$i][2];
			
			$oMeta = new DCL_MetadataDisplay();
			if ($aRecords[$i][3] == DCL_ENTITY_WORKORDER)
				$aDisplayRecords[$i][3] = $this->GetLink('menuAction=boWorkorders.viewjcn&jcn=' . $aRecords[$i][4] . '&seq=' . $aRecords[$i][5], '[' . $aRecords[$i][4] . '-' . $aRecords[$i][5] . ']' . $oMeta->GetWorkOrder($aRecords[$i][4], $aRecords[$i][5]));
			else if ($aRecords[$i][3] == DCL_ENTITY_PROJECT)
				$aDisplayRecords[$i][3] = $this->GetLink('menuAction=boProjects.viewproject&project=' . $aRecords[$i][4], '[' . $aRecords[$i][4] . ']' . $oMeta->GetProject($aRecords[$i][4]));
		}

		$oTable = new TableHtmlHelper();
		$oTable->addColumn('Version', 'string');
		$oTable->addColumn('Commit On', 'string');
		$oTable->addColumn('Comment', 'string');
		$oTable->addColumn('Commit For', 'html');
		$oTable->setData($aDisplayRecords);
		$oTable->setShowRownum(true);
		$oTable->setCaption('Version Commits');

		$aToolbarItems = $this->GetNavLinks();
		foreach ($aToolbarItems as $aToolbarItem)
		{
			$oTable->addToolbar($aToolbarItem['link'], $aToolbarItem['title']);
		}
		
		$oTable->render();

		$this->oDB->FreeResult();
	}
}
