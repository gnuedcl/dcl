<center>
<table border="0" cellpadding="2" cellspacing="0">
<tr><th class="formTitle">{$TXT_TITLE}</th></tr>
<tr><td class="formContainer">
	<table border="0" cellpadding="2" cellspacing="0" width="100%">
	<form method="post" action="{$VAL_FORMACTION}">
	<input type="hidden" name="menuAction" value="WorkOrder.DestroyAttachment">
	<input type="hidden" name="filename" value="{$VAL_FILENAME}">
	<input type="hidden" name="jcn" value="{$VAL_JCN}">
	<input type="hidden" name="seq" value="{$VAL_SEQ}">
	<tr><td>{$TXT_DELATTCONFIRM}<br></td></tr>
	<tr class="formFooter"><td style="text-align: right;">
			<input type="submit" value="{$BTN_YES}">
			&nbsp;
			<input type="reset" value="{$BTN_NO}" onclick="history.back();">
		</td>
	</tr>
	</form>
	</table>
</td></tr>
</table>
</center>
