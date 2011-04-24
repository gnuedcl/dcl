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

LoadStringResource('db');
LoadStringResource('prod');
class BuildManagerModel extends dclDB
{
	// Instance of dbProductBuildException
	var $oDbProductBuildException;

	public function __construct()
	{
		parent::__construct();
		
		$this->TableName = 'dcl_product_build_sccs';
		LoadSchema($this->TableName);
		
		$this->oDbProductBuildException = new ProductBuildExceptionModel();
		
		parent::Clear();
	}

	public function CheckBM($selected)
	{
		global $dcl_info, $g_oSession;
	
		//Getting Buildid from session, then create Build Item DB Object
		$aReleaseID = &$g_oSession->Value('releaseid');
		$aBuildID = &$g_oSession->Value('buildid');
		$oBuildItem = new dbProductBuildItem();
		
		$this->oDbProductBuildException->DeleteBySession($g_oSession->dcl_session_id, (int)$g_oSession->Value('buildid'));
					
		if (!is_array($selected) || count($selected) == 0)
		{
			trigger_error('Invalid or no items passed to dbBuildManager::CheckBM', E_USER_ERROR);
			return;
		}

		$g_oSession->Register('timecard', 0);
		$g_oSession->Edit();
		foreach ($selected as $jcnseq)
		{		
			list($jcn, $seq) = explode('.', $jcnseq);

			$sql = sprintf('SELECT version_item_apply_on FROM dcl_product_version_item WHERE entity_type_id = %d AND entity_id = %d AND entity_id2 = %d AND version_status_id = 1',
							DCL_ENTITY_WORKORDER, $jcn, $seq);
			$this->Query($sql);
			
			/*
			* Assocating workorders to a particular build, so BuildManager
			* can track the workorder
			*/
			if ($oBuildItem->Exists(array('product_build_id' => $aBuildID, 'entity_type_id' => DCL_ENTITY_WORKORDER, 'entity_id' => $jcn, 'entity_id2' => $seq)))	
			{
				$this->UpdateBMXref($jcn, $seq, 0);
				continue;		
			}

				$oBuildItem->product_build_id = $aBuildID;
				$oBuildItem->entity_type_id = DCL_ENTITY_WORKORDER;
				$oBuildItem->entity_id = $jcn;
				$oBuildItem->entity_id2 = $seq;
				$oBuildItem->created_on = DCL_NOW;
				$oBuildItem->Add();

				
			if (!$this->next_record())
			{
				$this->ErrorLog(DCL_ENTITY_WORKORDER, $jcn, $seq);
				continue;
			}
			
			if ($this->IsFieldNull(0))
			{
				// Never applied changes
				$this->InsertBM(-1, $jcn, $seq, 1);
			}
			else
			{
				// Applied changes before, have more
				$this->GetInfoFromSCCS($this->f(0), $jcn, $seq, 1);
			}
		} 
		$this->Complete();
	}
	
	public function GetInfoFromSCCS($pulldate = 0, $jcn, $seq, $where = 0)
	{		
		global $dcl_info, $g_oSession;

		$sql = "SELECT dcl_sccs_xref_id FROM dcl_sccs_xref WHERE dcl_entity_id=";
		$sql .= " $jcn AND dcl_entity_id2=";
		$sql .= " $seq AND ";				
	
		if ($where == 1)
		{
			$sql .= "sccs_checkin_on >";
			$sql .= " '$pulldate'";
			$update = 0;
		}
		else
		{
			$sql .= "sccs_checkin_on <";
			$sql .= $this->GetDateSQL();
			$update = 1;
		}
		$this->Query($sql);		
		
		$aChanges = $this->FetchAllRows();
		//Check to so wo# not found in the sccs tables, accurately gets applied 
		//to the release\Build
		if (count($aChanges) > 0)
		{				
			foreach ($aChanges as $sccsid)
			{
				$sccsid = $sccsid[0];
				$timecard = &$g_oSession->Value('timecard');
			
				if (!IsSet($timecard) || $timecard == 0)
				{
					$this->InsertBM($sccsid, $jcn, $seq, 1);
				//Set the ready for build flag, back to zero
					if ($update == 1)
						$this->UpdateBMXref($jcn, $seq, 1);
				}
				else
					$this->InsertBM($sccsid, $jcn, $seq);							
			}
		}
	}
	
	public function ErrorLog($entity_type, $id, $id2)
	{
		global $g_oSession;
		
		if (!$g_oSession->IsRegistered('ErrorLog'))
		{
			$g_oSession->Register('ErrorLog', 0);
			$g_oSession->Edit();
		}
		
		$this->oDbProductBuildException->session_id = $g_oSession->dcl_session_id;
		$this->oDbProductBuildException->product_build_id = $g_oSession->Value('buildid');
		$this->oDbProductBuildException->entity_type_id = $entity_type;
		$this->oDbProductBuildException->entity_id = $id;
		
		if ($entity_type == DCL_ENTITY_WORKORDER)
			$this->oDbProductBuildException->entity_id2 = $id2;
		else
			$this->oDbProductBuildException->entity_id2 = null;
		
		$this->oDbProductBuildException->Add();
	}
	
	
	public function CheckBuildManagerProduct($productid)
	{	
		$sql = "SELECT is_versioned from products where id=$productid";
		$this->Query($sql);

			return ($this->next_record() && strtoupper($this->f(0)) == 'Y');
	}
	
