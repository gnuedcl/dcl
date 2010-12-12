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
LoadStringResource('prj');

class boProjects
{
	function newproject()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$obj = new htmlProjectsform();
		$obj->Show();
	}

	function dbnewproject()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADD))
			throw new PermissionDeniedException();

		$objProject = new dbProjects();
		if ($objProject->Exists($_REQUEST['name']))
		{
			trigger_error(sprintf(STR_PRJ_ALREADYEXISTS, $_REQUEST['name']));
			return;
		}

		$objProject->InitFromGlobals();
		$objProject->createdby = $GLOBALS['DCLID'];
		$objProject->status = $dcl_info['DCL_DEFAULT_PROJECT_STATUS'];
		$objProject->Add();
		
		if (($iTplID = @DCL_Sanitize::ToInt($_REQUEST['template'])) === null)
		{
			$iTplID = 0;
		}
		
		if ($dcl_info['DCL_PROJECT_XML_TEMPLATES'] == 'Y' && $iTplID != 0)
		{
			// user selected a template, so we must generate workorders
			$objXMLProject = new xmlProjects();

			$params = explode('&', $_REQUEST['encodedparams']);
			$selectedParams = array();
			foreach ($params as $key => $param)
			{
				list($key, $val) = split('=', $param);
				$selectedParams[$key] = $val;
			}
			
			$objXMLProject->createProjectFromTemplate($objProject->projectid, $iTplID, $_REQUEST['projectdeadline'], $selectedParams);
		}

		if ($objProject->reportto != $GLOBALS['DCLID'])
			$this->SendNewMailMsg($objProject);

		$objHTMLProject = new htmlProjectsdetail();
		$objHTMLProject->Show($objProject->projectid, 0, 0);
	}

	function addtoproject()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK))
			throw new PermissionDeniedException();

		if (($jcn = @DCL_Sanitize::ToInt($_REQUEST['jcn'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (($seq = @DCL_Sanitize::ToInt($_REQUEST['seq'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$objPM = new dbProjectmap();
		if ($objPM->LoadByWO($jcn, $seq) != -1)
		{
			// Mapped implicitly (seq = 0) or explicitly (seq > 0)
			$objPM->GetRow();
			$objHTMLProjects = new htmlProjectsdetail();
			$objHTMLProjects->Show($objPM->projectid, 0, 0);
		}
		else
		{
			$objHTMLProjectmap = new htmlProjectmap();
			$objHTMLProjectmap->ChooseProjectForJCN($jcn, $seq);
		}
	}

	function dbaddtoproject()
	{
		global $g_oSec;
		
		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADDTASK))
			throw new PermissionDeniedException();

		$objPM = new dbProjectmap();
		$objPM->InitFromGlobals();
		if (IsSet($_REQUEST['addall']) && $_REQUEST['addall'] == '1')
		{
			$objPM->seq = 0;
			// Be sure all other entries for this JCN are deleted so they move to this project
			$objPM->Delete();
		}
		
		$objPM->Add();

		$objHTMLProjects = new htmlProjectsdetail();
		$objHTMLProjects->Show($objPM->projectid, 0, 0);
	}

	function viewproject()
	{
		global $g_oSec;
		
		commonHeader();

		if (($project = @DCL_Sanitize::ToInt($_REQUEST['project'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if ($project > 0)
		{
			if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEW, $project))
				throw new PermissionDeniedException();

			$obj = new htmlProjectsdetail();
			$wostatus = 0;
			$woresponsible = 0;
			
			if (($wostatus = @DCL_Sanitize::ToSignedInt($_REQUEST['wostatus'])) === null)
				$wostatus = 0;

			if (($woresponsible = @DCL_Sanitize::ToInt($_REQUEST['woresponsible'])) === null)
				$woresponsible = 0;

			$obj->show($project, $wostatus, $woresponsible);
		}
	}

	function showtree()
	{
		global $g_oSec;
		
		commonHeader();
		if (($project = @DCL_Sanitize::ToInt($_REQUEST['project'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if ($project > 0)
		{
			if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEW, $project))
				throw new PermissionDeniedException();

			$obj = new htmlProjectsdetail();
			$wostatus = 0;
			$woresponsible = 0;
			
			if (($wostatus = @DCL_Sanitize::ToInt($_REQUEST['wostatus'])) === null)
				$wostatus = 0;

			if (($woresponsible = @DCL_Sanitize::ToInt($_REQUEST['woresponsible'])) === null)
				$woresponsible = 0;

			$obj->showtree($project, $wostatus, $woresponsible);
		}
	}

	function modify()
	{
		global $g_oSec;
		
		commonHeader();
		if (($projectid = @DCL_Sanitize::ToInt($_REQUEST['projectid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_MODIFY, $projectid))
			throw new PermissionDeniedException();

		$obj = new dbProjects();
		if ($obj->Load($projectid) == -1)
			return;
		
		$objHTML = new htmlProjectsform();
		$objHTML->Show($obj);
	}

	function dbmodify()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (($projectid = @DCL_Sanitize::ToInt($_REQUEST['projectid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_MODIFY, $projectid))
			throw new PermissionDeniedException();

		$obj = new dbProjects();
		if ($obj->Load($projectid) != -1)
		{
			if (($status = @DCL_Sanitize::ToInt($_REQUEST['status'])) === null ||
			    ($parentprojectid = @DCL_Sanitize::ToInt($_REQUEST['parentprojectid'])) === null
				)
			{
				throw new InvalidDataException();
			}
		
			$oStatus = new StatusModel();
			if ($oStatus->GetStatusType($status) == 2 && $oStatus->GetStatusType($obj->status) != 2)
			{
				// moving to closed
				$obj->finalclose = date($dcl_info['DCL_DATE_FORMAT']);
			}
			elseif ($oStatus->GetStatusType($status) != 2 && $oStatus->GetStatusType($obj->status) == 2)
			{
				// reopened
				$obj->finalclose = '';
			}

			$bChangeParent = ($obj->parentprojectid != $parentprojectid);
			$iOriginalParent = $obj->parentprojectid;
			$obj->InitFromGlobals();
			$objHTML = new htmlProjectsdetail();
			if ($bChangeParent && $obj->parentprojectid > 0)
			{
				if (!$obj->ParentIsNotChild($obj->projectid, $obj->parentprojectid))
				{
					trigger_error(STR_BO_PARENTISCHILD);

					$obj->parentprojectid = $iOriginalParent;
					
					$oForm = new htmlProjectsform();
					$oForm->Show($obj);
					return;
				}
			}

			$obj->Edit();
			$objHTML->Show($obj->projectid, 0, 0);
		}
		else
		{
			$o = new htmlProjects();
			$o->show();
		}
	}

	function delete()
	{
		global $g_oSec;
		
		commonHeader();
		if (($projectid = @DCL_Sanitize::ToInt($_REQUEST['projectid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_DELETE, $projectid))
			throw new PermissionDeniedException();

		$obj = new dbProjects();
		if ($obj->Load($projectid) == -1)
			return;
			
		ShowDeleteYesNo('Project', 'boProjects.dbdelete', $obj->projectid, $obj->name, false, 'projectid');
	}

	function dbdelete()
	{
		global $g_oSec;
		
		commonHeader();
		if (($projectid = @DCL_Sanitize::ToInt($_REQUEST['projectid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_DELETE, $projectid))
			throw new PermissionDeniedException();

		$obj = new dbProjects();
		$obj->projectid = $projectid;
		$obj->Delete();

		// Wipe out any watches anyone may have had
		$oWatch = new dbWatches();
		$oWatch->DeleteByObjectID(2, $obj->projectid);

		// Browse around some more
		$objHTMLProjects = new htmlProjects();
		$objHTMLProjects->show();
	}

	function unmap()
	{
		global $g_oSec;
		
		commonHeader();

		if (($jcn = @DCL_Sanitize::ToInt($_REQUEST['jcn'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (($seq = @DCL_Sanitize::ToInt($_REQUEST['seq'])) === null)
		{
			throw new InvalidDataException();
		}
		
		$obj = new dbProjectmap();
		if ($obj->LoadByWO($jcn, $seq) != -1)
		{
			if ($g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_REMOVETASK, $obj->projectid))
				$this->dbunmap($jcn, $seq);
			else
				throw new PermissionDeniedException();

			$objPrj = new htmlProjectsdetail();
			$objPrj->Show($projectid, 0, 0);
		}
		else
		{
			// shouldn't get here
			throw new PermissionDeniedException();
		}
	}

	// Only intended to be called as a utility function - no UI output unless needed
	function dbunmap($jcn, $seq, $unmapseqonly = false, $allforjcn = false)
	{
		$obj = new dbProjectmap();
		if ($obj->LoadByWOFilter($jcn, $seq, $unmapseqonly, $allforjcn) == -1)
			return;

		if ($obj->next_record())
		{
			if ($allforjcn == true)
			{
				do
				{
					$obj->GetRow();
					$obj->Delete();
				}
				while ($obj->next_record());
			}
			else
			{
				$obj->GetRow();

				// Remove the mapping here
				$obj->Delete();
				if ($obj->seq == 0)
				{
					// It was implicitly mapped - explicitly relink all but this seq
					// No auditing needed here since these aren't moving or being removed
					$obj->MapAllExcept($obj->projectid, $jcn, $seq);
				}
			}
		}
	}

	function upload()
	{
		global $g_oSec;
		
		commonHeader();
		if (($projectid = @DCL_Sanitize::ToInt($_REQUEST['projectid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ATTACHFILE, $projectid))
			throw new PermissionDeniedException();

		$obj = new htmlProjects();
		$obj->ShowUploadFileForm($projectid);
	}

	function doupload()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (($projectid = @DCL_Sanitize::ToInt($_REQUEST['projectid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ATTACHFILE, $projectid))
			throw new PermissionDeniedException();

		if (($sFileName = DCL_Sanitize::ToFileName('userfile')) !== null)
		{
			$o = new boFile();
			$o->iType = DCL_ENTITY_PROJECT;
			$o->iKey1 = $projectid;
			$o->sFileName = DCL_Sanitize::ToActualFileName('userfile');
			$o->sTempFileName = $sFileName;
			$o->sRoot = $dcl_info['DCL_FILE_PATH'] . '/attachments';
			$o->Upload();
		}
		else
		{
			trigger_error('Invalid request');
			return;
		}

		$objHTML = new htmlProjectsdetail();
		$objHTML->Show($projectid, 0, 0);
	}

	function deleteattachment()
	{
		global $g_oSec;
		
		commonHeader();
		if (($projectid = @DCL_Sanitize::ToInt($_REQUEST['projectid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_REMOVEFILE, $projectid))
			throw new PermissionDeniedException();
			
		if (!@DCL_Sanitize::IsValidFileName($_REQUEST['filename']))
		{
			trigger_error('Invalid file name.');
			return;
		}
			
		$obj = new htmlProjects();
		$obj->ShowDeleteAttachmentYesNo($projectid, $_REQUEST['filename']);
	}

	function dodeleteattachment()
	{
		global $dcl_info, $g_oSec;

		commonHeader();
		if (($projectid = @DCL_Sanitize::ToInt($_REQUEST['projectid'])) === null)
		{
			throw new InvalidDataException();
		}
		
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_REMOVEFILE, $projectid))
			throw new PermissionDeniedException();

		if (!@DCL_Sanitize::IsValidFileName($_REQUEST['filename']))
		{
			trigger_error('Invalid file name.');
			return;
		}
			
		$attachPath = $dcl_info['DCL_FILE_PATH'] . '/attachments/prj/' . substr($projectid, -1) . '/' . $projectid . '/';
		if (is_file($attachPath . $_REQUEST['filename']) && is_readable($attachPath . $_REQUEST['filename']))
			unlink($attachPath . $_REQUEST['filename']);

		$objHTML = new htmlProjectsdetail();
		$objHTML->Show($projectid, 0, 0);
	}

	function showmy()
	{
		commonHeader();
		$obj = new htmlProjects();
		$obj->my(0);
	}

	function batchMove($aSource)
	{
		if (!is_array($aSource) || !isset($aSource['selected']) || !is_array($aSource['selected']))
			return;

		$oDB = new dclDB;
		$oDB->BeginTransaction();

		$objPM = new dbProjectmap();
		$objPM->projectid = $aSource['projectid'];

		foreach ($aSource['selected'] as $val)
		{
			list($woid, $seq) = explode('.', $val);
			if (DCL_Sanitize::ToInt($woid) !== null && DCL_Sanitize::ToInt($seq) !== null)
			{
				$this->dbunmap($woid, $seq, false, false);
				$objPM->jcn = $woid;
				$objPM->seq = $seq;
				$objPM->Add();
			}
		}

		$oDB->EndTransaction();
	}

	function SendNewMailMsg($obj)
	{
		global $dcl_info;

		if ($dcl_info['DCL_SMTP_ENABLED'] != 'Y')
			return;

		if (!is_object($obj))
			throw new PermissionDeniedException();

		$objPersonnel = new PersonnelModel();
		if ($objPersonnel->Load($obj->reportto) == -1)
			return;
		
		if ($objPersonnel->email == '')
			return;

		$mailMsg = STR_BO_PROJECTMAILMSG . phpCrLf . phpCrLf;
		$mailMsg .= STR_PRJ_PROJECT . ': ' . $obj->name . phpCrLf;
		if ($obj->projectdeadline != '')
			$mailMsg .= STR_PRJ_DEADLINE . ': ' . $obj->projectdeadline . phpCrLf;

		$mailMsg .= STR_PRJ_DESCRIPTION . ': ' . $obj->description;
		$mailMsg .= phpCrLf . phpCrLf . STR_PRJ_EMAILSIG;

		$oMail = new Smtp();
		$oMail->to = $objPersonnel->email;
		$oMail->from = $GLOBALS['USEREMAIL'];
		$oMail->subject = sprintf(STR_PRJ_EMAILSUBJECT, $obj->name);
		$oMail->body = $mailMsg;
		$oMail->Send();

		trigger_error(sprintf(STR_BO_MAILSENT, $objPersonnel->email), E_USER_NOTICE);
	}

	function GetProjectPath($jcn, $seq)
	{
		$aProjects = array();

		$objPM = new dbProjectmap();
		if ($objPM->LoadByWO($jcn, $seq) != -1)
		{
			$objDBPrj = new dbProjects();
			$project_path = explode(',', $objDBPrj->GetProjectParents($objPM->projectid, true));
			while (list($key, $project_id) = each($project_path))
			{
				$objDBPrj->Load($project_id);
				$aProjects[] = array('project_id' => $project_id, 'name' => $objDBPrj->name);
			}

			if (count($aProjects) > 0)
				$aProjects = array_reverse($aProjects);
		}

		return $aProjects;
	}

	function GetParentProjectPath($projectid)
	{
		$aProjects = array();

		$objDBPrj = new dbProjects();
		$sParentProjects = $objDBPrj->GetProjectParents($projectid);
		if ($sParentProjects == '')
			return null;

		$project_path = explode(',', $objDBPrj->GetProjectParents($projectid));
		foreach ($project_path as $project_id)
		{
			$objDBPrj->Load($project_id);
			$aProjects[] = array('project_id' => $project_id, 'name' => $objDBPrj->name);
		}

		if (count($aProjects) > 0)
			$aProjects = array_reverse($aProjects);

		return $aProjects;
	}
}
