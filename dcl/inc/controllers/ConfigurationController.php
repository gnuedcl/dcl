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

LoadStringResource('bo');

class ConfigurationController
{
	public function Edit()
	{
		$presenter = new ConfigurationPresenter();
		$presenter->Edit();
	}

	public function Update()
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_MODIFY))
			throw new PermissionDeniedException();

		$aCheckboxes = array('DCL_AUTO_DATE', 'DCL_PROJECT_XML_TEMPLATES', 'DCL_SMTP_ENABLED',
							'DCL_GATEWAY_TICKET_ENABLED', 'DCL_GATEWAY_TICKET_AUTORESPOND',
							'DCL_GATEWAY_TICKET_REPLY', 'DCL_PROJECT_INCLUDE_CHILD_STATS',
							'DCL_PROJECT_INCLUDE_PARENT_STATS', 'DCL_PROJECT_BROWSE_PARENTS_ONLY',
							'DCL_WO_NOTIFICATION_HTML', 'DCL_TCK_NOTIFICATION_HTML',
							'DCL_GATEWAY_WO_ENABLED', 'DCL_GATEWAY_WO_AUTORESPOND',
							'DCL_GATEWAY_WO_REPLY', 'DCL_WO_SECONDARY_ACCOUNTS_ENABLED',
							'DCL_WIKI_ENABLED', 'DCL_SCCS_ENABLED', 'DCL_SMTP_AUTH_REQUIRED',
							'DCL_BUILD_MANAGER_ENABLED', 'DCL_SEC_AUDIT_ENABLED',
							'DCL_SEC_AUDIT_LOGIN_ONLY', 'DCL_FORCE_SECURE_GRAVATAR'
						);

		// get all of the settings except for DCL_VERSION and LAST_CONFIG_UPDATE and update
		// the ones that have changed.
		$bHasUpdates = false;

		$model = new ConfigurationModel();
		$model->LoadForModify();
		while ($model->next_record())
		{
			$model->GetRow();
			if (IsSet($_REQUEST[$model->dcl_config_name]))
				$_REQUEST[$model->dcl_config_name] = $model->GPCStripSlashes($_REQUEST[$model->dcl_config_name]);

			// checkboxes need special handling
			if (in_array($model->dcl_config_name, $aCheckboxes))
			{
				$newVal = (IsSet($_REQUEST[$model->dcl_config_name]) && $_REQUEST[$model->dcl_config_name] == 'Y') ? 'Y' : 'N';
				if ($newVal != $dcl_info[$model->dcl_config_name])
				{
					$model->{$model->dcl_config_field} = $newVal;
					$modelTemp = $model;
					$modelTemp->Edit();
					$dcl_info[$model->dcl_config_name] = $newVal;
					$bHasUpdates = true;
				}
			}
			elseif (isset($_REQUEST[$model->dcl_config_name]) && $dcl_info[$model->dcl_config_name] != $_REQUEST[$model->dcl_config_name])
			{
				$model->{$model->dcl_config_field} = $_REQUEST[$model->dcl_config_name];
				$modelTemp = $model;
				$modelTemp->Edit();
				$dcl_info[$model->dcl_config_name] = $_REQUEST[$model->dcl_config_name];
				$bHasUpdates = true;
			}
		}

		if ($bHasUpdates)
		{
			$modelTemp->UpdateTimeStamp();
			SetRedirectMessage('Success', 'Configuration updated successfully.');
		}
		else
		{
			SetRedirectMessage('Info', 'No configuration changes detected.');
		}

		RedirectToAction('SystemSetup', 'Index');
	}
}
