<script language="JavaScript">
{literal}
function validateTicket(f)
{
	var reg = /\d+/;
	var id = f.elements['ticketid'].value;
	if (reg.test(id) && id > 0)
		return true;

	alert('You must enter a numeric value greater than 0 for the ticket ID.');
	f.elements['ticketid'].focus();

	return false;
}

function validateWorkorder(f)
{
	var reg = /\d+/;
	var woid = f.elements['jcn'].value;
	var seq = f.elements['seq'].value;
	if (reg.test(woid) && woid > 0)
	{
		if (seq == '')
			return true;
			
		if (reg.test(seq) && seq > 0)
			return true;

		alert('You must enter a numeric value greater than 0 for the work order sequence or leave it blank.');
		f.elements['seq'].focus();

		return false;
	}

	alert('You must enter a numeric value greater than 0 for the work order ID.');
	f.elements['jcn'].focus();

	return false;
}

function validateView(f)
{
	if (f.elements['viewid'].selectedIndex > 0)
		return true;
	
	alert('You must select a valid view first.');
	return false;
}
{/literal}
</script>
<h3>{$VAL_LOGGEDINAS}</h3>
<table border="0" width="100%">
	<tr><td width="50%" valign="top" style="border: solid #cecece 2px;">
		<table border="0" width="100%">
		<tr><th class="sectionHeader">{$smarty.const.STR_TCK_MYTICKETS}</th></tr>
{section loop=$VAL_TICKETS name=ticket}
{cycle name=set1 assign=rowClass values="odd,even"}
		<tr class="{$rowClass}"><td><a href="{$URL_MAIN_PHP}?menuAction=boTickets.view&ticketid={$VAL_TICKETS[ticket].ticketid}">({$VAL_TICKETS[ticket].ticketid}) {$VAL_TICKETS[ticket].summary|escape}</a></td></tr>
		{if $smarty.section.ticket.last}
			{cycle name=set1 assign=rowClass values="odd,even"}
		<tr class="{$rowClass}"><td style="text-align: right;"><a href="{$URL_MAIN_PHP}?menuAction=htmlTickets.show&filterReportto={$VAL_ID}">{$smarty.const.STR_CMMN_VIEW}</a></td></tr>
		{/if}
{sectionelse}
		<tr><td>{$smarty.const.STR_TCK_NOOPENTICKETS}</td></tr>
{/section}
		</table>
		<table border="0" width="100%">
		<tr><th class="sectionHeader">{$smarty.const.STR_TCK_MYSUBMISSIONS}</th></tr>
{section loop=$VAL_TICKETSUBMISSIONS name=ticket}
{cycle name=set2 assign=rowClass values="odd,even"}
		<tr class="{$rowClass}"><td><a href="{$URL_MAIN_PHP}?menuAction=boTickets.view&ticketid={$VAL_TICKETSUBMISSIONS[ticket].ticketid}">({$VAL_TICKETSUBMISSIONS[ticket].ticketid}) {$VAL_TICKETSUBMISSIONS[ticket].summary|escape}</a></td></tr>
		{if $smarty.section.ticket.last}
		{cycle name=set2 assign=rowClass values="odd,even"}
		<tr class="{$rowClass}"><td style="text-align: right;"><a href="{$URL_MAIN_PHP}?menuAction=htmlTickets.showSubmissions">{$smarty.const.STR_CMMN_VIEW}</a></td></tr>
		{/if}
{sectionelse}
		<tr><td>{$smarty.const.STR_TCK_NOSUBMISSIONS}</td></tr>
{/section}
		</table>
		<table border="0" width="100%">
		<tr><th class="sectionHeader">{$smarty.const.STR_TCK_SEARCHTITLE}</th></tr>
		<tr><td><form action="{$URL_MAIN_PHP}" method="POST" onsubmit="return validateTicket(this);">
			<input type="hidden" name="menuAction" value="boTickets.view">
			{$smarty.const.STR_TCK_TICKET}# <input type="text" name="ticketid" size="8">
			<input type="submit" value="{$smarty.const.STR_CMMN_FIND}"></form>
		</td></tr>
		<tr><td><form action="{$URL_MAIN_PHP}" method="POST" onsubmit="return validateView(this);">
			<input type="hidden" name="menuAction" value="boViews.exec">
			{dcl_select_views table=tickets}
			<input type="submit" value="{$smarty.const.STR_CMMN_VIEW}"></form>
		</td></tr>
		</table>
	</td>
	<td width="50%" valign="top" style="border: solid #cecece 2px;">
		<table border="0" width="100%">
		<tr><th class="sectionHeader">{$smarty.const.STR_WO_MYWO}</th></tr>
{section loop=$VAL_WORKORDERS name=wo}
{cycle name=set3 assign=rowClass values="odd,even"}
		<tr class="{$rowClass}"><td>{if $VAL_WORKORDERS[wo].color != ""}<span style="color:{$VAL_WORKORDERS[wo].color};font-weight: bold;">!&nbsp;<span>{/if}<a href="{$URL_MAIN_PHP}?menuAction=WorkOrder.Detail&jcn={$VAL_WORKORDERS[wo].jcn}&seq={$VAL_WORKORDERS[wo].seq}">({$VAL_WORKORDERS[wo].jcn}-{$VAL_WORKORDERS[wo].seq}) {$VAL_WORKORDERS[wo].summary|escape}</a></td></tr>
		{if $smarty.section.wo.last}
		{cycle name=set3 assign=rowClass values="odd,even"}
		<tr class="{$rowClass}"><td style="text-align: right;"><a href="{$URL_MAIN_PHP}?menuAction=WorkOrder.SearchMy">{$smarty.const.STR_CMMN_VIEW}</a></td></tr>
		{/if}
{sectionelse}
		<tr><td>{$smarty.const.STR_WO_NOOPEN}</td></tr>
{/section}
		</table>
		<table border="0" width="100%">
		<tr><th class="sectionHeader">{$smarty.const.STR_WO_MYSUBMISSIONS}</th></tr>
{section loop=$VAL_WOSUBMISSIONS name=wo}
{cycle name=set4 assign=rowClass values="odd,even"}
		<tr class="{$rowClass}"><td>{if $VAL_WORKORDERS[wo].color != ""}<span style="color:{$VAL_WORKORDERS[wo].color};font-weight: bold;">!&nbsp;<span>{/if}<a href="{$URL_MAIN_PHP}?menuAction=WorkOrder.Detail&jcn={$VAL_WOSUBMISSIONS[wo].jcn}&seq={$VAL_WOSUBMISSIONS[wo].seq}">({$VAL_WOSUBMISSIONS[wo].jcn}-{$VAL_WOSUBMISSIONS[wo].seq}) {$VAL_WOSUBMISSIONS[wo].summary|escape}</a></td></tr>
		{if $smarty.section.wo.last}
		{cycle name=set4 assign=rowClass values="odd,even"}
		<tr class="{$rowClass}"><td style="text-align: right;"><a href="{$URL_MAIN_PHP}?menuAction=WorkOrder.SearchMyCreated">{$smarty.const.STR_CMMN_VIEW}</a></td></tr>
		{/if}
{sectionelse}
		<tr><td>{$smarty.const.STR_WO_NOSUBMISSIONS}</td></tr>
{/section}
		</table>
		<table border="0" width="100%">
		<tr><th class="sectionHeader">{$smarty.const.STR_WO_SEARCHTITLE}</th></tr>
		<tr><td>
			<form action="{$URL_MAIN_PHP}" method="POST" onsubmit="return validateWorkorder(this);">
			<input type="hidden" name="menuAction" value="WorkOrder.Detail">
			{$smarty.const.STR_WO_JCN} <input type="text" name="jcn" size="8">&nbsp;{$smarty.const.STR_WO_SEQ} <input type="text" name="seq" size="3">&nbsp;&nbsp;&nbsp;
			<input type="submit" value="{$smarty.const.STR_CMMN_FIND}"></form>
		</td></tr>
		<tr><td><form action="{$URL_MAIN_PHP}" method="POST" onsubmit="return validateView(this);">
			<input type="hidden" name="menuAction" value="boViews.exec">
			{dcl_select_views table=workorders}
			<input type="submit" value="{$smarty.const.STR_CMMN_VIEW}"></form>
		</td></tr>
		</table>
	</td></tr>
	<tr><td colspan="2" valign="top" style="border: solid #cecece 2px;">
		<table border="0" width="100%">
		<tr><th class="sectionHeader">{$smarty.const.STR_WTCH_MYWTCH}</th></tr>
{section loop=$VAL_WATCHES name=watch}
{cycle name=set5 assign=rowClass values="odd,even"}
		<tr class="{$rowClass}"><td><a href="{$VAL_WATCHES[watch].link}">({$VAL_WATCHES[watch].group|escape}) {$VAL_WATCHES[watch].summary|escape}</a></td></tr>
		{if $smarty.section.watch.last}
			{cycle name=set5 assign=rowClass values="odd,even"}
		<tr class="{$rowClass}"><td style="text-align: right;"><a href="{$URL_MAIN_PHP}?menuAction=boWatches.showAll">{$smarty.const.STR_CMMN_VIEW}</a></td></tr>
		{/if}
{sectionelse}
		<tr><td>{$smarty.const.STR_WTCH_YOUHAVENONE}</td></tr>
{/section}
		</table>
	</td></tr>
	<tr><td colspan="2" valign="top" style="border: solid #cecece 2px;">
		<table border="0" width="100%">
		<tr><th class="sectionHeader">{$smarty.const.STR_PROD_MYPROD}</th></tr>
{section loop=$VAL_PRODUCTS name=product}
{cycle name=set6 assign=rowClass values="odd,even"}
		<tr class="{$rowClass}"><td><a href="{$URL_MAIN_PHP}?menuAction=Product.Detail&id={$VAL_PRODUCTS[product].id}">{$VAL_PRODUCTS[product].name|escape}</a></td></tr>
		{if $smarty.section.product.last}
			{cycle name=set6 assign=rowClass values="odd,even"}
		<tr class="{$rowClass}"><td style="text-align: right;"><a href="{$URL_MAIN_PHP}?menuAction=Product.Index&filterLead={$VAL_ID}">{$smarty.const.STR_CMMN_VIEW}</a></td></tr>
		{/if}
{sectionelse}
		<tr><td>{$smarty.const.STR_PROD_NOTLEAD}</td></tr>
{/section}
		</table>
	</td></tr>
	<tr><td colspan="2" valign="top" style="border: solid #cecece 2px;">
		<table border="0" width="100%">
		<tr><th class="sectionHeader">{$smarty.const.STR_PRJ_MYPRJ}</th></tr>
{section loop=$VAL_PROJECTS name=project}
{cycle name=set7 assign=rowClass values="odd,even"}
		<tr class="{$rowClass}"><td><a href="{$URL_MAIN_PHP}?menuAction=boProjects.viewproject&wostatus=0&project={$VAL_PROJECTS[project].projectid}">({$VAL_PROJECTS[project].projectid}) {$VAL_PROJECTS[project].name|escape}</a></td></tr>
		{if $smarty.section.project.last}
			{cycle name=set7 assign=rowClass values="odd,even"}
		<tr class="{$rowClass}"><td style="text-align: right;"><a href="{$URL_MAIN_PHP}?menuAction=htmlProjects.show&filterReportto={$VAL_ID}">{$smarty.const.STR_CMMN_VIEW}</a></td></tr>
		{/if}
{sectionelse}
		<tr><td>{$smarty.const.STR_PRJ_NOTLEAD}</td></tr>
{/section}
		</table>
	</td></tr>
</table>
