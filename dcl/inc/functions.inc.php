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

// Common definitions
define('phpCrLf', "\r\n");
define('phpTab', "\t");

// Modes
define('DCL_MODE_NEW', 1);
define('DCL_MODE_EDIT', 2);
define('DCL_MODE_COPY', 3);

// Entities
define('DCL_ENTITY_GLOBAL', 0);
define('DCL_ENTITY_PROJECT', 1);
define('DCL_ENTITY_WORKORDER', 2);
define('DCL_ENTITY_TICKET', 3);
define('DCL_ENTITY_PRODUCT', 4);
define('DCL_ENTITY_ORG', 5);
define('DCL_ENTITY_DEPARTMENT', 6);
define('DCL_ENTITY_PERSONNEL', 7);
define('DCL_ENTITY_ACTION', 8);
define('DCL_ENTITY_STATUS', 9);
define('DCL_ENTITY_PRIORITY', 10);
define('DCL_ENTITY_SEVERITY', 11);
define('DCL_ENTITY_TIMECARD', 12);
define('DCL_ENTITY_RESOLUTION', 13);
define('DCL_ENTITY_CONTACT', 14);
define('DCL_ENTITY_FAQ', 15);
define('DCL_ENTITY_FAQTOPIC', 16);
define('DCL_ENTITY_FAQQUESTION', 17);
define('DCL_ENTITY_FAQANSWER', 18);
define('DCL_ENTITY_FORMS', 19);
define('DCL_ENTITY_ADMIN', 20);
define('DCL_ENTITY_ATTRIBUTESETS', 21);
define('DCL_ENTITY_FORMTEMPLATES', 22);
define('DCL_ENTITY_ADDRTYPE', 23);
define('DCL_ENTITY_EMAILTYPE', 24);
define('DCL_ENTITY_NOTETYPE', 25);
define('DCL_ENTITY_PHONETYPE', 26);
define('DCL_ENTITY_URLTYPE', 27);
define('DCL_ENTITY_SOURCE', 28);
define('DCL_ENTITY_LOOKUP', 29);
define('DCL_ENTITY_PREFS', 30);
define('DCL_ENTITY_PRODUCTMODULE', 31);
define('DCL_ENTITY_SAVEDSEARCH', 32);
define('DCL_ENTITY_WORKORDERTYPE', 33);
define('DCL_ENTITY_CHANGELOG', 34);
define('DCL_ENTITY_SESSION', 35);
define('DCL_ENTITY_ROLE', 36);
define('DCL_ENTITY_ORGTYPE', 37);
define('DCL_ENTITY_CONTACTTYPE', 38);
define('DCL_ENTITY_BUILDMANAGER', 39);
define('DCL_ENTITY_WORKORDER_TASK', 40);
define('DCL_ENTITY_WORKSPACE', 41);
define('DCL_ENTITY_TEST_CASE', 42);
define('DCL_ENTITY_FUNCTIONAL_SPEC', 43);
define('DCL_ENTITY_HOTLIST', 44);

// Permissions
define('DCL_PERM_ADMIN', 0);
define('DCL_PERM_ADD', 1);
define('DCL_PERM_MODIFY', 2);
define('DCL_PERM_DELETE', 3);
define('DCL_PERM_VIEW', 4);
define('DCL_PERM_VIEWPRIVATE', 5);
define('DCL_PERM_VIEWACCOUNT', 6);
define('DCL_PERM_VIEWSUBMITTED', 7);
define('DCL_PERM_COPYTOWO', 8);
define('DCL_PERM_ASSIGN', 9);
define('DCL_PERM_ACTION', 10);
define('DCL_PERM_PASSWORD', 11);
define('DCL_PERM_IMPORT', 12);
define('DCL_PERM_SEARCH', 13);
define('DCL_PERM_SCHEDULE', 14);
define('DCL_PERM_REPORT', 15);
define('DCL_PERM_ADDTASK', 16);
define('DCL_PERM_REMOVETASK', 17);
define('DCL_PERM_ATTACHFILE', 18);
define('DCL_PERM_REMOVEFILE', 19);
define('DCL_PERM_VIEWWIKI', 20);
define('DCL_PERM_PUBLICONLY', 21);
define('DCL_PERM_VIEWFILE', 22);
define('DCL_PERM_AUDIT', 23);
define('DCL_PERM_VERSIONCHECK', 24);

