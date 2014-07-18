<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
<form class="form-horizontal" role="form">
	{dcl_form_control id=filter controlsize=10 label=Filter}
	{dcl_input_text id=filter value=""}
	{/dcl_form_control}
</form>
<form id="config-form" class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="Configuration.Update">
	<fieldset>
		<legend>{$smarty.const.STR_CFG_SYSTEMTITLE|escape}</legend>
		{dcl_form_control id=DCL_DATE_FORMAT controlsize=4 label=$smarty.const.STR_CFG_DATEFORMAT required=true help=$smarty.const.STR_CFG_DATEFORMATHELP}
			{$CMB_DATEFORMAT}
		{/dcl_form_control}
		{dcl_form_control id=DCL_DATE_FORMATDB controlsize=4 label=$smarty.const.STR_CFG_DATEFORMATDB required=true help=$smarty.const.STR_CFG_DATEFORMATDBHELP}
			{$CMB_DATEFORMATDB}
		{/dcl_form_control}
		{dcl_form_control id=DCL_TIMESTAMP_FORMAT controlsize=4 label=$smarty.const.STR_CFG_TIMESTAMPFORMAT required=true help=$smarty.const.STR_CFG_TIMESTAMPFORMATHELP}
			{$CMB_TIMESTAMPFORMAT}
		{/dcl_form_control}
		{dcl_form_control id=DCL_TIMESTAMP_FORMATDB controlsize=4 label=$smarty.const.STR_CFG_TIMESTAMPFORMATDB required=true help=$smarty.const.STR_CFG_TIMESTAMPFORMATDBHELP}
			{$CMB_TIMESTAMPFORMATDB}
		{/dcl_form_control}
		{dcl_form_control id=DCL_MAX_UPLOAD_FILE_SIZE controlsize=4 label=$smarty.const.STR_CFG_MAXUPLOADFILESIZE required=true help=$smarty.const.STR_CFG_MAXUPLOADFILESIZEHELP}
			{dcl_input_text id=DCL_MAX_UPLOAD_FILE_SIZE maxlength=10 value=$VAL_MAXUPLOADFILESIZE}
		{/dcl_form_control}
		{dcl_form_control id=DCL_DEFAULT_LANGUAGE controlsize=4 label=$smarty.const.STR_CFG_LANGUAGE required=true help=$smarty.const.STR_CFG_LANGUAGEHELP}
			{$CMB_DEFAULT_LANGUAGE}
		{/dcl_form_control}
		{dcl_form_control id=DCL_PRIVATE_KEY controlsize=10 label=$smarty.const.STR_CFG_PRIVATEKEY required=true help=$smarty.const.STR_CFG_PRIVATEKEYHELP}
			{dcl_input_text id=DCL_PRIVATE_KEY maxlength=255 value=$VAL_PRIVATEKEY}
		{/dcl_form_control}
		{dcl_form_control id=DCL_APP_NAME controlsize=10 label=$smarty.const.STR_CFG_APPNAME required=true help=$smarty.const.STR_CFG_APPNAMEHELP}
			{dcl_input_text id=DCL_APP_NAME maxlength=255 value=$VAL_APPNAME}
		{/dcl_form_control}
		{dcl_form_control id=DCL_LOGIN_MESSAGE controlsize=10 label=$smarty.const.STR_CFG_LOGINMESSAGE required=true help=$smarty.const.STR_CFG_LOGINMESSAGEHELP}
			{dcl_input_text id=DCL_LOGIN_MESSAGE maxlength=255 value=$VAL_LOGINMESSAGE}
		{/dcl_form_control}
		{dcl_form_control id=DCL_HTML_TITLE controlsize=10 label=$smarty.const.STR_CFG_HTMLTITLE required=true help=$smarty.const.STR_CFG_HTMLTITLEHELP}
			{dcl_input_text id=DCL_HTML_TITLE maxlength=255 value=$VAL_HTMLTITLE}
		{/dcl_form_control}
		{dcl_form_control id=DCL_ROOT controlsize=10 label=$smarty.const.STR_CFG_ROOT required=true help=$smarty.const.STR_CFG_ROOTHELP}
		{dcl_input_text id=DCL_ROOT maxlength=255 value=$VAL_ROOT}
		{/dcl_form_control}
		{dcl_form_control id=DCL_FILE_PATH controlsize=10 label=$smarty.const.STR_CFG_FILEPATH required=true help=$smarty.const.STR_CFG_FILEPATHHELP}
		{dcl_input_text id=DCL_FILE_PATH maxlength=255 value=$VAL_FILEPATH}
		{/dcl_form_control}
		{dcl_form_control id=DCL_GD_TYPE controlsize=4 label=$smarty.const.STR_CFG_GDTYPE required=true help=$smarty.const.STR_CFG_GDTYPEHELP}
		{$CMB_GDTYPE}
		{/dcl_form_control}
		{dcl_form_control id=DCL_SESSION_TIMEOUT controlsize=2 label=$smarty.const.STR_CFG_SESSIONTIMEOUT required=true help=$smarty.const.STR_CFG_SESSIONTIMEOUTHELP}
		{dcl_input_text id=DCL_SESSION_TIMEOUT maxlength=5 value=$VAL_SESSIONTIMEOUT}
		{/dcl_form_control}
		{dcl_form_control id=DCL_SEC_AUDIT_ENABLED controlsize=10 label=$smarty.const.STR_CFG_SECAUDITENABLED required=true help=$smarty.const.STR_CFG_SECAUDITENABLEDHELP}
		<input type="checkbox" id="DCL_SEC_AUDIT_ENABLED" name="DCL_SEC_AUDIT_ENABLED" value="Y"{if $VAL_SECAUDITENABLED == "Y"} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=DCL_SEC_AUDIT_LOGIN_ONLY controlsize=10 label=$smarty.const.STR_CFG_SECAUDITLOGINONLY required=true help=$smarty.const.STR_CFG_SECAUDITLOGINONLYHELP}
		<input type="checkbox" id="DCL_SEC_AUDIT_LOGIN_ONLY" name="DCL_SEC_AUDIT_LOGIN_ONLY" value="Y"{if $VAL_SECAUDITLOGINONLY == "Y"} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=DCL_FORCE_SECURE_COOKIE controlsize=10 label="Force Secure Cookie" required=true help="Force secure (SSL-only) cookie.  Useful if you have an SSL connection that terminates at a load balancer instead of the web server."}
			<input type="checkbox" id="DCL_FORCE_SECURE_COOKIE" name="DCL_FORCE_SECURE_COOKIE" value="Y"{if $VAL_FORCESECURECOOKIE == "Y"} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=DCL_FORCE_SECURE_GRAVATAR controlsize=10 label=$smarty.const.STR_CFG_FORCESECUREGRAVATAR required=true help=$smarty.const.STR_CFG_FORCESECUREGRAVATARHELP}
		<input type="checkbox" id="DCL_FORCE_SECURE_GRAVATAR" name="DCL_FORCE_SECURE_GRAVATAR" value="Y"{if $VAL_FORCESECUREGRAVATAR == "Y"} checked{/if}>
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_CFG_EMAILSERVERTITLE}</legend>
		{dcl_form_control id=DCL_SMTP_ENABLED controlsize=10 label=$smarty.const.STR_CFG_SMTPENABLED required=true help=$smarty.const.STR_CFG_SMTPENABLEDHELP}
		<input type="checkbox" id="DCL_SMTP_ENABLED" name="DCL_SMTP_ENABLED" value="Y"{if $VAL_SMTPENABLED == "Y"} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=DCL_SMTP_SERVER controlsize=10 label=$smarty.const.STR_CFG_SMTPSERVER required=true help=$smarty.const.STR_CFG_SMTPSERVERHELP}
		{dcl_input_text id=DCL_SMTP_SERVER maxlength=255 value=$VAL_SMTPSERVER}
		{/dcl_form_control}
		{dcl_form_control id=DCL_SMTP_PORT controlsize=2 label=$smarty.const.STR_CFG_SMTPPORT required=true help=$smarty.const.STR_CFG_SMTPPORTHELP}
		{dcl_input_text id=DCL_SMTP_PORT maxlength=5 value=$VAL_SMTPPORT}
		{/dcl_form_control}
		{dcl_form_control id=DCL_SMTP_AUTH_REQUIRED controlsize=10 label=$smarty.const.STR_CFG_SMTPAUTHREQUIRED required=true help=$smarty.const.STR_CFG_SMTPAUTHREQUIREDHELP}
		<input type="checkbox" id="DCL_SMTP_AUTH_REQUIRED" name="DCL_SMTP_AUTH_REQUIRED" value="Y"{if $VAL_SMTPAUTHREQUIRED == "Y"} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=DCL_SMTP_AUTH_USER controlsize=10 label=$smarty.const.STR_CFG_SMTPAUTHUSER required=true help=$smarty.const.STR_CFG_SMTPAUTHUSERHELP}
		{dcl_input_text id=DCL_SMTP_AUTH_USER maxlength=255 value=$VAL_SMTPAUTHUSER}
		{/dcl_form_control}
		{dcl_form_control id=DCL_SMTP_AUTH_PWD controlsize=10 label=$smarty.const.STR_CFG_SMTPAUTHPWD required=true help=$smarty.const.STR_CFG_SMTPAUTHPWDHELP}
		{dcl_input_text id=DCL_SMTP_AUTH_PWD maxlength=255 value=$VAL_SMTPAUTHPWD}
		{/dcl_form_control}
		{dcl_form_control id=DCL_SMTP_TIMEOUT controlsize=2 label=$smarty.const.STR_CFG_SMTPTIMEOUT required=true help=$smarty.const.STR_CFG_SMTPTIMEOUTHELP}
		{dcl_input_text id=DCL_SMTP_TIMEOUT maxlength=5 value=$VAL_SMTPTIMEOUT}
		{/dcl_form_control}
		{dcl_form_control id=DCL_SMTP_DEFAULT_EMAIL controlsize=10 label=$smarty.const.STR_CFG_SMTPDEFAULTEMAIL required=true help=$smarty.const.STR_CFG_SMTPDEFAULTEMAILHELP}
		{dcl_input_text id=DCL_SMTP_DEFAULT_EMAIL maxlength=255 value=$VAL_SMTPDEFAULTEMAIL}
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_CFG_LOOKNFEELTITLE}</legend>
		{dcl_form_control id=DCL_DEF_TEMPLATE_SET controlsize=4 label=$smarty.const.STR_CFG_DEFTEMPLATESET required=true help=$smarty.const.STR_CFG_DEFTEMPLATESETHELP}
		{$CMB_DEFTEMPLATESET}
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_CFG_WORKORDERTITLE}</legend>
		{dcl_form_control id=DCL_DEF_STATUS_ASSIGN_WO controlsize=4 label=$smarty.const.STR_CFG_DEFAULTSTATUSASSIGN required=true help=$smarty.const.STR_CFG_DEFAULTSTATUSASSIGNHELP}
		{$CMB_DEFAULTSTATUSASSIGN}
		{/dcl_form_control}
		{dcl_form_control id=DCL_DEF_STATUS_UNASSIGN_WO controlsize=4 label=$smarty.const.STR_CFG_DEFAULTSTATUSUNASSIGN required=true help=$smarty.const.STR_CFG_DEFAULTSTATUSUNASSIGNHELP}
		{$CMB_DEFAULTSTATUSUNASSIGN}
		{/dcl_form_control}
		{dcl_form_control id=DCL_DEF_PRIORITY controlsize=4 label=$smarty.const.STR_CFG_DEFAULTPRIORITY required=true help=$smarty.const.STR_CFG_DEFAULTPRIORITYHELP}
		{$CMB_DEFAULTPRIORITY}
		{/dcl_form_control}
		{dcl_form_control id=DCL_DEF_SEVERITY controlsize=4 label=$smarty.const.STR_CFG_DEFAULTSEVERITY required=true help=$smarty.const.STR_CFG_DEFAULTSEVERITYHELP}
		{$CMB_DEFAULTSEVERITY}
		{/dcl_form_control}
		{dcl_form_control id=DCL_AUTO_DATE controlsize=10 label=$smarty.const.STR_CFG_AUTODATE required=true help=$smarty.const.STR_CFG_AUTODATEHELP}
		<input type="checkbox" id="DCL_AUTO_DATE" name="DCL_AUTO_DATE" value="Y"{if $VAL_AUTODATE == "Y"} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=DCL_TIME_CARD_ORDER controlsize=4 label=$smarty.const.STR_CFG_TIMECARDORDER required=true help=$smarty.const.STR_CFG_TIMECARDORDERHELP}
		{$CMB_TIMECARDORDER}
		{/dcl_form_control}
		{dcl_form_control id=DCL_WO_NOTIFICATION_HTML controlsize=10 label=$smarty.const.STR_CFG_WONOTIFICATIONHTML required=true help=$smarty.const.STR_CFG_WONOTIFICATIONHTMLHELP}
		<input type="checkbox" id="DCL_WO_NOTIFICATION_HTML" name="DCL_WO_NOTIFICATION_HTML" value="Y"{if $VAL_WONOTIFICATIONHTML == "Y"} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=DCL_WO_EMAIL_TEMPLATE controlsize=10 label=$smarty.const.STR_CFG_WOEMAILTEMPLATE required=true help=$smarty.const.STR_CFG_WOEMAILTEMPLATEHELP}
		{dcl_input_text id=DCL_WO_EMAIL_TEMPLATE maxlength=255 value=$VAL_WOEMAILTEMPLATE}
		{/dcl_form_control}
		{dcl_form_control id=DCL_WO_EMAIL_TEMPLATE_PUBLIC controlsize=10 label=$smarty.const.STR_CFG_WOEMAILTEMPLATEPUBLIC required=true help=$smarty.const.STR_CFG_WOEMAILTEMPLATEPUBLICHELP}
		{dcl_input_text id=DCL_WO_EMAIL_TEMPLATE_PUBLIC maxlength=255 value=$VAL_WOEMAILTEMPLATEPUBLIC}
		{/dcl_form_control}
		{dcl_form_control id=DCL_WO_SECONDARY_ACCOUNTS_ENABLED controlsize=10 label=$smarty.const.STR_CFG_WOSECONDARYACCOUNTSENABLED required=true help=$smarty.const.STR_CFG_WOSECONDARYACCOUNTSENABLEDHELP}
		<input type="checkbox" id="DCL_WO_SECONDARY_ACCOUNTS_ENABLED" name="DCL_WO_SECONDARY_ACCOUNTS_ENABLED" value="Y"{if $VAL_WOSECONDARYACCOUNTSENABLED == "Y"} checked{/if}>
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_CFG_PROJECTTITLE}</legend>
		{dcl_form_control id=DCL_DEFAULT_PROJECT_STATUS controlsize=4 label=$smarty.const.STR_CFG_DCLDEFAULTPROJECTSTATUS required=true help=$smarty.const.STR_CFG_DCLDEFAULTPROJECTSTATUSHELP}
		{$CMB_DEFAULTPROJECTSTATUS}
		{/dcl_form_control}
		{dcl_form_control id=DCL_PROJECT_XML_TEMPLATES controlsize=10 label=$smarty.const.STR_CFG_DCLPROJECTXMLTEMPLATES required=true help=$smarty.const.STR_CFG_DCLPROJECTXMLTEMPLATESHELP}
		<input type="checkbox" id="DCL_PROJECT_XML_TEMPLATES" name="DCL_PROJECT_XML_TEMPLATES" value="Y"{if $VAL_PRJXMLTMPL == "Y"} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=DCL_PROJECT_BROWSE_PARENTS_ONLY controlsize=10 label=$smarty.const.STR_CFG_DCLPROJECTBROWSEPARENTSONLY required=true help=$smarty.const.STR_CFG_DCLPROJECTBROWSEPARENTSONLYHELP}
		<input type="checkbox" id="DCL_PROJECT_BROWSE_PARENTS_ONLY" name="DCL_PROJECT_BROWSE_PARENTS_ONLY" value="Y"{if $VAL_PRJBROWSEPARENTSONLY == "Y"} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=DCL_PROJECT_INCLUDE_CHILD_STATS controlsize=10 label=$smarty.const.STR_CFG_DCLPROJECTINCLUDECHILDSTATS required=true help=$smarty.const.STR_CFG_DCLPROJECTINCLUDECHILDSTATSHELP}
		<input type="checkbox" id="DCL_PROJECT_INCLUDE_CHILD_STATS" name="DCL_PROJECT_INCLUDE_CHILD_STATS" value="Y"{if $VAL_PRJCHLDSTATS == "Y"} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=DCL_PROJECT_INCLUDE_PARENT_STATS controlsize=10 label=$smarty.const.STR_CFG_DCLPROJECTINCLUDEPARENTSTATS required=true help=$smarty.const.STR_CFG_DCLPROJECTINCLUDEPARENTSTATSHELP}
		<input type="checkbox" id="DCL_PROJECT_INCLUDE_PARENT_STATS" name="DCL_PROJECT_INCLUDE_PARENT_STATS" value="Y"{if $VAL_PRJPRNTSTATS == "Y"} checked{/if}>
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_CFG_TICKETTITLE}</legend>
		{dcl_form_control id=DCL_DEFAULT_TICKET_STATUS controlsize=10 label=$smarty.const.STR_CFG_DCLDEFAULTTICKETSTATUS required=true help=$smarty.const.STR_CFG_DCLDEFAULTTICKETSTATUSHELP}
		{$CMB_DEFAULTTICKETSTATUS}
		{/dcl_form_control}
		{dcl_form_control id=DCL_TCK_NOTIFICATION_HTML controlsize=10 label=$smarty.const.STR_CFG_TCKNOTIFICATIONHTML required=true help=$smarty.const.STR_CFG_TCKNOTIFICATIONHTMLHELP}
		<input type="checkbox" id="DCL_TCK_NOTIFICATION_HTML" name="DCL_TCK_NOTIFICATION_HTML" value="Y"{if $VAL_TCKNOTIFICATIONHTML == "Y"} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=DCL_TCK_EMAIL_TEMPLATE controlsize=10 label=$smarty.const.STR_CFG_TCKEMAILTEMPLATE required=true help=$smarty.const.STR_CFG_TCKEMAILTEMPLATEHELP}
		{dcl_input_text id=DCL_TCK_EMAIL_TEMPLATE maxlength=255 value=$VAL_TCKEMAILTEMPLATE}
		{/dcl_form_control}
		{dcl_form_control id=DCL_TCK_EMAIL_TEMPLATE_PUBLIC controlsize=10 label=$smarty.const.STR_CFG_TCKEMAILTEMPLATEPUBLIC required=true help=$smarty.const.STR_CFG_TCKEMAILTEMPLATEPUBLICHELP}
		{dcl_input_text id=DCL_TCK_EMAIL_TEMPLATE_PUBLIC maxlength=255 value=$VAL_TCKEMAILTEMPLATEPUBLIC}
		{/dcl_form_control}
		{dcl_form_control id=DCL_CQQ_PERCENT controlsize=4 label=$smarty.const.STR_CFG_CQQPERCENT required=true help=$smarty.const.STR_CFG_CQQPERCENTHELP}
		{$CMB_CQQPERCENT}
		{/dcl_form_control}
		{dcl_form_control id=DCL_CQQ_FROM controlsize=10 label=$smarty.const.STR_CFG_CQQFROM required=true help=$smarty.const.STR_CFG_CQQFROMHELP}
		{dcl_input_text id=DCL_CQQ_FROM maxlength=255 value=$VAL_CQQFROM}
		{/dcl_form_control}
		{dcl_form_control id=DCL_CQQ_SUBJECT controlsize=10 label=$smarty.const.STR_CFG_CQQSUBJECT required=true help=$smarty.const.STR_CFG_CQQSUBJECTHELP}
		{dcl_input_text id=DCL_CQQ_SUBJECT maxlength=255 value=$VAL_CQQSUBJECT}
		{/dcl_form_control}
		{dcl_form_control id=DCL_CQQ_TEMPLATE controlsize=10 label=$smarty.const.STR_CFG_CQQTEMPLATE required=true help=$smarty.const.STR_CFG_CQQTEMPLATEHELP}
		{dcl_input_text id=DCL_CQQ_TEMPLATE maxlength=255 value=$VAL_CQQTEMPLATE}
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_CFG_WIKI}</legend>
		{dcl_form_control id=DCL_WIKI_ENABLED controlsize=10 label=$smarty.const.STR_CFG_WIKIENABLED required=true help=$smarty.const.STR_CFG_WIKIENABLEDHELP}
		<input type="checkbox" id="DCL_WIKI_ENABLED" name="DCL_WIKI_ENABLED" value="Y"{if $VAL_WIKIENABLED == "Y"} checked{/if}>
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_CFG_GATEWAYTICKETTITLE}</legend>
		{dcl_form_control id=DCL_GATEWAY_TICKET_ENABLED controlsize=10 label=$smarty.const.STR_CFG_GATEWAYTICKETENABLED required=true help=$smarty.const.STR_CFG_GATEWAYTICKETENABLEDHELP}
		<input type="checkbox" id="DCL_GATEWAY_TICKET_ENABLED" name="DCL_GATEWAY_TICKET_ENABLED" value="Y"{if $VAL_GATEWAYTICKETENABLED == "Y"} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=DCL_GATEWAY_TICKET_AUTORESPOND controlsize=10 label=$smarty.const.STR_CFG_GATEWAYTICKETAUTORESPOND required=true help=$smarty.const.STR_CFG_GATEWAYTICKETAUTORESPONDHELP}
		<input type="checkbox" id="DCL_GATEWAY_TICKET_AUTORESPOND" name="DCL_GATEWAY_TICKET_AUTORESPOND" value="Y"{if $VAL_GATEWAYTICKETAUTORESPOND == "Y"} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=DCL_GATEWAY_TICKET_AUTORESPONSE_EMAIL controlsize=10 label=$smarty.const.STR_CFG_GATEWAYTICKETAUTORESPONSEEMAIL required=true help=$smarty.const.STR_CFG_GATEWAYTICKETAUTORESPONSEEMAILHELP}
		{dcl_input_text id=DCL_GATEWAY_TICKET_AUTORESPONSE_EMAIL maxlength=255 value=$VAL_GATEWAYTICKETAUTORESPONSEEMAIL}
		{/dcl_form_control}
		{dcl_form_control id=DCL_GATEWAY_TICKET_REPLY controlsize=10 label=$smarty.const.STR_CFG_GATEWAYTICKETREPLY required=true help=$smarty.const.STR_CFG_GATEWAYTICKETREPLYHELP}
		<input type="checkbox" id="DCL_GATEWAY_TICKET_REPLY" name="DCL_GATEWAY_TICKET_REPLY" value="Y"{if $VAL_GATEWAYTICKETREPLY == "Y"} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=DCL_GATEWAY_TICKET_REPLY_LOGGED_BY controlsize=4 label=$smarty.const.STR_CFG_GATEWAYTICKETREPLYLOGGEDBY required=true help=$smarty.const.STR_CFG_GATEWAYTICKETREPLYLOGGEDBYHELP}
		{$CMB_GATEWAYTICKETREPLYLOGGEDBY}
		{/dcl_form_control}
		{dcl_form_control id=DCL_GATEWAY_TICKET_ACCOUNT controlsize=4 label=$smarty.const.STR_CFG_GATEWAYTICKETACCOUNT required=true help=$smarty.const.STR_CFG_GATEWAYTICKETACCOUNTHELP}
		{$CMB_GATEWAYTICKETACCOUNT}
		{/dcl_form_control}
		{dcl_form_control id=DCL_GATEWAY_TICKET_STATUS controlsize=4 label=$smarty.const.STR_CFG_GATEWAYTICKETSTATUS required=true help=$smarty.const.STR_CFG_GATEWAYTICKETSTATUSHELP}
		{$CMB_GATEWAYTICKETSTATUS}
		{/dcl_form_control}
		{dcl_form_control id=DCL_GATEWAY_TICKET_PRIORITY controlsize=4 label=$smarty.const.STR_CFG_GATEWAYTICKETPRIORITY required=true help=$smarty.const.STR_CFG_GATEWAYTICKETPRIORITYHELP}
		{$CMB_GATEWAYTICKETPRIORITY}
		{/dcl_form_control}
		{dcl_form_control id=DCL_GATEWAY_TICKET_SEVERITY controlsize=4 label=$smarty.const.STR_CFG_GATEWAYTICKETSEVERITY required=true help=$smarty.const.STR_CFG_GATEWAYTICKETSEVERITYHELP}
		{$CMB_GATEWAYTICKETSEVERITY}
		{/dcl_form_control}
		{dcl_form_control id=DCL_GATEWAY_TICKET_FILE_PATH controlsize=10 label=$smarty.const.STR_CFG_GATEWAYTICKETFILEPATH required=true help=$smarty.const.STR_CFG_GATEWAYTICKETFILEPATHHELP}
		{dcl_input_text id=DCL_GATEWAY_TICKET_FILE_PATH maxlength=255 value=$VAL_GATEWAYTICKETFILEPATH}
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_CFG_GATEWAYWOTITLE}</legend>
		{dcl_form_control id=DCL_GATEWAY_WO_ENABLED controlsize=10 label=$smarty.const.STR_CFG_GATEWAYWOENABLED required=true help=$smarty.const.STR_CFG_GATEWAYWOENABLEDHELP}
		<input type="checkbox" id="DCL_GATEWAY_WO_ENABLED" name="DCL_GATEWAY_WO_ENABLED" value="Y"{if $VAL_GATEWAYWOENABLED == "Y"} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=DCL_GATEWAY_WO_AUTORESPOND controlsize=10 label=$smarty.const.STR_CFG_GATEWAYWOAUTORESPOND required=true help=$smarty.const.STR_CFG_GATEWAYWOAUTORESPONDHELP}
		<input type="checkbox" id="DCL_GATEWAY_WO_AUTORESPOND" name="DCL_GATEWAY_WO_AUTORESPOND" value="Y"{if $VAL_GATEWAYWOAUTORESPOND == "Y"} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=DCL_GATEWAY_WO_AUTORESPONSE_EMAIL controlsize=10 label=$smarty.const.STR_CFG_GATEWAYWOAUTORESPONSEEMAIL required=true help=$smarty.const.STR_CFG_GATEWAYWOAUTORESPONSEEMAILHELP}
		{dcl_input_text id=DCL_GATEWAY_WO_AUTORESPONSE_EMAIL maxlength=255 value=$VAL_GATEWAYWOAUTORESPONSEEMAIL}
		{/dcl_form_control}
		{dcl_form_control id=DCL_GATEWAY_WO_REPLY controlsize=10 label=$smarty.const.STR_CFG_GATEWAYWOREPLY required=true help=$smarty.const.STR_CFG_GATEWAYWOREPLYHELP}
		<input type="checkbox" id="DCL_GATEWAY_WO_REPLY" name="DCL_GATEWAY_WO_REPLY" value="Y"{if $VAL_GATEWAYWOREPLY == "Y"} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=DCL_GATEWAY_TICKET_FILE_PATH controlsize=4 label=$smarty.const.STR_CFG_GATEWAYWOREPLYLOGGEDBY required=true help=$smarty.const.STR_CFG_GATEWAYWOREPLYLOGGEDBYHELP}
		{$CMB_GATEWAYWOREPLYLOGGEDBY}
		{/dcl_form_control}
		{dcl_form_control id=DCL_GATEWAY_WO_ACCOUNT controlsize=4 label=$smarty.const.STR_CFG_GATEWAYWOACCOUNT required=true help=$smarty.const.STR_CFG_GATEWAYWOACCOUNTHELP}
		{$CMB_GATEWAYWOACCOUNT}
		{/dcl_form_control}
		{dcl_form_control id=DCL_GATEWAY_WO_STATUS controlsize=4 label=$smarty.const.STR_CFG_GATEWAYWOSTATUS required=true help=$smarty.const.STR_CFG_GATEWAYWOSTATUSHELP}
		{$CMB_GATEWAYWOSTATUS}
		{/dcl_form_control}
		{dcl_form_control id=DCL_GATEWAY_WO_PRIORITY controlsize=4 label=$smarty.const.STR_CFG_GATEWAYWOPRIORITY required=true help=$smarty.const.STR_CFG_GATEWAYWOPRIORITYHELP}
		{$CMB_GATEWAYWOPRIORITY}
		{/dcl_form_control}
		{dcl_form_control id=DCL_GATEWAY_WO_SEVERITY controlsize=4 label=$smarty.const.STR_CFG_GATEWAYWOSEVERITY required=true help=$smarty.const.STR_CFG_GATEWAYWOSEVERITYHELP}
		{$CMB_GATEWAYWOSEVERITY}
		{/dcl_form_control}
		{dcl_form_control id=DCL_GATEWAY_WO_FILE_PATH controlsize=10 label=$smarty.const.STR_CFG_GATEWAYWOFILEPATH required=true help=$smarty.const.STR_CFG_GATEWAYWOFILEPATHHELP}
		{dcl_input_text id=DCL_GATEWAY_WO_FILE_PATH maxlength=255 value=$VAL_GATEWAYWOFILEPATH}
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_CFG_SCM}</legend>
		{dcl_form_control id=DCL_SCCS_ENABLED controlsize=10 label=$smarty.const.STR_CFG_SCCSENABLED required=true help=$smarty.const.STR_CFG_SCCSENABLEDHELP}
		<input type="checkbox" id="DCL_SCCS_ENABLED" name="DCL_SCCS_ENABLED" value="Y"{if $VAL_SCCSENABLED == "Y"} checked{/if}>
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="button" onclick="validateAndSubmit(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#filter").focus();
		$("#content").find("select").select2({ minimumResultsForSearch: 10 });

		var $configLabels = $("#config-form").find("div.form-group > label");
		$("#filter").on("keyup", function() {
			var filterVal = $("#filter").val();
			if (filterVal == "") {
				$configLabels.parent().show();
				return;
			}

			var regex = new RegExp(filterVal, "i");
			$configLabels.each(function() {
				if (regex.test($(this).text())) {
					$(this).parent().show();
				}
				else {
					$(this).parent().hide();
				}
			});
		});
	});

	function validateAndSubmit(f)
	{
		f.submit();
	}
</script>