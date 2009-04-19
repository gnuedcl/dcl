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

class DCL_MetadataDisplay
{
	var $oPersonnel;
	var $oStatus;
	var $oSeverity;
	var $oPriority;
	var $oProduct;
	var $oDepartment;
	var $oWorkOrderType;
	var $oModule;
	var $oSource;
	var $oContact;
	var $oContactPhone;
	var $oContactEmail;
	var $oContactUrl;
	var $oOrg;
	var $oOrgPhone;
	var $oOrgEmail;
	var $oOrgUrl;
	var $oOrgAddr;
	var $oProject;
	var $oAction;
	var $oTicket;
	var $oWorkOrder;
	var $oTag;
	var $oHotlist;
	var $oProductVersion;
	var $oTimeCard;

	function DCL_MetadataDisplay()
	{
		$this->oPersonnel = null;
		$this->oStatus = null;
		$this->oSeverity = null;
		$this->oPriority = null;
		$this->oProduct = null;
		$this->oWorkOrderType = null;
		$this->oModule = null;
		$this->oSource = null;
		$this->oContact = null;
		$this->oContactPhone = null;
		$this->oContactEmail = null;
		$this->oContactUrl = null;
		$this->oOrg = null;
		$this->oOrgPhone = null;
		$this->oOrgEmail = null;
		$this->oOrgUrl = null;
		$this->oOrgAddr = null;
		$this->oProject = null;
		$this->oAction = null;
		$this->oTicket = null;
		$this->oWorkOrder = null;
		$this->oTag = null;
		$this->oHotlist = null;
		$this->oProductVersion = null;
		$this->oTimeCard = null;
	}

	function IsValidID($id)
	{
		return (isset($id) && $id !== null && $id != '' && is_numeric($id) && $id > 0);
	}
	
	function TriggerError($sMessage)
	{
		trigger_error($sMessage, E_USER_ERROR);
		return null;
	}

	function GetStatus($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oStatus == null)
			$this->oStatus =& CreateObject('dcl.dbStatuses');

		if ($this->oStatus->Load($id, false) == -1)
			return $this->TriggerError("Could not find status ID $id");