// Audit events
define('DCL_EVENT_ADD', 1);
define('DCL_EVENT_DELETE', 2);

// Form states
define('DCL_FORM_ADD', 1);
define('DCL_FORM_MODIFY', 2);
define('DCL_FORM_DELETE', 3);
define('DCL_FORM_COPY', 4);
define('DCL_FORM_COPYFROMTICKET', 5);

// BuildManager 
define('DCL_BUILDMANAGER_SUBMIT', 1);
define('DCL_BUILDMANAGER_APPLIED', 2);
define('DCL_BUILDMANAGER_COMPLETE', 3);

// Smarty settings
define('SMARTY_DIR', DCL_ROOT . 'inc/');

// Others
define('DCL_NOW', 'now()');

function __autoload($className)
{
	if (file_exists(DCL_ROOT . 'inc/' . $className . '.php'))
	{
		require_once(DCL_ROOT . 'inc/' . $className . '.php');
		return;
	}

	$areas = array('Controller' => 'controllers',
					'Model' => 'models',
					'Presenter' => 'presenters',
					'Exception' => 'exceptions',
					'Helper' => 'helpers');

	foreach ($areas as $suffix => $directory)
	{
		if (substr($className, -strlen($suffix)) == $suffix && file_exists(DCL_ROOT . 'inc/' . $directory . '/' . $className . '.php'))
		{
			require_once(DCL_ROOT . 'inc/' . $directory . '/' . $className . '.php');
			return;
		}
	}

	if (substr($className, 0, 11) === 'DCL_Plugin_')
	{
		$pluginParts = explode('_', $className, 4);
		if (count($pluginParts) > 3)
		{
			$classPath = GetPluginDir() . strtolower($pluginParts[2]) . '/DCL_Plugin_' . $pluginParts[2] . '_' . $pluginParts[3] . '.php';
			if (file_exists($classPath))
			{
				require_once($classPath);
			}
		}

		return;
	}

	if ($className == 'Smarty')
	{
		require_once(DCL_ROOT . 'inc/Smarty.class.php');
		return;
	}

	if ($className === 'pData')
	{
		require_once(DCL_ROOT . 'vendor/pChart/pData.class');
		return;
	}

	if ($className === 'pChart')
	{
		require_once(DCL_ROOT . 'vendor/pChart/pChart.class');
		return;
	}
}

function menuLink($target = '', $params = '')
{
	if ($target == '')
		$target = DCL_WWW_ROOT . 'main.php';

	if (substr($target, 0, strlen(DCL_WWW_ROOT)) == DCL_WWW_ROOT)
		$sRet = substr($target, strlen(DCL_WWW_ROOT));
	else
		$sRet = $target;

	if ($params != '')
		$sRet .= '?' . $params;

	return $sRet;
}

function UrlAction($controller, $action, $params = '')
{
	return DCL_WWW_ROOT . 'main.php?menuAction=' . $controller . '.' . $action . ($params != '' ? '&' : '') . $params;
}

function SetRedirectMessage($title, $text)
{
	global $g_oSession;
	
	$g_oSession->Register('REDIRECT_TITLE', $title);
	$g_oSession->Register('REDIRECT_TEXT', $text);
	$g_oSession->Edit();
}

function RedirectToAction($controller, $action, $params = '')
{
	header('Location: ' . UrlAction($controller, $action, $params));
	exit;
}

