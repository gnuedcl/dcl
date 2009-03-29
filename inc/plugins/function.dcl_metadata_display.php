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

function smarty_function_dcl_metadata_display($params, &$smarty)
{
	global $g_oMetaData, $g_oSec, $g_oSession;
	
	if (!isset($params['type']))
	{
		$smarty->trigger_error('dcl_metadata_display: missing parameter type');
		return;
	}

	if (!isset($params['value']))
	{
		$smarty->trigger_error('dcl_metadata_display: missing parameter value');
		return;
	}

	if (!isset($g_oMetaData) || !is_a($g_oMetaData, 'DCL_Metadatadisplay'))
		$g_oMetaData = CreateObject('dcl.DCL_MetadataDisplay');
		
	switch ($params['type'])
	{
		case 'action':
		case 'department':
		case 'module':
		case 'personnel':
		case 'priority':
		case 'product':
		case 'project':
		case 'severity':
		case 'source':
		case 'status':
		case 'ticket':
			$sMethodName = 'Get' . ucfirst($params['type']);
			return $g_oMetaData->$sMethodName($params['value']);
			
		case 'wotype':
			return $g_oMetaData->GetWorkOrderType($params['value']);
			
		case 'product_version':
			return $g_oMetaData->GetProductVersion($params['value']);
			
		case 'wo_project':
			if (!isset($params['value2']))
			{
				$smarty->trigger_error('dcl_metadata_display: missing parameter value2');
				return;
			}
			
			$oProjects =& CreateObject('dcl.boProjects');
			$aProjects = $oProjects->GetProjectPath($params['value'], $params['value2']);
			if (count($aProjects) > 0)
			{
				$sRetVal = '';
				foreach ($aProjects as $aProject)
				{
					if ($sRetVal != '')
						$sRetVal .= ' / ';
					else
						$sRetVal = '/ ';
						
					$sRetVal .= '[' . $aProject['project_id'] . '] ' . $aProject['name'];
				}
				
				return $sRetVal;
			}
			
			return '';
			
		case 'workorder':
			if (!isset($params['value2']))
			{
				$smarty->trigger_error('dcl_metadata_display: missing parameter value2');
				return;
			}
			return $g_oMetaData->GetWorkOrder($params['value'], $params['value2']);

		case 'contact_name':
			$aContact = $g_oMetaData->GetContact($params['value']);
			return $aContact['name'];
			
		case 'contact_phone':
			$aContact = $g_oMetaData->GetContact($params['value']);
			return $aContact['phone'];
		
		case 'contact_phone_type':
			$aContact = $g_oMetaData->GetContact($params['value']);
			return $aContact['phonetype'];
			
		case 'contact_email':
			$aContact = $g_oMetaData->GetContact($params['value']);
			return $aContact['email'];
			
		case 'contact_email_type':
			$aContact = $g_oMetaData->GetContact($params['value']);
			return $aContact['emailtype'];
			
		case 'contact_url':
			$aContact = $g_oMetaData->GetContact($params['value']);
			return $aContact['url'];
			
		case 'contact_url_type':
			$aContact = $g_oMetaData->GetContact($params['value']);
			return $aContact['urltype'];
			
		case 'contact_org_id':
			$aContact = $g_oMetaData->GetContact($params['value']);
			return $aContact['org_id'];
			
		case 'contact_org_name':
			$aContact = $g_oMetaData->GetContact($params['value']);
			return $aContact['org_name'];

		case 'org_name':
			$aOrg = $g_oMetaData->GetOrganization($params['value']);
			return $aOrg['name'];
			
		case 'org_phone':
			$aOrg = $g_oMetaData->GetOrganization($params['value']);
			return $aOrg['phone'];
		
		case 'org_phone_type':
			$aOrg = $g_oMetaData->GetOrganization($params['value']);
			return $aOrg['phonetype'];
			
		case 'org_email':
			$aOrg = $g_oMetaData->GetOrganization($params['value']);
			return $aOrg['email'];
			
		case 'org_email_type':
			$aOrg = $g_oMetaData->GetOrganization($params['value']);
			return $aOrg['emailtype'];
			
		case 'org_url':
			$aOrg = $g_oMetaData->GetOrganization($params['value']);
			return $aOrg['url'];
			
		case 'org_url_type':
			$aOrg = $g_oMetaData->GetOrganization($params['value']);
			return $aOrg['urltype'];
			
		case 'wo_org':
			if (!isset($params['value2']))
			{
				$smarty->trigger_error('dcl_metadata_display: missing parameter value2');
				return;
			}

			$oAcct =& CreateObject('dcl.dbWorkOrderAccount');
			$aOrgs = array();
			if ($oAcct->Load($params['value'], $params['value2']) != -1)
			{
				$bHasPerm = $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_VIEWACCOUNT) || $g_oSec->IsOrgUser();
				$bViewAll = !$g_oSec->IsOrgUser();
				if ($bHasPerm)
					$aOrgs = split(',', $g_oSession->Value('member_of_orgs'));
	
				$aOrgNames = array();
				do
				{
					$oAcct->GetRow();
					if ($bViewAll || ($bHasPerm && in_array($oAcct->account_id, $aOrgs)))
					{
						$aOrgNames[] = $oAcct->account_name;
					}
				}
				while ($oAcct->next_record());
			}
			
			if (count($aOrgNames) > 0)
				return join('; ', $aOrgNames);
				
			return '';
	}

	return $params['value'];
}
?>