		return $this->oStatus->name;
	}

	function GetPersonnel($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oPersonnel == null)
			$this->oPersonnel =& CreateObject('dcl.dbPersonnel');

		if ($this->oPersonnel->Load($id, false) == -1)
			return $this->TriggerError("Could not find personnel ID $id");

		return $this->oPersonnel->short;
	}

	function GetSeverity($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oSeverity == null)
			$this->oSeverity =& CreateObject('dcl.dbSeverities');

		if ($this->oSeverity->Load($id, false) == -1)
			return $this->TriggerError("Could not find severity ID $id");

		return $this->oSeverity->name;
	}

	function GetPriority($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oPriority == null)
			$this->oPriority =& CreateObject('dcl.dbPriorities');

		if ($this->oPriority->Load($id, false) == -1)
			return $this->TriggerError("Could not find priority ID $id");

		return $this->oPriority->name;
	}

	function GetProduct($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oProduct == null)
			$this->oProduct =& CreateObject('dcl.dbProducts');

		if ($this->oProduct->Load($id, false) == -1)
			return $this->TriggerError("Could not find product ID $id");

		return $this->oProduct->name;
	}

	function GetProductVersion($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oProductVersion == null)
			$this->oProductVersion =& CreateObject('dcl.dbProductVersion');

		if ($this->oProductVersion->Load($id, false) == -1)
			return $this->TriggerError("Could not find product version ID $id");

		return $this->oProductVersion->product_version_text;
	}

	function GetProject($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oProject == null)
			$this->oProject =& CreateObject('dcl.dbProjects');

		if ($this->oProject->Load($id, false) == -1)
			return $this->TriggerError("Could not find project ID $id");

		return $this->oProject->name;
	}

	function GetWorkOrderType($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oWorkOrderType == null)
			$this->oWorkOrderType =& CreateObject('dcl.dbWorkOrderType');

		if ($this->oWorkOrderType->Load(array('wo_type_id' => $id)) == -1)
			return $this->TriggerError("Could not find work order type ID $id");

		return $this->oWorkOrderType->type_name;
	}

	function GetModule($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oModule == null)
			$this->oModule =& CreateObject('dcl.dbProductModules');

		if ($this->oModule->Load($id, false) == -1)
			return $this->TriggerError("Could not find module ID $id");

		return $this->oModule->module_name;
	}

	function GetSource($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oSource == null)
			$this->oSource =& CreateObject('dcl.dbEntitySource');

		if ($this->oSource->Load(array('entity_source_id' => $id)) == -1)
			return $this->TriggerError("Could not find source ID $id");

		return $this->oSource->entity_source_name;
	}

	function &GetContact($id)
	{
		$aRetVal = array('name' => '', 'phonetype' => '', 'phone' => '', 'emailtype' => '', 'email' => '', 'urltype' => '', 'url' => '', 'org_id' => '', 'org_name' => '');
		if (!$this->IsValidID($id))
			return $aRetVal;

		if ($this->oContact == null)
		{
			$this->oContact =& CreateObject('dcl.dbContact');
			$this->oContactPhone =& CreateObject('dcl.dbContactPhone');
			$this->oContactEmail =& CreateObject('dcl.dbContactEmail');
			$this->oContactUrl =& CreateObject('dcl.dbContactUrl');
		}

		if ($this->oContact->Load($id) != -1)
		{
			$aRetVal['name'] = sprintf('%s %s', $this->oContact->first_name, $this->oContact->last_name);
			$aRetVal['last_name'] = $this->oContact->last_name;
			$aRetVal['first_name'] = $this->oContact->first_name;
			
			if ($this->oContactPhone->GetPrimaryPhone($id))
			{
				$aRetVal['phonetype'] = $this->oContactPhone->f(0);
				$aRetVal['phone'] = $this->oContactPhone->f(1);
			}

			if ($this->oContactEmail->GetPrimaryEmail($id))
			{
				$aRetVal['emailtype'] = $this->oContactEmail->f(0);
				$aRetVal['email'] = $this->oContactEmail->f(1);
			}
			
			if ($this->oContactUrl->GetPrimaryUrl($id))
			{
				$aRetVal['urltype'] = $this->oContactUrl->f(0);
				$aRetVal['url'] = $this->oContactUrl->f(1);
			}
			
			$aOrg = $this->oContact->GetFirstOrg($id);
			if (count($aOrg) > 0)
			{
				$aRetVal['org_id'] = $aOrg['org_id'];
				$aRetVal['org_name'] = $aOrg['name'];
			}
		}

		return $aRetVal;
	}

	function &GetOrganization($id)
	{
		$aRetVal = array('name' => '', 'phonetype' => '', 'phone' => '', 'emailtype' => '', 'email' => '', 'urltype' => '', 'url' => '');
		if (!$this->IsValidID($id))
			return $aRetVal;

		if ($this->oOrg == null)
		{
			$this->oOrg =& CreateObject('dcl.dbOrg');
			$this->oOrgPhone =& CreateObject('dcl.dbOrgPhone');
			$this->oOrgEmail =& CreateObject('dcl.dbOrgEmail');
			$this->oOrgUrl =& CreateObject('dcl.dbOrgUrl');
			$this->oOrgAddr =& CreateObject('dcl.dbOrgAddr');
		}

		if ($this->oOrg->Load($id) != -1)
		{
			$aRetVal['name'] = $this->oOrg->name;

			if ($this->oOrgPhone->GetPrimaryPhone($id))
			{
				$aRetVal['phonetype'] = $this->oOrgPhone->f(0);
				$aRetVal['phone'] = $this->oOrgPhone->f(1);
			}

			if ($this->oOrgEmail->GetPrimaryEmail($id))
			{
				$aRetVal['emailtype'] = $this->oOrgEmail->f(0);
				$aRetVal['email'] = $this->oOrgEmail->f(1);
			}
			
			if ($this->oOrgUrl->GetPrimaryUrl($id))
			{
				$aRetVal['urltype'] = $this->oOrgUrl->f(0);
				$aRetVal['url'] = $this->oOrgUrl->f(1);
			}
			
			if ($this->oOrgAddr->GetPrimaryAddress($id))
			{
				$aRetVal['addrtype'] = $this->oOrgAddr->f('addr_type_name');
				$aRetVal['addr'] = '';

				if ($this->oOrgAddr->f('add1') != '')
					$aRetVal['addr'] .= $this->oOrgAddr->f('add1');

				if ($this->oOrgAddr->f('add2') != '')
				{
					if ($aRetVal['addr'] != '')
						$aRetVal['addr'] .= "\n";
						
					$aRetVal['addr'] .= $this->oOrgAddr->f('add2');
				}
				
				$sCityStateZip = '';
				$aCityStateZip = array('city' => ', ', 'state' => '   ', 'zip' => ' ', 'country' => '');
				foreach ($aCityStateZip as $sKey => $sSuffix)
				{
					if ($this->oOrgAddr->f($sKey) != '')
					{
						$sCityStateZip .= $this->oOrgAddr->f($sKey) . $sSuffix;
					}
				}
				
				if ($sCityStateZip != '')
				{
					if ($aRetVal['addr'] != '')
						$aRetVal['addr'] .= "\n";
						
					$aRetVal['addr'] .= $sCityStateZip;
				}
			}
		}

		return $aRetVal;
	}

	function GetAction($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oAction == null)
			$this->oAction =& CreateObject('dcl.dbActions');

		if ($this->oAction->Load($id) == -1)
			return $this->TriggerError("Could not find action ID $id");

		return $this->oAction->name;
	}

	function GetWorkOrder($jcn, $seq)
	{
		if (!$this->IsValidID($jcn) || !$this->IsValidID($seq))
			return '';

		if ($this->oWorkOrder == null)
			$this->oWorkOrder =& CreateObject('dcl.dbWorkorders');

		if ($this->oWorkOrder->Load($jcn, $seq) == -1)
			return $this->TriggerError("Could not find workorder ID $jcn-$seq");

		return $this->oWorkOrder->summary;
	}

	function GetTicket($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oTicket == null)
			$this->oTicket =& CreateObject('dcl.dbTickets');

		if ($this->oTicket->Load($id) == -1)
			return $this->TriggerError("Could not find ticket ID $id");

		return $this->oTicket->summary;
	}
	
	function GetDepartment($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oDepartment == null)
			$this->oDepartment =& CreateObject('dcl.dbDepartments');

		if ($this->oDepartment->Load($id) == -1)
			return $this->TriggerError("Could not find department ID $id");

		return $this->oDepartment->name;
	}
	
	function GetTags($entity_id, $entity_key_id, $entity_key_id2 = 0)
	{
		if (!$this->IsValidID($entity_id) || !$this->IsValidID($entity_key_id))
			return '';
			
		if ($entity_id == DCL_ENTITY_WORKORDER && !$this->IsValidID($entity_key_id2))
			return '';
			
		if ($this->oTag == null)
			$this->oTag =& CreateObject('dcl.dbEntityTag');
			
		return $this->oTag->getTagsForEntity($entity_id, $entity_key_id, $entity_key_id2);
	}
	
	function GetHotlist($entity_id, $entity_key_id, $entity_key_id2 = 0)
	{
		if (!$this->IsValidID($entity_id) || !$this->IsValidID($entity_key_id))
			return '';
			
		if ($entity_id == DCL_ENTITY_WORKORDER && !$this->IsValidID($entity_key_id2))
			return '';
			
		if ($this->oHotlist == null)
			$this->oHotlist =& CreateObject('dcl.dbEntityHotlist');
			
		return $this->oHotlist->getTagsForEntity($entity_id, $entity_key_id, $entity_key_id2);
	}
	
	function GetLastTimeCard($jcn, $seq)
	{
		global $g_oSec;
		
		if (!$this->IsValidID($jcn) || !$this->IsValidID($seq))
			return '';

		if ($this->oTimeCard == null)
			$this->oTimeCard =& CreateObject('dcl.dbTimeCards');

		if ($this->oTimeCard->LoadLast($jcn, $seq, $g_oSec->IsPublicUser()) == -1)
			return null;
			
		$aTimeCard = $this->oTimeCard->Record;

		return $aTimeCard;
	}
}
?>