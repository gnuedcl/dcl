<!-- $Id: htmlProjectmapForm.tpl,v 1.3 2006/11/27 06:00:52 mdean Exp $ -->
<center>
<table cellpadding="2" cellspacing="0">
	<tr><th class="formTitle">{TXT_FUNCTION}</th></tr>
	<tr><td class="formContainer">
		<table cellspacing="0" cellpadding="2" style="width: 100%; border: 0px none;">
			<form name="AddToProject" method="post" action="{VAL_FORMACTION}">
			{HIDDEN_VARS}
			<tr>
				<td><b>{TXT_CHOOSEPRJ}:</b></td>
				<td>{CMB_PROJECT}</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="checkbox" id="addall" name="addall" value="1"><label for="addall">{TXT_ADDALLSEQ}</label>
				</td>
			</tr>
			<tr class="formFooter">
				<td colspan="2" align="right">
					<input type="submit" value="{BTN_OK}">
					<input type="reset" value="{BTN_RESET}">
				</td>
			</tr>
			</form>
		</table>
	</td></tr>
</table>
</center>
