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

class SessionHelper
{
	public static function Start($userId, $email, $short, $contactId)
	{
		global $g_oSession, $dcl_info, $g_oSec;

		if (!isset($dcl_info) || !is_array($dcl_info) || count($dcl_info) == 0)
		{
			$oConfig = new ConfigurationModel();
			$dcl_info = array();
			$oConfig->Load();
		}

		if (!isset($g_oSec) || !is_object($g_oSec) || !is_a($g_oSec, 'SecurityHelper'))
		{
			$g_oSec = new SecurityHelper();
		}

		$g_oSession = new SessionModel();
		if (!$g_oSession->conn)
			Refresh('logout.php?cd=3');

		$g_oSession->personnel_id = $userId;
		$g_oSession->Add();

		$oPreferences = new PreferencesModel();
		$oPreferences->Load($userId);

		$g_oSession->Register('DCLID', $userId);
		define('DCLID', $userId);

		$g_oSession->Register('DCLNAME', trim($short));
		$g_oSession->Register('USEREMAIL', $email);
		$g_oSession->Register('contact_id', $contactId);
		$g_oSession->Register('dcl_info', $dcl_info);
		$g_oSession->Register('dcl_preferences', $oPreferences->preferences_data);
		$g_oSession->Register('CSRF_TOKEN', AntiCsrf::GenerateToken());

		if (is_array($oPreferences->preferences_data) && isset($oPreferences->preferences_data['DCL_PREF_DEFAULT_WORKSPACE']))
		{
			$workspaceModel = new WorkspaceModel();
			$workspaceModel->SetCurrentWorkspace($oPreferences->preferences_data['DCL_PREF_DEFAULT_WORKSPACE'], false);
		}

		// If we have org restrictions, cache the affiliated orgs for this contact record
		if ($contactId != null && $contactId > 0)
		{
			if ($g_oSec->IsOrgUser())
			{
				$oContact = new ContactModel();
				$aOrgs = $oContact->GetOrgArray($contactId);
				$g_oSession->Register('member_of_orgs', join(',', $aOrgs));

				// Also grab the filtered product list for the orgs
				$oOrg = new OrganizationModel();
				$aProducts = $oOrg->GetProductArray($aOrgs);
				if (count($aProducts) == 0)
					$aProducts = array('-1');

				$g_oSession->Register('org_products', join(',', $aProducts));
			}
		}

		$g_oSession->Edit();
	}
}