<!-- $Id: htmlVersion.tpl,v 1.3 2006/11/27 06:00:52 mdean Exp $ -->
<center>
<table border="0" cellspacing="0" cellpadding="2">
<tr><th class="formTitle">{TXT_TITLE}</th></tr>
<tr><td class="formContainer">
	<table border="0" cellspacing="2" cellpadding="2" width="100%">
	<tr><td class="bottomSeparator" style="font-weight: bold;">{TXT_DCL}:</td>
		<td class="bottomSeparator">{VAL_DCLVERSION}&nbsp;</td>
	</tr>
	<tr><td class="bottomSeparator" style="font-weight: bold;">{TXT_SERVEROS}:</td>
		<td class="bottomSeparator">{VAL_SERVEROS}&nbsp;</td>
	</tr>
	<tr><td class="bottomSeparator" style="font-weight: bold;">{TXT_SERVERNAME}:</td>
		<td class="bottomSeparator">{VAL_SERVERNAME}&nbsp;</td>
	</tr>
	<tr><td class="bottomSeparator" style="font-weight: bold;">{TXT_WEBSERVER}:</td>
		<td class="bottomSeparator">{VAL_SERVERSOFTWARE}&nbsp;</td>
	</tr>
	<tr><td style="font-weight: bold;">{TXT_PHPVER}:</td>
		<td>{VAL_PHPVERSION}&nbsp;</td>
	</tr>
	</table>
</td></tr>
<tr><th class="formTitle">{TXT_YOURVER}</th></tr>
<tr><td class="formContainer">
	<table border="0" cellspacing="2" cellpadding="2" width="100%">
	<tr><td class="bottomSeparator" style="font-weight: bold;">{TXT_YOURIP}:</td>
		<td class="bottomSeparator">{VAL_REMOTEADDR}&nbsp;</td>
	</tr>
	<tr><td style="font-weight: bold;">{TXT_YOURBROWSER}:</td>
		<td>{VAL_HTTPUSERAGENT}&nbsp;</td>
	</tr>
	</table>
</td></tr>
</table>
</center>
