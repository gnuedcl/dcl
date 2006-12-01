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

class DCL_Sanitize
{
	function ToInt($vValue)
	{
		if (ereg('^[0-9]+$', $vValue))
			return (int)$vValue;
			
		return null;
	}
	
	function ToSignedInt($vValue)
	{
		if (ereg('^[-]?[0-9]+$', $vValue))
			return (int)$vValue;
			
		return null;
	}
	
	function ToIntArray($vValue)
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
				if (($iVal = DCL_Sanitize::ToInt($aArray[$i])) !== null)
				{
					if ($aRetVal === null)
						$aRetVal = array();
						
					$aRetVal[] = $iVal;
				}
			}
		}
		
		return $aRetVal;
	}
	
	function ToDecimal($vValue)
	{
		if (ereg('^([0-9]*[\.][0-9]+)|([0-9]+[\.]?[0-9]*)$', $vValue))
			return (float)$vValue;
			
		return null;
	}
	
	function ToDate($vValue)
	{
		$oDate = new DCLDate;
		
		// Set to 0 to invalidate by default.  If parsing doesn't succeed, it will remain 0
		$oDate->time = 0;
		$oDate->SetFromDisplay($vValue);
		
		return $oDate->ToDisplay();
	}
	
	function ToDateTime($vValue)
	{
		$oDate = new DCLTimestamp;

		// Set to 0 to invalidate by default.  If parsing doesn't succeed, it will remain 0
		$oDate->time = 0;
		$oDate->SetFromDisplay($vValue);

		return $oDate->ToDisplay();
	}
	
	function ToYN($vValue)
	{
		return $vValue == 'Y' ? 'Y' : 'N';
	}

	function ToFileName($sFieldName)
	{
		if (is_uploaded_file($_FILES[$sFieldName]['tmp_name']))
			return $_FILES[$sFieldName]['tmp_name'];
			
		return null;
	}
	
	function ToActualFileName($sFieldName)
	{
		if (DCL_Sanitize::ToFileName($sFieldName) === null)
			return null;
			
		return $_FILES[$sFieldName]['name'];
	}
	
	function IsValidFileName($sFileName)
	{
		// no file system separators in file names
		return !ereg("[/\\]", $sFileName);
	}
	
	function IsValidPathName($sPathName)
	{
		// just make sure we don't have dir traversal
		return !ereg("[\.\.]", $sPathName);
	}
	
	function IsValidFieldName($sFieldName)
	{
		return ereg("^[A-Za-z_][A-Za-z0-9_]+([\.][A-Za-z0-9_]+)?$", $sFieldName);
	}
}
?>