function UseHttps()
{
	global $dcl_info;

	// These values may or may not fill in and depend on the web server, so we'll check the common settings
	// To force secure Gravatar request (if this function doesn't work), set DCL_FORCE_SECURE_GRAVATAR = Y in configuration
	return (IsSet($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' && $_SERVER['HTTPS'] != '0') || $_SERVER['SERVER_PORT'] == 443;
}

function GPCStripSlashes($thisString)
{
	if (get_magic_quotes_gpc() == 0)
		return $thisString;

	return stripslashes($thisString);
}

function CleanArray(&$aArray)
{
	if (get_magic_quotes_gpc() == 0)
		return;

	foreach ($aArray as $k => $v)
	{
		if (!is_array($aArray[$k]))
		{
			$aArray[$k] = GPCStripSlashes($aArray[$k]);
		}
		else
		{
		    CleanArray($aArray[$k]);
		}
	}
}

function GetPrefLang()
{
	global $dcl_info, $g_oSession;

	// TODO: Allow browser override and check if locale available, if so configured
	$lang = '';
	if (is_object($g_oSession))
	{
		$oPrefs = new dbPreferences();
		$oPrefs->preferences_data = $g_oSession->Value('dcl_preferences');

		$lang = $oPrefs->Value('DCL_PREF_LANGUAGE');
	}

	if ($lang == '')
	{
		if (isset($dcl_info) && is_array($dcl_info) && isset($dcl_info['DCL_DEFAULT_LANGUAGE']))
			$lang = $dcl_info['DCL_DEFAULT_LANGUAGE'];
		else
			$lang = 'en';
	}

	return $lang;
}

function LoadStringResource($name)
{
	include_once(sprintf(DCL_ROOT . 'lang/%s/%s.php', GetPrefLang(), $name));
}

function LoadSchema($sTableName)
{
	if (!isset($GLOBALS['phpgw_baseline']) || !is_array($GLOBALS['phpgw_baseline']))
		$GLOBALS['phpgw_baseline'] = array();

	include_once(sprintf(DCL_ROOT . 'schema/schema.%s.php', $sTableName));
}

function Invoke($sClassMethod)
{
	global $dcl_info;
	
	if ($dcl_info['DCL_SEC_AUDIT_ENABLED']=='Y' && $dcl_info['DCL_SEC_AUDIT_LOGIN_ONLY'] == 'N')
	{
		$oSecAuditDB = new dbSecAudit();
		$paramArray = array('ticketid' => null, 'jcn' => null, 'seq' => null, 'begindate' => null, 'enddate' => null, 'project' => null, 'org_id' => null, 'contact_id' => null, 'id' => null);

		$values = '';
		foreach ($paramArray as $param => $value)
		{
			if (isset($_REQUEST[$param]))
			{
				if ($values != '')
				{
					$values .= ', ';
				}

				$values .= $param . '=>' . $_REQUEST[$param];
			}
		}

		$oSecAuditDB->Add($menuAction, $values);
	}

	list($class, $method) = explode(".", $sClassMethod);
	if (!class_exists($class))
	{
		$class .= 'Controller';
		if (!class_exists($class))
		{
			trigger_error('Invoke could not find class: ' . $class, E_USER_ERROR);
			return;
		}
	}

	$obj = new $class();
	if (!method_exists($obj, $method))
	{
		trigger_error('Class ' . $class . ' does not contain a definition for method ' . $method, E_USER_ERROR);
		return;
	}
	
	$obj->$method();
}

function InvokePlugin($sPluginName, &$aParams = null, $method = 'Invoke')
{	
	list($type, $name) = explode(".", $sPluginName);
	$class = 'DCL_Plugin_' . $type . '_' . $name;
	
	if (!class_exists($class))
	{
		// If we can't import it, no plugin has been set up
		return;
	}
	
	$obj = new $class();
	if (!method_exists($obj, $method))
	{
		trigger_error('Plugin class ' . $class . ' does not contain a definition for method ' . $method, E_USER_ERROR);
		return;
	}
	
	$obj->$method($aParams);
}

function EvaluateReturnTo()
{
	global $g_oSec;
	
	// Always check the return value of this function to see if processing should continue
	// or the method should be invoked
	if (defined('EVALUATE_RETURN_TO_CALLED'))
		return EVALUATE_RETURN_TO_CALLED;
	
	if (IsSet($_REQUEST['return_to']))
	{
		$aReturnTo = array();
		parse_str($_REQUEST['return_to'], $aReturnTo);

		if (count($aReturnTo) > 0 && isset($aReturnTo['menuAction']))
		{
			foreach ($aReturnTo as $sKey => $oValue)
				$_REQUEST[$sKey] = $oValue;
			
			if ($g_oSec->ValidateMenuAction() == true)
			{
				Invoke($_REQUEST['menuAction']);
	
				define('EVALUATE_RETURN_TO_CALLED', true);
				return true;
			}
		}
	}

	define('EVALUATE_RETURN_TO_CALLED', false);
	return false;
}

function &CreateViewObject($sType = '')
{
	$oRetVal = null;

	switch ($sType)
	{
		case 'workorders':
			$oRetVal = new htmlWorkOrderResults();
			break;
		case 'tickets':
			$oRetVal = new htmlTicketResults();
			break;
		case 'dcl_product_module':
			$oRetVal = new htmlProductModuleView();
			break;
		default:
			$oRetVal = new htmlView();
	}
	
	return $oRetVal;
}

function GetPluginDir()
{
	global $dcl_info;
	
	return $dcl_info['DCL_FILE_PATH'] . '/plugins/';
}

function IsTemplateValid($sTemplate)
{
	if ($sTemplate == null || trim($sTemplate) == '')
		return false;
	
	return file_exists(DCL_ROOT . 'templates/' . $sTemplate);
}

function GetDefaultTemplateSet()
{
	// Session must be initialized before calling this!
	global $g_oSession, $dcl_info;

	if (isset($g_oSession) || is_object($g_oSession))
	{
		$o = new dbPreferences();
		$o->preferences_data = $g_oSession->Value('dcl_preferences');

		if (IsTemplateValid($o->Value('DCL_PREF_TEMPLATE_SET')))
			return $o->Value('DCL_PREF_TEMPLATE_SET');
	}

	if (IsTemplateValid($dcl_info['DCL_DEF_TEMPLATE_SET']))
		return $dcl_info['DCL_DEF_TEMPLATE_SET'];
		
	return 'default';
}

function CreateTemplate($arrTemplate)
{
	global $dcl_info, $phpgw;

	// Create a template object and hook it up to the template in the
	// configured template set
	$Template = new DCLTemplate();
	$Template->set_root(DCL_ROOT . 'templates/' . GetDefaultTemplateSet());
	$Template->set_file($arrTemplate);

	return $Template;
}

function GetHiddenVar($var, $val)
{
	return '<input type="hidden" name="' . $var . '" value="' . $val . '">';
}

function array_remove_keys(&$aArray, $vKeys)
{
	if (!is_array($vKeys))
	{
		if (isset($aArray[$vKeys]))
			unset($aArray[$vKeys]);
	}
	else
	{
		foreach ($vKeys as $key => $value)
		{
			if (isset($aArray[$key]))
				unset($aArray[$key]);
		}
	}
}

function GetJSDateFormat()
{
	global $dcl_info;

	$calDateFormat = str_replace('m', 'mm', $dcl_info['DCL_DATE_FORMAT']);
	$calDateFormat = str_replace('d', 'dd', $calDateFormat);
	return str_replace('Y', 'y', $calDateFormat);
}

function buildMenuArray()
{
	global $dcl_info, $DCL_MENU, $g_oSec;

	// TODO: remove after implementing module enable/disable
	$dcl_info['DCL_MODULE_WO_ENABLED'] = true;
	$dcl_info['DCL_MODULE_PROJECTS_ENABLED'] = true;
	$dcl_info['DCL_MODULE_TICKETS_ENABLED'] = true;
	$dcl_info['DCL_MODULE_TESTS_ENABLED'] = false;
	$dcl_info['DCL_MODULE_SPECS_ENABLED'] = false;

	$DCL_MENU = array();

	if ($dcl_info['DCL_MODULE_WO_ENABLED'])
	{
		$aViews = array();
		if ($g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH))
		{
			$oDB = new dbViews();
			if ($oDB->ListByUser($GLOBALS['DCLID'], DCL_ENTITY_WORKORDER) !== -1)
			{
				while ($oDB->next_record())
				{
					$aViews[$oDB->f('name')] = array('boViews.exec&viewid=' . $oDB->f('viewid'), true);
				}
			}
		}
		
		$DCL_MENU[DCL_MENU_WORKORDERS] = array(
				DCL_MENU_MYWOS => array('htmlWorkorders.show&filterReportto=' . $GLOBALS['DCLID'], $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ACTION)),
				DCL_MENU_NEW => array('boWorkorders.newjcn', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_ADD)),
				DCL_MENU_IMPORT => array('boWorkorders.csvupload', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_IMPORT)),
				DCL_MENU_ACTIVITY => array('reportPersonnelActivity.getparameters', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_REPORT)),
				DCL_MENU_GRAPH => array('boWorkorders.graph', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_REPORT)),
				DCL_MENU_STATISTICS => array('htmlWOStatistics.ShowUserVsProductStatusForm', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_REPORT)),
				'Metrics' => array('htmlMetricsWorkOrders.getparameters', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_REPORT)),
				DCL_MENU_SCHEDULE => array('scheduleByPerson.SelectPersonToSchedule', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_SCHEDULE)),
				DCL_MENU_SEARCH => array('htmlWOSearches.Show', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH)),
				DCL_MENU_VIEWS => array($aViews, count($aViews) > 0),
				DCL_MENU_BROWSE => array('htmlWorkorders.show', $g_oSec->HasAnyPerm(array(DCL_ENTITY_WORKORDER => array($g_oSec->PermArray(DCL_PERM_VIEW), $g_oSec->PermArray(DCL_PERM_VIEWSUBMITTED), $g_oSec->PermArray(DCL_PERM_VIEWACCOUNT)))))
			);
	}

	if ($dcl_info['DCL_MODULE_PROJECTS_ENABLED'])
	{
		$DCL_MENU[DCL_MENU_PROJECTS] = array(
				DCL_MENU_MYPROJECTS => array('htmlProjects.show&filterReportto=' . $GLOBALS['DCLID'], $g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEW)),
				DCL_MENU_NEW => array('boProjects.newproject', $g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_ADD)),
				DCL_MENU_VIEW => array('htmlProjects.show', $g_oSec->HasPerm(DCL_ENTITY_PROJECT, DCL_PERM_VIEW))
			);
	}

	if ($dcl_info['DCL_MODULE_TICKETS_ENABLED'])
	{
		$aViews = array();
		if ($g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_SEARCH))
		{
			$oDB = new dbViews();
			if ($oDB->ListByUser($GLOBALS['DCLID'], DCL_ENTITY_TICKET) !== -1)
			{
				while ($oDB->next_record())
				{
					$aViews[$oDB->f('name')] = array('boViews.exec&viewid=' . $oDB->f('viewid'), true);
				}
			}
		}
		
		$DCL_MENU[DCL_MENU_TICKETS] = array(
				DCL_MENU_MYTICKETS => array('htmlTickets.show&filterReportto=' . $GLOBALS['DCLID'], $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ACTION)),
				DCL_MENU_NEW => array('boTickets.add', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ADD)),
				DCL_MENU_ACTIVITY => array('reportTicketActivity.getparameters', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_REPORT)),
				DCL_MENU_GRAPH => array('boTickets.graph', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_REPORT)),
				DCL_MENU_STATISTICS => array('htmlTicketStatistics.ShowUserVsProductStatusForm', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_REPORT)),
				DCL_MENU_SEARCH => array('htmlTicketSearches.Show', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_SEARCH)),
				DCL_MENU_VIEWS => array($aViews, count($aViews) > 0),
				DCL_MENU_BROWSE => array('htmlTickets.show', $g_oSec->HasAnyPerm(array(DCL_ENTITY_TICKET => array($g_oSec->PermArray(DCL_PERM_VIEW), $g_oSec->PermArray(DCL_PERM_VIEWSUBMITTED), $g_oSec->PermArray(DCL_PERM_VIEWACCOUNT)))))
			);
	}
	
	if ($dcl_info['DCL_MODULE_TESTS_ENABLED'])
	{
		$DCL_MENU['Testing'] = array(
				'New Test Case' => array('', true),
				'Search Test Cases' => array('', true),
				'New Test Condition' => array('', true),
				'Run Test Conditions' => array('', true),
				'Browse Test Conditions' => array('', true),
				'Search Test Conditions' => array('', true)
			);
	}
	
	if ($dcl_info['DCL_MODULE_SPECS_ENABLED'])
	{
		$DCL_MENU['Specs'] = array(
				'New Functional Spec' => array('', true),
				'Browse Functional Specs' => array('', true),
				'Search Functional Specs' => array('', true),
				'New Use Case' => array('', true),
				'Browse Use Cases' => array('', true),
				'Search Use Cases' => array('', true)
		);
	}
	
	$DCL_MENU[DCL_MENU_MANAGE] = array(
			'Organizations' => array('htmlOrgBrowse.show&filterActive=Y', $g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_VIEW)),
			'Contacts' => array('htmlContactBrowse.show&filterActive=Y', $g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_VIEW)),
			STR_CMMN_TAGS => array('htmlTags.browse', $g_oSec->HasPerm(DCL_ENTITY_WORKORDER, DCL_PERM_SEARCH) || $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_SEARCH)),
			DCL_MENU_CHECKLISTS => array('boChecklists.show', $g_oSec->HasPerm(DCL_ENTITY_FORMS, DCL_PERM_VIEW)),
			'Workspaces' => array('htmlWorkspaceBrowse.show', $g_oSec->HasPerm(DCL_ENTITY_WORKSPACE, DCL_PERM_VIEW)),
			'Hotlists' => array('htmlHotlistBrowse.show', $g_oSec->HasPerm(DCL_ENTITY_HOTLIST, DCL_PERM_VIEW)),
			DCL_MENU_PRODUCTS => array('htmlProducts.PrintAll', $g_oSec->HasPerm(DCL_ENTITY_PRODUCT, DCL_PERM_VIEW)),
			DCL_MENU_VIEWS => array('htmlViews.PrintAll', $g_oSec->HasPerm(DCL_ENTITY_SAVEDSEARCH, DCL_PERM_VIEW)),
			DCL_MENU_WATCHES => array('boWatches.showall', $g_oSec->HasAnyPerm(array(DCL_ENTITY_TICKET => array($g_oSec->PermArray(DCL_PERM_VIEW), $g_oSec->PermArray(DCL_PERM_VIEWSUBMITTED), $g_oSec->PermArray(DCL_PERM_VIEWACCOUNT)),
																					DCL_ENTITY_WORKORDER => array($g_oSec->PermArray(DCL_PERM_VIEW), $g_oSec->PermArray(DCL_PERM_VIEWSUBMITTED), $g_oSec->PermArray(DCL_PERM_VIEWACCOUNT))))),
			DCL_MENU_AGGREGATESTATS => array('htmlAgg.Init', $g_oSec->HasAnyPerm(array(DCL_ENTITY_TICKET => array($g_oSec->PermArray(DCL_PERM_REPORT)), DCL_ENTITY_WORKORDER => array($g_oSec->PermArray(DCL_PERM_REPORT))))),
			'Metrics' => array('htmlMetrics.show', $g_oSec->HasAnyPerm(array(DCL_ENTITY_TICKET => array($g_oSec->PermArray(DCL_PERM_REPORT)), DCL_ENTITY_WORKORDER => array($g_oSec->PermArray(DCL_PERM_REPORT))))),
			DCL_MENU_MAINWIKI => array('htmlWiki.show&name=FrontPage&type=0', $g_oSec->HasPerm(DCL_ENTITY_GLOBAL, DCL_PERM_VIEWWIKI))
		);

	$DCL_MENU[DCL_MENU_ADMIN] = array(
			DCL_MENU_CHANGEPASSWORD => array('boPersonnel.passwd', $g_oSec->HasPerm(DCL_ENTITY_PREFS, DCL_PERM_PASSWORD)),
			DCL_MENU_PREFERENCES => array('htmlPreferences.modify', $g_oSec->HasPerm(DCL_ENTITY_PREFS, DCL_PERM_MODIFY)),
			DCL_MENU_SYSTEMSETUP => array('SystemSetup.Index', $g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW)),
			DCL_MENU_SESSIONS => array('htmlSession.Show', $g_oSec->HasPerm(DCL_ENTITY_SESSION, DCL_PERM_VIEW)),
			DCL_MENU_SEC_AUDITING => array('boSecAudit.Show', $g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_MODIFY))
		);

	if ($dcl_info['DCL_WIKI_ENABLED'] != 'Y' && isset($DCL_MENU[DCL_MENU_ADMIN][DCL_MENU_MAINWIKI]))
		unset($DCL_MENU[DCL_MENU_ADMIN][DCL_MENU_MAINWIKI]);

	if ($dcl_info['DCL_SCCS_ENABLED'] != 'Y' && isset($DCL_MENU[DCL_MENU_ADMIN]['Metrics']))
		unset($DCL_MENU[DCL_MENU_ADMIN]['Metrics']);

	InvokePlugin('UI.Menu', $DCL_MENU);

	$DCL_MENU[DCL_MENU_HELP] = array(
			DCL_MENU_FAQS => array('boFaq.ShowAll', $g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_VIEW)),
			DCL_MENU_DCLHOMEPAGE => array('http://dcl.sourceforge.net/index.php', true),
			'GNU Enterprise' => array('http://www.gnuenterprise.org/index.php', true),
			DCL_MENU_LICENSEINFO => array(DCL_WWW_ROOT . 'gpl.php', true),
			DCL_MENU_VERSIONINFO => array('htmlVersion.DisplayVersionInfo', true)
		);
}

