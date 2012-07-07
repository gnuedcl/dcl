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

class OrganizationImagePresenter
{
	private $_imageHelper;
	private $_additionalColors;
	private $_maxSliceIndex;

	public function __construct()
	{
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Thu, 11 Mar 1993 08:00:00 GMT');

		$this->_maxSliceIndex = 11;
		$this->_additionalColors = array(
			array(94, 48, 0),
			array(201, 34, 0),
			array(247, 143, 1),
			array(255, 238, 208),
			array(90, 181, 110),
			array(105, 210, 231),
			array(167, 219, 216),
			array(224, 228, 204),
			array(243, 134, 48),
			array(250, 105, 0)
			);
	}

	private function RenderPieChart($model, $chartTitle)
	{
		$index = 0;
		$items = array();
		$itemCounts = array();
		while ($model->next_record())
		{
			if ($index > $this->_maxSliceIndex)
			{
				// Group the rest under "Other"
				$iModIndex = $this->_maxSliceIndex;
				$itemCounts[$iModIndex] += $model->f(1);
				$items[$iModIndex] = $itemCounts[$iModIndex] . ': Other';

				continue;
			}

			$items[] = $model->f(1) . ': ' . $model->f(0);
			$itemCounts[] = $model->f(1);

			$index++;
		}

		if (count($itemCounts) == 0)
		{
			$itemCounts[] = 1;
			$items[] = '0: No Matches';
		}

		$chartWidth = 540;
		$chartHeight = 240;
		$this->_imageHelper = new ChartHelper($chartWidth, $chartHeight);

		$this->_imageHelper->Data->AddPoint($itemCounts, 'Count');
		$this->_imageHelper->Data->AddPoint($items, 'Item');
		$this->_imageHelper->Data->AddAllSeries();
		$this->_imageHelper->Data->SetAbsciseLabelSerie('Item');

		$index = 8;
		foreach ($this->_additionalColors as $rgb)
			$this->_imageHelper->Chart->setColorPalette($index++, $rgb[0], $rgb[1], $rgb[2]);

		$this->_imageHelper->Chart->drawGraphAreaGradient(250, 250, 250, 50, TARGET_BACKGROUND);

		$this->_imageHelper->Chart->drawPieGraph($this->_imageHelper->Data->GetData(), $this->_imageHelper->Data->GetDataDescription(), 150, 110, 110, PIE_PERCENTAGE, true, 50, 20, 5);
		$this->_imageHelper->Chart->drawPieLegend(310, 30, $this->_imageHelper->Data->GetData(), $this->_imageHelper->Data->GetDataDescription(), 250, 250, 250);
		$this->_imageHelper->Chart->drawTextBox(0, 0, 540, 20, $chartTitle, 0, 255, 255, 255, ALIGN_CENTER, true, 0, 0, 0, 40);
		$this->_imageHelper->Chart->addBorder(2);

		$this->_imageHelper->Chart->Stroke();
		exit;
	}

	public function WorkOrderOrganizationChart()
	{
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_VIEW);

		$model = new OrganizationModel();
		if ($model->GetOrganizationCount() == -1)
			exit;

		$this->RenderPieChart($model, 'Work Orders By Organization');
	}

	public function TicketOrganizationChart()
	{
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_VIEW);

		$model = new OrganizationModel();
		if ($model->GetOrganizationCountTicket() == -1)
			exit;

		$this->RenderPieChart($model, 'Tickets By Organization');
	}

	public function WorkOrderStatusChart($orgId)
	{
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_VIEW, $orgId);

		$model = new OrganizationModel();
		if ($model->GetStatusCount($orgId) == -1)
			exit;

		$this->RenderPieChart($model, 'Work Orders By Status');
	}

	public function WorkOrderDepartmentChart($orgId)
	{
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_VIEW, $orgId);

		$model = new OrganizationModel();
		if ($model->GetDepartmentCount($orgId) == -1)
			exit;

		$this->RenderPieChart($model, 'Work Orders By Department');
	}

	public function WorkOrderSeverityChart($orgId)
	{
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_VIEW, $orgId);

		$model = new OrganizationModel();
		if ($model->GetSeverityCount($orgId) == -1)
			exit;

		$this->RenderPieChart($model, 'Work Orders By Severity');
	}

	public function WorkOrderPriorityChart($orgId)
	{
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_VIEW, $orgId);

		$model = new OrganizationModel();
		if ($model->GetPriorityCount($orgId) == -1)
			exit;

		$this->RenderPieChart($model, 'Work Orders By Priority');
	}

	public function WorkOrderModuleChart($orgId)
	{
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_VIEW, $orgId);

		$model = new OrganizationModel();
		if ($model->GetModuleCount($orgId) == -1)
			exit;

		$this->RenderPieChart($model, 'Work Orders By Module');
	}

	public function WorkOrderTypeChart($orgId)
	{
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_VIEW, $orgId);

		$model = new OrganizationModel();
		if ($model->GetTypeCount($orgId) == -1)
			exit;

		$this->RenderPieChart($model, 'Work Orders By Type');
	}

	public function TicketStatusChart($orgId)
	{
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_VIEW, $orgId);

		$model = new OrganizationModel();
		if ($model->GetStatusCountTicket($orgId) == -1)
			exit;

		$this->RenderPieChart($model, 'Tickets By Status');
	}

	public function TicketTypeChart($orgId)
	{
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_VIEW, $orgId);

		$model = new OrganizationModel();
		if ($model->GetTypeCountTicket($orgId) == -1)
			exit;

		$this->RenderPieChart($model, 'Tickets By Type');
	}

	public function TicketPriorityChart($orgId)
	{
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_VIEW, $orgId);

		$model = new OrganizationModel();
		if ($model->GetPriorityCountTicket($orgId) == -1)
			exit;

		$this->RenderPieChart($model, 'Tickets By Priority');
	}

	public function TicketModuleChart($orgId)
	{
		RequirePermission(DCL_ENTITY_ORG, DCL_PERM_VIEW, $orgId);

		$model = new OrganizationModel();
		if ($model->GetModuleCountTicket($orgId) == -1)
			exit;

		$this->RenderPieChart($model, 'Tickets By Module');
	}
}
