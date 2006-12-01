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

LoadStringResource('menu');
LoadStringResource('usr');
LoadStringResource('cfg');

class htmlPreferences
{
	function modify()
	{
		global $dcl_info, $g_oSession, $g_oSec;

		commonHeader();
		if (!$g_oSec->HasPerm(DCL_ENTITY_PREFS, DCL_PERM_MODIFY))
			return PrintPermissionDenied();

		$t =& CreateSmarty();
		
		$t->assign('PERM_MODIFYCONTACT', $g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_MODIFY) || $g_oSec->HasPerm(DCL_ENTITY_GLOBAL, DCL_PERM_ADMIN));
		$t->assign('VAL_CONTACTID', $g_oSession->Value('contact_id'));
		
		// Reuse methods from here for lang and template
		$o =& CreateObject('dcl.htmlConfig');
		$t->assign('CMB_DEFAULTTEMPLATESET', $o->GetTemplatesCombo('DCL_PREF_TEMPLATE_SET', GetDefaultTemplateSet()));

		$lang = $dcl_info['DCL_DEFAULT_LANGUAGE'];
		$oPrefs =& CreateObject('dcl.dbPreferences');
		$oPrefs->preferences_data = $g_oSession->Value('dcl_preferences');
		if (isset($oPrefs->preferences_data) && is_array($oPrefs->preferences_data))
		{
			if ($oPrefs->Value('DCL_PREF_LANGUAGE') != '')
				$lang = $oPrefs->Value('DCL_PREF_LANGUAGE');
		}

		$t->assign('CMB_DEFAULTLANGUAGE', $o->GetLangCombo('DCL_PREF_LANGUAGE', $lang));

		SmartyDisplay($t, 'htmlPreferences.tpl');
	}

	function submitModify()
	{
		global $g_oSession, $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_PREFS, DCL_PERM_MODIFY))
			return PrintPermissionDenied();
			
		$o = CreateObject('dcl.dbPersonnel');
		if ($o->Load($GLOBALS['DCLID']) != -1)
		{
			if ($GLOBALS['USEREMAIL'] != $_REQUEST['email'])
			{
				$o->email = $_REQUEST['email'];
				$o->edit();

				$GLOBALS['USEREMAIL'] = $o->email;
				$g_oSession->Register('USEREMAIL', $o->email);
				$g_oSession->Edit();
			}
		}

		unset($o);

		$bHasChanges = false;
		$o = CreateObject('dcl.dbPreferences');
		$o->personnel_id = $GLOBALS['DCLID'];
		$o->preferences_data = $g_oSession->Value('dcl_preferences');
		if (!isset($o->preferences_data) || !is_array($o->preferences_data) || count($o->preferences_data) < 1)
		{
			$o->preferences_data = array(
					'DCL_PREF_TEMPLATE_SET' => $dcl_info['DCL_DEF_TEMPLATE_SET'],
					'DCL_PREF_LANGUAGE' => $dcl_info['DCL_DEFAULT_LANGUAGE']
				);

			$o->Add();

			$bHasChanges = true;
		}

		$sOldTpl = $o->preferences_data['DCL_PREF_TEMPLATE_SET'];

		foreach ($_REQUEST as $pref => $setting)
		{
			if (substr($pref, 0, 9) != 'DCL_PREF_')
				continue;

			if ($o->Value($pref) != $setting)
			{
				$bHasChanges = true;
				$o->Register($pref, $setting);
			}
		}

		if ($bHasChanges)
		{
			$o->Edit();
			$g_oSession->Register('dcl_preferences', $o->preferences_data);
			$g_oSession->Edit();
		}

		// Template change?
		$sNewTpl = $o->preferences_data['DCL_PREF_TEMPLATE_SET'];
		if ($sNewTpl != $sOldTpl)
		{
			// Do we need to break out of frames?
			$menuAction = 'menuAction=htmlPreferences.modify';
			$sNewIsFramed = file_exists(DCL_ROOT . 'templates/' . $sNewTpl . '/frameset.php');
			$sOldIsFramed = file_exists(DCL_ROOT . 'templates/' . $sOldTpl . '/frameset.php');

			if ($sOldIsFramed)
			{
				if ($sNewIsFramed)
					RefreshTop(menuLink(DCL_WWW_ROOT . 'templates/' . $sNewTpl . '/frameset.php', $menuAction));
				else
					RefreshTop(menuLink('', $menuAction));
			}
			else if ($sNewIsFramed)
			{
				RefreshTop(menuLink(DCL_WWW_ROOT . 'templates/' . $sNewTpl . '/frameset.php', $menuAction));
			}
		}

		$this->modify();
	}
}
?>
