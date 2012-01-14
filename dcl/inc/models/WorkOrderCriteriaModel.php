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

class WorkOrderCriteriaModel
{
	public $Personnel;
	public $Status;
	public $IsPublic;
	public $CreatedOn;
	public $ClosedOn;
	public $StatusOn;
	public $LastActionOn;
	public $DeadlineOn;
	public $EstStartOn;
	public $EstEndOn;
	public $StartOn;
	public $ModuleId;
	public $SearchText;
	public $Tags;
	public $Hotlist;
	public $Columns;
	public $Groups;
	public $Order;
	public $ColumnHdrs;
	public $Title;

	public $Account;
	public $EntitySourceId;
	public $Severity;
	public $Priority;
	public $DclStatusType;
	public $Product;
	public $Department;
	public $Project;
	public $WoTypeId;

	public $DateFrom;
	public $DateTo;
	
	public $SearchCreateBy;
	public $SearchClosedBy;
	public $SearchResponsible;
	
	public $SearchSummary;
	public $SearchNotes;
	public $SearchDescription;
	
	public $SearchCreatedOn;
	public $SearchClosedOn;
	public $SearchStatusOn;
	public $SearchLastActionOn;
	
	public $SearchDeadlineOn;
	public $SearchEstStartOn;
	public $SearchEstEndOn;
	public $SearchStartOn;
	
	public function __construct()
	{
		$this->SearchCreateBy = false;
		$this->SearchClosedBy = false;
		$this->SearchResponsible = false;
		$this->SearchSummary = false;
		$this->SearchNotes = false;
		
		$this->SearchDescription = false;
		$this->SearchCreatedOn = false;
		$this->SearchClosedOn = false;
		$this->SearchStatusOn = false;
		$this->SearchLastActionOn = false;
		
		$this->SearchDeadlineOn = false;
		$this->SearchEstStartOn = false;
		$this->SearchEstEndOn = false;
		$this->SearchStartOn = false;
	}
	
