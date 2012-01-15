{dcl_validator_init}
<script language="JavaScript">
{literal}
function validateAndSubmitForm(form)
{
{/literal}
	var aValidators = new Array(
			new ValidatorString(form.elements["name"], "{$TXT_NAME}")
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
<form class="styled" name="submitForm" method="POST" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
	{if $org_id}<input type="hidden" name="org_id" value="{$org_id}">{/if}
	{if $hideMenu}<input type="hidden" name="hideMenu" value="{$hideMenu}">{/if}
	{if $return_to}<input type="hidden" name="return_to" value="{$return_to|escape}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION}</legend>
		<div class="required">
			<label for="name">{$smarty.const.STR_CMMN_NAME}:</label>
			<input type="text" size="50" maxlength="50" name="name" value="{$VAL_NAME|escape}">
		</div>
		<div>
			<label for="alias">Alias:</label>
			<input type="text" name="alias" id="alias" size="50" maxlength="50" value="{$VAL_ALIAS|escape}">
		</div>
		<div>
			<fieldset>
				<legend>Type</legend>
				<div>
					{foreach item=typeItem key=typeItemID from=$orgTypes}
						<span style="white-space: nowrap;"><label for="org_type_id_{$typeItemID}"><input type="checkbox" name="org_type_id[]" value="{$typeItemID}" id="org_type_id_{$typeItemID}"{if $typeItem.selected == "true"} checked{/if}> {$typeItem.desc}</label></span>
					{/foreach}
				</div>
			</fieldset>
		</div>
	</fieldset>
	<fieldset>
		<legend>Primary Address</legend>
		<div>
			<label for="addr_type_id">Type:</label>
			{$CMB_ADDRTYPE}
		</div>
		<div>
			<label for="add1">Address:</label>
			<input type="text" name="add1" id="add1" size="30" maxlength="50" value="{$VAL_ADD1|escape}">
		</div>
		<div>
			<label for="add2">Address 2:</label>
			<input type="text" name="add2" id="add2" size="30" maxlength="50" value="{$VAL_ADD2|escape}">
		</div>
		<div>
			<label for="city">City:</label>
			<input type="text" name="city" id="city" size="30" maxlength="50" value="{$VAL_CITY|escape}">
		</div>
		<div>
			<label for="state">State:</label>
			<input type="text" name="state" id="state" size="30" maxlength="30" value="{$VAL_STATE|escape}">
		</div>
		<div>
			<label for="zip">Zip:</label>
			<input type="text" name="zip" id="zip" size="20" maxlength="20" value="{$VAL_ZIP|escape}">
		</div>
		<div>
			<label for="country">Country:</label>
			<input type="text" name="country" id="country" size="30" maxlength="40" value="{$VAL_COUNTRY|escape}">
		</div>
	</fieldset>
	<fieldset>
		<legend>Primary Phone</legend>
		<div>
			<label for="phone_type_id">Type:</label>
			{$CMB_PHONETYPE}
		</div>
		<div>
			<label for="phone_number">Number:</label>
			<input type="text" name="phone_number" id="phone_number" size="30" maxlength="30" value="{$VAL_PHONE|escape}">
		</div>
	</fieldset>
	<fieldset>
		<legend>Primary Email</legend>
		<div>
			<label for="email_type_id">Type:</label>
			{$CMB_EMAILTYPE}
		</div>
		<div>
			<label for="email_addr">Address:</label>
			<input type="text" name="email_addr" id="email_addr" size="30" maxlength="100" value="{$VAL_EMAIL|escape}">
		</div>
	</fieldset>
	<fieldset>
		<legend>Primary URL</legend>
		<div>
			<label for="url_type_id">Type:</label>
			{$CMB_URLTYPE}
		</div>
		<div>
			<label for="url_addr">URL:</label>
			<input type="text" name="url_addr" id="url_addr" size="30" maxlength="150" value="{$VAL_URL|escape}">
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_OK}">
			<input type="button" onclick="location.href = '{$URL_BACK}';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>
<script language="JavaScript">
if (document.forms["submitForm"].elements["name"])
	document.forms["submitForm"].elements["name"].focus();
</script>