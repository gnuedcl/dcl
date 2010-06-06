<!-- $Id$ -->
{dcl_validator_init}
<script language="JavaScript">
{literal}
function validateAndSubmitForm(form)
{
{/literal}
	var aValidators = new Array(
			new ValidatorString(form.elements["name"], "{$smarty.const.STR_FAQ_NAME}"),
			new ValidatorString(form.elements["description"], "{$smarty.const.STR_FAQ_DESCRIPTION}")
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
<form class="styled" name="FAQFORM" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
	{if $VAL_FAQID}<input type="hidden" name="faqid" value="{$VAL_FAQID}">{/if}
	<fieldset>
		<legend>{if $IS_EDIT}{$smarty.const.STR_FAQ_EDIT}{else}{$smarty.const.STR_FAQ_ADDNEW}{/if}</legend>
		<div>
			<label for="active">{$smarty.const.STR_CMMN_ACTIVE}:</label>
			<input type="checkbox" id="active" name="active" value="Y"{if $VAL_ACTIVE == "Y"} checked{/if}>
		</div>
		<div class="required">
			<label for="name">{$smarty.const.STR_FAQ_NAME}:</label>
			<input type="text" size="70" maxlength="100" id="name" name="name" value="{$VAL_NAME|escape}">
		</div>
		<div class="required">
			<label for="description">{$smarty.const.STR_FAQ_DESCRIPTION}:</label>
			<textarea id="description" name="description" rows="6" cols="70" wrap valign="top">{$VAL_DESCRIPTION|escape}</textarea>
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="button" onclick="history.back();" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>