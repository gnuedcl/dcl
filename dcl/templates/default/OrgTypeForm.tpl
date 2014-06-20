{dcl_validator_init}
<script language="JavaScript">
function validateAndSubmitForm(form)

{

	var aValidators = new Array(
			new ValidatorString(form.elements["org_type_name"], "{$smarty.const.STR_CMMN_NAME}")
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
<form class="styled" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$menuAction}">
	{if $org_type_id}<input type="hidden" name="org_type_id" value="{$org_type_id}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION}</legend>
		<div class="required">
			<label for="org_type_name">{$smarty.const.STR_CMMN_NAME}:</label>
			<input type="text" size="30" maxlength="30" id="org_type_name" name="org_type_name" value="{$VAL_NAME|escape}">
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=OrganizationType.Index';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>
<script language="JavaScript">
if (document.getElementById("org_type_name"))
	document.getElementById("org_type_name").focus();
</script>