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

class Filter
{
	public function ToInt($vValue)
	{
		if (preg_match('/^[0-9]+$/', $vValue))
			return (int)$vValue;
			
		return null;
	}
	
	public function ToSignedInt($vValue)
	{
		if (preg_match('/^[-]?[0-9]+$/', $vValue))
			return (int)$vValue;
			
		return null;
	}
	
	public function ToIntArray($vValue)
	{
		$aRetVal = null;
		if (is_array($vValue))
			$aArray = $vValue;
		else
			$aArray = explode(',', $vValue);

		if (count($aArray) > 0)
		{
			for ($i = 0; $i < count($aArray); $i++)
			{
				if (($iVal = Filter::ToInt($aArray[$i])) !== null)
				{
					if ($aRetVal === null)
						$aRetVal = array();
						
					$aRetVal[] = $iVal;
				}
			}
		}
		
		return $aRetVal;
	}
	
	public function ToDecimal($vValue)
	{
		if (preg_match('/^([0-9]*[\.][0-9]+)|([0-9]+[\.]?[0-9]*)$/', $vValue))
			return (float)$vValue;
			
		return null;
	}
	
	public function ToDate($vValue)
	{
		$oDate = new DateHelper;
		
		// Set to 0 to invalidate by default.  If parsing doesn't succeed, it will remain 0
		$oDate->time = 0;
		$oDate->SetFromDisplay($vValue);
		
		return $oDate->ToDisplay();
	}
	
	public function ToDateTime($vValue)
	{
		$oDate = new TimestampHelper;

		// Set to 0 to invalidate by default.  If parsing doesn't succeed, it will remain 0
		$oDate->time = 0;
		$oDate->SetFromDisplay($vValue);

		return $oDate->ToDisplay();
	}
	
	public function ToYN($vValue)
	{
		return $vValue == 'Y' ? 'Y' : 'N';
	}

	public function ToFileName($sFieldName, $iIndex = -1)
	{
		if ($iIndex == -1)
		{
			if (is_uploaded_file($_FILES[$sFieldName]['tmp_name']))
				return $_FILES[$sFieldName]['tmp_name'];
		}
		else
		{
			if (is_uploaded_file($_FILES[$sFieldName]['tmp_name'][$iIndex]))
				return $_FILES[$sFieldName]['tmp_name'][$iIndex];
		}
			
		return null;
	}
	
	public function ToActualFileName($sFieldName, $iIndex = -1)
	{
		if (Filter::ToFileName($sFieldName) === null)
			return null;
			
		if ($iIndex == -1)
			return $_FILES[$sFieldName]['name'];
			
		return $_FILES[$sFieldName]['name'][$iIndex];
	}
	
	public function IsValidFileName($sFileName)
	{
		// no file system separators in file names
		return !preg_match("#[/\\]#", $sFileName);
	}
	
	public function IsValidPathName($sPathName)
	{
		// just make sure we don't have dir traversal
		return !preg_match("/[\.\.]/", $sPathName);
	}
	
	public function IsValidFieldName($sFieldName)
	{
		return preg_match("/^[a-z_][a-z0-9_]+([\.][a-z0-9_]+)?$/i", $sFieldName);
	}
}