function commonHeader($formValidateSrc = '', $onLoad = '')
{
	if (defined('HTML_HEADER_GENERATED'))
		return;

	header('Content-Type: text/html; charset=iso-8859-1');
	header('Expires: Fri, 11 Oct 1991 17:01:00 GMT');
	header('Cache-Control: no-cache, must-revalidate');

	global $g_oSession, $dcl_info, $dcl_domain, $dcl_domain_info;
	define('HTML_HEADER_GENERATED', 1);
	
	$bHideMenu = (isset($_REQUEST['hideMenu']) && $_REQUEST['hideMenu'] == 'true');

	$title = '[' . $dcl_domain_info[$dcl_domain]['name'] . ' / ' . $GLOBALS['DCLNAME'] . ']';
	if ($dcl_info['DCL_HTML_TITLE'] != '')
		$title .= '&nbsp;-&nbsp;' . $dcl_info['DCL_HTML_TITLE'];

	if (!$bHideMenu)
	{
		LoadStringResource('menu');
		buildMenuArray();
	}
	
	$t = new DCL_Smarty();
	$t->assign('VAL_TITLE', $title);
	$t->Render('index.tpl');

	$sTemplateSet = GetDefaultTemplateSet();
	if (!$bHideMenu && file_exists(DCL_ROOT . 'templates/' . $sTemplateSet . '/menu.php'))
	{
		include(DCL_ROOT . 'templates/' . $sTemplateSet . '/menu.php');
		renderDCLMenu();
	}

	if ($g_oSession->IsRegistered('REDIRECT_TEXT'))
	{
		$presenter = new RedirectMessagePresenter();
		$presenter->Render();
		
		$g_oSession->Unregister('REDIRECT_TITLE');
		$g_oSession->Unregister('REDIRECT_TEXT');
		$g_oSession->Edit();
	}
}

