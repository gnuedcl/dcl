<html><head>
	<!-- $Id$ -->
	<title>DCL Notification</title>
	<style type="text/css">
		form {margin-bottom: 0px}
		a {text-decoration: none; font-weight: bold;}
		a:hover {text-decoration: underline; color: #FF6666;}
		.alight {text-decoration: none; color: {COLOR_LIGHT};}
		.alightu {text-decoration: underline; color: {COLOR_LIGHT};}
		.adark {text-decoration: none; color: {COLOR_DARK};}
		.agrey {text-decoration: none; color: #111111; font-weight: normal;}
		.agreyb {text-decoration: none; color: #111111; font-weight: bold;}
		.agreyu {text-decoration: underline; color: #111111; font-weight: normal;}
		.highlight {color: {COLOR_LIGHT}; background-color: {COLOR_DARK}; }
		.light {color: {COLOR_LIGHT};}
		.header { border-bottom: solid black 2px; }
		body { font-family: Tahoma, Verdana, Arial, Helvetica; font-size: 11px; }
		th { font-family: Tahoma, Verdana, Arial, Helvetica; font-size: 11px; }
		td { font-family: Tahoma, Verdana, Arial, Helvetica; font-size: 11px; }
		input { font-family: Tahoma, Verdana, Arial, Helvetica; font-size: 11px; }
		select { font-family: Tahoma, Verdana, Arial, Helvetica; font-size: 11px; }
		textarea { font-family: Tahoma, Verdana, Arial, Helvetica; font-size: 11px; }
	</style>
</head><body>
<table border="0" width="400px">
<tr><th colspan="2"><font size="+2">DCL Ticket Notification</font></td></tr>
<tr><td colspan="2"><i>You are receiving this e-mail because you are (1) directly involved, or (2) have a watch on this ticket, or (3) have a watch on an account or product associated with this ticket.  This is a snapshot of the ticket.  <a href="{URL_DETAIL}">Click here to view online.</a></i></td></tr>
<tr><td class="header" colspan="2" bgcolor="black"><font color="white"><b>[{VAL_TICKETID}] {VAL_SUMMARY}</b></font></td></tr>
<tr><td width="50%" valign="top">
	<table style="border: solid #cecece 2px;" border="0" width="100%">
	<tr><th colspan="2" bgcolor="#cecece">Ticket</th></tr>
	<tr><td nowrap><b>{TXT_RESPONSIBLE}</b></td><td nowrap>{VAL_RESPONSIBLE}</td></tr>
	<tr><td nowrap><b>{TXT_STATUS}</b></td><td nowrap>{VAL_STATUS} <b>on</b> {VAL_STATUSON}</td></tr>
	<tr><td nowrap><b>{TXT_PRIORITY}</b></td><td nowrap>{VAL_PRIORITY}</td></tr>
	<tr><td nowrap><b>{TXT_TYPE}</b></td><td nowrap>{VAL_TYPE}</td></tr>
	<tr><td nowrap><b>{TXT_TIME}</b></td><td nowrap>{VAL_TIME}</td></tr>
	</table>
</td><td width="50%" valign="top">
	<table style="border: solid #cecece 2px;" border="0" width="100%">
	<tr><th colspan="2" bgcolor="#cecece">Product Info</th></tr>
	<tr><td nowrap><b>{TXT_PRODUCT}</b></td><td nowrap>{VAL_PRODUCT}</td></tr>
	<tr><td nowrap><b>{TXT_MODULE}</b></td><td nowrap>{VAL_MODULE}</td></tr>
	<tr><td nowrap><b>{TXT_VERSION}</b></td><td nowrap>{VAL_VERSION}</td></tr>
	</table>
</td></tr>
<tr><td width="50%" valign="top">
	<table style="border: solid #cecece 2px;" border="0" width="100%">
	<tr><th colspan="2" bgcolor="#cecece">Dates</th></tr>
	<tr><td nowrap><b>{TXT_OPENEDBY}</b></td><td nowrap>{VAL_OPENEDBY} <b>on</b> {VAL_OPENEDON}</td></tr>
	<tr><td nowrap><b>{TXT_CLOSEDBY}</b></td><td nowrap>{VAL_CLOSEDBY} <b>on</b> {VAL_CLOSEDON}</td></tr>
	<tr><td nowrap><b>{TXT_LASTACTION}</b></td><td nowrap>{VAL_LASTACTION}</td></tr>
	</table>
</td><td width="50%" valign="top">
	<table style="border: solid #cecece 2px;" border="0" width="100%">
	<tr><th colspan="2" bgcolor="#cecece">Contact Info</th></tr>
	<tr><td nowrap><b>{TXT_ACCOUNT}</b></td><td nowrap>{VAL_ACCOUNT}</td></tr>
	<tr><td nowrap><b>{TXT_CONTACT}</b></td><td nowrap>{VAL_CONTACT}</td></tr>
	<tr><td nowrap><b>{TXT_CONTACTPHONE}</b></td><td nowrap>{VAL_CONTACTPHONE}</td></tr>
	</table>
</td></tr>
<tr><td colspan="2">
	<table style="border: solid #cecece 2px;" border="0" width="100%">
	<tr><th bgcolor="#cecece">{TXT_ISSUE}</th></tr>
	<tr><td>{VAL_ISSUE}</td></tr>
	</table>
</td></tr>
<!-- BEGIN resolutions -->
<tr><td colspan="2">
	<table style="border: solid #cecece 2px;" border="0" width="100%">
	<tr><th bgcolor="black" align="left" colspan="4"><font color="white">Logged By {VAL_LOGGEDBY} on {VAL_LOGGEDON}</font></th></tr>
	<tr><td nowrap width="25%"><b>{TXT_STATUS}</b></td><td nowrap width="25%">{VAL_RESSTATUS}</td><td nowrap width="25%"><b>{TXT_TIME}</b></td><td nowrap width="25%">{VAL_RESTIME}</td></tr>
	<tr><td colspan="4"><b>{TXT_RESOLUTION}:</b> {VAL_RESOLUTION}</td></tr>
	</table>
</td></tr>
<!-- END resolutions -->
</table>
</body></html>
