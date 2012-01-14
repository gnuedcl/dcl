<!-- $Id$ -->
<script language="JavaScript">
function validateAndSubmit(f)
{literal}
{
	f.submit();
}
{/literal}
</script>
<form class="styled" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="Configuration.Update">
	<fieldset>
		<legend>{$smarty.const.STR_CFG_SYSTEMTITLE}</legend>
		<div class="required"><label for="DCL_DATE_FORMAT">{$smarty.const.STR_CFG_DATEFORMAT}:</label>{$CMB_DATEFORMAT}<span>{$smarty.const.STR_CFG_DATEFORMATHELP}</span></div>
		<div class="required"><label for="DCL_DATE_FORMATDB">{$smarty.const.STR_CFG_DATEFORMATDB}:</label>{$CMB_DATEFORMATDB}<span>{$smarty.const.STR_CFG_DATEFORMATDBHELP}</span></div>
		<div class="required"><label for="DCL_TIMESTAMP_FORMAT">{$smarty.const.STR_CFG_TIMESTAMPFORMAT}:</label>{$CMB_TIMESTAMPFORMAT}<span>{$smarty.const.STR_CFG_TIMESTAMPFORMATHELP}</span></div>
		<div class="required"><label for="DCL_TIMESTAMP_FORMATDB">{$smarty.const.STR_CFG_TIMESTAMPFORMATDB}:</label>{$CMB_TIMESTAMPFORMATDB}<span>{$smarty.const.STR_CFG_TIMESTAMPFORMATDBHELP}</span></div>
		<div class="required">
			<label for="DCL_MAX_UPLOAD_FILE_SIZE">{$smarty.const.STR_CFG_MAXUPLOADFILESIZE}:</label>
			<input type="text" id="DCL_MAX_UPLOAD_FILE_SIZE" name="DCL_MAX_UPLOAD_FILE_SIZE" size="10" maxlength="10" value="{$VAL_MAXUPLOADFILESIZE|escape}">
			<span>{$smarty.const.STR_CFG_MAXUPLOADFILESIZEHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_DEFAULT_LANGUAGE">{$smarty.const.STR_CFG_LANGUAGE}:</label>
			{$CMB_DEFAULT_LANGUAGE}
			<span>{$smarty.const.STR_CFG_LANGUAGEHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_PRIVATE_KEY">{$smarty.const.STR_CFG_PRIVATEKEY}:</label>
			<input type="text" id="DCL_PRIVATE_KEY" name="DCL_PRIVATE_KEY" size="20" maxlength="255" value="{$VAL_PRIVATEKEY|escape}">
			<span>{$smarty.const.STR_CFG_PRIVATEKEYHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_APP_NAME">{$smarty.const.STR_CFG_APPNAME}:</label>
			<input type="text" id="DCL_APP_NAME" name="DCL_APP_NAME" size="50" maxlength="255" value="{$VAL_APPNAME|escape}">
			<span>{$smarty.const.STR_CFG_APPNAMEHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_LOGIN_MESSAGE">{$smarty.const.STR_CFG_LOGINMESSAGE}:</label>
			<input type="text" id="DCL_LOGIN_MESSAGE" name="DCL_LOGIN_MESSAGE" size="50" maxlength="255" value="{$VAL_LOGINMESSAGE|escape}">
			<span>{$smarty.const.STR_CFG_LOGINMESSAGEHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_HTML_TITLE">{$smarty.const.STR_CFG_HTMLTITLE}:</label>
			<input type="text" id="DCL_HTML_TITLE" name="DCL_HTML_TITLE" size="50" maxlength="255" value="{$VAL_HTMLTITLE|escape}">
			<span>{$smarty.const.STR_CFG_HTMLTITLEHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_ROOT">{$smarty.const.STR_CFG_ROOT}:</label>
			<input type="text" id="DCL_ROOT" name="DCL_ROOT" size="50" maxlength="255" value="{$VAL_ROOT|escape}">
			<span>{$smarty.const.STR_CFG_ROOTHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_FILE_PATH">{$smarty.const.STR_CFG_FILEPATH}:</label>
			<input type="text" id="DCL_FILE_PATH" name="DCL_FILE_PATH" size="50" maxlength="255" value="{$VAL_FILEPATH|escape}">
			<span>{$smarty.const.STR_CFG_FILEPATHHELP}</span>
		</div>
		<div class="required"><label for="DCL_GD_TYPE">{$smarty.const.STR_CFG_GDTYPE}:</label>{$CMB_GDTYPE}<span>{$smarty.const.STR_CFG_GDTYPEHELP}</span></div>
		<div class="required">
			<label for="DCL_SESSION_TIMEOUT">{$smarty.const.STR_CFG_SESSIONTIMEOUT}:</label>
			<input type="text" id="DCL_SESSION_TIMEOUT" name="DCL_SESSION_TIMEOUT" size="5" maxlength="5" value="{$VAL_SESSIONTIMEOUT|escape}">
			<span>{$smarty.const.STR_CFG_SESSIONTIMEOUTHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_SEC_AUDIT_ENABLED">{$smarty.const.STR_CFG_SECAUDITENABLED}:</label>
			<input type="checkbox" name="DCL_SEC_AUDIT_ENABLED" id="DCL_SEC_AUDIT_ENABLED" value="Y"{if $VAL_SECAUDITENABLED == "Y"} checked{/if}>
			<span>{$smarty.const.STR_CFG_SECAUDITENABLEDHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_SEC_AUDIT_LOGIN_ONLY">{$smarty.const.STR_CFG_SECAUDITLOGINONLY}:</label>
			<input type="checkbox" name="DCL_SEC_AUDIT_LOGIN_ONLY" id="DCL_SEC_AUDIT_LOGIN_ONLY" value="Y"{if $VAL_SECAUDITLOGINONLY == "Y"} checked{/if}>
			<span>{$smarty.const.STR_CFG_SECAUDITLOGINONLYHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_FORCE_SECURE_GRAVATAR">{$smarty.const.STR_CFG_FORCESECUREGRAVATAR}:</label>
			<input type="checkbox" name="DCL_FORCE_SECURE_GRAVATAR" id="DCL_FORCE_SECURE_GRAVATAR" value="Y"{if $VAL_FORCESECUREGRAVATAR == "Y"} checked{/if}>
			<span>{$smarty.const.STR_CFG_FORCESECUREGRAVATARHELP}</span>
		</div>
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_CFG_EMAILSERVERTITLE}</legend>
		<div class="required">
			<label for="DCL_SMTP_ENABLED">{$smarty.const.STR_CFG_SMTPENABLED}:</label>
			<input type="checkbox" name="DCL_SMTP_ENABLED" id="DCL_SMTP_ENABLED" value="Y"{if $VAL_SMTPENABLED == "Y"} checked{/if}>
			<span>{$smarty.const.STR_CFG_SMTPENABLEDHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_SMTP_SERVER">{$smarty.const.STR_CFG_SMTPSERVER}:</label>
			<input type="text" id="DCL_SMTP_SERVER" name="DCL_SMTP_SERVER" size="30" maxlength="255" value="{$VAL_SMTPSERVER|escape}">
			<span>{$smarty.const.STR_CFG_SMTPSERVERHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_SMTP_PORT">{$smarty.const.STR_CFG_SMTPPORT}:</label>
			<input type="text" id="DCL_SMTP_PORT" name="DCL_SMTP_PORT" size="5" maxlength="5" value="{$VAL_SMTPPORT|escape}">
			<span>{$smarty.const.STR_CFG_SMTPPORTHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_SMTP_AUTH_REQUIRED">{$smarty.const.STR_CFG_SMTPAUTHREQUIRED}:</label>
			<input type="checkbox" name="DCL_SMTP_AUTH_REQUIRED" id="DCL_SMTP_AUTH_REQUIRED" value="Y"{if $VAL_SMTPAUTHREQUIRED == "Y"} checked{/if}>
			<span>{$smarty.const.STR_CFG_SMTPAUTHREQUIREDHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_SMTP_AUTH_USER">{$smarty.const.STR_CFG_SMTPAUTHUSER}:</label>
			<input type="text" id="DCL_SMTP_AUTH_USER" name="DCL_SMTP_AUTH_USER" size="30" maxlength="255" value="{$VAL_SMTPAUTHUSER|escape}">
			<span>{$smarty.const.STR_CFG_SMTPAUTHUSERHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_SMTP_AUTH_PWD">{$smarty.const.STR_CFG_SMTPAUTHPWD}:</label>
			<input type="text" id="DCL_SMTP_AUTH_PWD" name="DCL_SMTP_AUTH_PWD" size="30" maxlength="255" value="{$VAL_SMTPAUTHPWD|escape}">
			<span>{$smarty.const.STR_CFG_SMTPAUTHPWDHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_SMTP_TIMEOUT">{$smarty.const.STR_CFG_SMTPTIMEOUT}:</label>
			<input type="text" id="DCL_SMTP_TIMEOUT" name="DCL_SMTP_TIMEOUT" size="5" maxlength="5" value="{$VAL_SMTPTIMEOUT|escape}">
			<span>{$smarty.const.STR_CFG_SMTPTIMEOUTHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_SMTP_DEFAULT_EMAIL">{$smarty.const.STR_CFG_SMTPDEFAULTEMAIL}:</label>
			<input type="text" id="DCL_SMTP_DEFAULT_EMAIL" name="DCL_SMTP_DEFAULT_EMAIL" size="30" maxlength="255" value="{$VAL_SMTPDEFAULTEMAIL|escape}">
			<span>{$smarty.const.STR_CFG_SMTPDEFAULTEMAILHELP}</span>
		</div>
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_CFG_LOOKNFEELTITLE}</legend>
		<div class="required">
			<label for="DCL_DEF_TEMPLATE_SET">{$smarty.const.STR_CFG_DEFTEMPLATESET}:</label>
			{$CMB_DEFTEMPLATESET}
			<span>{$smarty.const.STR_CFG_DEFTEMPLATESETHELP}</span>
		</div>
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_CFG_WORKORDERTITLE}</legend>
		<div class="required"><label for="DCL_DEF_STATUS_ASSIGN_WO">{$smarty.const.STR_CFG_DEFAULTSTATUSASSIGN}:</label>{$CMB_DEFAULTSTATUSASSIGN}<span>{$smarty.const.STR_CFG_DEFAULTSTATUSASSIGNHELP}</span></div>
		<div class="required">
			<label for="DCL_DEF_STATUS_UNASSIGN_WO">{$smarty.const.STR_CFG_DEFAULTSTATUSUNASSIGN}:</label>
			{$CMB_DEFAULTSTATUSUNASSIGN}
			<span>{$smarty.const.STR_CFG_DEFAULTSTATUSUNASSIGNHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_DEF_PRIORITY">{$smarty.const.STR_CFG_DEFAULTPRIORITY}:</label>
			{$CMB_DEFAULTPRIORITY}
			<span>{$smarty.const.STR_CFG_DEFAULTPRIORITYHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_DEF_SEVERITY">{$smarty.const.STR_CFG_DEFAULTSEVERITY}:</label>
			{$CMB_DEFAULTSEVERITY}
			<span>{$smarty.const.STR_CFG_DEFAULTSEVERITYHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_AUTO_DATE">{$smarty.const.STR_CFG_AUTODATE}:</label>
			<input type="checkbox" name="DCL_AUTO_DATE" id="DCL_AUTO_DATE" value="Y"{if $VAL_AUTODATE == "Y"} checked{/if}>
			<span>{$smarty.const.STR_CFG_AUTODATEHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_TIME_CARD_ORDER">{$smarty.const.STR_CFG_TIMECARDORDER}:</label>
			{$CMB_TIMECARDORDER}
			<span>{$smarty.const.STR_CFG_TIMECARDORDERHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_WO_NOTIFICATION_HTML">{$smarty.const.STR_CFG_WONOTIFICATIONHTML}:</label>
			<input type="checkbox" name="DCL_WO_NOTIFICATION_HTML" id="DCL_WO_NOTIFICATION_HTML" value="Y"{if $VAL_WONOTIFICATIONHTML == "Y"} checked{/if}>
			<span>{$smarty.const.STR_CFG_WONOTIFICATIONHTMLHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_WO_EMAIL_TEMPLATE">{$smarty.const.STR_CFG_WOEMAILTEMPLATE}:</label>
			<input type="text" name="DCL_WO_EMAIL_TEMPLATE" id="DCL_WO_EMAIL_TEMPLATE" size="20" maxlength="255" value="{$VAL_WOEMAILTEMPLATE|escape}">
			<span>{$smarty.const.STR_CFG_WOEMAILTEMPLATEHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_WO_EMAIL_TEMPLATE_PUBLIC">{$smarty.const.STR_CFG_WOEMAILTEMPLATEPUBLIC}:</label>
			<input type="text" name="DCL_WO_EMAIL_TEMPLATE_PUBLIC" id="DCL_WO_EMAIL_TEMPLATE_PUBLIC" size="20" maxlength="255" value="{$VAL_WOEMAILTEMPLATEPUBLIC|escape}">
			<span>{$smarty.const.STR_CFG_WOEMAILTEMPLATEPUBLICHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_WO_SECONDARY_ACCOUNTS_ENABLED">{$smarty.const.STR_CFG_WOSECONDARYACCOUNTSENABLED}:</label>
			<input type="checkbox" name="DCL_WO_SECONDARY_ACCOUNTS_ENABLED" id="DCL_WO_SECONDARY_ACCOUNTS_ENABLED" value="Y"{if $VAL_WOSECONDARYACCOUNTSENABLED == "Y"} checked{/if}></td>
			<span>{$smarty.const.STR_CFG_WOSECONDARYACCOUNTSENABLEDHELP}</span>
		</div>
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_CFG_PROJECTTITLE}</legend>
		<div class="required">
			<label for="DCL_DEFAULT_PROJECT_STATUS">{$smarty.const.STR_CFG_DCLDEFAULTPROJECTSTATUS}:</label>
			{$CMB_DEFAULTPROJECTSTATUS}
			<span>{$smarty.const.STR_CFG_DCLDEFAULTPROJECTSTATUSHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_PROJECT_XML_TEMPLATES">{$smarty.const.STR_CFG_DCLPROJECTXMLTEMPLATES}:</label>
			<input type="checkbox" name="DCL_PROJECT_XML_TEMPLATES" id="DCL_PROJECT_XML_TEMPLATES" value="Y"{if $VAL_PRJXMLTMPL == "Y"} checked{/if}>
			<span>{$smarty.const.STR_CFG_DCLPROJECTXMLTEMPLATESHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_PROJECT_BROWSE_PARENTS_ONLY">{$smarty.const.STR_CFG_DCLPROJECTBROWSEPARENTSONLY}:</label>
			<input type="checkbox" name="DCL_PROJECT_BROWSE_PARENTS_ONLY" id="DCL_PROJECT_BROWSE_PARENTS_ONLY" value="Y"{if $VAL_PRJBROWSEPARENTSONLY == "Y"} checked{/if}>
			<span>{$smarty.const.STR_CFG_DCLPROJECTBROWSEPARENTSONLYHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_PROJECT_INCLUDE_CHILD_STATS">{$smarty.const.STR_CFG_DCLPROJECTINCLUDECHILDSTATS}:</label>
			<input type="checkbox" name="DCL_PROJECT_INCLUDE_CHILD_STATS" id="DCL_PROJECT_INCLUDE_CHILD_STATS" value="Y"{if $VAL_PRJCHLDSTATS == "Y"} checked{/if}>
			<span>{$smarty.const.STR_CFG_DCLPROJECTINCLUDECHILDSTATSHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_PROJECT_INCLUDE_PARENT_STATS">{$smarty.const.STR_CFG_DCLPROJECTINCLUDEPARENTSTATS}:</label>
			<input type="checkbox" name="DCL_PROJECT_INCLUDE_PARENT_STATS" id="DCL_PROJECT_INCLUDE_PARENT_STATS" value="Y"{if $VAL_PRJPRNTSTATS == "Y"} checked{/if}>
			<span>{$smarty.const.STR_CFG_DCLPROJECTINCLUDEPARENTSTATSHELP}</span>
		</div>
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_CFG_TICKETTITLE}</legend>
		<div class="required">
			<label for="DCL_DEFAULT_TICKET_STATUS">{$smarty.const.STR_CFG_DCLDEFAULTTICKETSTATUS}:</label>
			{$CMB_DEFAULTTICKETSTATUS}
			<span>{$smarty.const.STR_CFG_DCLDEFAULTTICKETSTATUSHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_TCK_NOTIFICATION_HTML">{$smarty.const.STR_CFG_TCKNOTIFICATIONHTML}:</label>
			<input type="checkbox" name="DCL_TCK_NOTIFICATION_HTML" id="DCL_TCK_NOTIFICATION_HTML" value="Y"{if $VAL_TCKNOTIFICATIONHTML == "Y"} checked{/if}>
			<span>{$smarty.const.STR_CFG_TCKNOTIFICATIONHTMLHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_TCK_EMAIL_TEMPLATE">{$smarty.const.STR_CFG_TCKEMAILTEMPLATE}:</label>
			<input type="text" id="DCL_TCK_EMAIL_TEMPLATE" name="DCL_TCK_EMAIL_TEMPLATE" size="50" maxlength="255" value="{$VAL_TCKEMAILTEMPLATE|escape}">
			<span>{$smarty.const.STR_CFG_TCKEMAILTEMPLATEHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_TCK_EMAIL_TEMPLATE_PUBLIC">{$smarty.const.STR_CFG_TCKEMAILTEMPLATEPUBLIC}:</label>
			<input type="text" id="DCL_TCK_EMAIL_TEMPLATE_PUBLIC" name="DCL_TCK_EMAIL_TEMPLATE_PUBLIC" size="50" maxlength="255" value="{$VAL_TCKEMAILTEMPLATEPUBLIC|escape}">
			<span>{$smarty.const.STR_CFG_TCKEMAILTEMPLATEPUBLICHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_CQQ_PERCENT">{$smarty.const.STR_CFG_CQQPERCENT}:</label>
			{$CMB_CQQPERCENT}
			<span>{$smarty.const.STR_CFG_CQQPERCENTHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_CQQ_FROM">{$smarty.const.STR_CFG_CQQFROM}:</label>
			<input type="text" id="DCL_CQQ_FROM" name="DCL_CQQ_FROM" size="50" maxlength="255" value="{$VAL_CQQFROM|escape}">
			<span>{$smarty.const.STR_CFG_CQQFROMHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_CQQ_SUBJECT">{$smarty.const.STR_CFG_CQQSUBJECT}:</label>
			<input type="text" id="DCL_CQQ_SUBJECT" name="DCL_CQQ_SUBJECT" size="50" maxlength="255" value="{$VAL_CQQSUBJECT|escape}">
			<span>{$smarty.const.STR_CFG_CQQSUBJECTHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_CQQ_TEMPLATE">{$smarty.const.STR_CFG_CQQTEMPLATE}:</label>
			<input type="text" id="DCL_CQQ_TEMPLATE" name="DCL_CQQ_TEMPLATE" size="50" maxlength="255" value="{$VAL_CQQTEMPLATE|escape}">
			<span>{$smarty.const.STR_CFG_CQQTEMPLATEHELP}</span>
		</div>
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_CFG_WIKI}</legend>
		<div class="required">
			<label for="DCL_WIKI_ENABLED">{$smarty.const.STR_CFG_WIKIENABLED}:</label>
			<input type="checkbox" name="DCL_WIKI_ENABLED" id="DCL_WIKI_ENABLED" value="Y"{if $VAL_WIKIENABLED == "Y"} checked{/if}>
			<span>{$smarty.const.STR_CFG_WIKIENABLEDHELP}</span>
		</div>
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_CFG_GATEWAYTICKETTITLE}</legend>
		<div class="required">
			<label for="DCL_GATEWAY_TICKET_ENABLED">{$smarty.const.STR_CFG_GATEWAYTICKETENABLED}:</label>
			<input type="checkbox" name="DCL_GATEWAY_TICKET_ENABLED" id="DCL_GATEWAY_TICKET_ENABLED" value="Y"{if $VAL_GATEWAYTICKETENABLED == "Y"} checked{/if}>
			<span>{$smarty.const.STR_CFG_GATEWAYTICKETENABLEDHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_GATEWAY_TICKET_AUTORESPOND">{$smarty.const.STR_CFG_GATEWAYTICKETAUTORESPOND}:</label>
			<input type="checkbox" name="DCL_GATEWAY_TICKET_AUTORESPOND" id="DCL_GATEWAY_TICKET_AUTORESPOND" value="Y"{if $VAL_GATEWAYTICKETAUTORESPOND == "Y"} checked{/if}>
			<span>{$smarty.const.STR_CFG_GATEWAYTICKETAUTORESPONDHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_GATEWAY_TICKET_AUTORESPONSE_EMAIL">{$smarty.const.STR_CFG_GATEWAYTICKETAUTORESPONSEEMAIL}:</label>
			<input type="text" id="DCL_GATEWAY_TICKET_AUTORESPONSE_EMAIL" name="DCL_GATEWAY_TICKET_AUTORESPONSE_EMAIL" size="50" maxlength="255" value="{$VAL_GATEWAYTICKETAUTORESPONSEEMAIL|escape}">
			<span>{$smarty.const.STR_CFG_GATEWAYTICKETAUTORESPONSEEMAILHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_GATEWAY_TICKET_REPLY">{$smarty.const.STR_CFG_GATEWAYTICKETREPLY}</label>:</label>
			<input type="checkbox" name="DCL_GATEWAY_TICKET_REPLY" id="DCL_GATEWAY_TICKET_REPLY" value="Y"{if $VAL_GATEWAYTICKETREPLY == "Y"} checked{/if}>
			<span>{$smarty.const.STR_CFG_GATEWAYTICKETREPLYHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_GATEWAY_TICKET_REPLY_LOGGED_BY">{$smarty.const.STR_CFG_GATEWAYTICKETREPLYLOGGEDBY}:</label>
			{$CMB_GATEWAYTICKETREPLYLOGGEDBY}
			<span>{$smarty.const.STR_CFG_GATEWAYTICKETREPLYLOGGEDBYHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_GATEWAY_TICKET_ACCOUNT">{$smarty.const.STR_CFG_GATEWAYTICKETACCOUNT}:</label>
			{$CMB_GATEWAYTICKETACCOUNT}
			<span>{$smarty.const.STR_CFG_GATEWAYTICKETACCOUNTHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_GATEWAY_TICKET_STATUS">{$smarty.const.STR_CFG_GATEWAYTICKETSTATUS}:</label>
			{$CMB_GATEWAYTICKETSTATUS}
			<span>{$smarty.const.STR_CFG_GATEWAYTICKETSTATUSHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_GATEWAY_TICKET_PRIORITY">{$smarty.const.STR_CFG_GATEWAYTICKETPRIORITY}:</label>
			{$CMB_GATEWAYTICKETPRIORITY}
			<span>{$smarty.const.STR_CFG_GATEWAYTICKETPRIORITYHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_GATEWAY_TICKET_SEVERITY">{$smarty.const.STR_CFG_GATEWAYTICKETSEVERITY}:</label>
			{$CMB_GATEWAYTICKETSEVERITY}
			<span>{$smarty.const.STR_CFG_GATEWAYTICKETSEVERITYHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_GATEWAY_TICKET_FILE_PATH">{$smarty.const.STR_CFG_GATEWAYTICKETFILEPATH}:</label>
			<input type="text" id="DCL_GATEWAY_TICKET_FILE_PATH" name="DCL_GATEWAY_TICKET_FILE_PATH" size="50" maxlength="255" value="{$VAL_GATEWAYTICKETFILEPATH|escape}">
			<span>{$smarty.const.STR_CFG_GATEWAYTICKETFILEPATHHELP}</span>
		</div>
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_CFG_GATEWAYWOTITLE}</legend>
		<div class="required">
			<label for="DCL_GATEWAY_WO_ENABLED">{$smarty.const.STR_CFG_GATEWAYWOENABLED}:</label>
			<input type="checkbox" name="DCL_GATEWAY_WO_ENABLED" id="DCL_GATEWAY_WO_ENABLED" value="Y"{if $VAL_GATEWAYWOENABLED == "Y"} checked{/if}>
			<span>{$smarty.const.STR_CFG_GATEWAYWOENABLEDHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_GATEWAY_WO_AUTORESPOND">{$smarty.const.STR_CFG_GATEWAYWOAUTORESPOND}:</label>
			<input type="checkbox" name="DCL_GATEWAY_WO_AUTORESPOND" id="DCL_GATEWAY_WO_AUTORESPOND" value="Y"{if $VAL_GATEWAYWOAUTORESPOND == "Y"} checked{/if}>
			<span>{$smarty.const.STR_CFG_GATEWAYWOAUTORESPONDHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_GATEWAY_TICKET_FILE_PATH">{$smarty.const.STR_CFG_GATEWAYWOAUTORESPONSEEMAIL}:</label>
			<input type="text" name="DCL_GATEWAY_WO_AUTORESPONSE_EMAIL" size="50" maxlength="255" value="{$VAL_GATEWAYWOAUTORESPONSEEMAIL|escape}">
			<span>{$smarty.const.STR_CFG_GATEWAYWOAUTORESPONSEEMAILHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_GATEWAY_WO_REPLY">{$smarty.const.STR_CFG_GATEWAYWOREPLY}:</label>
			<input type="checkbox" name="DCL_GATEWAY_WO_REPLY" id="DCL_GATEWAY_WO_REPLY" value="Y"{if $VAL_GATEWAYWOREPLY == "Y"} checked{/if}>
			<span>{$smarty.const.STR_CFG_GATEWAYWOREPLYHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_GATEWAY_TICKET_FILE_PATH">{$smarty.const.STR_CFG_GATEWAYWOREPLYLOGGEDBY}:</label>
			{$CMB_GATEWAYWOREPLYLOGGEDBY}
			<span>{$smarty.const.STR_CFG_GATEWAYWOREPLYLOGGEDBYHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_GATEWAY_TICKET_FILE_PATH">{$smarty.const.STR_CFG_GATEWAYWOACCOUNT}:</label>
			{$CMB_GATEWAYWOACCOUNT}
			<span>{$smarty.const.STR_CFG_GATEWAYWOACCOUNTHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_GATEWAY_TICKET_FILE_PATH">{$smarty.const.STR_CFG_GATEWAYWOSTATUS}:</label>
			{$CMB_GATEWAYWOSTATUS}
			<span>{$smarty.const.STR_CFG_GATEWAYWOSTATUSHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_GATEWAY_TICKET_FILE_PATH">{$smarty.const.STR_CFG_GATEWAYWOPRIORITY}:</label>
			{$CMB_GATEWAYWOPRIORITY}
			<span>{$smarty.const.STR_CFG_GATEWAYWOPRIORITYHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_GATEWAY_TICKET_FILE_PATH">{$smarty.const.STR_CFG_GATEWAYWOSEVERITY}:</label>
			{$CMB_GATEWAYWOSEVERITY}
			<span>{$smarty.const.STR_CFG_GATEWAYWOSEVERITYHELP}</span>
		</div>
		<div class="required">
			<label for="DCL_GATEWAY_TICKET_FILE_PATH">{$smarty.const.STR_CFG_GATEWAYWOFILEPATH}:</label>
			<input type="text" name="DCL_GATEWAY_WO_FILE_PATH" size="50" maxlength="255" value="{$VAL_GATEWAYWOFILEPATH|escape}">
			<span>{$smarty.const.STR_CFG_GATEWAYWOFILEPATHHELP}</span>
		</div>
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_CFG_SCM}</legend>
		<div class="required">
			<label for="DCL_SCCS_ENABLED">{$smarty.const.STR_CFG_SCCSENABLED}:</label>
			<input type="checkbox" name="DCL_SCCS_ENABLED" id="DCL_SCCS_ENABLED" value="Y"{if $VAL_SCCSENABLED == "Y"} checked{/if}>
			<span>{$smarty.const.STR_CFG_SCCSENABLEDHELP}</span>
		</div>
	</fieldset>
	<fieldset>
		<div class="submit"><input type="button" onclick="validateAndSubmit(this.form);" value="{$smarty.const.STR_CMMN_SAVE}"></div>
	</fieldset>
</form>