function ExportArray(&$aFieldNames, &$aData, $filename = 'dclexport.txt')
{
	// Silent function to export tab delimited file and force browser to
	// force the user to save the file.
	header('Content-Type: application/binary; name="' . $filename . '"');
	header('Content-Disposition: attachment; filename="' . $filename . '"');

	// Make object, run query, and (for now) blindly dump data.  The first
	// record will contain column headings.  Any tabs within data will be replaced
	// by spaces since our fields are tab delimited.
	$record = '';
	if (count($aFieldNames) > 0)
	{
		foreach ($aFieldNames as $val)
		{
			$val = str_replace(phpTab, ' ', $val);
			if ($record != '')
				$record .= phpTab;

			$record .= $val;
		}
	}

	// Output field headings
	echo $record . phpCrLf;

	// Now for the records
	for ($i = 0; $i < count($aData); $i++)
	{
		$record = '';
		for ($j = 0; $j < count($aData[$i]); $j++)
		{
			if ($j > 0)
				$record .= phpTab;

			$record .= str_replace(phpTab, ' ', $aData[$i][$j]);
		}

		echo $record . phpCrLf;
	}

	exit; // Don't output footer
}

function ShowDeleteYesNo($title, $action, $id, $name, $canBeDeactivated = true, $idfield = 'id', $id2 = 0, $id2field = '')
{
	global $dcl_info;

	$t = new DCL_Smarty();

	$t->assign('TXT_TITLE', sprintf(STR_CMMN_DELETEITEM, $title));
	if ($canBeDeactivated)
	{
		$t->assign('TXT_DEACTIVATENOTE', STR_CMMN_DEACTIVATENOTE);
	}
	
	$t->assign('VAL_MENUACTION', $action);
	$t->assign('VAL_IDFIELD', $idfield);
	$t->assign('VAL_ID', $id);

	if ($id2field != '')
	{
		$t->assign('VAL_ID2FIELD', $id2field);
		$t->assign('VAL_ID2', $id2);
	}

	$t->assign('VAL_NAME', $name);
	$t->assign('VAL_WARNING', sprintf(STR_CMMN_DELETECONFIRM, $title, $name));

	$t->Render('htmlDeleteItem.tpl');
}

