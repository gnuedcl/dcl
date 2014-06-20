{dcl_validator_init}
<script language="JavaScript">
function validateAndSubmitForm(form)

{

	var aValidators = new Array(
			new ValidatorSelection(form.elements["email_type_id"], "Type"),
			new ValidatorString(form.elements["email_addr"], "E-Mail")
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
	{if $VAL_ORGID}<input type="hidden" name="org_id" value="{$VAL_ORGID}">{/if}
	{if $VAL_ORGEMAILID}<input type="hidden" name="org_email_id" value="{$VAL_ORGEMAILID}">{/if}
	{if $VAL_CONTACTID}<input type="hidden" name="contact_id" value="{$VAL_CONTACTID}">{/if}
	{if $VAL_CONTACTEMAILID}<input type="hidden" name="contact_email_id" value="{$VAL_CONTACTEMAILID}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION}</legend>
		{if $VAL_ORGID}<div class="help">{$VAL_ORGNAME|escape}</div>{/if}
		{if $VAL_CONTACTID}<div class="help">{$VAL_FIRSTNAME|escape} {$VAL_LASTNAME|escape}</div>{/if}
		<div>
			<label for="preferred">Primary:</label>
			<input type="checkbox" id="preferred" name="preferred" value="Y"{if $VAL_PREFERRED == "Y"} checked="true" onclick="return false;"{/if}>
			{if $VAL_PREFERRED == "Y"}<span>This is the preferred email address.  If you do not want this to be the preferred email address, select another email address as the preferred email.</span>{/if}
		</div>
		<div class="required">
			<label for="email_type_id">Type:</label>
			{$CMB_EMAILTYPE}
		</div>
		<div class="required">
			<label for="email_addr">E-Mail:</label>
			<input type="text" name="email_addr" size="50" maxlength="100" value="{$VAL_EMAILADDR|escape}">
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
if (document.forms["submitForm"].elements["email_addr"])
	document.forms["submitForm"].elements["email_addr"].focus();
</script>