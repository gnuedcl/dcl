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

LoadStringResource('db');
class WorkOrderModel extends DbProvider
{
	var $aContactOrgs;
	var $aOrgs;
	var $iWoid;
	var $iSeq;
	
	public function __construct()
	{
		parent::__construct();
		$this->TableName = 'workorders';
		LoadSchema($this->TableName);
		$this->AuditEnabled = true;
		
		$this->aContactOrgs = array();
		$this->aOrgs = array();
		$this->iWoid = -1;
		$this->iSeq = -1;

		parent::Clear();
	}

	public function Add()
	{
		global $dcl_info, $g_oSec;

		// Fill in the blanks if this is a stripped down workorder input
		// by someone who cannot assign it due to insufficient security
		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ASSIGN))
		{
			if ($this->responsible == 0)
			{
				$objProduct = new ProductModel();
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

	public function Edit($aIgnoreFields = '')
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

	public function Delete($aID)
	{
		$this->BeginTransaction();
		
		try
		{
			ProjectsModel::RemoveTask($this->jcn, $this->seq, true);

			$workOrderOrgModel = new WorkOrderOrganizationModel();
			$workOrderOrgModel->DeleteByWorkOrder($this->jcn, $this->seq);

			$workOrderTaskModel = new WorkOrderTaskModel();
			$workOrderTaskModel->DeleteByWorkOrder($this->jcn, $this->seq);

			$entityTagModel = new EntityTagModel();
			$entityTagModel->deleteByEntity(DCL_ENTITY_WORKORDER, $this->jcn, $this->seq);

			$entityHotlistModel = new EntityHotlistModel();
			$entityHotlistModel->deleteByEntity(DCL_ENTITY_WORKORDER, $this->jcn, $this->seq);

			$fileHelper = new FileHelper();
			$fileHelper->DeleteAttachments(DCL_ENTITY_WORKORDER, $this->jcn, $this->seq);

			$query = 'DELETE FROM timecards WHERE jcn=' . $this->jcn . ' AND seq=' . $this->seq;
			$this->Execute($query);

			$this->Audit(array('jcn' => $this->jcn, 'seq' => $this->seq));

			$query = 'DELETE FROM workorders WHERE jcn=' . $this->jcn . ' AND seq=' . $this->seq;
			$this->Execute($query);

			$this->EndTransaction();
		}
		catch (Exception $ex)
		{
			$this->RollbackTransaction();
			throw $ex;
		}
	}

	public function LoadByIdSeq($jcn, $seq)
	{
		global $g_oSec;

		if (!isset($jcn) || !is_numeric($jcn) || $jcn < 1 || !isset($seq) || !is_numeric($seq) || $seq < 1)
			throw new InvalidDataException("Invalid work order ID passed to Load: $jcn-$seq");

		$this->Clear();
		$oRetVal = parent::Load(array('jcn' => $jcn, 'seq' => $seq));
		if ($oRetVal !== -1)
		{
			$bIsPublic = false;
			if (($g_oSec->IsPublicUser() || $g_oSec->IsOrgUser()) && !$this->CanView($this, DCLID, $bIsPublic))
			{
				throw new PermissionDeniedException();
			}
		}
		
		return $oRetVal;
	}

	public function LoadSequencesExcept($jcn, $seq)
	{
		if (!$this->Query("SELECT seq FROM workorders WHERE jcn=$jcn AND seq != $seq"))
			return -1;
	}

	public function IsInAProject()
	{
		$obj = new DbProvider;
		if ($obj->Query('SELECT count(*) FROM projectmap WHERE jcn=' . $this->jcn . ' and seq in (0,' . $this->seq . ')') == -1)
			return false;

		$obj->next_record();
		return ($obj->f(0) > 0);
	}

	public function NewID()
	{
		$this->BeginTransaction();
		$this->Insert('INSERT INTO dcl_wo_id (seq) VALUES (1)');
		$wo_id = $this->GetLastInsertID('dcl_wo_id');
		$this->EndTransaction();

		return $wo_id;
	}

	public function NewSequence($wo_id)
	{
		$this->BeginTransaction();
		$this->Execute(sprintf('UPDATE dcl_wo_id SET seq = seq + 1 WHERE jcn = %d', $wo_id));
		$seq = $this->ExecuteScalar(sprintf('SELECT seq FROM dcl_wo_id WHERE jcn = %d', $wo_id));
		$this->EndTransaction();

		return $seq;
	}
	
	public function CanView(&$obj, $iPersonnelID, &$bIsPublic)
	{
		global $dcl_info, $g_oSession, $g_oSec;
		
		$bCanView = true;
		$bIsPublic = false;
		$oUR = new UserRoleModel();
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
					$oDBProduct = new ProductModel();
					if ($oDBProduct->Load($obj->product) !== -1)
					{
						$bCanView = ($oDBProduct->is_public == 'Y');
						if ($bCanView)
						{
							$aProducts = explode(',', $g_oSession->Value('org_products'));
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
					$oWOA = new WorkOrderOrganizationModel();
					if ($oWOA->Load($obj->jcn, $obj->seq) != -1)
					{
						$this->iWoid = $obj->jcn;
						$this->iSeq = $obj->seq;
						$this->aOrgs = array();
						while ($oWOA->next_record())
						{
							array_push($this->aOrgs, $oWOA->f(2));
						}
						
						$bCanView = (count($this->aOrgs) > 0);
					}
					else
						$bCanView = false;
				}
				
				if (!$bCanView)
					return false;
					
				$oDB = new DbProvider;
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
	
	/**
	 *
	 * @param type $days
	 * @param type $dateFrom
	 * @param type $product
	 * @return LineGraphImageHelper 
	 */
	public function Graph($days, $dateFrom, $product)
	{
		$lineGraphImageHelper = new LineGraphImageHelper();
		$workOrderModel = new WorkOrderModel();
		
		$beginDate = new TimestampHelper();
		$endDate = new TimestampHelper();
		$testDate = new DateHelper();
		$testTS = new TimestampHelper();

		$endDate->SetFromDisplay($dateFrom . ' 23:59:59');
		$beginDate->SetFromDisplay($dateFrom . ' 00:00:00');
		$beginDate->time -= (($days - 1) * 86400);
		
		$query = sprintf('SELECT %1$s, %2$s FROM workorders WHERE %3$s (createdon BETWEEN %4$s AND %5$s OR closedon BETWEEN %4$s AND %5$s)',
				$workOrderModel->ConvertTimestamp('createdon', 'createdon'),
				$workOrderModel->ConvertTimestamp('createdon', 'createdon'),
				$product > 0 ? 'product = ' . $product . ' AND' : '',
				$workOrderModel->DisplayToSQL($beginDate->ToDisplay()),
				$workOrderModel->DisplayToSQL($endDate->ToDisplay())
				);

		$workOrderModel->Query($query);

		$lineGraphImageHelper->data[0] = array(); // Open
		$lineGraphImageHelper->data[1] = array(); // Closed

		$daysBack = array();
		$testDate->time = $beginDate->time;
		for ($i = 0; $i < $days; $i++)
		{
			$daysBack[$i] = $testDate->time;
			// Set the relevant object properties while we're at it
			$lineGraphImageHelper->line_captions_x[$i] = date('m/d', $testDate->time);
			$lineGraphImageHelper->data[0][$i] = 0;
			$lineGraphImageHelper->data[1][$i] = 0;

			$testDate->time += 86400;
		}

		while ($workOrderModel->next_record())
		{
			$iTime = 0;
			for ($y = 0; $y < 2; $y++)
			{
				if ($y == 0)
				{
					$testTS->SetFromDB($workOrderModel->f($y));
					$iTime = $testTS->time;
				}
				else
				{
					$testDate->SetFromDB($workOrderModel->f($y));
					$iTime = $testDate->time;
				}

				$j = $days - 1;
				while ($j >= 0)
				{
					if ($iTime >= $daysBack[$j])
					{
						if (!IsSet($lineGraphImageHelper->data[$y][$j]))
							$lineGraphImageHelper->data[$y][$j] = 0;
						$lineGraphImageHelper->data[$y][$j]++;
						break;
					}

					$j--;
				}
			}
		}

		$lineGraphImageHelper->title = STR_BO_WOGRAPHTITLE;
		if ($product > 0)
		{
			$oDB = new ProductModel();
			if ($oDB->Load($product) != -1)
				$lineGraphImageHelper->title .= ' ' . $oDB->name;
		}

		$lineGraphImageHelper->caption_y = STR_BO_WOGRAPHCAPTIONY;
		$lineGraphImageHelper->caption_x = STR_BO_GRAPHCAPTIONX;
		$lineGraphImageHelper->num_lines_y = 15;
		$lineGraphImageHelper->num_lines_x = $days;
		$lineGraphImageHelper->colors = array('red', 'blue');
		
		return $lineGraphImageHelper;
	}
	
	public function ListSelected($selected)
	{
		$query = 'select a.jcn, a.seq, b.short, c.name, e.name, a.summary from workorders a ' . $this->JoinKeyword . ' personnel b on a.responsible = b.id ';
		$query .= $this->JoinKeyword . ' statuses c on a.status = c.id left join projectmap d on a.jcn = d.jcn and (a.seq = d.seq or d.seq = 0) ';
		$query .= 'left join dcl_projects e on d.projectid = e.projectid ';
		$query .= 'where (';

		$bFirst = true;
		foreach ($selected as $jcnseq)
		{
			list($jcn, $seq) = explode('.', $jcnseq);
			if (Filter::ToInt($jcn) === null || Filter::ToInt($seq) === null)
				continue;
				
			if ($bFirst)
				$bFirst = false;
			else
				$query .= ' or ';

			$query .= "(a.jcn=$jcn and a.seq=$seq)";
		}

		if ($bFirst)
			return;
			
		$query .= ')';
		
		return $this->Query($query);
	}
}