function GetYesNoCombo($default = 'Y', $cbName = 'active', $size = 0, $noneOption = true)
{
	$str = "<select id=\"$cbName\" name=\"$cbName";
	if ($size > 0)
		$str .= '[]" multiple size=' . $size;
	else
		$str .= '"';
	$str .= '>';
	if ($size == 0 && $noneOption == true)
		$str .= sprintf('<option value="?">%s', STR_CMMN_SELECTONE);

	$str .= '<option value="Y"';
	if ((is_array($default) && in_array('Y', $default)) || $default === 'Y')
		$str .= ' selected';

	$str .= sprintf('>%s</option>', STR_CMMN_YES);

	$str .= '<option value="N"';

	if ((is_array($default) && in_array('N', $default)) || $default === 'N')
		$str .= ' selected';

	$str .= sprintf('>%s</option>', STR_CMMN_NO);

	$str .= '</select>';

	return $str;
}

function ShowInfo($sMessage, $sFile, $iLine, $aBacktrace)
{
	$o = htmlMessageInfo::GetInstance();
	$o->SetShow($sMessage, $sFile, $iLine, $aBacktrace);
}

function ShowWarning($sMessage, $sFile, $iLine, $aBacktrace)
{
	$o = htmlMessageWarning::GetInstance();
	$o->SetShow($sMessage, $sFile, $iLine, $aBacktrace);
}

