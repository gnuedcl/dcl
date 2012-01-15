{dcl_validator_init}
<script language="JavaScript">
function validateAndSubmitForm(form)
{literal}
{
{/literal}
	var aValidators = new Array(
			new ValidatorString(form.elements["contact_type_name"], "{$smarty.const.STR_CMMN_NAME}")
		);
{literal}
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
{/literal}
</script>
<form class="styled" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$menuAction}">
	{if $contact_type_id}<input type="hidden" name="contact_type_id" value="{$contact_type_id}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION}</legend>
		<div class="required">
			<label for="contact_type_name">{$smarty.const.STR_CMMN_NAME}:</label>
			<input type="text" size="30" maxlength="30" id="contact_type_name" name="contact_type_name" value="{$VAL_NAME|escape}">
		</div>
		<div>
			<label for="contact_type_is_main">Main?</label>
			<input type="checkbox" id="contact_type_is_main" name="contact_type_is_main" value="Y"{if $VAL_MAIN == 'Y'} checked{/if}>
			<span>Check this box to display contacts of this type on organization details.</span>
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=ContactType.Index';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>
<script language="JavaScript">
if (document.getElementById("contact_type_name"))
	document.getElementById("contact_type_name").focus();
</script>