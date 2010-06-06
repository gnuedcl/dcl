<!-- $Id$ -->
{dcl_selector_init}
{dcl_validator_init}
<script language="JavaScript">
function validateAndSubmitForm(form)
{literal}
{
{/literal}
	var aValidators = new Array(
			new ValidatorString(form.elements["first_name"], "{$VAL_FIRSTNAME|escape}"),
			new ValidatorString(form.elements["last_name"], "{$VAL_LASTNAME|escape}"),
			new ValidatorInteger(form.elements["org_id"], "Organization", true)
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
	<input type="hidden" name="menuAction" value="htmlContactForm.submitAdd">
	{if $org_id}<input type="hidden" name="org_id" value="{$org_id}">{/if}
	{if $hideMenu}<input type="hidden" name="hideMenu" value="{$hideMenu}">{/if}
	{if $return_to}<input type="hidden" name="return_to" value="{$return_to|escape}">{/if}
	{if $fromBrowse}<input type="hidden" name="fromBrowse" value="{$fromBrowse|escape}">{/if}
	<fieldset>
		<legend>Add New Contact</legend>
		<div class="required">
			<label for="first_name">First Name:</label>
			<input type="text" size="50" maxlength="50" id="first_name" name="first_name" value="{$VAL_FIRSTNAME|escape}">
		</div>
		<div>
			<label for="middle_name">Middle Name:</label>
			<input type="text" size="50" maxlength="50" id="middle_name" name="middle_name" value="{$VAL_MIDDLENAME|escape}">
		</div>
		<div class="required">
			<label for="last_name">Last Name:</label>
			<input type="text" size="50" maxlength="50" id="last_name" name="last_name" value="{$VAL_LASTNAME|escape}">
		</div>
		<div>
			<fieldset>
				<legend>Type</legend>
				<div>
					{foreach item=typeItem key=typeItemID from=$contactTypes}
						<span style="white-space: nowrap;"><label for="contact_type_id_{$typeItemID}"><input type="checkbox" name="contact_type_id[]" value="{$typeItemID}" id="contact_type_id_{$typeItemID}"{if $typeItem.selected == "true"} checked{/if}> {$typeItem.desc}</label></span>
					{/foreach}
				</div>
			</fieldset>
		</div>
	</fieldset>
	<fieldset>
		<legend>Organization</legend>
		<div>
			<label for="org_id">Organization:</label>
			{dcl_selector_org name=org_id window_name=_dcl_selector_org_}
		</div>
	</fieldset>
	<fieldset>
		<legend>Primary Address</legend>
		<div>
			<label for="addr_type_id">Type:</label>
			{dcl_select_addresstype}
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
			{dcl_select_phonetype}
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
			{dcl_select_emailtype}
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
			{dcl_select_urltype}
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
