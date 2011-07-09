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

class imgProject
{
	var $img;
	var $aAddlColors;
	var $iMaxSliceIndex;
	
	function imgProject()
	{
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Thu, 11 Mar 1993 08:00:00 GMT');
		
		$this->iMaxSliceIndex = 11;
		$this->aAddlColors = array(
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
	
	function pie($oDB, $sTitle)
	{
		$iIndex = 0;
		$aItem = array();
		$aCount = array();
		while ($oDB->next_record())
		{
			if ($iIndex > $this->iMaxSliceIndex)
			{
				// Group the rest under "Other"
				$iModIndex = $this->iMaxSliceIndex;
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

		$iWidth = 460;
		$iHeight = count($aCount) > 9 ? 220 : 200;
		$this->img = new DCL_Chart($iWidth, $iHeight);
		
		$this->img->Data->AddPoint($aCount, 'Count');
		$this->img->Data->AddPoint($aItem, 'Item');
		$this->img->Data->AddAllSeries();
		$this->img->Data->SetAbsciseLabelSerie('Item');
		
		$iIndex = 8;
		foreach ($this->aAddlColors as $rgb)
			$this->img->Chart->setColorPalette($iIndex++, $rgb[0], $rgb[1], $rgb[2]);
				
		$this->img->Chart->drawFilledRoundedRectangle(7, 7, $iWidth - 7, $iHeight - 7, 5, 240, 240, 240);
		$this->img->Chart->drawRoundedRectangle(5, 5, $iWidth - 5, $iHeight - 5, 5, 230, 230, 230);
		
		$this->img->Chart->drawPieGraph($this->img->Data->GetData(), $this->img->Data->GetDataDescription(), 150, 90, 110, PIE_PERCENTAGE, true, 50, 20, 5);
		$this->img->Chart->drawPieLegend(310, 30, $this->img->Data->GetData(), $this->img->Data->GetDataDescription(), 250, 250, 250);
		$this->img->Chart->drawTitle(314, 24, $sTitle, 32, 32, 32);
		
		$this->img->Chart->Stroke();
		exit;
	}
	
	function byStatus()
	{
		global $g_oSec;
		
		if (($id = Filter::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}
			
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEW, $id))
			throw new PermissionDeniedException();
			
		$oDB = new ProjectMapModel();
		if ($oDB->GetStatusCount($id) == -1)
			exit;

		$this->pie($oDB, 'Work Orders By Status');
	}
	
	function byDepartment()
	{
		global $g_oSec;
		
		if (($id = Filter::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}
			
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEW, $id))
			throw new PermissionDeniedException();
			
		$oDB = new ProjectMapModel();
		if ($oDB->GetDepartmentCount($id) == -1)
			exit;

		$this->pie($oDB, 'Work Orders By Department');
	}
	
	function bySeverity()
	{
		global $g_oSec;
		
		if (($id = Filter::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}
			
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEW, $id))
			throw new PermissionDeniedException();
			
		$oDB = new ProjectMapModel();
		if ($oDB->GetSeverityCount($id) == -1)
			exit;

		$this->pie($oDB, 'Work Orders By Severity');
	}
	
	function byPriority()
	{
		global $g_oSec;
		
		if (($id = Filter::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}
			
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEW, $id))
			throw new PermissionDeniedException();
			
		$oDB = new ProjectMapModel();
		if ($oDB->GetPriorityCount($id) == -1)
			exit;

		$this->pie($oDB, 'Work Orders By Priority');
	}
	
	function byModule()
	{
		global $g_oSec;
		
		if (($id = Filter::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}
			
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEW, $id))
			throw new PermissionDeniedException();
			
		$oDB = new ProjectMapModel();
		if ($oDB->GetModuleCount($id) == -1)
			exit;

		$this->pie($oDB, 'Work Orders By Module');
	}
	
	function byType()
	{
		global $g_oSec;
		
		if (($id = Filter::ToInt($_REQUEST['id'])) === null)
		{
			throw new InvalidDataException();
		}
			
		if (!$g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEW, $id))
			throw new PermissionDeniedException();
			
		$oDB = new ProjectMapModel();
		if ($oDB->GetTypeCount($id) == -1)
			exit;

		$this->pie($oDB, 'Work Orders By Type');
	}
}
