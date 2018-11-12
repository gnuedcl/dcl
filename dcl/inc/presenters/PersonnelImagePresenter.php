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

class PersonnelImagePresenter
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

		// Try to avoid 'Only variables should be passed by reference' error
		$GD = $this->_imageHelper->Data->GetData();
		$GDD = $this->_imageHelper->Data->GetDataDescription();
		$this->_imageHelper->Chart->drawPieGraph($GD, $GDD, 150, 110, 110, PIE_PERCENTAGE, true, 50, 20, 5);
		$this->_imageHelper->Chart->drawPieLegend(310, 30, $GD, $GDD, 250, 250, 250);

//		$this->_imageHelper->Chart->drawPieGraph($this->_imageHelper->Data->GetData(), $this->_imageHelper->Data->GetDataDescription(), 150, 110, 110, PIE_PERCENTAGE, true, 50, 20, 5);
//		$this->_imageHelper->Chart->drawPieLegend(310, 30, $this->_imageHelper->Data->GetData(), $this->_imageHelper->Data->GetDataDescription(), 250, 250, 250);
		$this->_imageHelper->Chart->drawTextBox(0, 0, 540, 20, $sTitle, 0, 255, 255, 255, ALIGN_CENTER, true, 0, 0, 0, 40);
		$this->_imageHelper->Chart->addBorder(2);
		
		$this->_imageHelper->Chart->Stroke();
		exit;
	}
	
	public function StatusChart($personnelId)
	{
		RequirePermission(DCL_ENTITY_PERSONNEL, DCL_PERM_VIEW, $personnelId);
			
		$model = new PersonnelModel();
		if ($model->GetStatusCount($personnelId) == -1)
			exit;

		$this->RenderPieChart($model, 'Work Orders By Status');
	}
	
	public function SeverityChart($personnelId)
	{
		RequirePermission(DCL_ENTITY_PERSONNEL, DCL_PERM_VIEW, $personnelId);
			
		$model = new PersonnelModel();
		if ($model->GetSeverityCount($personnelId) == -1)
			exit;

		$this->RenderPieChart($model, 'Work Orders By Severity');
	}
	
	public function PriorityChart($personnelId)
	{
		RequirePermission(DCL_ENTITY_PERSONNEL, DCL_PERM_VIEW, $personnelId);
			
		$model = new PersonnelModel();
		if ($model->GetPriorityCount($personnelId) == -1)
			exit;

		$this->RenderPieChart($model, 'Work Orders By Priority');
	}
	
	public function ProductChart($personnelId)
	{
		RequirePermission(DCL_ENTITY_PERSONNEL, DCL_PERM_VIEW, $personnelId);
			
		$model = new PersonnelModel();
		if ($model->GetProductCount($personnelId) == -1)
			exit;

		$this->RenderPieChart($model, 'Work Orders By Product');
	}
	
	public function TypeChart($personnelId)
	{
		RequirePermission(DCL_ENTITY_PERSONNEL, DCL_PERM_VIEW, $personnelId);
			
		$model = new PersonnelModel();
		if ($model->GetTypeCount($personnelId) == -1)
			exit;

		$this->RenderPieChart($model, 'Work Orders By Type');
	}
}
