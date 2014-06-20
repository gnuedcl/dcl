{dcl_validator_init}
<script language="JavaScript">
function validateAndSubmitForm(form)

{

	var aValidators = new Array(
			new ValidatorString(form.elements["alias"], "Alias")
		);

	for (var i in aValidators)
	{
		if (!aValidators[i].isValid())
		{
			alert(aValidators[i].getError());
			if (typeof(aValidators[i]._Element.focus) == "function")
				aValidators[i]._Element.focus();
			return;
		}
	}

	form.submit();
}

</script>
<form class="styled" method="post" name="submitForm" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
	<input type="hidden" name="org_id" value="{$VAL_ORGID}">
	{if $VAL_ORGALIASID}<input type="hidden" name="org_alias_id" value="{$VAL_ORGALIASID}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION}</legend>
		<div class="help">{$VAL_ORGNAME}</div>
		<div class="required">
			<label for="alias">Alias:</label>
			<input type="text" id="alias" name="alias" size="50" maxlength="50" value="{$VAL_ALIAS|escape}">
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="button" onclick="location.href = '{$URL_BACK}';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>
<script language="JavaScript">
if (document.getElementById("alias"))
	document.getElementById("alias").focus();
</script>