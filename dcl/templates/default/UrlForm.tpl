{dcl_validator_init}
<script language="JavaScript">
function validateAndSubmitForm(form)
{literal}
{
{/literal}
	var aValidators = new Array(
			new ValidatorSelection(form.elements["url_type_id"], "Type"),
			new ValidatorString(form.elements["url_addr"], "URL Address")
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
	{if $VAL_ORGURLID}<input type="hidden" name="org_url_id" value="{$VAL_ORGURLID}">{/if}
	{if $VAL_CONTACTID}<input type="hidden" name="contact_id" value="{$VAL_CONTACTID}">{/if}
	{if $VAL_CONTACTURLID}<input type="hidden" name="contact_url_id" value="{$VAL_CONTACTURLID}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION}</legend>
		{if $VAL_ORGID}<div class="help">{$VAL_ORGNAME|escape}</div>{/if}
		{if $VAL_CONTACTID}<div class="help">{$VAL_FIRSTNAME|escape} {$VAL_LASTNAME|escape}</div>{/if}
		<div>
			<label for="preferred">Primary:</label>
			<input type="checkbox" id="preferred" name="preferred" value="Y"{if $VAL_PREFERRED == "Y"} checked="true" onclick="return false;"{/if}>
			{if $VAL_PREFERRED == "Y"}<span>This is the preferred URL.  If you do not want this to be the preferred URL, select another URL as the preferred URL.</span>{/if}
		</div>
		<div class="required">
			<label for="url_type_id">Type:</label>
			{$CMB_URLTYPE}
		</div>
		<div class="required">
			<label for="email_addr">URL Address:</label>
			<input type="text" name="url_addr" size="50" maxlength="150" value="{$VAL_URLADDR|escape|trim}">
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
if (document.forms["submitForm"].elements["url_addr"])
	document.forms["submitForm"].elements["url_addr"].focus();
</script>