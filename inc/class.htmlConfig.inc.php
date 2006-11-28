<?php
/*
 * $Id: class.htmlConfig.inc.php,v 1.1.1.1 2006/11/27 05:30:45 mdean Exp $
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

LoadStringResource('cfg');

class htmlConfig
{
	function Show()
	{
		global $dcl_info, $g_oSec;

		if (!$g_oSec->HasPerm(DCL_ENTITY_ADMIN, DCL_PERM_MODIFY))
			return PrintPermissionDenied();
			
		$t =& CreateSmarty();
		
		// System setup
		$t->assign('CMB_DATEFORMAT', $this->GetDateCombo('DCL_DATE_FORMAT', $dcl_info['DCL_DATE_FORMAT']));
		$t->assign('CMB_DATEFORMATDB', $this->GetDateCombo('DCL_DATE_FORMAT_DB', $dcl_info['DCL_DATE_FORMAT_DB']));
		$t->assign('CMB_TIMESTAMPFORMAT', $this->GetTimestampCombo('DCL_TIMESTAMP_FORMAT', $dcl_info['DCL_TIMESTAMP_FORMAT']));
		$t->assign('CMB_TIMESTAMPFORMATDB', $this->GetTimestampCombo('DCL_TIMESTAMP_FORMAT_DB', $dcl_info['DCL_TIMESTAMP_FORMAT_DB']));
		$t->assign('VAL_MAXUPLOADFILESIZE', $dcl_info['DCL_MAX_UPLOAD_FILE_SIZE']);
		$t->assign('CMB_DEFAULT_LANGUAGE', $this->GetLangCombo('DCL_DEFAULT_LANGUAGE', $dcl_info['DCL_DEFAULT_LANGUAGE']));
		$t->assign('VAL_PRIVATEKEY', $dcl_info['DCL_PRIVATE_KEY']);
		$t->assign('VAL_APPNAME', $dcl_info['DCL_APP_NAME']);
		$t->assign('VAL_LOGINMESSAGE', $dcl_info['DCL_LOGIN_MESSAGE']);
		$t->assign('VAL_HTMLTITLE', $dcl_info['DCL_HTML_TITLE']);
		$t->assign('VAL_FILEPATH', $dcl_info['DCL_FILE_PATH']);
		$t->assign('VAL_ROOT', $dcl_info['DCL_ROOT']);
		$t->assign('CMB_GDTYPE', $this->GetGraphicsCombo('DCL_GD_TYPE', $dcl_info['DCL_GD_TYPE']));
		$t->assign('VAL_SESSIONTIMEOUT', $dcl_info['DCL_SESSION_TIMEOUT']);
		
		// SMTP Server
		$t->assign('VAL_SMTPENABLED', $dcl_info['DCL_SMTP_ENABLED']);
		$t->assign('VAL_SMTPSERVER', $dcl_info['DCL_SMTP_SERVER']);
		$t->assign('VAL_SMTPPORT', $dcl_info['DCL_SMTP_PORT']);
		$t->assign('VAL_SMTPAUTHREQUIRED', $dcl_info['DCL_SMTP_AUTH_REQUIRED']);
		$t->assign('VAL_SMTPAUTHUSER', $dcl_info['DCL_SMTP_AUTH_USER']);
		$t->assign('VAL_SMTPAUTHPWD', $dcl_info['DCL_SMTP_AUTH_PWD']);
		$t->assign('VAL_SMTPTIMEOUT', $dcl_info['DCL_SMTP_TIMEOUT']);
		$t->assign('VAL_SMTPDEFAULTEMAIL', $dcl_info['DCL_SMTP_DEFAULT_EMAIL']);

		// Look and feel
		$t->assign('CMB_DEFTEMPLATESET', $this->GetTemplatesCombo('DCL_DEF_TEMPLATE_SET', $dcl_info['DCL_DEF_TEMPLATE_SET']));
		
		// Work Orders
		$t->assign('CMB_DEFAULTSTATUSASSIGN', $this->GetStatusCombo('DCL_DEF_STATUS_ASSIGN_WO', $dcl_info['DCL_DEF_STATUS_ASSIGN_WO']));
		$t->assign('CMB_DEFAULTSTATUSUNASSIGN', $this->GetStatusCombo('DCL_DEF_STATUS_UNASSIGN_WO', $dcl_info['DCL_DEF_STATUS_UNASSIGN_WO']));
		$t->assign('CMB_DEFAULTPRIORITY', $this->GetPriorityCombo('DCL_DEF_PRIORITY', $dcl_info['DCL_DEF_PRIORITY']));
		$t->assign('CMB_DEFAULTSEVERITY', $this->GetSeverityCombo('DCL_DEF_SEVERITY', $dcl_info['DCL_DEF_SEVERITY']));
		$t->assign('VAL_AUTODATE', $dcl_info['DCL_AUTO_DATE']);
		$t->assign('CMB_TIMECARDORDER', $this->GetDisplayOrderCombo('DCL_TIME_CARD_ORDER', $dcl_info['DCL_TIME_CARD_ORDER']));
		$t->assign('VAL_WONOTIFICATIONHTML', $dcl_info['DCL_WO_NOTIFICATION_HTML']);
		$t->assign('VAL_WOEMAILTEMPLATE', $dcl_info['DCL_WO_EMAIL_TEMPLATE']);
		$t->assign('VAL_WOSECONDARYACCOUNTSENABLED', $dcl_info['DCL_WO_SECONDARY_ACCOUNTS_ENABLED']);
		
		// Projects
		$t->assign('VAL_PRJXMLTMPL', $dcl_info['DCL_PROJECT_XML_TEMPLATES']);
		$t->assign('VAL_PRJCHLDSTATS', $dcl_info['DCL_PROJECT_INCLUDE_CHILD_STATS']);
		$t->assign('VAL_PRJPRNTSTATS', $dcl_info['DCL_PROJECT_INCLUDE_PARENT_STATS']);
		$t->assign('VAL_PRJBROWSEPARENTSONLY', $dcl_info['DCL_PROJECT_BROWSE_PARENTS_ONLY']);
		$t->assign('CMB_DEFAULTPROJECTSTATUS', $this->GetStatusCombo('DCL_DEFAULT_PROJECT_STATUS', $dcl_info['DCL_DEFAULT_PROJECT_STATUS']));

		// Tickets
		$t->assign('CMB_CQQPERCENT', $this->GetPercentCombo('DCL_CQQ_PERCENT', $dcl_info['DCL_CQQ_PERCENT']));
		$t->assign('VAL_CQQFROM', $dcl_info['DCL_CQQ_FROM']);
		$t->assign('VAL_CQQSUBJECT', $dcl_info['DCL_CQQ_SUBJECT']);
		$t->assign('VAL_CQQTEMPLATE', $dcl_info['DCL_CQQ_TEMPLATE']);
		$t->assign('CMB_DEFAULTTICKETSTATUS', $this->GetStatusCombo('DCL_DEFAULT_TICKET_STATUS', $dcl_info['DCL_DEFAULT_TICKET_STATUS']));
		$t->assign('VAL_TCKNOTIFICATIONHTML', $dcl_info['DCL_TCK_NOTIFICATION_HTML']);
		$t->assign('VAL_TCKEMAILTEMPLATE', $dcl_info['DCL_TCK_EMAIL_TEMPLATE']);
		
		// Wiki
		$t->assign('VAL_WIKIENABLED', $dcl_info['DCL_WIKI_ENABLED']);

		// e-Mail Gateway for Tickets
		$t->assign('VAL_GATEWAYTICKETENABLED', $dcl_info['DCL_GATEWAY_TICKET_ENABLED']);
		$t->assign('VAL_GATEWAYTICKETAUTORESPOND', $dcl_info['DCL_GATEWAY_TICKET_AUTORESPOND']);
		$t->assign('VAL_GATEWAYTICKETAUTORESPONSEEMAIL', $dcl_info['DCL_GATEWAY_TICKET_AUTORESPONSE_EMAIL']);
		$t->assign('VAL_GATEWAYTICKETREPLY', $dcl_info['DCL_GATEWAY_TICKET_REPLY']);
		$t->assign('CMB_GATEWAYTICKETSTATUS', $this->GetStatusCombo('DCL_GATEWAY_TICKET_STATUS', $dcl_info['DCL_GATEWAY_TICKET_STATUS']));
		$t->assign('CMB_GATEWAYTICKETPRIORITY', $this->GetPriorityCombo('DCL_GATEWAY_TICKET_PRIORITY', $dcl_info['DCL_GATEWAY_TICKET_PRIORITY']));
		$t->assign('CMB_GATEWAYTICKETSEVERITY', $this->GetSeverityCombo('DCL_GATEWAY_TICKET_SEVERITY', $dcl_info['DCL_GATEWAY_TICKET_SEVERITY']));
		$t->assign('CMB_GATEWAYTICKETACCOUNT', $this->GetAccountCombo('DCL_GATEWAY_TICKET_ACCOUNT', $dcl_info['DCL_GATEWAY_TICKET_ACCOUNT']));
		$t->assign('CMB_GATEWAYTICKETREPLYLOGGEDBY', $this->GetPersonnelCombo('DCL_GATEWAY_TICKET_REPLY_LOGGED_BY', $dcl_info['DCL_GATEWAY_TICKET_REPLY_LOGGED_BY']));
		$t->assign('VAL_GATEWAYTICKETFILEPATH', $dcl_info['DCL_GATEWAY_TICKET_FILE_PATH']);

		// e-Mail Gateway for Work Orders
		$t->assign('VAL_GATEWAYWOENABLED', $dcl_info['DCL_GATEWAY_WO_ENABLED']);
		$t->assign('VAL_GATEWAYWOAUTORESPOND', $dcl_info['DCL_GATEWAY_WO_AUTORESPOND']);
		$t->assign('VAL_GATEWAYWOAUTORESPONSEEMAIL', $dcl_info['DCL_GATEWAY_WO_AUTORESPONSE_EMAIL']);
		$t->assign('VAL_GATEWAYWOREPLY', $dcl_info['DCL_GATEWAY_WO_REPLY']);
		$t->assign('CMB_GATEWAYWOSTATUS', $this->GetStatusCombo('DCL_GATEWAY_WO_STATUS', $dcl_info['DCL_GATEWAY_WO_STATUS']));
		$t->assign('CMB_GATEWAYWOPRIORITY', $this->GetPriorityCombo('DCL_GATEWAY_WO_PRIORITY', $dcl_info['DCL_GATEWAY_WO_PRIORITY']));
		$t->assign('CMB_GATEWAYWOSEVERITY', $this->GetSeverityCombo('DCL_GATEWAY_WO_SEVERITY', $dcl_info['DCL_GATEWAY_WO_SEVERITY']));
		$t->assign('VAL_GATEWAYWOFILEPATH', $dcl_info['DCL_GATEWAY_WO_FILE_PATH']);
		$t->assign('CMB_GATEWAYWOACCOUNT', $this->GetAccountCombo('DCL_GATEWAY_WO_ACCOUNT', $dcl_info['DCL_GATEWAY_WO_ACCOUNT']));
		$t->assign('CMB_GATEWAYWOREPLYLOGGEDBY', $this->GetPersonnelCombo('DCL_GATEWAY_WO_REPLY_LOGGED_BY', $dcl_info['DCL_GATEWAY_WO_REPLY_LOGGED_BY']));

		// SCM
		$t->assign('VAL_SCCSENABLED', $dcl_info['DCL_SCCS_ENABLED']);
		$t->assign('VAL_BUILDMANAGERENABLED', $dcl_info['DCL_BUILD_MANAGER_ENABLED']);

		SmartyDisplay($t, 'htmlConfig.tpl');
return;

		// e-Mail Gateway for Work Orders
		$Template->set_var('TXT_GATEWAYWOTITLE', STR_CFG_GATEWAYWOTITLE);
		$Template->set_var('TXT_GATEWAYWOENABLED', STR_CFG_GATEWAYWOENABLED);
		$Template->set_var('TXT_GATEWAYWOAUTORESPOND', STR_CFG_GATEWAYWOAUTORESPOND);
		$Template->set_var('TXT_GATEWAYWOAUTORESPONSEEMAIL', STR_CFG_GATEWAYWOAUTORESPONSEEMAIL);
		$Template->set_var('TXT_GATEWAYWOREPLY', STR_CFG_GATEWAYWOREPLY);
		$Template->set_var('TXT_GATEWAYWOSTATUS', STR_CFG_GATEWAYWOSTATUS);
		$Template->set_var('TXT_GATEWAYWOPRIORITY', STR_CFG_GATEWAYWOPRIORITY);
		$Template->set_var('TXT_GATEWAYWOSEVERITY', STR_CFG_GATEWAYWOSEVERITY);
		$Template->set_var('TXT_GATEWAYWOFILEPATH', STR_CFG_GATEWAYWOFILEPATH);
		$Template->set_var('TXT_GATEWAYWOACCOUNT', STR_CFG_GATEWAYTICKETACCOUNT);
		$Template->set_var('TXT_GATEWAYWOREPLYLOGGEDBY', STR_CFG_GATEWAYTICKETREPLYLOGGEDBY);

		$Template->set_var('VAL_GATEWAYWOENABLED', $dcl_info['DCL_GATEWAY_WO_ENABLED']);
		$Template->set_var('VAL_GATEWAYWOAUTORESPOND', $dcl_info['DCL_GATEWAY_WO_AUTORESPOND']);
		$Template->set_var('VAL_GATEWAYWOAUTORESPONSEEMAIL', $dcl_info['DCL_GATEWAY_WO_AUTORESPONSE_EMAIL']);
		$Template->set_var('VAL_GATEWAYWOREPLY', $dcl_info['DCL_GATEWAY_WO_REPLY']);
		$Template->set_var('CMB_GATEWAYWOSTATUS', $this->GetStatusCombo('DCL_GATEWAY_WO_STATUS', $dcl_info['DCL_GATEWAY_WO_STATUS']));
		$Template->set_var('CMB_GATEWAYWOPRIORITY', $this->GetPriorityCombo('DCL_GATEWAY_WO_PRIORITY', $dcl_info['DCL_GATEWAY_WO_PRIORITY']));
		$Template->set_var('CMB_GATEWAYWOSEVERITY', $this->GetSeverityCombo('DCL_GATEWAY_WO_SEVERITY', $dcl_info['DCL_GATEWAY_WO_SEVERITY']));
		$Template->set_var('VAL_GATEWAYWOFILEPATH', $dcl_info['DCL_GATEWAY_WO_FILE_PATH']);

		// SCCS Integration
		$Template->set_var('TXT_SCM', 'Software Config Mgmt');
		$Template->set_var('TXT_SCCSENABLED', 'SCCS Integration Enabled');
		$Template->set_var('TXT_BUILDMANAGERENABLED', 'Build Manager Enabled (Requires SCCS)');

		$Template->set_var('VAL_SCCSENABLED', $dcl_info['DCL_SCCS_ENABLED']);
		$Template->set_var('VAL_BUILDMANAGERENABLED', $dcl_info['DCL_BUILD_MANAGER_ENABLED']);

		$Template->pparse('out', 'hForm');
	}

	function _GetCombo($sName, $aOptions, $sDefault)
	{
		$retVal = '<select name="' . $sName . '">';

		if (is_array($aOptions))
		{
			reset($aOptions);
			while (list($key, $val) = each($aOptions))
			{
				$retVal .= '<option value="' . $key . '"';
				if ($key == $sDefault)
					$retVal .= ' selected';

				$retVal .= '>' . $val . '</option>';
			}
		}

		$retVal .= '</select>';

		return $retVal;
	}

	function GetDateCombo($sName, $sDefault)
	{
		$aOptions = array(
				'm/d/Y' => 'mm/dd/yyyy',
				'd/m/Y' => 'dd/mm/yyyy',
				'm.d.Y' => 'mm.dd.yyyy',
				'd.m.Y' => 'dd.mm.yyyy',
				'm-d-Y' => 'mm-dd-yyyy',
				'd-m-Y' => 'dd-mm-yyyy',
				'Y/m/d' => 'yyyy/mm/dd',
				'Y.m.d' => 'yyyy.mm.dd',
				'Y-m-d' => 'yyyy-mm-dd',
				'Ymd' => 'yyyymmdd'
			);

		return $this->_GetCombo($sName, $aOptions, $sDefault);
	}

	function GetTimestampCombo($sName, $sDefault)
	{
		$aOptions = array(
				'm/d/Y H:i:s' => 'mm/dd/yyyy hh:mm:ss',
				'd/m/Y H:i:s' => 'dd/mm/yyyy hh:mm:ss',
				'm.d.Y H:i:s' => 'mm.dd.yyyy hh:mm:ss',
				'd.m.Y H:i:s' => 'dd.mm.yyyy hh:mm:ss',
				'm-d-Y H:i:s' => 'mm-dd-yyyy hh:mm:ss',
				'd-m-Y H:i:s' => 'dd-mm-yyyy hh:mm:ss',
				'Y/m/d H:i:s' => 'yyyy/mm/dd hh:mm:ss',
				'Y.m.d H:i:s' => 'yyyy.mm.dd hh:mm:ss',
				'Y-m-d H:i:s' => 'yyyy-mm-dd hh:mm:ss',
				'YmdHis' => 'yyyymmddhhmmss'
			);

		return $this->_GetCombo($sName, $aOptions, $sDefault);
	}

	function GetLangCombo($sName, $sDefault)
	{
		$aOptions = array(
				'en' => 'English',
				'fr' => 'French',
				'de' => 'German',
				'it' => 'Italian',
				'ru' => 'Russian',
				'es' => 'Spanish',
				'sl' => 'Sloven��ina',
				'sv' => 'Swedish'
			);

		return $this->_GetCombo($sName, $aOptions, $sDefault);
	}

	function GetGraphicsCombo($sName, $sDefault)
	{
		$aOptions = array(
				'gif' => 'GIF&nbsp;&nbsp;',
				'png' => 'PNG&nbsp;&nbsp;',
				'jpeg' => 'JPEG&nbsp;&nbsp;'
			);

		return $this->_GetCombo($sName, $aOptions, $sDefault);
	}

	function GetTemplatesCombo($sName, $sDefault)
	{
		$aOptions = array();
		$sPath = './templates/';
		if (is_dir($sPath) && $hDir = opendir($sPath))
		{
			while ($fileName = readdir($hDir))
				if (is_dir($sPath . $fileName) && $fileName != '.' && $fileName != '..' && $fileName != 'CVS' && $fileName != 'custom')
					$aOptions[$fileName] = $fileName;

			if ($hDir)
				closedir($hDir);
		}

		return $this->_GetCombo($sName, $aOptions, $sDefault);
	}

	function GetSecurityCombo($sName, $sDefault)
	{
		$aOptions = array();
		for ($i = 1; $i < 10; $i++)
			$aOptions[strval($i)] = 'Level ' . $i;

		return $this->_GetCombo($sName, $aOptions, $sDefault);
	}

	function GetStatusCombo($sName, $sDefault)
	{
		$o = CreateObject('dcl.htmlStatuses');
		return $o->GetCombo(intval($sDefault), $sName);
	}

	function GetPriorityCombo($sName, $sDefault)
	{
		$o = CreateObject('dcl.htmlPriorities');
		return $o->GetCombo(intval($sDefault), $sName);
	}

	function GetSeverityCombo($sName, $sDefault)
	{
		$o = CreateObject('dcl.htmlSeverities');
		return $o->GetCombo(intval($sDefault), $sName);
	}

	function GetAccountCombo($sName, $sDefault)
	{
		$o =& CreateObject('dcl.dbOrg');
		$aOptions = $o->GetOptions('org_id', 'name', 'active', $bActiveOnly = true);

		$aSelect = array();
		foreach ($aOptions as $Org)
		{
			$aSelect[$Org['org_id']] = $Org['name'];
		}

		return $this->_GetCombo($sName, $aSelect, (int)$sDefault);
	}

	function GetPersonnelCombo($sName, $sDefault)
	{
		$o = CreateObject('dcl.htmlPersonnel');
		return $o->GetCombo(intval($sDefault), $sName);
	}

	function GetDisplayOrderCombo($sName, $sDefault)
	{
		$aOptions = array(
				'ASC' => 'Oldest First',
				'DESC' => 'Newest First'
			);

		return $this->_GetCombo($sName, $aOptions, $sDefault);
	}

	function GetPercentCombo($sName, $sDefault)
	{
		$aOptions = array();
		for ($i = 0; $i < 100; $i += 5)
			$aOptions[strval($i)] = $i . '%';

		$aOptions['101'] = '100%'; // PHP3 and 4 are off from each other in rand, so 101 covers both

		return $this->_GetCombo($sName, $aOptions, $sDefault);
	}
}
?>
