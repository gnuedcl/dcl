<script language="JavaScript">

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

</script>
<fieldset>
<legend><span style="font-weight: bold;">{$smarty.const.STR_WO_MYWOSEARCHES}</span></legend>
<table border="0" width="100%">
<tr><td><form action="{$URL_MAIN_PHP}" method="POST" onsubmit="return validateWorkorder(this);">
	<input type="hidden" name="menuAction" value="WorkOrder.Detail">
	{$smarty.const.STR_WO_JCN} <input type="text" name="jcn" size="8">&nbsp;{$smarty.const.STR_WO_SEQ} <input type="text" name="seq" size="3">&nbsp;&nbsp;&nbsp;
	<input type="submit" value="{$smarty.const.STR_CMMN_FIND}"></form></td></tr>
<tr><td><form action="{$URL_MAIN_PHP}" method="POST" onsubmit="return validateView(this);">
	<input type="hidden" name="menuAction" value="boViews.exec">
	{$CMB_VIEWS}
	<input type="submit" value="{$smarty.const.STR_CMMN_VIEW}"></form></td></tr>
</table>
</fieldset>
