<!-- $Id$ -->
{dcl_selector_init}
{dcl_validator_init}
<script language="JavaScript">
{literal}
if (!document.body.onload)
	document.body.onload = function() { document.forms['userInputForm'].elements['short'].focus(); }

function validateAndSubmitForm(form)
{
{/literal}
	var aValidators = new Array(
			new ValidatorString(form.elements["short"], "{$smarty.const.STR_USR_LOGIN}"),
			new ValidatorString(form.elements["pwd"], "{$smarty.const.STR_USR_PASSWORD}"),
			new ValidatorString(form.elements["pwd2"], "{$smarty.const.STR_USR_CONFIRMPWD}"),
			new ValidatorSelection(form.elements["reportto"], "{$smarty.const.STR_USR_REPORTTO}"),
			new ValidatorSelection(form.elements["department"], "{$smarty.const.STR_USR_DEPARTMENT}"),
			new ValidatorSelector(form.elements["contact_id"], "{$smarty.const.STR_CMMN_CONTACT}")
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

	if (form.elements["pwd"] && form.elements["pwd2"])
	{
		if (form.elements["pwd"].value != form.elements["pwd2"].value)
		{
			alert("Your passwords do not match!  Please enter them again.");
			form.elements["pwd"].value = "";
			form.elements["pwd2"].value = "";
			form.elements["pwd"].focus();
			return;
		}
	}
	
	form.submit();
}
{/literal}
</script>
<form class="styled" name="userInputForm" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{if $IS_EDIT}Personnel.Update{else}Personnel.Insert{/if}">
	{if $IS_EDIT}<input type="hidden" name="id" value="{$VAL_PERSONNELID}">{/if}
	<fieldset>
		<legend>{if $IS_EDIT}{$smarty.const.STR_USR_EDIT}{else}{$smarty.const.STR_USR_ADD}{/if}</legend>
		<div class="required">
			<label for="short">{$smarty.const.STR_USR_LOGIN}:</label>
			<input type="text" maxlength="25" size="25" id="short" name="short" value="{$VAL_SHORT}">
		</div>
		<div class="required">
			<label for="active">{$smarty.const.STR_USR_ACTIVE}:</label>
			<input type="checkbox" name="active" id="active"{if $VAL_ACTIVE == "Y"} checked{/if}>
		</div>
		<div class="required">
			<label for="reportto">{$smarty.const.STR_USR_REPORTTO}:</label>
			{dcl_select_personnel name=reportto default=$VAL_REPORTTO}
		</div>
		<div class="required">
			<label for="department">{$smarty.const.STR_USR_DEPARTMENT}:</label>
			{dcl_select_department name=department default=$VAL_DEPARTMENT}
		</div>
		<div class="required">
			<label for="contact_id">{$smarty.const.STR_CMMN_CONTACT}:</label>
			{dcl_selector_contact name=contact_id value=$VAL_CONTACTID decoded=$VAL_CONTACTNAME}
		</div>
{if $IS_EDIT == false}
		<div class="required">
			<label for="pwd">{$smarty.const.STR_USR_PASSWORD}:</label>
			<input type="password" id="pwd" name="pwd" size="20">
		</div>
		<div class="required">
			<label for="pwd2">{$smarty.const.STR_USR_CONFIRMPWD}:</label>
			<input type="password" id="pwd2" name="pwd2" size="20">
		</div>
{/if}
		<div>
			<fieldset>
				<legend>Roles</legend>
				<div>
					{foreach item=roleItem key=roleName from=$Roles name=role}
						<label for="role{$roleItem.role_id}"><input type="checkbox" name="roles[]" value="{$roleItem.role_id}" id="role{$roleItem.role_id}"{if $roleItem.selected == "true"} checked{/if}> {$roleName}</label>
					{/foreach}
				</div>
			</fieldset>
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="button" onclick="location.href='{$URL_MAIN_PHP}?menuAction=Personnel.Index&filterActive=Y';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>