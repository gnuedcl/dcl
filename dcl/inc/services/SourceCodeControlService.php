<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2011 Free Software Foundation
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

class SourceCodeControlService
{
	public function __construct()
	{
		if (!defined('SERVICE_AUTH'))
		{
			header('HTTP/1.1 403 Forbidden');
			exit;
		}
	}
	
	public function returnEmptyResponse()
	{
		echo '<?xml version="1.0" encoding="UTF-8"?><dataset></dataset>';
		exit;
	}
	
	public function returnForbidden()
	{
		header('HTTP/1.1 403 Forbidden');
		exit;
	}
	
	public function getUserID()
	{
		$model = new AuthenticateSqlModel();
		$authInfo = array();
		if ($model->IsValidLogin($authInfo))
		{
			echo '<?xml version="1.0" encoding="UTF-8"?><dataset><personnel><id>' . $authInfo['id'] . '</id></personnel></dataset>';
			exit;
		}
		
		SourceCodeControlService::returnEmptyResponse();
	}
	
	public function getSccsSecurity()
	{
		if (($id = @Filter::ToInt($_REQUEST['personnel_id'])) === null)
		{
			SourceCodeControlService::returnEmptyResponse();
		}
		
		$sSQL = 'SELECT DISTINCT rp.perm_id FROM dcl_user_role ur, dcl_role_perm rp WHERE ur.role_id = rp.role_id AND personnel_id = ' . $id;
		$sSQL .= ' AND ((ur.entity_type_id = rp.entity_id AND ur.entity_type_id = 0 AND ur.entity_id1 = 0 AND ur.entity_id2 = 0) OR';
		$sSQL .= ' (rp.entity_id = 0 AND ur.entity_type_id = 0 AND ur.entity_id1 = 0 AND ur.entity_id2 = 0))';
		
		$oDB = new PersonnelModel();
		$oDB->Query($sSQL);
		$sRetVal = '<?xml version="1.0" encoding="UTF-8"?><dataset>';

		while ($oDB->next_record())
			$sRetVal .= '<dcl_role_perm><perm_id>' . $oDB->f(0) . '</perm_id></dcl_role_perm>';
			
		echo $sRetVal, '</dataset>';
		exit;
	}
	
	public function getRepositoryByID()
	{
		if (($id = @Filter::ToInt($_REQUEST['dcl_sccs_id'])) === null)
		{
			SourceCodeControlService::returnEmptyResponse();
		}
		
		$oDB = new SccsModel();
		if ($oDB->Load($id) != -1)
		{
			$sRetVal = '<?xml version="1.0" encoding="UTF-8"?><dataset>';
			$sRetVal .= '<dcl_sccs><sccs_repository>' . htmlspecialchars($oDB->sccs_repository) . '</sccs_repository>';
			$sRetVal .= '<sccs_descr>' . htmlspecialchars($oDB->sccs_descr) . '</sccs_descr></dcl_sccs>';
			
			echo $sRetVal, '</dataset>';
			exit;
		}

		SourceCodeControlService::returnEmptyResponse();
	}
	
	public function getRepositoryByPath()
	{
		$oDB = new SccsModel();
		$sPath = $oDB->GPCStripSlashes($_POST['sccs_repository']);
		if ($oDB->LoadByPath($sPath) != -1)
		{
			$sRetVal = '<?xml version="1.0" encoding="UTF-8"?><dataset><dcl_sccs>';
			$sRetVal .= '<dcl_sccs_id>' . $oDB->dcl_sccs_id . '</dcl_sccs_id>';
			$sRetVal .= '<sccs_descr>' . htmlspecialchars($oDB->sccs_descr) . '</sccs_descr>';

			echo $sRetVal, '</dcl_sccs></dataset>';
			exit;
		}

		SourceCodeControlService::returnEmptyResponse();
	}
	
	public function addRepository()
	{
		$oDB = new SccsModel();
		$oDB->InitFrom_POST();
		$oDB->Add();
	}
	
	public function getWorkOrder()
	{
		if (($iWOID = Filter::ToInt($_POST['woid'])) === null || ($iSeq = Filter::ToInt($_POST['seq'])) === null)
		{
			SourceCodeControlService::returnEmptyResponse();
		}
		
		$oDB = new WorkOrderModel();
		if ($oDB->Query("SELECT summary, description, status, responsible, product, etchours FROM workorders WHERE jcn = $iWOID AND seq = $iSeq") != -1 && $oDB->next_record())
		{
			$sRetVal = '<?xml version="1.0" encoding="UTF-8"?><dataset><workorders>';
			$sRetVal .= '<summary>' . htmlspecialchars($oDB->f('summary')) . '</summary>';
			$sRetVal .= '<description>' . htmlspecialchars($oDB->f('description')) . '</description>';
			$sRetVal .= '<status>' . $oDB->f('status') . '</status>';
			$sRetVal .= '<responsible>' . $oDB->f('responsible') . '</responsible>';
			$sRetVal .= '<product>' . $oDB->f('product') . '</product>';
			$sRetVal .= '<etchours>' . $oDB->f('etchours') . '</etchours>';
						
			echo $sRetVal, '</workorders></dataset>';
			exit;
		}
		
		SourceCodeControlService::returnEmptyResponse();
	}
	
	public function getProject()
	{
		if (($iProjectID = Filter::ToInt($_POST['projectid'])) === null)
		{
			SourceCodeControlService::returnEmptyResponse();
		}
		
		$oDB = new ProjectsModel();
		if ($oDB->Query("SELECT name, description FROM dcl_projects WHERE projectid = $iProjectID") != -1 && $oDB->next_record())
		{
			$sRetVal = '<?xml version="1.0" encoding="UTF-8"?><dataset><dcl_projects>';
			$sRetVal .= '<name>' . htmlspecialchars($oDB->f('name')) . '</name>';
			$sRetVal .= '<description>' . htmlspecialchars($oDB->f('description')) . '</description>';
						
			echo $sRetVal, '</dcl_projects></dataset>';
			exit;
		}
		
		SourceCodeControlService::returnEmptyResponse();
	}
	
	public function checkin()
	{
		$iEntityTypeID = Filter::ToInt($_POST['dcl_entity_type_id']);
		$iEntityID = Filter::ToInt($_POST['dcl_entity_id']);
		$iEntityID2 = Filter::ToInt($_POST['dcl_entity_id2']);
		$iSccsID = Filter::ToInt($_POST['dcl_sccs_id']);
		$iUserID = Filter::ToInt($_POST['personnel_id']);
		
		if ($iEntityTypeID === null || $iEntityID === null || $iEntityID2 === null || $iSccsID === null || $iUserID === null)
		{
			SourceCodeControlService::returnForbidden();
		}
		
		$oDB = new SccsXrefModel();
		$oDB->InitFrom_POST();
		$oDB->sccs_checkin_on = 'now()';
		$oDB->Add();
	}
}
