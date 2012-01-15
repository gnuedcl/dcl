<script language="JavaScript">
function validateAndSubmitForm(form)
{
	c = form.elements["project"];
	if (c.options[c.selectedIndex].value < 1)
	{
		alert("You must select a project to view!");
		return;
	}
	form.submit();
}
</script>

<center>
<table border="0" cellspacing="0">
<tr><th class="formTitle">{TXT_TITLE}</th></tr>
<tr><td class="formContainer">
	<table border="0" cellspacing="0" width="100%">
	<form name="ViewProject" method="post" action="{VAL_FORMACTION}">
	<input type="hidden" name="menuAction" value="boProjects.viewproject">
	<input type="hidden" name="wostatus" value="0">
	<tr><td><span class="highlight">{TXT_CHOOSEPRJ}</span></td><td>{CMB_PROJECT}</td></tr>
	<tr class="formFooter">
		<td colspan="2" style="text-align: right;">
			<input type="button" value="{BTN_VIEW}" onclick="validateAndSubmitForm(this.form);">
			&nbsp;<input type="reset" value="{BTN_RESET}">
		</td>
	</tr>
	</form>
	</table>
</td></tr>
</table>
</center>
