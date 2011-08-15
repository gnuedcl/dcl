<center>
<table border="0" cellspacing="0" cellpadding="2">
<tr><th class="formTitle">{$TXT_TITLE}</th></tr>
<tr><td class="formContainer">
	<table border="0" width="100%" cellspacing="0" cellpadding="2">
	<form name="AddOption" method="post" action="{$VAL_FORMACTION}">
	<input type="hidden" name="menuAction" value="boViews.dbadd">
	<input type="hidden" name="whoid" value="{$VAL_DCLID}">
	<input type="hidden" name="tablename" value="{$VAL_TABLENAME}">
	{$VAL_VIEWURL}
	<tr><td><span class="highlight">{$TXT_PUBLIC}:</span></td>
		<td>{$CMB_ISPUBLIC}</td>
	</tr>
	<tr><td><span class="highlight">{$TXT_NAME}:</span></td>
		<td><input type="text" size="50" maxlength="100" name="name"></td>
	</tr>
	<tr class="formFooter">
		<td colspan="2">
			<table style="border: 0px none; width: 100%" cellspacing="0" cellpadding="0">
				<tr>
					<td><span class="highlight">{$TXT_HIGHLIGHTEDNOTE}</span></td>
					<td style="text-align: right;">
						<input type="submit" value="{$BTN_SAVE}">
						&nbsp;
						<input type="button" onclick="history.back();" value="{$BTN_CANCEL}">
					</td>
				</tr>
			</table>
		</td>
	</tr>
	</form>
	</table>
</td></tr>
</table>
</center>
