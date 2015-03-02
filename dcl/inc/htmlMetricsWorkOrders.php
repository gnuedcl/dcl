<?php
/*
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

LoadStringResource('wo');
class htmlMetricsWorkOrders
{
	var $oProduct;
	var $oProject;
	var $aProjects;

	function htmlMetricsWorkOrders()
	{
		$this->oProduct = new ProductModel();
		$this->oProject = new ProjectsModel();
		$this->aProjects = array();
	}

	function getparameters($needHdr = true)
	{
		if ($needHdr == true)
			commonHeader();

		$oProducts = new ProductHtmlHelper();
		$oProjects = new ProjectHtmlHelper();

		$oSmarty = new SmartyHelper();
		$oSmarty->assign('VAL_FORMACTION', menuLink());
		$oSmarty->assign('CMB_PRODUCTS', $oProducts->Select(IsSet($_REQUEST['products']) ? Filter::ToInt($_REQUEST['products']) : 0, 'products', 'name', 0, 8, false));
		$oSmarty->assign('CMB_PROJECTS', $oProjects->GetCombo(IsSet($_REQUEST['projects']) ? Filter::ToInt($_REQUEST['projects']) : 0, 'projects', 0, 8));
		$oSmarty->assign('VAL_BEGINDATE', IsSet($_REQUEST['begindate']) ? Filter::ToDate($_REQUEST['begindate']) : '');
		$oSmarty->assign('VAL_ENDDATE', IsSet($_REQUEST['enddate']) ? Filter::ToDate($_REQUEST['enddate']) : '');

		$oSmarty->Render('MetricsWorkOrders.tpl');
	}

	function showAll()
	{
		commonHeader();

		$productNames = '';
		if (isset($_REQUEST['products']))
		{
			$aProducts = Filter::ToIntArray($_REQUEST['products']);
			foreach ($aProducts as $product_id)
			{
				if ($this->oProduct->Load($product_id) == -1)
				    continue;
				    
				$productNames .= '<span class="badge alert-info">' . htmlspecialchars($this->oProduct->name, ENT_QUOTES, 'UTF-8') . '</span> ';
			}
		}

		$projectNames = '';
		if (isset($_REQUEST['projects']))
		{
			$aProjects = Filter::ToIntArray($_REQUEST['projects']);
			$oProjectMap = new ProjectMapModel();
			if (isset($_REQUEST['childProjects']) && $_REQUEST['childProjects'] == '1')
			{
				$this->aProjects = array();
				foreach ($aProjects as $iProjectID)
				{
					// already have the project (probably picked up by being child of another selected project already processed)
					if (in_array($iProjectID, $this->aProjects))
						continue;

					$this->aProjects[] = $iProjectID;

					$sChildProjects = $oProjectMap->GetProjectChildren($iProjectID);
					if ($sChildProjects != '')
						$this->aProjects = array_merge($this->aProjects, explode(',', $sChildProjects));
				}
			}
			else
			{
				$this->aProjects = $aProjects;
			}

			$oProjectMap->Query('SELECT name FROM dcl_projects WHERE projectid IN (' . implode(',', $this->aProjects) . ')');
			while ($oProjectMap->next_record())
				$projectNames .= '<span class="badge alert-info">' . htmlspecialchars($oProjectMap->f(0), ENT_QUOTES, 'UTF-8') . '</span> ';
		}

		if ($projectNames != '')
			echo '<h4>Projects</h4><div>' . $projectNames . '</div>';

		if ($productNames != '')
			echo '<h4>Products</h4><div>' . $productNames . '</div>';

		$beginDate = Filter::ToDate($_REQUEST['begindate']);
		$endDate = Filter::ToDate($_REQUEST['enddate']);
		if ($beginDate !== null || $endDate !== null)
		{
			echo '<h5>Date Range: ';
			if ($beginDate !== null)
			{
				echo $beginDate;
				if ($endDate !== null)
					echo '&nbsp;-&nbsp;';
			}

			if ($endDate !== null)
				echo $endDate;

			echo '</h5>';
		}

		$this->executeStatus();
		$this->executePriority();
		$this->executeOpened();
		$this->executeClosed();
		$this->executeWorked();
	}
	
	function executeItem($sSQL, $sCaption, $sAggregateBy)
	{
		$oDB = new DbProvider;
		if ($oDB->query($sSQL) != -1)
		{
			$oTable = new TableHtmlHelper();
			$oTable->addColumn($sAggregateBy, 'html');
			$oTable->addColumn('Count', 'numeric');
			
			$aData = array();
			$iTotal = 0;
			
			$sFilterProduct = '';
			if (isset($_REQUEST['products']))
			{
				$aProducts = Filter::ToIntArray($_REQUEST['products']);
				if (count($aProducts) == 1)
					$sFilterProduct = '&filterProduct=' . $aProducts[0];
			}

			while ($oDB->next_record())
			{
				if ($sAggregateBy == STR_WO_STATUS)
					array_push($aData, array('<a href="' . menuLink('', 'menuAction=WorkOrder.Browse&filterStatus=' . $oDB->f(0) . $sFilterProduct) . '">' . $oDB->f(1) . '</a>', $oDB->f(2)));
				else
					array_push($aData, array('<a href="' . menuLink('', 'menuAction=WorkOrder.Browse&filterPriority=' . $oDB->f(0) . $sFilterProduct) . '">' . $oDB->f(1) . '</a>', $oDB->f(2)));
					
				$iTotal += $oDB->f(2);
			}

			if ($iTotal == 0)
				return;
			
			$oTable->setData($aData);
			$oTable->addFooter('Total');
			$oTable->addFooter($iTotal);

			echo '<div class="panel panel-default">';
			echo '<div class="panel-heading"><h4 class="panel-title">';
			echo htmlspecialchars($sCaption, ENT_QUOTES, 'UTF-8');
			echo '</h4></div>';

			$oTable->render();

			echo '</div>';
		}
	}

	private function GetWorkOrderOrgWhereClause()
	{
		global $g_oSec, $g_oSession;

		if (!$g_oSec->IsOrgUser())
			return '';

		$memberOfOrgs = $g_oSession->Value('member_of_orgs');
		if ($memberOfOrgs != '')
			$values = explode(',', $memberOfOrgs);
		else
			$values = array('-1');

		$organizationIds = join(',', $values);

		$sql = " AND (w.jcn in (select wo_id from dcl_wo_account where account_id in (" . $organizationIds . "))";
		$sql .= " AND w.seq in (select seq from dcl_wo_account where w.jcn = wo_id And account_id in (" . $organizationIds . "))) ";

		return $sql;
	}

	private function GetProductPublicClause()
	{
		global $g_oSec;

		if (!$g_oSec->IsPublicUser())
			return '';

		return " AND p.is_public = 'Y'";
	}

	private function GetWorkOrderPublicClause()
	{
		if (!IsPublicUser())
			return '';

		return " AND w.is_public = 'Y'";
	}

	function executeStatus()
	{
		commonHeader();

		$sSQL = 'SELECT s.id, s.name, count(*) FROM workorders w, statuses s';

		if (count($this->aProjects) > 0)
			$sSQL .= ', projectmap pm';

		if (IsPublicUser())
			$sSQL .= ', products p';

		$sSQL .= ' WHERE w.status = s.id ';
		if (isset($_REQUEST['products']))
		{
			$aProducts = Filter::ToIntArray($_REQUEST['products']);
			if (count($aProducts) > 0)
				$sSQL .= ' AND w.product IN (' . join(',', $aProducts) . ')';
		}

		if (count($this->aProjects) > 0)
			$sSQL .= ' AND w.jcn = pm.jcn AND pm.seq IN (0, w.seq) AND pm.projectid in (' . implode(',', $this->aProjects) . ')';

		if (IsPublicUser())
			$sSQL .= ' AND w.product = p.id';

		$sSQL .= $this->GetWorkOrderOrgWhereClause();
		$sSQL .= $this->GetWorkOrderPublicClause();
		$sSQL .= $this->GetProductPublicClause();

		$sSQL .= ' GROUP BY s.id, s.name ORDER BY 2 DESC';
		
		$this->executeItem($sSQL, 'All Work Orders By Status', STR_WO_STATUS);
	}

	function executePriority()
	{
		commonHeader();

		$sSQL = 'SELECT pr.id, pr.name, count(*) FROM workorders w, statuses s, priorities pr';

		if (count($this->aProjects) > 0)
			$sSQL .= ', projectmap pm';

		if (IsPublicUser())
			$sSQL .= ', products p';

		$sSQL .= ' WHERE w.priority = pr.id AND w.status = s.id AND s.dcl_status_type != 2 ';
		if (isset($_REQUEST['products']))
		{
			$aProducts = Filter::ToIntArray($_REQUEST['products']);
			if (count($aProducts) > 0)
				$sSQL .= ' AND w.product IN (' . join(',', $aProducts) . ')';
		}

		if (count($this->aProjects) > 0)
			$sSQL .= ' AND w.jcn = pm.jcn AND pm.seq IN (0, w.seq) AND pm.projectid in (' . implode(',', $this->aProjects) . ')';

		if (IsPublicUser())
			$sSQL .= ' AND w.product = p.id';

		$sSQL .= $this->GetWorkOrderOrgWhereClause();
		$sSQL .= $this->GetWorkOrderPublicClause();
		$sSQL .= $this->GetProductPublicClause();

		$sSQL .= ' GROUP BY pr.id, pr.name ORDER BY 2 DESC';

		$this->executeItem($sSQL, 'Current Work Orders By Priority', STR_WO_PRIORITY);
	}

	function executeOpened()
	{
		commonHeader();

		$sSQL = 'SELECT pr.id, pr.name, count(*) FROM workorders w, priorities pr';

		if (count($this->aProjects) > 0)
			$sSQL .= ', projectmap pm';

		if (IsPublicUser())
			$sSQL .= ', products p';

		$sSQL .= ' WHERE w.priority = pr.id ';
		if (isset($_REQUEST['products']))
		{
			$aProducts = Filter::ToIntArray($_REQUEST['products']);
			if (count($aProducts) > 0)
				$sSQL .= ' AND w.product IN (' . join(',', $aProducts) . ')';
		}

		if (count($this->aProjects) > 0)
			$sSQL .= ' AND w.jcn = pm.jcn AND pm.seq IN (0, w.seq) AND pm.projectid in (' . implode(',', $this->aProjects) . ')';

		$oDB = new WorkOrderModel();
		$beginDate = Filter::ToDate($_REQUEST['begindate']);
		$endDate = Filter::ToDate($_REQUEST['enddate']);
		if ($beginDate !== null && $endDate !== null)
			$sSQL .= ' AND w.lastactionon BETWEEN ' . $oDB->DisplayToSQL($beginDate . ' 00:00:00') . ' AND ' . $oDB->DisplayToSQL($endDate . ' 23:59:59');
		else if ($beginDate !== null)
			$sSQL .= ' AND w.lastactionon >= ' . $oDB->DisplayToSQL($beginDate . ' 00:00:00');
		else if ($endDate !== null)
			$sSQL .= ' AND w.lastactionon <= ' . $oDB->DisplayToSQL($endDate . ' 23:59:59');

		if (IsPublicUser())
			$sSQL .= ' AND w.product = p.id';

		$sSQL .= $this->GetWorkOrderOrgWhereClause();
		$sSQL .= $this->GetWorkOrderPublicClause();
		$sSQL .= $this->GetProductPublicClause();

		$sSQL .= ' GROUP BY pr.id, pr.name ORDER BY 2 DESC';

		$this->executeItem($sSQL, 'Work Orders Opened', STR_WO_PRIORITY);
	}

	function executeClosed()
	{
		commonHeader();

		$sSQL = 'SELECT pr.id, pr.name, count(*) FROM workorders w, priorities pr, statuses s';

		if (count($this->aProjects) > 0)
			$sSQL .= ', projectmap pm';

		if (IsPublicUser())
			$sSQL .= ', products p';

		$sSQL .= ' WHERE w.status = s.id AND w.priority = pr.id AND s.dcl_status_type = 2 ';
		if (isset($_REQUEST['products']))
		{
			$aProducts = Filter::ToIntArray($_REQUEST['products']);
			if (count($aProducts) > 0)
				$sSQL .= ' AND w.product IN (' . join(',', $aProducts) . ')';
		}

		if (count($this->aProjects) > 0)
			$sSQL .= ' AND w.jcn = pm.jcn AND pm.seq IN (0, w.seq) AND pm.projectid in (' . implode(',', $this->aProjects) . ')';

		$oDB = new WorkOrderModel();
		$beginDate = Filter::ToDate($_REQUEST['begindate']);
		$endDate = Filter::ToDate($_REQUEST['enddate']);
		if ($beginDate !== null && $endDate !== null)
			$sSQL .= ' AND w.lastactionon BETWEEN ' . $oDB->DisplayToSQL($beginDate . ' 00:00:00') . ' AND ' . $oDB->DisplayToSQL($endDate . ' 23:59:59');
		else if ($beginDate !== null)
			$sSQL .= ' AND w.lastactionon >= ' . $oDB->DisplayToSQL($beginDate . ' 00:00:00');
		else if ($endDate !== null)
			$sSQL .= ' AND w.lastactionon <= ' . $oDB->DisplayToSQL($endDate . ' 23:59:59');

		if (IsPublicUser())
			$sSQL .= ' AND w.product = p.id';

		$sSQL .= $this->GetWorkOrderOrgWhereClause();
		$sSQL .= $this->GetWorkOrderPublicClause();
		$sSQL .= $this->GetProductPublicClause();

		$sSQL .= ' GROUP BY pr.id, pr.name ORDER BY 2 DESC';

		$this->executeItem($sSQL, 'Work Orders Closed', STR_WO_PRIORITY);
	}

	function executeWorked()
	{
		commonHeader();

		$sSQL = 'SELECT s.id, s.name, count(*) FROM workorders w, statuses s';

		if (count($this->aProjects) > 0)
			$sSQL .= ', projectmap pm';

		if (IsPublicUser())
			$sSQL .= ', products p';

		$sSQL .= ' WHERE w.status = s.id ';
		if (isset($_REQUEST['products']))
		{
			$aProducts = Filter::ToIntArray($_REQUEST['products']);
			if (count($aProducts) > 0)
				$sSQL .= ' AND w.product IN (' . join(',', $aProducts) . ')';
		}

		if (count($this->aProjects) > 0)
			$sSQL .= ' AND w.jcn = pm.jcn AND pm.seq IN (0, w.seq) AND pm.projectid in (' . implode(',', $this->aProjects) . ')';

		$oDB = new WorkOrderModel();
		$beginDate = Filter::ToDate($_REQUEST['begindate']);
		$endDate = Filter::ToDate($_REQUEST['enddate']);
		if ($beginDate !== null && $endDate !== null)
			$sSQL .= ' AND w.lastactionon BETWEEN ' . $oDB->DisplayToSQL($beginDate . ' 00:00:00') . ' AND ' . $oDB->DisplayToSQL($endDate . ' 23:59:59');
		else if ($beginDate !== null)
			$sSQL .= ' AND w.lastactionon >= ' . $oDB->DisplayToSQL($beginDate . ' 00:00:00');
		else if ($endDate !== null)
			$sSQL .= ' AND w.lastactionon <= ' . $oDB->DisplayToSQL($endDate . ' 23:59:59');
		else
			$sSQL .= ' AND w.lastactionon IS NOT NULL';

		if (IsPublicUser())
			$sSQL .= ' AND w.product = p.id';

		$sSQL .= $this->GetWorkOrderOrgWhereClause();
		$sSQL .= $this->GetWorkOrderPublicClause();
		$sSQL .= $this->GetProductPublicClause();

		$sSQL .= ' GROUP BY s.id, s.name ORDER BY 2 DESC';

		$this->executeItem($sSQL, 'Work Orders Touched', STR_WO_STATUS);
	}
}
