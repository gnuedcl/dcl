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

class WorkOrderCsvImport
{
	private $displayHelper;
	
	public function Import($fileName)
	{
		$hFile = fopen($fileName, 'r');
		if(!$hFile)
			throw new Exception('Could not open file for import.');

		$newjcns = array();
		$line = 1;
		$fields = fgetcsv($hFile);
		
		$workOrderModel = new WorkOrderModel();
		$workOrderModelTemp = new WorkOrderModel();
		$projectMapModel = new ProjectMapModel();
		$watches = new boWatches();

		while ($data = fgetcsv($hFile))
		{
			$line++;
			$projectId = -1;
			$moduleId = -1;
			$workOrderModel->Clear();

			foreach ($data as $i => $val)
			{
				if (!is_numeric($val))
				{
					switch ($fields[$i])
					{
						case 'product':
							$newVal = $this->LookupId($workOrderModelTemp, 'products', $val);
							break;
						case 'module_id':
							$moduleId = $val;
							continue;
							break;
						case 'account':
							$newVal = $this->LookupId($workOrderModelTemp, 'accounts', $val);
							break;
						case 'wo_type_id':
							$newVal = $this->LookupId($workOrderModelTemp, 'dcl_wo_type', $val, 'wo_type_id', 'type_name');
							break;
						case 'entity_source_id':
							$newVal = $this->LookupId($workOrderModelTemp, 'dcl_entity_source', $val, 'entity_source_id', 'entity_source_name');
							break;
						case 'priority':
							$newVal = $this->LookupId($workOrderModelTemp, 'priorities', $val);
							break;
						case 'severity':
							$newVal = $this->LookupId($workOrderModelTemp, 'severities', $val);
							break;
						case 'responsible':
							$newVal = $this->LookupId($workOrderModelTemp, 'personnel', $val);
							break;
						case 'project':
							$newVal = $this->LookupId($workOrderModelTemp, 'dcl_projects', $val, 'projectid', 'name');
							$projectId = $newVal;
							break;
						default:
							$newVal = $val;
					}

					if ($newVal == -1)
					{
						// An error on mapping
						ShowError(sprintf(STR_BO_CSVMAPERR, $fields[$i], $line));
						continue 2;       // On to next line in the file
					}

					$val = $newVal;
				}
				else if ($fields[$i] == 'module_id')
				{
					$moduleId = $val;
				}
				else
				{
					$val = Filter::ToInt($val);
					if ($val === null || !$this->EntityExists($fields[$i], $val))
					{
						// An error on mapping
						ShowError(sprintf(STR_BO_CSVMAPERR, $fields[$i], $line));
						continue 2;       // On to next line in the file
					}
				}

				if ($fields[$i] != 'project' && $fields[$i] != 'module_id')
				{
					// This will ignore nonexisting members
					// Only works in PHP4 because Clear() initializes each field!
					if (isset($workOrderModel->$fields[$i]))
						$workOrderModel->$fields[$i] = $val;
				}
			}

			// Lookup module if specified
			if ($moduleId != -1)
			{
				if (is_numeric($moduleId))
				{
					// just verify this module exists for this product
					if ($workOrderModelTemp->ExecuteScalar("SELECT COUNT(*) FROM dcl_product_module WHERE product_module_id = $moduleId AND product_id = " . $workOrderModel->product) > 0)
						$workOrderModel->module_id = $moduleId;
				}
				else
					$workOrderModel->module_id = $this->LookupId($workOrderModelTemp, 'dcl_product_module', $moduleId, 'product_module_id', 'module_name', 'product_id', $workOrderModel->product);
			}

			$workOrderModel->createby = DCLID;
			$workOrderModel->Add();

			if ($workOrderModel->jcn > 0)
			{
				if ($projectId > 0)
				{
					// Project specified, so try to add it
					$projectMapModel->projectid = $projectId;
					$projectMapModel->jcn = $workOrderModel->jcn;
					$projectMapModel->seq = $workOrderModel->seq;
					$projectMapModel->Add();
				}

				// Add it to our new work order collection
				$newjcns[] = $workOrderModel->jcn;

				// Send notification
				$watches->sendNotification($workOrderModel, '4,1');
			}
		}

		return $newjcns;
	}
	
	private function LookupId($obj, $table, $value, $pk = 'id', $fd = 'short', $fd2 = '', $val2 = '')
	{
		$sSQL = "SELECT $pk FROM $table WHERE $fd = " . $obj->Quote($value);
		if ($fd2 != '' && $val2 != '')
			$sSQL .= " AND $fd2 = $val2";

		$obj->Query($sSQL);
		if($obj->next_record())
			return $obj->f(0);

		return -1;
	}
	
	private function EntityExists($field, $id)
	{
		if ($this->displayHelper === null)
			$this->displayHelper = new DisplayHelper();

		$oVal = null;
		switch ($field)
		{
			case 'account':
				$aRetVal = $this->displayHelper->GetOrganization($id);
				$oVal = $aRetVal['name'];
				break;
			case 'contact_id':
				$aRetVal = $this->displayHelper->GetContact($id);
				$oVal = $aRetVal['name'];
				break;
			case 'product':
				$oVal = $this->displayHelper->GetProduct($id);
				break;
			case 'wo_type_id':
				$oVal = $this->displayHelper->GetWorkOrderType($id);
				break;
			case 'priority':
				$oVal = $this->displayHelper->GetPriority($id);
				break;
			case 'severity':
				$oVal = $this->displayHelper->GetSeverity($id);
				break;
			case 'responsible':
				$oVal = $this->displayHelper->GetPersonnel($id);
				break;
			case 'project':
				$oVal = $this->displayHelper->GetProject($id);
				break;
			case 'entity_source_id':
				$oVal = $this->displayHelper->GetSource($id);
				break;
			default:
				$oVal = 1;
		}

		return ($oVal !== null && $oVal != '');
	}
}
