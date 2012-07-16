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

class ProjectImagePresenter
{
	private $_imageHelper;
	private $_additionalColors;
	private $_maxSlideIndex;
	
	public function __construct()
	{
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Thu, 11 Mar 1993 08:00:00 GMT');
		
		$this->_maxSlideIndex = 11;
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
	
	public function RenderPieChart($oDB, $sTitle)
	{
		$iIndex = 0;
		$aItem = array();
		$aCount = array();
		while ($oDB->next_record())
		{
			if ($iIndex > $this->_maxSlideIndex)
			{
				// Group the rest under "Other"
				$iModIndex = $this->_maxSlideIndex;
				$aCount[$iModIndex] += $oDB->f(1);
				$aItem[$iModIndex] = $aCount[$iModIndex] . ': Other';
					
				continue;
			}
			
			$aItem[] = $oDB->f(1) . ': ' . $oDB->f(0);
			$aCount[] = $oDB->f(1);
			
			$iIndex++;
		}

		if (count($aCount) == 0)
		{
			$aCount[] = 1;
			$aItem[] = '0: No Matches';
		}

		$iWidth = 540;
		$iHeight = 240;
		$this->_imageHelper = new ChartHelper($iWidth, $iHeight);
		
		$this->_imageHelper->Data->AddPoint($aCount, 'Count');
		$this->_imageHelper->Data->AddPoint($aItem, 'Item');
		$this->_imageHelper->Data->AddAllSeries();
		$this->_imageHelper->Data->SetAbsciseLabelSerie('Item');
		
		$iIndex = 8;
		foreach ($this->_additionalColors as $rgb)
			$this->_imageHelper->Chart->setColorPalette($iIndex++, $rgb[0], $rgb[1], $rgb[2]);
				
		$this->_imageHelper->Chart->drawGraphAreaGradient(250, 250, 250, 50, TARGET_BACKGROUND);
		
		$this->_imageHelper->Chart->drawPieGraph($this->_imageHelper->Data->GetData(), $this->_imageHelper->Data->GetDataDescription(), 150, 110, 110, PIE_PERCENTAGE, true, 50, 20, 5);
		$this->_imageHelper->Chart->drawPieLegend(310, 30, $this->_imageHelper->Data->GetData(), $this->_imageHelper->Data->GetDataDescription(), 250, 250, 250);
		$this->_imageHelper->Chart->drawTextBox(0, 0, 540, 20, $sTitle, 0, 255, 255, 255, ALIGN_CENTER, true, 0, 0, 0, 40);
		$this->_imageHelper->Chart->addBorder(2);

		$this->_imageHelper->Chart->Stroke();
		exit;
	}
	
	public function StatusChart($projectId, $children)
	{
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_VIEW, $projectId);
			
		$model = new ProjectMapModel();
		if ($model->GetStatusCount($projectId, $children) == -1)
			exit;

		$this->RenderPieChart($model, 'Work Orders By Status');
	}
	
	public function DepartmentChart($projectId, $children)
	{
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_VIEW, $projectId);
			
		$oDB = new ProjectMapModel();
		if ($oDB->GetDepartmentCount($projectId, $children) == -1)
			exit;

		$this->RenderPieChart($oDB, 'Work Orders By Department');
	}
	
	public function SeverityChart($projectId, $children)
	{
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_VIEW, $projectId);
			
		$model = new ProjectMapModel();
		if ($model->GetSeverityCount($projectId, $children) == -1)
			exit;

		$this->RenderPieChart($model, 'Work Orders By Severity');
	}
	
	public function PriorityChart($projectId, $children)
	{
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_VIEW, $projectId);
			
		$model = new ProjectMapModel();
		if ($model->GetPriorityCount($projectId, $children) == -1)
			exit;

		$this->RenderPieChart($model, 'Work Orders By Priority');
	}
	
	public function ModuleChart($projectId, $children)
	{
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_VIEW, $projectId);
			
		$model = new ProjectMapModel();
		if ($model->GetModuleCount($projectId, $children) == -1)
			exit;

		$this->RenderPieChart($model, 'Work Orders By Module');
	}
	
	public function TypeChart($projectId, $children)
	{
		RequirePermission(DCL_ENTITY_PROJECT, DCL_PERM_VIEW, $projectId);
			
		$model = new ProjectMapModel();
		if ($model->GetTypeCount($projectId, $children) == -1)
			exit;

		$this->RenderPieChart($model, 'Work Orders By Type');
	}
}
