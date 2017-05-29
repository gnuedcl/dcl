<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2014 Free Software Foundation
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

class htmlAudit
{
	var $aAudit;
	var $aAuditAccount;
	var $aAuditProject;
	var $aAuditWorkOrder;
	var $oMeta;

	function __construct()
	{
		$this->aAudit = array();
		$this->aAuditAccount = array();
		$this->aAuditProject = array();
		$this->oMeta = new DisplayHelper();
	}

	function show()
	{
		global $dcl_info, $g_oSec;

		commonHeader();

		if (!$g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_REPORT))
			throw new PermissionDeniedException();
			
		if (($type = Filter::ToInt($_REQUEST['type'])) === null ||
		    ($id = Filter::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}

		$oSmarty = new SmartyHelper();
		$oAudit = new AuditModel();

		switch ($type)
		{
			case DCL_ENTITY_WORKORDER:
			    if (($id2 = Filter::ToInt($_REQUEST['id2'])) === null)
			    {
					throw new InvalidDataException();
				}
				
				$this->aAudit = $oAudit->LoadDiff('WorkOrderModel', array('jcn' => $id, 'seq' => $id2));
				$oSmarty->assign('VAL_ID', sprintf('%d-%d', $id, $id2));
				$oSmarty->assign('VAL_SUMMARY', $this->oMeta->GetWorkOrder($id, $id2));
				$oSmarty->assign('LNK_BACK', menuLink('', "menuAction=WorkOrder.Detail&jcn=$id&seq=$id2"));

				$oAccount = new WorkOrderOrganizationModel();
				$this->aAuditAccount = $oAccount->AuditWorkOrderList($id, $id2);

				$oProject = new ProjectMapModel();
				$this->aAuditProject = $oProject->AuditWorkOrderList($id, $id2);
				break;
			case DCL_ENTITY_PROJECT:
				$this->aAudit = $oAudit->LoadDiff('ProjectsModel', array('projectid' => $id));
				$oSmarty->assign('VAL_ID', $id);
				$oSmarty->assign('VAL_SUMMARY', $this->oMeta->GetProject($id));
				$oSmarty->assign('LNK_BACK', menuLink('', "menuAction=Project.Detail&id=$id&wostatus=0"));

				$oProject = new ProjectMapModel();
				$this->aAuditWorkOrder = $oProject->AuditProjectList($id);
				break;
			case DCL_ENTITY_TICKET:
				$this->aAudit = $oAudit->LoadDiff('TicketsModel', array('ticketid' => $id));
				$oSmarty->assign('VAL_ID', $id);
				$oSmarty->assign('VAL_SUMMARY', $this->oMeta->GetTicket($id));
				$oSmarty->assign('LNK_BACK', menuLink('', "menuAction=boTickets.view&ticketid=$id"));
				break;
		}

		$this->prepareForDisplay();

		$oSmarty->assignByRef('VAL_AUDITTRAIL', $this->aAudit);
		$oSmarty->assignByRef('VAL_AUDITACCOUNT', $this->aAuditAccount);
		$oSmarty->assignByRef('VAL_AUDITPROJECT', $this->aAuditProject);
		$oSmarty->assignByRef('VAL_AUDITWORKORDER', $this->aAuditWorkOrder);

		$oSmarty->Render('AuditTrail.tpl');
	}

	function prepareForDisplay()
	{
		foreach ($this->aAudit as $iVersion => $aDiff)
		{
			$this->aAudit[$iVersion]['audit_by'] = $this->oMeta->GetPersonnel($this->aAudit[$iVersion]['audit_by']);

			for ($i = 0; $i < count($this->aAudit[$iVersion]['changes']); $i++)
			{
				$sField = $this->aAudit[$iVersion]['changes'][$i]['field'];
				if ($sField == 'contact_id')
				{
					$aContactOld = $this->oMeta->GetContact($this->aAudit[$iVersion]['changes'][$i]['old']);
					$aContactNew = $this->oMeta->GetContact($this->aAudit[$iVersion]['changes'][$i]['new']);

					$this->aAudit[$iVersion]['changes'][$i]['old'] = $aContactOld['name'];
					$this->aAudit[$iVersion]['changes'][$i]['new'] = $aContactNew['name'];
					continue;
				}

				if ($sField == 'org_id' || $sField == 'account' || $sField == 'account_id')
				{
					$aOrgOld = $this->oMeta->GetOrganization($this->aAudit[$iVersion]['changes'][$i]['old']);
					$aOrgNew = $this->oMeta->GetOrganization($this->aAudit[$iVersion]['changes'][$i]['new']);

					$this->aAudit[$iVersion]['changes'][$i]['old'] = $aOrgOld['name'];
					$this->aAudit[$iVersion]['changes'][$i]['new'] = $aOrgNew['name'];
					continue;
				}

				$sDecodeFunction = '';
				switch ($sField)
				{
					case 'audit_by':
					case 'responsible':
					case 'closedby':
						$sDecodeFunction = 'GetPersonnel';
						break;
					case 'status':
						$sDecodeFunction = 'GetStatus';
						break;
					case 'wo_type_id':
						$sDecodeFunction = 'GetWorkOrderType';
						break;
					case 'severity':
					case 'type':
						$sDecodeFunction = 'GetSeverity';
						break;
					case 'priority':
						$sDecodeFunction = 'GetPriority';
						break;
					case 'product':
						$sDecodeFunction = 'GetProduct';
						break;
					case 'module_id':
						$sDecodeFunction = 'GetModule';
						break;
					case 'project':
					case 'projectid':
					case 'parentprojectid':
						$sDecodeFunction = 'GetProject';
						break;
					case 'entity_source_id':
						$sDecodeFunction = 'GetSource';
						break;
				}

				if ($sDecodeFunction != '')
				{
					$this->aAudit[$iVersion]['changes'][$i]['old'] = $this->oMeta->$sDecodeFunction($this->aAudit[$iVersion]['changes'][$i]['old']);
					$this->aAudit[$iVersion]['changes'][$i]['new'] = $this->oMeta->$sDecodeFunction($this->aAudit[$iVersion]['changes'][$i]['new']);
				}
			}
		}
	}
}
