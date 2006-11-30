<?php
/*
 * $Id: functions.inc.php,v 1.1.1.1 2006/11/27 05:30:50 mdean Exp $
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

// Embedded state
define('DCL_STANDALONE', 0);
define('DCL_PHPGW', 1);
define('DCL_XOOPS', 2);

// Audit events
define('DCL_EVENT_ADD', 1);
define('DCL_EVENT_DELETE', 2);

// Form states
define('DCL_FORM_ADD', 1);
define('DCL_FORM_MODIFY', 2);
define('DCL_FORM_DELETE', 3);
define('DCL_FORM_COPY', 4);
define('DCL_FORM_COPYFROMTICKET', 5);

// Smarty settings
define('SMARTY_DIR', DCL_ROOT . 'inc/');

function menuLink($target = '', $params = '')
{
	global $phpgw;

	if (defined('DCL_EMBEDDED_STATE') && DCL_EMBEDDED_STATE == DCL_PHPGW)
	{
		// In phpGW, this must be installed under /dcl
		if ($target == '')
			$target = '/dcl/main.php';

		return $phpgw->link($target, $params);
	}

	if ($target == '')
		$target = DCL_WWW_ROOT . 'main.php';

	if (substr($target, 0, strlen(DCL_WWW_ROOT)) == DCL_WWW_ROOT)
		$sRet = substr($target, DCL_WWW_ROOT);
	else
		$sRet = $target;

	if ($params != '')
		$sRet .= '?' . $params;

	return $sRet;
}

function GetSourceArray()
{
	if ($GLOBALS['HTTP_SERVER_VARS']['REQUEST_METHOD'] == 'GET')
		return 'HTTP_GET_VARS';

	return 'HTTP_POST_VARS';
}

function GPCStripSlashes($thisString)
{
	if (get_magic_quotes_gpc() == 0)
		return $thisString;

	return stripslashes($thisString);
}

function CleanVars($which)
{
	if (get_magic_quotes_gpc() == 0)
		return;

	foreach ($GLOBALS[$which] as $k => $v)
	{
		if (!is_array($GLOBALS[$which][$k]))
		{
			$GLOBALS[$which][$k] = GPCStripSlashes($GLOBALS[$which][$k]);
		}
		else
		{
		    $GLOBALS[$which][$k] = CleanArray($GLOBALS[$which][$k]);
		}
	}
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
		$oPrefs = CreateObject('dcl.dbPreferences');
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
	list($class, $method) = explode(".", $sClassMethod);
	import($class);
	if (!class_exists($class))
	{
		trigger_error('Invoke could not find class: ' . $class, E_USER_ERROR);
		return;
	}

	$obj = new $class;
	if (!method_exists($obj, $method))
	{
		trigger_error('Class ' . $class . ' does not contain a definition for method ' . $method, E_USER_ERROR);
		return;
	}
	
	$obj->$method();
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

function import($className)
{
	if (!file_exists(DCL_ROOT . 'inc/class.' . $className . '.inc.php'))
	{
		trigger_error('Class not found: ' . $className, E_USER_ERROR);
		return;
	}
	
	include_once(DCL_ROOT . 'inc/class.' . $className . '.inc.php');
}

function &CreateViewObject($sType = '')
{
	$oRetVal = null;

	switch ($sType)
	{
		case 'workorders':
			$oRetVal = CreateObject('dcl.htmlWorkOrderResults');
			break;
		case 'tickets':
			$oRetVal = CreateObject('dcl.htmlTicketResults');
			break;
		case 'dcl_product_module':
			$oRetVal = CreateObject('dcl.htmlProductModuleView');
			break;
		default:
			$oRetVal = CreateObject('dcl.htmlView');
	}
	
	return $oRetVal;
}

if (!function_exists('CreateObject'))
{
	function &CreateObject($className)
	{
		$className = substr($className, 4);

		import($className);

		$obj = new $className;

		return $obj;
	}
}

function &GetAuthenticator()
{
	$oRetVal = null;
	
	if (DCL_EMBEDDED_STATE == DCL_PHPGW)
		$oRetVal = CreateObject('dcl.boAuthenticatePHPGW');
	else if (DCL_EMBEDDED_STATE == DCL_XOOPS)
		$oRetVal = CreateObject('dcl.boAuthenticateXOOPS');
	else
		$oRetVal = CreateObject('dcl.boAuthenticate');
		
	return $oRetVal;
}

function &GetPageObject()
{
	$oRetVal = null;
	
	if (DCL_EMBEDDED_STATE == DCL_PHPGW)
		$oRetVal = CreateObject('dcl.PagePHPGW');
	else if (DCL_EMBEDDED_STATE == DCL_XOOPS)
		$oRetVal = CreateObject('dcl.PageXOOPS');
	else
		$oRetVal = CreateObject('dcl.Page');
		
	return $oRetVal;
}

function GetDefaultTemplateSet()
{
	// Session must be initialized before calling this!
	global $g_oSession, $dcl_info;

	if (isset($g_oSession) || is_object($g_oSession))
	{
		$o = CreateObject('dcl.dbPreferences');
		$o->preferences_data = $g_oSession->Value('dcl_preferences');

		if ($o->Value('DCL_PREF_TEMPLATE_SET') != '')
			return $o->Value('DCL_PREF_TEMPLATE_SET');
	}

	return $dcl_info['DCL_DEF_TEMPLATE_SET'];
}

function CreateTemplate($arrTemplate)
{
	global $dcl_info, $phpgw;

	// Create a template object and hook it up to the template in the
	// configured template set
	$Template = CreateObject('dcl.DCLTemplate');
	$Template->set_root(DCL_ROOT . 'templates/' . GetDefaultTemplateSet());
	$Template->set_file($arrTemplate);

	return $Template;
}

function &CreateSmarty()
{
	require_once(DCL_ROOT . 'inc/Smarty.class.php');

	$sDefaultTemplateSet = GetDefaultTemplateSet();

	$oSmarty = new Smarty;
	$oSmarty->assign('DIR_JS', DCL_WWW_ROOT . "templates/$sDefaultTemplateSet/js/");
	$oSmarty->assign('DIR_CSS', DCL_WWW_ROOT . "templates/$sDefaultTemplateSet/css/");
	$oSmarty->assign('DIR_IMG', DCL_WWW_ROOT . "templates/$sDefaultTemplateSet/img/");
	$oSmarty->assign('WWW_ROOT', DCL_WWW_ROOT);
	$oSmarty->assign('URL_MAIN_PHP', menuLink());

	return $oSmarty;
}

function SmartyInit(&$oSmarty, &$sTemplateName, $sTemplateSet = '')
{
	if ($sTemplateSet == '')
		$sDefaultTemplateSet = GetDefaultTemplateSet();
	else
		$sDefaultTemplateSet = $sTemplateSet;

	$oSmarty->template_dir = DCL_ROOT . "templates/$sDefaultTemplateSet/";
	if (!$oSmarty->template_exists($sTemplateName) && $sDefaultTemplateSet != 'default')
	{
		$sDefaultTemplateSet = 'default';
		$oSmarty->template_dir = DCL_ROOT . "templates/default/";
		if (!$oSmarty->template_exists($sTemplateName))
		{
			trigger_error("Cannot find template [$sTemplateName]");
			return;
		}
	}

	// Have the template
	$oSmarty->compile_dir = DCL_ROOT . 'templates/' . $sDefaultTemplateSet . '/templates_c';
}

function SmartyDisplay(&$oSmarty, $sTemplateName, $sTemplateSet = '')
{
	SmartyInit($oSmarty, $sTemplateName, $sTemplateSet);
	$oSmarty->display($sTemplateName);
}

function SmartyFetch(&$oSmarty, $sTemplateName, $sTemplateSet = '')
{
	SmartyInit($oSmarty, $sTemplateName, $sTemplateSet);
	return $oSmarty->fetch($sTemplateName);
}

function RefreshTop($sRefreshTo)
{
	$t = CreateTemplate(array('hForm' => 'refreshTop.tpl'));
	$t->set_var('LNK_REFRESH', $sRefreshTo);
	$t->pparse('out', 'hForm');
	exit();
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

function GetHelpLink($dark = false)
{
	global $dcl_info;

	$linkText = '?';

	$linkClass = $dark == true ? 'adark' : 'alight';

	return sprintf('[&nbsp;<a class="%s" href="#" onClick="javascript:window.open(\'./str/%s/help/%s.php\', \'dclhelp\', \'width=400,height=400,resizable=yes,scrollbars=yes\');">%s</a>&nbsp;]',
		$linkClass,
		$dcl_info['DCL_DEFAULT_LANGUAGE'],
		$_REQUEST['menuAction'],
		$linkText);
}

function GetJSDateFormat()
{
	global $dcl_info;

	$calDateFormat = str_replace('m', 'mm', $dcl_info['DCL_DATE_FORMAT']);
	$calDateFormat = str_replace('d', 'dd', $calDateFormat);
	return str_replace('Y', 'y', $calDateFormat);
}

function IncludeCalendar()
{
	if (defined('DCL_CALENDAR_INCLUDED'))
		return;

	define('DCL_CALENDAR_INCLUDED', 1);

	$t = CreateTemplate(array('hForm' => 'htmlCalendar.tpl'));

	$calDateFormat = str_replace('mm', '%m', GetJSDateFormat());
	$calDateFormat = str_replace('dd', '%d', $calDateFormat);
	$calDateFormat = str_replace('y', '%Y', $calDateFormat);

	$t->set_var('VAL_JSDATEFORMAT', $calDateFormat);
	$t->pparse('out', 'hForm');
}

function buildMenuArray()
{
	global $dcl_info, $DCL_MENU, $g_oSec;

	// Is DCL contained in another app?
	$bContained = (DCL_EMBEDDED_STATE != DCL_STANDALONE);

	// TODO: remove after implementing module enable/disable
	$dcl_info['DCL_MODULE_WO_ENABLED'] = true;
	$dcl_info['DCL_MODULE_PROJECTS_ENABLED'] = true;
	$dcl_info['DCL_MODULE_TICKETS_ENABLED'] = true;

	$DCL_MENU = array();
	if (!$bContained)
		$DCL_MENU[DCL_MENU_HOME] = array('htmlMyDCL.show', true);

	if ($dcl_info['DCL_MODULE_WO_ENABLED'])
	{
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
		$DCL_MENU[DCL_MENU_TICKETS] = array(
				DCL_MENU_MYTICKETS => array('htmlTickets.show&filterReportto=' . $GLOBALS['DCLID'], $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ACTION)),
				DCL_MENU_NEW => array('boTickets.add', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_ADD)),
				DCL_MENU_ACTIVITY => array('reportTicketActivity.getparameters', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_REPORT)),
				DCL_MENU_GRAPH => array('boTickets.graph', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_REPORT)),
				DCL_MENU_STATISTICS => array('htmlTicketStatistics.ShowUserVsProductStatusForm', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_REPORT)),
				DCL_MENU_SEARCH => array('htmlTicketSearches.Show', $g_oSec->HasPerm(DCL_ENTITY_TICKET, DCL_PERM_SEARCH)),
				DCL_MENU_BROWSE => array('htmlTickets.show', $g_oSec->HasAnyPerm(array(DCL_ENTITY_TICKET => array($g_oSec->PermArray(DCL_PERM_VIEW), $g_oSec->PermArray(DCL_PERM_VIEWSUBMITTED), $g_oSec->PermArray(DCL_PERM_VIEWACCOUNT)))))
			);
	}
	
	$DCL_MENU[DCL_MENU_MANAGE] = array(
			'Organizations' => array('htmlOrgBrowse.show&filterActive=Y', $g_oSec->HasPerm(DCL_ENTITY_ORG, DCL_PERM_VIEW)),
			'Contacts' => array('htmlContactBrowse.show&filterActive=Y', $g_oSec->HasPerm(DCL_ENTITY_CONTACT, DCL_PERM_VIEW)),
			DCL_MENU_CHECKLISTS => array('boChecklists.show', $g_oSec->HasPerm(DCL_ENTITY_FORMS, DCL_PERM_VIEW)),
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
			DCL_MENU_SYSTEMSETUP => array('boAdmin.ShowSystemConfig', $g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_VIEW)),
			DCL_MENU_SESSIONS => array('htmlSession.Show', $g_oSec->HasPerm(DCL_ENTITY_SESSION, DCL_PERM_VIEW))
		);

	if ($dcl_info['DCL_WIKI_ENABLED'] != 'Y' && isset($DCL_MENU[DCL_MENU_ADMIN][DCL_MENU_MAINWIKI]))
		unset($DCL_MENU[DCL_MENU_ADMIN][DCL_MENU_MAINWIKI]);

	if ($dcl_info['DCL_SCCS_ENABLED'] != 'Y' && isset($DCL_MENU[DCL_MENU_ADMIN]['Metrics']))
		unset($DCL_MENU[DCL_MENU_ADMIN]['Metrics']);

	$DCL_MENU[DCL_MENU_HELP] = array(
			DCL_MENU_FAQS => array('boFaq.ShowAll', $g_oSec->HasPerm(DCL_ENTITY_FAQ, DCL_PERM_VIEW)),
			DCL_MENU_CLEARSCREEN => array('clearScreen', true),
			DCL_MENU_DCLHOMEPAGE => array('http://dcl.sourceforge.net/index.php', true),
			'GNU Enterprise' => array('http://www.gnuenterprise.org/index.php', true),
			DCL_MENU_LICENSEINFO => array('gpl.php', true),
			DCL_MENU_VERSIONINFO => array('htmlVersion.DisplayVersionInfo', true)
		);

	if (!$bContained)
		$DCL_MENU[DCL_MENU_LOGOFF] = array('logout.php', true);
}

function GetCharSet()
{
	$lang = GetPrefLang();
	switch ($lang)
	{
		case 'ru':
			return 'koi8-r';
		default:
			return 'iso-8859-1';
	}
}

function commonHeader($formValidateSrc = '', $onLoad = '')
{
	if (defined('HTML_HEADER_GENERATED'))
		return;

	global $phpgw, $dcl_info, $dcl_domain, $dcl_domain_info;
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
	
	$t =& CreateSmarty();

	$t->assign('VAL_TITLE', $title);
	$t->assign('CHARSET', GetCharSet());

	if (DCL_EMBEDDED_STATE == DCL_PHPGW)
		SmartyDisplay($oSmarty, 'contained.tpl');
	else if (DCL_EMBEDDED_STATE == DCL_XOOPS)
		SmartyDisplay($oSmarty, 'xoops.tpl');
	else
		SmartyDisplay($oSmarty, 'index.tpl');

	if (!$bHideMenu && file_exists(DCL_ROOT . 'templates/' . $sTemplateSet . '/menu.php'))
	{
		include(DCL_ROOT . 'templates/' . $sTemplateSet . '/menu.php');
		renderDCLMenu();
	}
}

function ExportArray(&$aFieldNames, &$aData, $filename = 'dclexport.txt')
{
	// Silent function to export tab delimited file and force browser to
	// force the user to save the file.
	header('Content-Type: application/binary; name=dclexport.txt');
	header('Content-Disposition: attachment; filename=dclexport.txt');

	// Make object, run query, and (for now) blindly dump data.  The first
	// record will contain column headings.  Any tabs within data will be replaced
	// by spaces since our fields our tab delimited.
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

// FIXME: only left in htmlChecklistForm
function GetCalendarLink($linkTo)
{
	// Should have called IncludeCalendar() before this code goes out
	$link = '<a href="javascript:doNothing()" ';
	$link .= 'onclick="showCalendar(\'' . $linkTo . '\');">';
	$link .= '<img src="img/calendar.gif" border="0"></a>';

	return $link;
}

function ShowDeleteYesNo($title, $action, $id, $name, $canBeDeactivated = true, $idfield = 'id', $id2 = 0, $id2field = '')
{
	global $dcl_info;

	$t = CreateSmarty();

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

	SmartyDisplay($t, 'htmlDeleteItem.tpl');
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

function PrintPermissionDenied()
{
	trigger_error(STR_CMMN_PERMISSIONDENIED, E_USER_ERROR);

	return -1;
}

function ShowInfo($sMessage, $sFile, $iLine, $aBacktrace)
{
	import('htmlMessageInfo');
	$o = htmlMessageInfo::GetInstance();
	$o->SetShow($sMessage, $sFile, $iLine, $aBacktrace);
}

function ShowWarning($sMessage, $sFile, $iLine, $aBacktrace)
{
	import('htmlMessageWarning');
	$o = htmlMessageWarning::GetInstance();
	$o->SetShow($sMessage, $sFile, $iLine, $aBacktrace);
}

function ShowError($sMessage, $sFile, $iLine, $aBacktrace)
{
	import('htmlMessageError');
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
?>
