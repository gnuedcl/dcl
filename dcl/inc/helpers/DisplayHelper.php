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

class DisplayHelper
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

	public function __construct()
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

	public function IsValidID($id)
	{
		return (isset($id) && $id !== null && $id != '' && is_numeric($id) && $id > 0);
	}
	
	public function ShowError($sMessage)
	{
		ShowError($sMessage);
		return null;
	}

	public function GetStatus($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oStatus == null)
			$this->oStatus = new StatusModel();

		if ($this->oStatus->Load($id, false) == -1)
			return $this->ShowError("Could not find status ID $id");

		return $this->oStatus->name;
	}

	public function GetPersonnel($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oPersonnel == null)
			$this->oPersonnel = new PersonnelModel();

		if ($this->oPersonnel->Load($id, false) == -1)
			return $this->ShowError("Could not find personnel ID $id");

		return $this->oPersonnel->short;
	}

	public function GetSeverity($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oSeverity == null)
			$this->oSeverity = new SeverityModel();

		if ($this->oSeverity->Load(array('id' => $id), false) == -1)
			return $this->ShowError("Could not find severity ID $id");

		return $this->oSeverity->name;
	}

	public function GetPriority($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oPriority == null)
			$this->oPriority = new PriorityModel();

		if ($this->oPriority->Load($id, false) == -1)
			return $this->ShowError("Could not find priority ID $id");

		return $this->oPriority->name;
	}

	public function GetProduct($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oProduct == null)
			$this->oProduct = new ProductModel();

		if ($this->oProduct->Load($id, false) == -1)
			return $this->ShowError("Could not find product ID $id");

		return $this->oProduct->name;
	}

	public function GetProductVersion($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oProductVersion == null)
			$this->oProductVersion = new ProductVersionModel();

		if ($this->oProductVersion->Load($id, false) == -1)
			return $this->ShowError("Could not find product version ID $id");

		return $this->oProductVersion->product_version_text;
	}

	public function GetProject($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oProject == null)
			$this->oProject = new ProjectsModel();

		if ($this->oProject->Load(array('projectid' => $id), false) == -1)
			return $this->ShowError("Could not find project ID $id");

		return $this->oProject->name;
	}

	public function GetWorkOrderType($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oWorkOrderType == null)
			$this->oWorkOrderType = new WorkOrderTypeModel();

		if ($this->oWorkOrderType->Load(array('wo_type_id' => $id)) == -1)
			return $this->ShowError("Could not find work order type ID $id");

		return $this->oWorkOrderType->type_name;
	}

	public function GetModule($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oModule == null)
			$this->oModule = new ProductModulesModel();

		if ($this->oModule->Load($id, false) == -1)
			return $this->ShowError("Could not find module ID $id");

		return $this->oModule->module_name;
	}

	public function GetSource($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oSource == null)
			$this->oSource = new EntitySourceModel();

		if ($this->oSource->Load(array('entity_source_id' => $id)) == -1)
			return $this->ShowError("Could not find source ID $id");

		return $this->oSource->entity_source_name;
	}

	public function &GetContact($id)
	{
		$aRetVal = array('name' => '', 'phonetype' => '', 'phone' => '', 'emailtype' => '', 'email' => '', 'urltype' => '', 'url' => '', 'org_id' => '', 'org_name' => '');
		if (!$this->IsValidID($id))
			return $aRetVal;

		if ($this->oContact == null)
		{
			$this->oContact = new ContactModel();
			$this->oContactPhone = new ContactPhoneModel();
			$this->oContactEmail = new ContactEmailModel();
			$this->oContactUrl = new ContactUrlModel();
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

	public function &GetOrganization($id)
	{
		$aRetVal = array('name' => '', 'phonetype' => '', 'phone' => '', 'emailtype' => '', 'email' => '', 'urltype' => '', 'url' => '');
		if (!$this->IsValidID($id))
			return $aRetVal;

		if ($this->oOrg == null)
		{
			$this->oOrg = new OrganizationModel();
			$this->oOrgPhone = new OrganizationPhoneModel();
			$this->oOrgEmail = new OrganizationEmailModel();
			$this->oOrgUrl = new OrganizationUrlModel();
			$this->oOrgAddr = new OrganizationAddressModel();
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

	public function GetAction($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oAction == null)
			$this->oAction = new ActionModel();

		if ($this->oAction->Load(array('id' => $id)) == -1)
			return $this->ShowError("Could not find action ID $id");

		return $this->oAction->name;
	}

	public function GetWorkOrder($jcn, $seq)
	{
		if (!$this->IsValidID($jcn) || !$this->IsValidID($seq))
			return '';

		if ($this->oWorkOrder == null)
			$this->oWorkOrder = new WorkOrderModel();

		if ($this->oWorkOrder->LoadByIdSeq($jcn, $seq) == -1)
			return $this->ShowError("Could not find workorder ID $jcn-$seq");

		return $this->oWorkOrder->summary;
	}

	public function GetTicket($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oTicket == null)
			$this->oTicket = new TicketsModel();

		if ($this->oTicket->Load($id) == -1)
			return $this->ShowError("Could not find ticket ID $id");

		return $this->oTicket->summary;
	}
	
	public function GetDepartment($id)
	{
		if (!$this->IsValidID($id))
			return '';

		if ($this->oDepartment == null)
			$this->oDepartment = new DepartmentModel();

		if ($this->oDepartment->Load(array('id' => $id)) == -1)
			return $this->ShowError("Could not find department ID $id");

		return $this->oDepartment->name;
	}
	
	public function GetTags($entity_id, $entity_key_id, $entity_key_id2 = 0)
	{
		if (!$this->IsValidID($entity_id) || !$this->IsValidID($entity_key_id))
			return '';
			
		if ($entity_id == DCL_ENTITY_WORKORDER && !$this->IsValidID($entity_key_id2))
			return '';
			
		if ($this->oTag == null)
			$this->oTag = new EntityTagModel();
			
		return $this->oTag->getTagsForEntity($entity_id, $entity_key_id, $entity_key_id2);
	}
	
	public function GetHotlist($entity_id, $entity_key_id, $entity_key_id2 = 0)
	{
		if (!$this->IsValidID($entity_id) || !$this->IsValidID($entity_key_id))
			return '';
			
		if ($entity_id == DCL_ENTITY_WORKORDER && !$this->IsValidID($entity_key_id2))
			return '';
			
		if ($this->oHotlist == null)
			$this->oHotlist = new EntityHotlistModel();
			
		return $this->oHotlist->getTagsForEntity($entity_id, $entity_key_id, $entity_key_id2);
	}
	
	public function GetHotlistWithPriority($entity_id, $entity_key_id, $entity_key_id2 = 0)
	{
		if (!$this->IsValidID($entity_id) || !$this->IsValidID($entity_key_id))
			return array();

		if ($entity_id == DCL_ENTITY_WORKORDER && !$this->IsValidID($entity_key_id2))
			return array();

		if ($this->oHotlist == null)
			$this->oHotlist = new EntityHotlistModel();

		return $this->oHotlist->getTagsWithPriorityForEntity($entity_id, $entity_key_id, $entity_key_id2);
	}

	public function GetLastTimeCard($jcn, $seq)
	{
		global $g_oSec;
		
		if (!$this->IsValidID($jcn) || !$this->IsValidID($seq))
			return '';

		if ($this->oTimeCard == null)
			$this->oTimeCard = new TimeCardsModel();

		if ($this->oTimeCard->LoadLast($jcn, $seq, $g_oSec->IsPublicUser()) == -1)
			return null;
			
		$aTimeCard = $this->oTimeCard->Record;

		return $aTimeCard;
	}
}
