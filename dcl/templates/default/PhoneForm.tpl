{dcl_validator_init}
<script language="JavaScript">
function validateAndSubmitForm(form)
{literal}
{
{/literal}
	var aValidators = new Array(
			new ValidatorSelection(form.elements["phone_type_id"], "Type"),
			new ValidatorString(form.elements["phone_number"], "Phone Number")
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
<form class="styled" method="post" name="submitForm" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
	{if $VAL_ORGID}<input type="hidden" name="org_id" value="{$VAL_ORGID}">{/if}
	{if $VAL_ORGPHONEID}<input type="hidden" name="org_phone_id" value="{$VAL_ORGPHONEID}">{/if}
	{if $VAL_CONTACTID}<input type="hidden" name="contact_id" value="{$VAL_CONTACTID}">{/if}
	{if $VAL_CONTACTPHONEID}<input type="hidden" name="contact_phone_id" value="{$VAL_CONTACTPHONEID}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION}</legend>
		{if $VAL_ORGID}<div class="help">{$VAL_ORGNAME|escape}</div>{/if}
		{if $VAL_CONTACTID}<div class="help">{$VAL_FIRSTNAME|escape} {$VAL_LASTNAME|escape}</div>{/if}
		<div>
			<label for="preferred">Primary:</label>
			<input type="checkbox" id="preferred" name="preferred" value="Y"{if $VAL_PREFERRED == "Y"} checked="true" onclick="return false;"{/if}>
			{if $VAL_PREFERRED == "Y"}<span>This is the preferred phone number.  If you do not want this to be the preferred phone number, select another phone number as the preferred number.</span>{/if}
		</div>
		<div class="required">
			<label for="phone_type_id">Type:</label>
			{$CMB_PHONETYPE}
		</div>
		<div class="required">
			<label for="phone_number">Phone Number:</label>
			<input type="text" name="phone_number" size="30" maxlength="30" value="{$VAL_PHONENUMBER|trim}">
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
if (document.forms["submitForm"].elements["phone_number"])
	document.forms["submitForm"].elements["phone_number"].focus();
</script>