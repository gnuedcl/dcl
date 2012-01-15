{dcl_calendar_init}
{dcl_validator_init}
<script language="JavaScript">
function validateAndSubmitForm(form)
{literal}
{
{/literal}
	var aValidators = new Array(
			new ValidatorSelection(form.elements["product_id"], "Product"),
			new ValidatorString(form.elements["license_id"], "License #"),
			new ValidatorDate(form.elements["registered_on"], "Registration Date"),
			new ValidatorDate(form.elements["expires_on"], "Expiration Date")
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
	{if $VAL_CONTACTID}<input type="hidden" name="contact_id" value="{$VAL_CONTACTID}">{/if}
	{if $VAL_CONTACTLICENSEID}<input type="hidden" name="contact_license_id" value="{$VAL_CONTACTLICENSEID}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION}</legend>
		{if $VAL_CONTACTID}<div class="help">{$VAL_FIRSTNAME|escape} {$VAL_LASTNAME|escape}</div>{/if}
		<div class="required">
			<label for="product_id">Product:</label>
			{dcl_select_product default="$VAL_PRODUCTID" active="$ACTIVE_ONLY" name="product_id"}
		</div>
		<div>
			<label for="product_version">Version:</label>
			<input type="text" id="product_version" name="product_version" size="20" maxlength="20" value="{$VAL_VERSION|escape}">
		</div>
		<div class="required">
			<label for="version">License #:</label>
			<input type="text" id="license_id" name="license_id" size="50" maxlength="50" value="{$VAL_LICENSEID|escape}">
		</div>
		<div class="required">
			<label for="registered_on">Registration Date:</label>
			{dcl_calendar name="registered_on" value="$VAL_REGISTEREDON"}
		</div>
		<div class="required">
			<label for="expires_on">Expiration Date:</label>
			{dcl_calendar name="expires_on" value="$VAL_EXPIRESON"}
		</div>
		<div>
			<label for="license_notes">Notes:</label>
			<textarea name="license_notes" id="license_notes" rows="4" cols="70" wrap valign="top">{$VAL_NOTES|escape}</textarea>
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