	/**
	 *
	 * @return WorkOrderSqlQueryHelper 
	 */
	public function GetSqlQueryHelper()
	{
		global $g_oSession;
		
		$view = new WorkOrderSqlQueryHelper();

		if (strlen($this->ColumnHdrs) > 0)
			$this->ColumnHdrs = explode(',', $this->ColumnHdrs);
		else
			$this->ColumnHdrs = array();

		if (strlen($this->Columns) > 0)
			$this->Columns = explode(',', $this->Columns);
		else
			$this->Columns = array();

		if (strlen($this->Groups) > 0)
			$this->Groups = explode(',', $this->Groups);
		else
			$this->Groups = array();

		if (strlen($this->Order) > 0)
			$this->Order = explode(',', $this->Order);
		else
			$this->Order = array();

		if (count($this->Personnel) > 0 || count($this->Department) > 0)
		{
			$fieldList = array('responsible', 'createby', 'closedby');
			$bStrippedDepartments = false;
			$pers_sel = array();
			
			foreach ($fieldList as $field)
			{
				if ($field == 'responsible' && !$this->SearchResponsible)
					continue;
				
				if ($field == 'createby' && !$this->SearchCreateBy)
					continue;
				
				if ($field == 'closedby' && !$this->SearchClosedBy)
					continue;
					
				if (count($this->Personnel) > 0)
				{
					if (!$bStrippedDepartments)
					{
						$bStrippedDepartments = true;

						// Have actual personnel?  If so, only set personnel for their associated departments instead of the department
						// then unset the department from the array
						foreach ($this->Personnel as $encoded_pers)
						{
							list($dpt_id, $pers_id) = explode(',', $encoded_pers);
							$pers_sel[] = $pers_id;
							if (count($this->Department) > 0 && in_array($dpt_id, $this->Department))
							{
								foreach ($this->Department as $key => $department_id)
								{
									if ($department_id == $dpt_id)
									{
										unset($this->Department[$key]);
										break;
									}
								}
							}
						}
					}

					$pers_sel = Filter::ToIntArray($pers_sel);
					if (count($pers_sel) > 0)
						$view->AddDef('filter', $field, $pers_sel);
				}

				if (count($this->Department) > 0)
					$view->AddDef('filter', $field . '.department', $this->Department);
			}
		}
		
		if (count($this->Priority) > 0)
			$view->AddDef('filter', 'priority', $this->Priority);

		if (count($this->Severity) > 0)
			$view->AddDef('filter', 'severity', $this->Severity);

		if (count($this->WoTypeId) > 0)
			$view->AddDef('filter', 'wo_type_id', $this->WoTypeId);

		if (count($this->EntitySourceId) > 0)
			$view->AddDef('filter', 'entity_source_id', $this->EntitySourceId);

		if (count($this->Tags) > 0)
			$view->AddDef('filter', 'dcl_tag.tag_desc', $this->Tags);

		if (count($this->Hotlist) > 0)
			$view->AddDef('filter', 'dcl_hotlist.hotlist_tag', $this->Hotlist);

		if (count($this->IsPublic) > 0)
		{
			foreach ($this->IsPublic as $publicValue)
			{
				if ($publicValue == 'Y' || $publicValue == 'N')
					$view->AddDef('filter', 'is_public', "'" . $publicValue . "'");
			}
		}

		if (count($this->ModuleId) > 0)
		{
			// Have modules?  If so, only set module IDs for their associated products instead of the product ID
			// then unset the product id from the array
			$module = array();
			foreach ($this->ModuleId as $encoded_mod)
			{
				list($mod_prod_id, $mod_id) = explode(',', $encoded_mod);
				$module[count($module)] = $mod_id;
				if (count($this->Product) > 0 && in_array($mod_prod_id, $this->Product))
				{
					foreach ($this->Product as $key => $product_id)
					{
						if ($product_id == $mod_prod_id)
						{
							unset($this->Product[$key]);
							break;
						}
					}
				}
			}

			$view->AddDef('filter', 'module_id', $module);
		}

		$g_oSession->Unregister('showBM');
		if (count($this->Product) > 0)
		{
			$view->AddDef('filter', 'product', $this->Product);
		}

		$g_oSession->Edit();

		if (($this->DclStatusType = Filter::ToIntArray($this->DclStatusType)) === null)
			$this->DclStatusType = array();
			
		if (count($this->Status) > 0)
		{
			// Have statuses?  If so, only set status IDs for their associated types instead of the status type ID
			// then unset the status type id from the array
			$statuses = array();
			
			foreach ($this->Status as $encoded_status)
			{
				list($type_id, $status_id) = explode(',', $encoded_status);
				if (($type_id = Filter::ToInt($type_id)) !== null && ($status_id = Filter::ToInt($status_id)) !== null)
				{
					$statuses[count($statuses)] = $status_id;
					if (count($this->DclStatusType) > 0 && in_array($type_id, $this->DclStatusType))
					{
						foreach ($this->DclStatusType as $key => $status_type_id)
						{
							if ($status_type_id == $type_id)
							{
								unset($this->DclStatusType[$key]);
								break;
							}
						}
					}
				}
			}

			$view->AddDef('filter', 'status', $statuses);
		}

		if (count($this->Account) > 0)
			$view->AddDef('filter', 'dcl_wo_account.account_id', $this->Account);

		// already sanitized this one above
		if (count($this->DclStatusType) > 0)
			$view->AddDef('filter', 'statuses.dcl_status_type', $this->DclStatusType);

		if (count($this->Project) > 0)
			$view->AddDef('filter', 'dcl_projects.projectid', $this->Project);

		if ($this->DateFrom != '' || $this->DateTo != '')
		{
			if ($this->SearchCreatedOn)
				$view->AddDef('filterdate', 'createdon', array($this->DateFrom, $this->DateTo));

			if ($this->SearchClosedOn)
				$view->AddDef('filterdate', 'closedon', array($this->DateFrom, $this->DateTo));

			if ($this->SearchStatusOn)
				$view->AddDef('filterdate', 'statuson', array($this->DateFrom, $this->DateTo));

			if ($this->SearchLastActionOn)
				$view->AddDef('filterdate', 'lastactionon', array($this->DateFrom, $this->DateTo));

			if ($this->SearchDeadlineOn)
				$view->AddDef('filterdate', 'deadlineon', array($this->DateFrom, $this->DateTo));

			if ($this->SearchEstStartOn)
				$view->AddDef('filterdate', 'eststarton', array($this->DateFrom, $this->DateTo));

			if ($this->SearchEstEndOn)
				$view->AddDef('filterdate', 'estendon', array($this->DateFrom, $this->DateTo));

			if ($this->SearchStartOn)
				$view->AddDef('filterdate', 'starton', array($this->DateFrom, $this->DateTo));
		}

		if ($this->SearchText != '')
		{
			if ($this->SearchSummary)
				$view->AddDef('filterlike', 'summary', $this->SearchText);

			if ($this->SearchNotes)
				$view->AddDef('filterlike', 'notes', $this->SearchText);
			
			if ($this->SearchDescription)
				$view->AddDef('filterlike', 'description', $this->SearchText);
		}

		if (count($this->Columns) > 0)
			$view->AddDef('columns', '', $this->Columns);

		if (count($this->Groups) > 0)
		{
			$groupOrder = array();
			foreach ($this->Groups as $groupField)
			{
				if ($groupField == 'priorities.name')
					$groupOrder[] = 'priorities.weight';
				else if ($groupField == 'severities.name')
					$groupOrder[] = 'severities.weight';
				else if ($groupField == 'dcl_hotlist.hotlist_tag')
				{
					$groupOrder[] = $groupField;
					$groupOrder[] = 'dcl_entity_hotlist.sort';
				}
				else
					$groupOrder[] = $groupField;
			}

			$view->AddDef('groups', '', $groupOrder);
		}

		if (count($this->ColumnHdrs) > 0)
			$view->AddDef('columnhdrs', '', $this->ColumnHdrs);

		if (count($this->Order) > 0)
		{
			$orderOrder = array();
			foreach ($this->Order as $orderField)
			{
				if ($orderField == 'priorities.name')
					$orderOrder[] = 'priorities.weight';
				else if ($orderField == 'severities.name')
					$orderOrder[] = 'severities.weight';
				else if ($orderField == 'dcl_hotlist.hotlist_tag')
				{
					$orderOrder[] = $orderField;
					$orderOrder[] = 'dcl_entity_hotlist.sort';
				}
				else
					$orderOrder[] = $orderField;
			}

			$view->AddDef('order', '', $orderOrder);
		}
		else
			$view->AddDef('order', '', array('jcn', 'seq'));

		$view->style = 'report';
		$view->title = $this->Title;
		
		return $view;
	}
}
