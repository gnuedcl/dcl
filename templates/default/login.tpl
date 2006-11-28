<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
        "http://www.w3.org/TR/REC-html40/loose.dtd">
<html>
<head>
<!-- $Id: login.tpl,v 1.7 2006/11/27 06:00:53 mdean Exp $ -->
<meta http-equiv="Set-Cookie" content="DCLINFO=;expires=Friday, 31-Dec-00 23:59:59 GMT">
<meta http-equiv="Expires" content="-1">
<title>{$TXT_TITLE}</title>
<script language="JavaScript">{literal}
function init()
{
    document.forms[0].elements['UID'].focus();
}
{/literal}</script>
<link rel="stylesheet" href="{$DIR_CSS}default.css" type="text/css"></link>
</head>
<body onload="init();">
<h3>{$VAL_WELCOME}</h3>
<form class="styled login" method="post" action="login.php">
{if $VAL_REFERTO}<input type="hidden" name="refer_to" value="{$VAL_REFERTO|escape:"rawurl"}">{/if}
	<fieldset>
		<legend>{$TXT_LOGIN}</legend>
{if $VAL_ERROR}
		<div class="help">{$VAL_ERROR}</div>
{/if}
		<div class="required">
			<label for="UID">{$TXT_USER}:</label>
			<input type="text" maxlength="25" size="25" id="UID" name="UID">
		</div>
		<div class="required">
			<label for="PWD">{$TXT_PASSWORD}:</label>
			<input type="password" size="25" id="PWD" name="PWD">
		</div>
		<div class="required">
			<label for="domain">{$TXT_DOMAIN}:</label>
			{$CMB_DOMAIN}
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="submit" value="{$BTN_LOGIN}">
			<input type="reset" value="{$BTN_CLEAR}">
		</div>
	</fieldset>
</form>
<div id="poweredby">Powered By <a target="_blank" href="http://www.gnuenterprise.org/">GNU Enterprise</a> <a target="_blank" href="http://dcl.sourceforge.net/">DCL</a> Copyright (C) 1999-2005 <a target="_blank" href="http://www.fsf.org/">Free Software Foundation</a></div>
</body></html>
