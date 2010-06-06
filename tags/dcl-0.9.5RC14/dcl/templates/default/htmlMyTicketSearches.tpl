<!-- $Id$ -->
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

function validateView(f)
{
	if (f.elements['viewid'].selectedIndex > 0)
		return true;
	
	alert('You must select a valid view first.');
	return false;
}
{/literal}
</script>
<fieldset>
<legend><span style="font-weight: bold;">{$smarty.const.STR_TCK_SEARCHTITLE}</span></legend>
<table border="0" width="100%">
<tr><td><form action="{$URL_MAIN_PHP}" method="POST" onsubmit="return validateTicket(this);">
	<input type="hidden" name="menuAction" value="boTickets.view">
	{$smarty.const.STR_TCK_TICKET}# <input type="text" name="ticketid" size="8">
	<input type="submit" value="{$smarty.const.STR_CMMN_FIND}"></form></td></tr>
<tr><td><form action="{$URL_MAIN_PHP}" method="POST" onsubmit="return validateView(this);">
	<input type="hidden" name="menuAction" value="boViews.exec">
	{$CMB_VIEWS}
	<input type="submit" value="{$smarty.const.STR_CMMN_VIEW}"></form></td></tr>
</table>
</fieldset>
