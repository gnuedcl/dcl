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

// Common definitions
define('phpCrLf', "\r\n");
define('phpTab', "\t");

// Log levels
define('DCL_LOG_TRACE', 1);
define('DCL_LOG_DEBUG', 2);
define('DCL_LOG_INFO', 3);
define('DCL_LOG_WARN', 4);
define('DCL_LOG_ERROR', 5);
define('DCL_LOG_FATAL', 6);

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
define('DCL_ENTITY_WORKORDER_TASK', 40);
define('DCL_ENTITY_WORKSPACE', 41);
define('DCL_ENTITY_TEST_CASE', 42);
define('DCL_ENTITY_FUNCTIONAL_SPEC', 43);
define('DCL_ENTITY_HOTLIST', 44);
define('DCL_ENTITY_ERRORLOG', 45);
define('DCL_ENTITY_ENVIRONMENT', 46);
define('DCL_ENTITY_OUTAGETYPE', 47);
define('DCL_ENTITY_OUTAGE', 48);
define('DCL_ENTITY_MEASUREMENTUNIT', 49);
define('DCL_ENTITY_MEASUREMENTTYPE', 50);
define('DCL_ENTITY_ORGMEASUREMENT', 51);
define('DCL_ENTITY_RUBRIC', 52);

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
define('DCL_PERM_SCORE', 24);

// Audit events
define('DCL_EVENT_ADD', 1);
define('DCL_EVENT_DELETE', 2);

// Smarty settings
define('SMARTY_DIR', DCL_ROOT . 'vendor/Smarty/');

// Others
define('DCL_NOW', 'now()');

LoadStringResource('menu');

function DclClassAutoLoader($className)
{
	if (file_exists(DCL_ROOT . 'inc/' . $className . '.php'))
	{
		require_once(DCL_ROOT . 'inc/' . $className . '.php');
		return;
	}

	if (file_exists(DCL_ROOT . 'inc/lib/' . $className . '.php'))
	{
		require_once(DCL_ROOT . 'inc/lib/' . $className . '.php');
		return;
	}

	$areas = array(
		'Controller' => 'controllers',
		'Model' => 'models',
		'ViewData' => 'models',
		'Presenter' => 'presenters',
		'ModelValidator' => 'validators',
		'Exception' => 'exceptions',
		'Helper' => 'helpers',
		'Service' => 'services'
	);

	foreach ($areas as $suffix => $directory)
	{
		if (mb_substr($className, -mb_strlen($suffix)) == $suffix && file_exists(DCL_ROOT . 'inc/' . $directory . '/' . $className . '.php'))
		{
			require_once(DCL_ROOT . 'inc/' . $directory . '/' . $className . '.php');
			return;
		}
	}

	if (mb_substr($className, 0, 13) === 'PluginHelper_')
	{
		$pluginParts = explode('_', $className, 3);
		if (count($pluginParts) > 2)
		{
			$classPath = GetPluginDir() . mb_strtolower($pluginParts[1]) . '/PluginHelper_' . $pluginParts[1] . '_' . $pluginParts[2] . '.php';
			if (file_exists($classPath))
			{
				require_once($classPath);
			}
		}

		return;
	}

	$vendorClasses = array(
		'Smarty' => DCL_ROOT . 'vendor/Smarty/Smarty.class.php',
		'Valitron\Validator' => DCL_ROOT . 'vendor/valitron/Validator.php',
		'pData' => DCL_ROOT . 'vendor/pChart/pData.class',
		'pChart' => DCL_ROOT . 'vendor/pChart/pChart.class'
	);

	if (isset($vendorClasses[$className]))
	{
		require_once($vendorClasses[$className]);
		return;
	}

	if (file_exists(GetPluginDir() . 'lib/' . $className . '.php'))
	{
		require_once(GetPluginDir() . 'lib/' . $className . '.php');
		return;
	}
}