	public function CheckWorkOrderProduct($jcn, $seq)
	{
		$sql = "SELECT product FROM workorders WHERE jcn=$jcn AND seq=$seq";
		$this->Query($sql);
		
		$this->CheckBuildManagerProduct($this->f(0));
		
	}
	
	public function InsertBM($sccsid, $jcn, $seq, $init = 0)
	{	
		global $g_oSession;
		
		$oBuildSccs = new dbProductBuildSccs();
		$oBuildSccs->product_build_id = $g_oSession->Value('buildid');
		
		if ($sccsid == -1)
		{
			$oSccs = new dbSccsXref();
			$oSccs->Query(sprintf('SELECT dcl_sccs_xref_id FROM dcl_sccs_xref WHERE dcl_entity_type_id = %d AND dcl_entity_id = %d AND dcl_entity_id2 = %d',
					DCL_ENTITY_WORKORDER,
					$jcn,
					$seq
				));
				
			while ($oSccs->next_record())
			{
				$oBuildSccs->sccs_xref_id = $oSccs->f(0);
				$oBuildSccs->Add();
			}
		}
		else
		{
			$oBuildSccs->sccs_xref_id = $sccsid;
			$oBuildSccs->Add();
		}
		
		if ($init == 1)
		{
			$this->UpdateBMXref($jcn, $seq);
		}
	}
	
	public function CheckVersionStatus($jcn, $seq, $productid)
	{
		//Checks the version item table for the DCL# and if the DCL# is found the release id
		//amd version status id	is passed to UpdateVersionStatus function.  Otherwise, no
		//buildmanager update happens with the timecard action of the DCL.
		global $dcl_info, $g_oSession;
		
		if (!$this->CheckBuildManagerProduct($productid))
			return;
		
		if (true)
		{
			$this->Connect();
			$sql = "SELECT product_version_id, version_status_id FROM dcl_product_version_item WHERE entity_id=$jcn AND entity_id2= $seq";
			$this->Query($sql);
			
			if ($this->next_record())
				$this->UpdateVersionStatus($this->f(0),$this->f(1), $jcn, $seq);
		}		
	}
	
	public function UpdateVersionStatus($releaseid,$status, $jcn, $seq)
	{
		//Update the version Item table, if the DCL number is found.
		//If the version status id is greater than 2 and set the status id
		//back to 1, so the dcl number can be added to another build.
		global $dcl_info;

		if ($status >= DCL_BUILDMANAGER_APPLIED)
		{
			$oVersionItem = new dbProductVersionItem();
			if ($oVersionItem->Load(array('product_version_id' => $releaseid,'entity_type_id' => DCL_ENTITY_WORKORDER, 'entity_id' => $jcn, 'entity_id2' => $seq)) != -1)			
			{
				$oVersionItem->version_status_id = DCL_BUILDMANAGER_SUBMIT;
				$oVersionItem->version_item_submit_on = 'now()';
				$oVersionItem->Edit();
			}
		}
	}
	
	public function UpdateBMXref($jcn, $seq, $init=0)
	{
		global $dcl_info, $g_oSession;
		
		if ($init == 0)
		{
			$oVersionItem = new dbProductVersionItem();
			if ($oVersionItem->Load(array('product_version_id' => $g_oSession->Value('releaseid'), 'entity_type_id' => DCL_ENTITY_WORKORDER, 'entity_id' => $jcn, 'entity_id2' => $seq)) != -1)
			{
				$oVersionItem->version_status_id = DCL_BUILDMANAGER_APPLIED;
				$oVersionItem->version_item_apply_on = 'now()';
				$oVersionItem->Edit();
			}
		}
	}
	
	public function Complete()
	{
		global $dcl_info, $g_oSession;

		$versionid = &$g_oSession->Value('releaseid');
		$buildid = &$g_oSession->Value('buildid');
		
		if ($g_oSession->IsRegistered('ErrorLog'))
		{			
			$obj = new htmlBuildManager();
			$obj->ShowErrorReport();	
		}
		//Show the build detail of work orders submitted
		$obj = new htmlBuildManager();
		$obj->ShowWOByBuild($versionid, $buildid);
	
		$this->DeleteSession();
	}
	
	public function DeleteSession()
	{
		global $g_oSec, $dcl_info, $g_oSession;	
		
		$g_oSession->UnRegister('ErrorLog');
		$g_oSession->UnRegister('BMselected');
		$g_oSession->UnRegister('buildid');
		$g_oSession->UnRegister('releaseid');
		$g_oSession->UnRegister('env');
		$g_oSession->UnRegister('timecard');
		$g_oSession->Edit();
	}
}
