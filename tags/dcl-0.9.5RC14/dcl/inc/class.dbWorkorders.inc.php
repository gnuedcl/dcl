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
class dbWorkorders extends dclDB
{
	var $aContactOrgs;
	var $aOrgs;
	var $iWoid;
	var $iSeq;
	
	function dbWorkorders()
	{
		parent::dclDB();
		$this->TableName = 'workorders';
		LoadSchema($this->TableName);
		$this->AuditEnabled = true;
		
		$this->aContactOrgs = array();
		$this->aOrgs = array();
		$this->iWoid = -1;
		$this->iSeq = -1;

		parent::Clear();
	}

	function Add()
	{
		global $dcl_info, $g_oSec;

		// Fill in the blanks if this is a stripped down workorder input
		// by someone who cannot assign it due to insufficient security
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ASSIGN))
		{
			if ($this->responsible == 0)
			{
				$objProduct = CreateObject('dcl.dbProducts');
				$objProduct->Load($this->product);
				$this->responsible = $objProduct->reportto;
				$this->status = $dcl_info['DCL_DEF_STATUS_UNASSIGN_WO'];
			}
			else
			{
				$this->status = $dcl_info['DCL_DEF_STATUS_ASSIGN_WO'];
			}

			$this->esthours = 0.0;
			$tomorrow = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'));
			$this->deadlineon = date($dcl_info['DCL_DATE_FORMAT'], $tomorrow);
			$this->eststarton = date($dcl_info['DCL_DATE_FORMAT'], $tomorrow);
			$this->estendon = date($dcl_info['DCL_DATE_FORMAT'], $tomorrow);
			$this->severity = $dcl_info['DCL_DEF_SEVERITY'];
			$this->priority = $dcl_info['DCL_DEF_PRIORITY'];
		}
		else
		{
			$this->status = $dcl_info['DCL_DEF_STATUS_ASSIGN_WO'];
		}

		if (IsSet($this->jcn) && $this->jcn > 0)
		{
			$this->seq = $this->NewSequence($this->jcn);
		}
		else
		{
			$this->jcn = $this->NewID();
			$this->seq = 1;
		}

		if ($this->module_id < 1)
			$this->module_id = null;

		if ($this->entity_source_id < 1)
			$this->entity_source_id = null;

		if ($this->reported_version_id < 1)
			$this->reported_version_id = null;

		if ($this->targeted_version_id < 1)
			$this->targeted_version_id = null;

		if ($this->fixed_version_id < 1)
			$this->fixed_version_id = null;

		$this->createdon = DCL_NOW;
		$this->statuson = DCL_NOW;

		parent::Add();
	}

	function Edit()
	{
		if ($this->closedby < 1)
		{
			$this->closedby = null;
			$this->closedon = null;
		}

		if ($this->module_id < 1)
			$this->module_id = null;

		if ($this->entity_source_id < 1)
			$this->entity_source_id = null;
			
		if ($this->reported_version_id < 1)
			$this->reported_version_id = null;

		if ($this->targeted_version_id < 1)
			$this->targeted_version_id = null;

		if ($this->fixed_version_id < 1)
			$this->fixed_version_id = null;

		parent::Edit();
	}

	function Delete()
	{
		// Should have been unmapped from any projects in a bo

		$this->BeginTransaction();
		// Bye, bye time cards!
		$query = 'DELETE FROM timecards WHERE jcn=' . $this->jcn . ' AND seq=' . $this->seq;
		$this->Execute($query);

		// And you! Clear off!
		$this->Audit(array('jcn' => $this->jcn, 'seq' => $this->seq));

		$query = 'DELETE FROM workorders WHERE jcn=' . $this->jcn . ' AND seq=' . $this->seq;
		$this->Execute($query);
		$this->EndTransaction();
	}

	function Load($jcn, $seq)
	{
		global $g_oSec;

		if (!isset($jcn) || !is_numeric($jcn) || $jcn < 1 || !isset($seq) || !is_numeric($seq) || $seq < 1)
			return trigger_error("Invalid work order ID passed to Load: $jcn-$seq");

		$this->Clear();
		$oRetVal = parent::Load(array('jcn' => $jcn, 'seq' => $seq));
		if ($oRetVal !== -1)
		{
			$bIsPublic = false;
			if (($g_oSec->IsPublicUser() || $g_oSec->IsOrgUser()) && !$this->CanView($this, $GLOBALS['DCLID'], $bIsPublic))
			{
				$oRetVal = -1;
				$this->Clear();
				PrintPermissionDenied();
			}
		}
		
		return $oRetVal;
	}

	function LoadSequencesExcept($jcn, $seq)
	{
		if (!$this->Query("SELECT seq FROM workorders WHERE jcn=$jcn AND seq != $seq"))
			return -1;
	}

	function IsInAProject()
	{
		$obj = new dclDB;
		if ($obj->Query('SELECT count(*) FROM projectmap WHERE jcn=' . $this->jcn . ' and seq in (0,' . $this->seq . ')') == -1)
			return false;

		$obj->next_record();
		return ($obj->f(0) > 0);
	}

	function NewID()
	{
		$this->BeginTransaction();
		$this->Insert('INSERT INTO dcl_wo_id (seq) VALUES (1)');
		$wo_id = $this->GetLastInsertID('dcl_wo_id');
		$this->EndTransaction();

		return $wo_id;
	}

	function NewSequence($wo_id)
	{
		$this->BeginTransaction();
		$this->Execute(sprintf('UPDATE dcl_wo_id SET seq = seq + 1 WHERE jcn = %d', $wo_id));
		$seq = $this->ExecuteScalar(sprintf('SELECT seq FROM dcl_wo_id WHERE jcn = %d', $wo_id));
		$this->EndTransaction();

		return $seq;
	}
	
	function CanView(&$obj, $iPersonnelID, &$bIsPublic)
	{
		global $dcl_info, $g_oSession, $g_oSec;
		
		$bCanView = true;
		$bIsPublic = false;
		$oUR =& CreateObject('dcl.dbUserRole');
		$oUR->ListPermissions($iPersonnelID, DCL_ENTITY_WORKORDER, 0, 0, array(DCL_PERM_PUBLICONLY, DCL_PERM_VIEWACCOUNT));
		while ($oUR->next_record() && $bCanView)
		{
			if ($oUR->f(0) == DCL_PERM_PUBLICONLY)
			{
				$bIsPublic = true;
				if ($bCanView)
					$bCanView = ($obj->is_public == 'Y');
					
				if ($bCanView)
				{
					$oDBProduct =& CreateObject('dcl.dbProducts');
					if ($oDBProduct->Load($obj->product) !== -1)
					{
						$bCanView = ($oDBProduct->is_public == 'Y');
						if ($bCanView)
						{
							$aProducts = split(',', $g_oSession->Value('org_products'));
							$bCanView = (count($aProducts) > 0 && in_array($obj->product, $aProducts));
						}
					}
					else
					{
						$bCanView = false;
					}
				}
			}
			else if ($oUR->f(0) == DCL_PERM_VIEWACCOUNT)
			{
				if ($obj->jcn != $this->iWoid || $obj->seq != $this->iSeq)
				{
					$oWOA =& CreateObject('dcl.dbWorkOrderAccount');
					if ($oWOA->Load($obj->jcn, $obj->seq) != -1)
					{
						$this->iWoid = $obj->jcn;
						$this->iSeq = $obj->seq;
						$this->aOrgs = array();
						do
						{
							array_push($this->aOrgs, $oWOA->f(2));
						} while ($oWOA->next_record());
						
						$bCanView = (count($this->aOrgs) > 0);
					}
					else
						$bCanView = false;
				}
				
				if (!$bCanView)
					return false;
					
				$oDB = new dclDB;
				$sSQL = "SELECT OC.org_id FROM dcl_org_contact OC JOIN personnel P ON OC.contact_id = P.contact_id WHERE P.id = $iPersonnelID";
				if ($oDB->Query($sSQL) != -1)
				{
					$this->aContactOrgs[$iPersonnelID] = array();
					while ($oDB->next_record())
					{
						array_push($this->aContactOrgs[$iPersonnelID], $oDB->f(0));
					}
					
					if (count($this->aContactOrgs[$iPersonnelID]) > 0)
						$bCanView = (count(array_intersect($this->aOrgs, $this->aContactOrgs[$iPersonnelID])) > 0);
					else
						$bCanView = false;
				}
				else
					$bCanView = false;
			}
		}
		
		return $bCanView;
	}
}
?>
