<!-- $Id$ -->
{dcl_validator_init}
<script language="JavaScript">
{literal}
function validateAndSubmitForm(form)
{
{/literal}
	var aValidators = new Array(
			new ValidatorString(form.elements["first_name"], "First Name"),
			new ValidatorString(form.elements["last_name"], "Last Name")
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
	<fieldset>
		<legend>{$TXT_FUNCTION}</legend>
		<div class="required">
			<label for="active">{$smarty.const.STR_CMMN_ACTIVE}:</label>
			<input type="checkbox" id="active" name="active" value="Y"{if $VAL_ACTIVE == 'Y'} checked{/if}>
		</div>
		<div class="required">
			<label for="">{$smarty.const.STR_CMMN_FIRSTNAME}:</label>
			<input type="text" size="50" maxlength="50" id="first_name" name="first_name" value="{$VAL_FIRSTNAME|escape}">
		</div>
		<div>
			<label for="">Middle Name:</label>
			<input type="text" size="50" maxlength="50" id="middle_name" name="middle_name" value="{$VAL_MIDDLENAME|escape}">
		</div>
		<div class="required">
			<label for="">{$smarty.const.STR_CMMN_LASTNAME}:</label>
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
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_OK}">
			<input type="button" onclick="location.href = '{$URL_BACK}';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>
<script language="JavaScript">
if (document.forms["submitForm"].elements["first_name"])
	document.forms["submitForm"].elements["first_name"].focus();
</script>