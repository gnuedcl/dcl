<?php
/*
 * This file is part of Double Choco Latte.
 * Copyright (C) 1999-2011 Free Software Foundation
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
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

class AuditModel
{
	function LoadDiff($class, $aID)
	{
		$model = new $class();
		
		$aAuditTrail = $model->AuditLoad($aID);
		$aDiff = array();
		
		// Diff returns something only if we have a trail
		if (count($aAuditTrail) > 1)
		{
			$aLastRecord = array();
			$iIndex = 0;
			foreach ($aAuditTrail as $iVersion => $aRecord)
			{
				if ($iVersion > 1)
				{
					$aDiff[$iIndex] = array();
					$aDiff[$iIndex]['audit_on'] = $aLastRecord['audit_on'];
					$aDiff[$iIndex]['audit_by'] = $aLastRecord['audit_by'];
					$aDiff[$iIndex]['audit_version'] = $aRecord['audit_version'] == '' ? 'Current' : $aRecord['audit_version'];
					
					$aDiff[$iIndex]['changes'] = array();
					foreach ($aRecord as $sField => $sValue)
					{
						if (substr($sField, 0, 6) == 'audit_')
							continue;
							
						if ($aLastRecord[$sField] != $sValue)
						{
							$aDiff[$iIndex]['changes'][] = array('field' => $sField, 'old' => $aLastRecord[$sField], 'new' => $sValue);
						}
					}
					
					$iIndex++;
				}
				
				$aLastRecord = $aRecord;
			}
		}
		
		return $aDiff;
	}
}