function ShowError($sMessage, $sFile, $iLine, $aBacktrace)
{
	$o = htmlMessageError::GetInstance();
	$o->SetShow($sMessage, $sFile, $iLine, $aBacktrace);
}

function dcl_error_handler($errno, $errstr, $errfile, $errline)
{
	global $g_oPage, $g_oSec;
	
	if (!($errno & error_reporting()))
		return;

	$aBacktrace = array();

	if (function_exists('debug_backtrace'))
	{
		$aBacktrace = debug_backtrace();
	}

 	switch ($errno)
	{
		case E_COMPILE_ERROR:
		case E_PARSE:
		case E_CORE_ERROR:
		case E_USER_ERROR:
		case E_ERROR:
			ShowError($errstr, $errfile, $errline, $aBacktrace);
			break;
		case E_CORE_WARNING:
		case E_USER_WARNING:
		case E_WARNING:
			ShowWarning($errstr, $errfile, $errline, $aBacktrace);
			break;
		case E_NOTICE:
		case E_USER_NOTICE:
			ShowInfo($errstr, $errfile, $errline, $aBacktrace);
			break;
	}

	if ($errno == E_COMPILE_ERROR || $errno == E_PARSE)
	{
		$g_oPage->EndPage();
		exit(255);
	}
}

error_reporting(E_ALL);
set_error_handler('dcl_error_handler');
