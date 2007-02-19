<html><head>
	<!-- $Id$ -->
	<title>DCL Notification</title>
	<style type="text/css">
		a {text-decoration: none; font-weight: bold;}
		a:hover {text-decoration: underline; color: #FF6666;}
		.header { border-bottom: solid black 2px; }
		body { font-family: Tahoma, Verdana, Arial, Helvetica; font-size: 11px; }
		th { font-family: Tahoma, Verdana, Arial, Helvetica; font-size: 11px; }
		td { font-family: Tahoma, Verdana, Arial, Helvetica; font-size: 11px; }
	</style>
</head><body>
<table border="0">
<tr><th colspan="2"><font size="+2">DCL Work Order Notification</font></td></tr>
<tr><td colspan="2"><i>You are receiving this e-mail because you are (1) directly involved, or (2) have a watch on this work order, or (3) have a watch on an account, product or project associated with this work order.  This is a snapshot of the work order.  <a href="{dcl_config name=DCL_ROOT}?menuAction=boWorkorders.viewjcn&jcn={$obj->jcn}&seq={$obj->seq}">Click here to view online.</a></i></td></tr>
<tr><td class="header" colspan="2" bgcolor="black"><font color="white"><b>[{$obj->jcn}-{$obj->seq}] {$obj->summary|escape}</b></font></td></tr>
<tr><td width="50%" valign="top">
	<table style="border: solid #cecece 2px;" border="0" width="100%">
	<tr><th colspan="2" bgcolor="#cecece">Work Order</th></tr>
	<tr><td nowrap><b>{$smarty.const.STR_WO_RESPONSIBLE|escape}</b></td><td nowrap>{dcl_metadata_display type='personnel' value="`$obj->responsible`"|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_WO_TYPE|escape}</b></td><td nowrap>{dcl_metadata_display type='wotype' value="`$obj->wo_type_id`"|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_WO_STATUS|escape}</b></td><td nowrap>{dcl_metadata_display type='status' value="`$obj->status`"|escape} ({$obj->statuson|escape})</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_WO_PRIORITY|escape}</b></td><td nowrap>{dcl_metadata_display type='priority' value="`$obj->priority`"|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_WO_SEVERITY|escape}</b></td><td nowrap>{dcl_metadata_display type='severity' value="`$obj->severity`"|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_WO_DEADLINE|escape}</b></td><td nowrap>{$obj->deadlineon}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_WO_PROJECT|escape}</b></td><td nowrap>{dcl_metadata_display type='wo_project' value="`$obj->jcn`" value2="`$obj->seq`"|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_WO_LASTACTION|escape}</b></td><td nowrap>{$obj->lastactionon}</td></tr>
	</table>
</td><td width="50%" valign="top">
	<table style="border: solid #cecece 2px;" border="0" width="100%">
	<tr><th colspan="2" bgcolor="#cecece">Product Info</th></tr>
	<tr><td nowrap><b>{$smarty.const.STR_WO_PRODUCT|escape}</b></td><td nowrap>{dcl_metadata_display type='product' value="`$obj->product`"|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_CMMN_MODULE|escape}</b></td><td nowrap>{dcl_metadata_display type='module' value="`$obj->module_id`"|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_WO_REVISION|escape}</b></td><td nowrap>{$obj->revision|escape}</td></tr>
	</table><br>
	<table style="border: solid #cecece 2px;" border="0" width="100%">
	<tr><th colspan="2" bgcolor="#cecece">Contact Info</th></tr>
	<tr><td><b>{$smarty.const.STR_WO_ACCOUNT|escape}</b></td><td>{dcl_metadata_display type='wo_org' value="`$obj->jcn`" value2="`$obj->seq`"|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_WO_CONTACT|escape}</b></td><td nowrap>{dcl_metadata_display type='contact_name' value="`$obj->contact_id`"|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_WO_CONTACTPHONE|escape}</b></td><td nowrap>{dcl_metadata_display type='contact_phone' value="`$obj->contact_id`"|escape}</td></tr>
	</table>
