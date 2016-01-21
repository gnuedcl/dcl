<html><head>
	<title>DCL Notification</title>
	<style type="text/css">
		a { text-decoration: none; font-weight: bold; }
		a:hover { text-decoration: underline; color: #FF6666; }
		.header { border-bottom: solid black 2px; }
		body { font-family: Tahoma, Verdana, Arial, Helvetica; font-size: 11px; }
		th { font-family: Tahoma, Verdana, Arial, Helvetica; font-size: 11px; }
		td { font-family: Tahoma, Verdana, Arial, Helvetica; font-size: 11px; }

	</style>
</head><body>
<table border="0" width="400px">
<tr><th colspan="2"><font size="+2">DCL Ticket Notification</font></td></tr>
<tr><td colspan="2"><i>You are receiving this e-mail because you are (1) directly involved, or (2) have a watch on this ticket, or (3) have a watch on an account or product associated with this ticket.  This is a snapshot of the ticket.  <a href="{dcl_config name="DCL_ROOT"}main.php?menuAction=boTickets.view&ticketid={$obj->ticketid}">Click here to view online.</a></i></td></tr>
<tr><td class="header" colspan="2" bgcolor="black"><font color="white"><b>[{$obj->ticketid}] {$obj->summary|escape}</b></font></td></tr>
<tr><td width="50%" valign="top">
	<table style="border: solid #cecece 2px;" border="0" width="100%">
	<tr><th colspan="2" bgcolor="#cecece">Ticket</th></tr>
	<tr><td nowrap><b>{$smarty.const.STR_TCK_RESPONSIBLE|escape}</b></td><td nowrap>{dcl_metadata_display type='personnel' value=$obj->responsible|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_TCK_STATUS|escape}</b></td><td nowrap>{dcl_metadata_display type='status' value=$obj->status|escape} <b>on</b> {$obj->statuson|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_TCK_PRIORITY|escape}</b></td><td nowrap>{dcl_metadata_display type='priority' value=$obj->priority|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_TCK_TYPE|escape}</b></td><td nowrap>{dcl_metadata_display type='severity' value=$obj->type|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_TCK_TIME|escape}</b></td><td nowrap>{$obj->time|escape}</td></tr>
	</table>
</td><td width="50%" valign="top">
	<table style="border: solid #cecece 2px;" border="0" width="100%">
	<tr><th colspan="2" bgcolor="#cecece">Product Info</th></tr>
	<tr><td nowrap><b>{$smarty.const.STR_TCK_PRODUCT|escape}</b></td><td nowrap>{dcl_metadata_display type='product' value=$obj->product|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_TCK_MODULE|escape}</b></td><td nowrap>{dcl_metadata_display type='module' value=$obj->module|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_TCK_VERSION|escape}</b></td><td nowrap>{$obj->version|escape}</td></tr>
	</table>
</td></tr>
<tr><td width="50%" valign="top">
	<table style="border: solid #cecece 2px;" border="0" width="100%">
	<tr><th colspan="2" bgcolor="#cecece">Dates</th></tr>
	<tr><td nowrap><b>{$smarty.const.STR_TCK_OPENEDBY|escape}</b></td><td nowrap>{dcl_metadata_display type='personnel' value=$obj->createdby|escape} <b>on</b> {$obj->createdon|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_TCK_CLOSEDBY|escape}</b></td><td nowrap>{dcl_metadata_display type='personnel' value=$obj->closedby|escape} on {$obj->closedon|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_TCK_LASTACTIONON|escape}</b></td><td nowrap>{$obj->lastactionon|escape}</td></tr>
	</table>
</td><td width="50%" valign="top">
	<table style="border: solid #cecece 2px;" border="0" width="100%">
	<tr><th colspan="2" bgcolor="#cecece">Contact Info</th></tr>
	<tr><td nowrap><b>{$smarty.const.STR_TCK_ACCOUNT|escape}</b></td><td nowrap>{dcl_metadata_display type='org_name' value=$obj->account|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_TCK_CONTACT|escape}</b></td><td nowrap>{dcl_metadata_display type='contact_name' value=$obj->contact_id|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_TCK_CONTACTPHONE|escape}</b></td><td nowrap>{dcl_metadata_display type='contact_phone' value=$obj->contact_id|escape}</td></tr>
	</table>
</td></tr>
<tr><td colspan="2">
	<table style="border: solid #cecece 2px;" border="0" width="100%">
	<tr><th bgcolor="#cecece">{$smarty.const.STR_TCK_ISSUE|escape}</th></tr>
	<tr><td>{$obj->issue|escape|nl2br}</td></tr>
	</table>
</td></tr>
{section name=tr loop=$VAL_RESOLUTIONS}
<tr><td colspan="2">
	<table style="border: solid #cecece 2px;" border="0" width="100%">
	<tr><th bgcolor="black" align="left" colspan="4"><font color="white">Logged By {$VAL_RESOLUTIONS[tr].loggedby|escape} On {$VAL_RESOLUTIONS[tr].loggedon|escape}</font></th></tr>
	<tr><td nowrap width="25%"><b>{$smarty.const.STR_TCK_STATUS|escape}</b></td><td nowrap width="25%">{$VAL_RESOLUTIONS[tr].status|escape}</td><td nowrap width="25%"><b>{$smarty.const.STR_TCK_TIME|escape}</b></td><td nowrap width="25%">{$VAL_RESOLUTIONS[tr].time|escape}</td></tr>
	<tr><td colspan="4"><b>{$smarty.const.STR_TCK_RESOLUTION|escape}:</b> {$VAL_RESOLUTIONS[tr].resolution|escape|nl2br}</td></tr>
	</table>
</td></tr>
{/section}
</table>
</body></html>
