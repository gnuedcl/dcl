<!-- $Id$ -->
<script language="JavaScript">
function validateAndSubmit(f)
{
	if (f.elements["dcl_lookup_name"].value == "")
	{
		alert("{TXT_NAME} is required.");
		return;
	}

	f.submit();
}
</script>
<center>
<table border="0" bgcolor="{COLOR_DARK}" cellpadding="2" cellspacing="0">
<tr><th><font color="{COLOR_LIGHT}">{TXT_TITLE}</font></th></tr>
<tr><td>
	<table border="0" bgcolor="{COLOR_LIGHT}" cellpadding="2" cellspacing="0">
	<form method="post" action="{VAL_FORMACTION}">
	{HIDDEN_VARS}
	<tr><td><span class="highlight">{TXT_ACTIVE}:</span></td>
		<td>{CMB_ACTIVE}</td>
	</tr>
	<tr><td><span class="highlight">{TXT_NAME}:</span></td>
		<td><input type="text" name="dcl_lookup_name" size="30" maxlength="50" value="{VAL_NAME}"></td>
	</tr>
	<tr><td align="center" colspan="2">
		<input type="button" value="{BTN_SAVE}" onclick="validateAndSubmit(this.form);">
		<input type="reset" value="{BTN_RESET}">
		</td>
	</tr>
	<tr><td colspan="2"><span class="highlight">{TXT_HIGHLIGHTEDNOTE}</span></td></tr>
	</form>
	</table>
</td></tr>
</table>
</center>