</td></tr>
<tr><td width="50%" valign="top">
	<table style="border: solid #cecece 2px;" border="0" width="100%">
	<tr><th colspan="2" bgcolor="#cecece">Dates</th></tr>
	<tr><td nowrap><b>{$smarty.const.STR_WO_OPENBY|escape}</b></td><td nowrap>{dcl_metadata_display type='personnel' value="`$obj->createby`"|escape} ({$obj->createdon|escape})</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_WO_CLOSEBY|escape}</b></td><td nowrap>{dcl_metadata_display type='personnel' value="`$obj->closedby`"|escape} ({$obj->closedon|escape})</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_WO_ESTSTART|escape}</b></td><td nowrap>{$obj->eststarton|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_WO_START|escape}</b></td><td nowrap>{$obj->starton|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_WO_ESTEND|escape}</b></td><td nowrap>{$obj->estendon|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_WO_END|escape}</b></td><td nowrap>{$obj->endon|escape}</td></tr>
	</table>
</td><td width="50%" valign="top">
	<table style="border: solid #cecece 2px;" border="0" width="100%">
	<tr><th colspan="2" bgcolor="#cecece">Time</th></tr>
	<tr><td nowrap><b>{$smarty.const.STR_WO_ESTHOURS|escape}</b></td><td nowrap>{$obj->esthours|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_WO_ACTHOURS|escape}</b></td><td nowrap>{$obj->totalhours|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_WO_ETCHOURS|escape}</b></td><td nowrap>{$obj->etchours|escape}</td></tr>
	</table>
</td></tr>
{if $obj->notes != ""}<tr><td colspan="2">
	<table style="border: solid #cecece 2px;" border="0" width="100%">
	<tr><th bgcolor="#cecece">{$smarty.const.STR_WO_NOTES|escape}</th></tr>
	<tr><td>{$obj->notes|escape}</td></tr>
	</table>
</td></tr>{/if}
<tr><td colspan="2">
	<table style="border: solid #cecece 2px;" border="0" width="100%">
	<tr><th bgcolor="#cecece">{$smarty.const.STR_WO_DESCRIPTION|escape}</th></tr>
	<tr><td>{$obj->description|escape}</td></tr>
	</table>
</td></tr>
{section name=tc loop=$VAL_TIMECARDS}
<tr><td colspan="2">
	<table style="border: solid #cecece 2px;" border="0" width="100%">
	<tr><th bgcolor="black" align="left" colspan="4"><font color="white">{$VAL_TIMECARDS[tc].actionby|escape} ({$VAL_TIMECARDS[tc].actionon|escape}) - {$VAL_TIMECARDS[tc].summary|escape}</font></th></tr>
	<tr><td nowrap width="25%"><b>{$smarty.const.STR_TC_STATUS}</b></td><td nowrap width="25%">{$VAL_TIMECARDS[tc].status|escape}</td><td nowrap width="25%"><b>{$smarty.const.STR_TC_HOURS|escape}</b></td><td nowrap width="25%">{$VAL_TIMECARDS[tc].hours|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_TC_ACTION|escape}</b></td><td nowrap>{$VAL_TIMECARDS[tc].action|escape}</td><td nowrap><b>{$smarty.const.STR_TC_VERSION|escape}</b></td><td nowrap>{$VAL_TIMECARDS[tc].version|escape}</td></tr>
	<tr><td nowrap><b>{$smarty.const.STR_CMMN_REASSIGN|escape}</b></td><td nowrap>{$VAL_TIMECARDS[tc].reassign_from_id|escape}</td><td nowrap><b>{$smarty.const.STR_CMMN_TO|escape}</b></td><td nowrap>{$VAL_TIMECARDS[tc].reassign_to_id|escape}</td></tr>
	{if $VAL_TIMECARDS[tc].description != ""}<tr><td colspan="4"><b>{$smarty.const.STR_TC_DESCRIPTION|escape}:</b> {$VAL_TIMECARDS[tc].description|escape}</td></tr>{/if}
	</table>
</td></tr>
{/section}
</table>
</body></html>