function menuLink($target = '', $params = '')
{
	if ($target == '')
		$target = DCL_WWW_ROOT . 'main.php';

	if (mb_substr($target, 0, mb_strlen(DCL_WWW_ROOT)) == DCL_WWW_ROOT)
		$sRet = mb_substr($target, mb_strlen(DCL_WWW_ROOT));
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

function HasPermission($entityId, $permissionId, $id1 = 0, $id2 = 0)
{
	global $g_oSec;

	return $g_oSec->HasPerm($entityId, $permissionId, $id1, $id2);
}

function RequirePermission($entityId, $permissionId, $id1 = 0, $id2 = 0)
{
	if (!HasPermission($entityId, $permissionId, $id1, $id2))
		throw new PermissionDeniedException();
}

function HasAnyPermission($entityId, array $permissionList, $id1 = 0, $id2 = 0)
{
	global $g_oSec;
	
	foreach ($permissionList as $permissionId)
	{
		if ($g_oSec->HasPerm($entityId, $permissionId, $id1, $id2))
			return true;
	}
	
	return false;
}

function RequireAnyPermission($entityId, array $permissionList, $id1 = 0, $id2 = 0)
{
	if (!HasAnyPermission($entityId, $permissionList, $id1, $id2))
		throw new PermissionDeniedException();
}

function RequirePost()
{
	if ($_SERVER["REQUEST_METHOD"] != "POST")
		throw new PermissionDeniedException ();
}

function IsPublicUser()
{
	global $g_oSec;
	
	return $g_oSec->IsPublicUser();
}

function IsOrgUser()
{
	global $g_oSec;

	return $g_oSec->IsOrgUser();
}

function UseHttps()
{
	// These values may or may not fill in and depend on the web server, so we'll check the common settings
	// To force secure Gravatar request (if this function doesn't work), set DCL_FORCE_SECURE_GRAVATAR = Y in configuration
	return (IsSet($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' && $_SERVER['HTTPS'] != '0') || $_SERVER['SERVER_PORT'] == 443;
}

function GPCStripSlashes($thisString)
{
	return $thisString;
}

function CleanArray(&$aArray)
{
}

function GetPrefLang()
{
	global $dcl_info, $g_oSession;

	// TODO: Allow browser override and check if locale available, if so configured
	$lang = '';
	if (is_object($g_oSession))
	{
		$oPrefs = new PreferencesModel();
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

	if (!in_array($lang, array('en', 'de', 'es', 'fr', 'it', 'sl', 'sv')))
		$lang = 'en';

	return $lang;
}

function LoadStringResource($name)
{
	require_once(sprintf(DCL_ROOT . 'lang/%s/%s.php', GetPrefLang(), $name));
}

function LoadSchema($sTableName)
{
	if (!isset($GLOBALS['phpgw_baseline']) || !is_array($GLOBALS['phpgw_baseline']))
		$GLOBALS['phpgw_baseline'] = array();

	$schemaFile = sprintf(DCL_ROOT . 'schema/schema.%s.php', $sTableName);
	if (!file_exists($schemaFile))
		throw new InvalidArgumentException("Schema for [$sTableName] not found.");

	require_once($schemaFile);
}

function Invoke($sClassMethod)
{
	global $dcl_info, $menuAction;

	if (!is_string($sClassMethod))
	{
		ShowError('Invalid request.');
		return;
	}

	$aClassMethod = explode('.', $sClassMethod);
	if (count($aClassMethod) != 2)
	{
		ShowError('Invalid request.');
		return;
	}
	
	list($class, $method) = explode(".", $sClassMethod);
	if (!class_exists($class))
	{
		$class .= 'Controller';
		if (!class_exists($class))
		{
			ShowError('Invalid request.');
			return;
		}
	}

	$obj = new $class();
	if (!method_exists($obj, $method))
	{
		ShowError('Invalid request.');
		return;
	}

	if ($dcl_info['DCL_SEC_AUDIT_ENABLED']=='Y' && $dcl_info['DCL_SEC_AUDIT_LOGIN_ONLY'] == 'N')
	{
		$oSecAuditDB = new SecurityAuditModel();
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

		$oSecAuditDB->id = DCLID;
		$oSecAuditDB->actionon = DCL_NOW;
		$oSecAuditDB->actiontxt = $menuAction;
		$oSecAuditDB->actionparam = $values;
		$oSecAuditDB->Add();
	}

	$obj->$method();
}

function InvokePlugin($sPluginName, &$aParams = null, $method = 'Invoke')
{	
	list($type, $name) = explode(".", $sPluginName);
	$class = 'PluginHelper_' . $type . '_' . $name;

	if (!class_exists($class))
	{
		// If we can't import it, no plugin has been set up
		return;
	}

	$obj = new $class();
	if (!method_exists($obj, $method))
	{
		ShowError('Invalid request.');
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

function GetPluginDir()
{
	global $dcl_info;
	
	return $dcl_info['DCL_FILE_PATH'] . '/plugins/';
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

function commonHeader()
{
	if (defined('HTML_HEADER_GENERATED'))
		return;

	define('HTML_HEADER_GENERATED', 1);
	header('Expires: Fri, 11 Oct 1991 17:01:00 GMT');
	header('Cache-Control: no-cache, must-revalidate');

	if (IsJsonRequest())
	{
		header('Content-Type: application/json; charset=utf-8');
		return;
	}

	header('Content-Type: text/html; charset=utf-8');
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

	$t = new SmartyHelper();

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

	$t->Render('DeleteItem.tpl');
}

function GetYesNoCombo($default = 'Y', $cbName = 'active', $size = 0, $noneOption = true)
{
	$str = "<select class=\"form-control\" id=\"$cbName\" name=\"$cbName";
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

function IsJsonRequest()
{
	return isset($_SERVER['CONTENT_TYPE']) && mb_stripos($_SERVER['CONTENT_TYPE'], '/json') > 0;
}

function AcceptJsonResponse()
{
	return isset($_SERVER['HTTP_ACCEPT']) && mb_stripos($_SERVER['HTTP_ACCEPT'], '/json') > 0;
}

function GetMessageObject($status, $message)
{
	$retVal = new stdClass();
	$retVal->status = $status;
	$retVal->message = $message;
	$retVal->errors = array();

	return $retVal;
}

function ShowInfo($sMessage)
{
	commonHeader();

	if (AcceptJsonResponse())
	{
		echo json_encode(GetMessageObject(STR_CMMN_INFORMATION, $sMessage));
		exit;
	}

	NotificationCollectionModel::GetInstance()->Add(new NotificationModel(DCL_LOG_INFO, $sMessage));
}

function ShowWarning($sMessage)
{
	commonHeader();

	if (AcceptJsonResponse())
	{
		echo json_encode(GetMessageObject(STR_CMMN_WARNING, $sMessage));
		exit;
	}

	NotificationCollectionModel::GetInstance()->Add(new NotificationModel(DCL_LOG_WARN, $sMessage));
}

function ShowError($sMessage)
{
	commonHeader();

	if (AcceptJsonResponse())
	{
		echo json_encode(GetMessageObject(STR_CMMN_ERROR, $sMessage));
		exit;
	}

	NotificationCollectionModel::GetInstance()->Add(new NotificationModel(DCL_LOG_ERROR, $sMessage));
}

function DclErrorLog($level, $message, $file, $line, $backTrace)
{
	try
	{
        if (empty($message))
            return null;

		$requestUri = $_SERVER['REQUEST_URI'];
		$queryStringStart = mb_strpos($requestUri, '?');
		if ($queryStringStart !== false)
			$requestUri = mb_substr($requestUri, 0, $queryStringStart);

		$logger = new ErrorLogModel();
		$logger->server_name = $_SERVER['SERVER_NAME'];
		$logger->script_name = $_SERVER['SCRIPT_NAME'];
		$logger->request_uri = $requestUri;
		$logger->query_string = $_SERVER['QUERY_STRING'];
		$logger->error_file = $file;
		$logger->error_line = $line;
		$logger->error_description = $message;
		$logger->log_level = $level;

		if (is_array($backTrace))
		{
			if (is_array($backTrace) && isset($backTrace['object']))
				unset($backTrace['object']);

			foreach ($backTrace as $frameIdx => $frame)
			{
				if (array_key_exists('object', $frame))
					unset($backTrace[$frameIdx]['object']);

				if (array_key_exists('args', $frame))
					$backTrace[$frameIdx]['args'] = array();
			}

			$logger->stack_trace = @json_encode($backTrace);
		}

		$logger->Add();

		return $logger;
	}
	catch (Exception $ex)
	{
		return null;
	}
}

function LogInfo($message, $file, $line, $backTrace)
{
	$logger = DclErrorLog(DCL_LOG_INFO, $message, $file, $line, $backTrace);
	if ($logger !== null && $logger->error_log_id > 0)
		ShowInfo('An info log entry was generated.  Please refer to log ID ' . $logger->error_log_id . '.');
	else
		ShowInfo('An info log entry was attempted, but was not able to be recorded.');
}

function LogWarning($message, $file, $line, $backTrace)
{
	$logger = DclErrorLog(DCL_LOG_WARN, $message, $file, $line, $backTrace);
	if ($logger !== null && $logger->error_log_id > 0)
		ShowWarning('A warning log entry was generated.  Please refer to log ID ' . $logger->error_log_id . '.');
	else
		ShowWarning('A warning log entry was attempted, but was not able to be recorded.');
}

function LogError($message, $file, $line, $backTrace)
{
	$logger = DclErrorLog(DCL_LOG_ERROR, $message, $file, $line, $backTrace);
	if ($logger !== null && $logger->error_log_id > 0)
		ShowError('An error log entry was generated.  Please refer to log ID ' . $logger->error_log_id . '.');
	else
		ShowError('An error log entry was attempted, but was not able to be recorded.');
}

function LogFatal($message, $file, $line, $backTrace)
{
	$logger = DclErrorLog(DCL_LOG_FATAL, $message, $file, $line, $backTrace);
	if ($logger !== null && $logger->error_log_id > 0)
		ShowError('A fatal log entry was generated.  Please refer to log ID ' . $logger->error_log_id . '.');
	else
		ShowError('A fatal log entry was attempted, but was not able to be recorded.');
}

function DclErrorHandler($errorNumber, $message, $file, $line)
{
	if (!($errorNumber & error_reporting()))
		return;

	$backTrace = debug_backtrace(false);

 	switch ($errorNumber)
	{
		case E_COMPILE_ERROR:
		case E_PARSE:
		case E_CORE_ERROR:
			LogFatal($message, $file, $line, $backTrace);
			break;
		case E_USER_ERROR:
		case E_ERROR:
			LogError($message, $file, $line, $backTrace);
			break;
		case E_CORE_WARNING:
		case E_USER_WARNING:
		case E_WARNING:
			LogWarning($message, $file, $line, $backTrace);
			break;
		case E_NOTICE:
		case E_USER_NOTICE:
			LogInfo($message, $file, $line, $backTrace);
			break;
	}

	if ($errorNumber == E_COMPILE_ERROR || $errorNumber == E_PARSE)
		exit(255);
}

function DclExceptionHandler($ex)
{
	// InvalidDataException and PermissionDeniedException will not be logged since they can generate too many entries under vulnerability scanning
	// Normal application use should not encounter these exceptions (unless there's a bug!)
	if ($ex instanceof InvalidDataException)
		ShowError('An error occurred when processing your request due to invalid data.');
	else if ($ex instanceof PermissionDeniedException)
		ShowError('Permission denied.');
	else
		LogError($ex->getMessage(), $ex->getFile(), $ex->getLine(), $ex->getTrace());

	if (!AcceptJsonResponse())
	{
		$smarty = new SmartyHelper();
		$smarty->Render('_Layout.tpl');
	}

	exit(255);
}

error_reporting(E_ALL ^ E_STRICT);

if (!defined('DCL_DEBUG') && !defined('DCL_INSTALLER_RUNNING'))
{
	set_error_handler('DclErrorHandler');
	set_exception_handler('DclExceptionHandler');
}

spl_autoload_register('DclClassAutoLoader');

Valitron\Validator::langDir(DCL_ROOT . 'vendor/valitron/lang');
Valitron\Validator::lang(GetPrefLang());