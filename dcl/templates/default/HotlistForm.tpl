{dcl_validator_init}
{dcl_selector_init}
<script language="JavaScript">
function validateAndSubmitForm(form)

{

	var aValidators = new Array(
			new ValidatorString(form.elements["hotlist_tag"], "{$smarty.const.STR_CMMN_NAME}"),
			new ValidatorString(form.elements["hotlist_desc"], "{$smarty.const.STR_WO_DESCRIPTION}")
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
<form class="styled" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$menuAction}">
	{if $VAL_ID}<input type="hidden" name="hotlist_id" value="{$VAL_ID}">{/if}
	<fieldset>
		<legend>{$VAL_TITLE}</legend>
		<div class="required">
			<label for="active">{$smarty.const.STR_CMMN_ACTIVE}:</label>
			<input type="checkbox" id="active" name="active" value="Y"{if $VAL_ACTIVE == 'Y'} checked{/if}>
		</div>
		<div class="required">
			<label for="hotlist_tag">{$smarty.const.STR_CMMN_NAME}:</label>
			<input type="text" size="20" maxlength="20" id="hotlist_tag" name="hotlist_tag" value="{$VAL_NAME|escape}">
		</div>
		<div class="required">
			<label for="hotlist_desc">{$smarty.const.STR_WO_DESCRIPTION}:</label>
			<textarea id="hotlist_desc" name="hotlist_desc" rows="4" cols="70" wrap valign="top">{$VAL_DESCRIPTION|escape}</textarea>
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=htmlHotlistBrowse.show';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>