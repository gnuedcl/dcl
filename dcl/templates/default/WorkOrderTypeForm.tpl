{dcl_validator_init}
<script language="JavaScript">
{literal}
function validateAndSubmitForm(form){
{/literal}
	var aValidators = new Array(new ValidatorString(form.elements["type_name"], "{$smarty.const.STR_CMMN_NAME}"));
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
<form class="styled" method="post" action="{$URL_MAIN_PHP}">
{if $IS_EDIT}
	<input type="hidden" name="menuAction" value="WorkOrderType.Update">
	<input type="hidden" name="wo_type_id" value="{$VAL_WO_TYPE_ID}">
	<fieldset>
		<legend>{$smarty.const.STR_WO_EDITWORKORDERTYPE}</legend>
{else}
	<input type="hidden" name="menuAction" value="WorkOrderType.Insert">
	<fieldset>
		<legend>{$smarty.const.STR_WO_ADDWORKORDERTYPE}</legend>
{/if}
		<div>
			<label for="active">{$smarty.const.STR_CMMN_ACTIVE}:</label>
			{$CMB_ACTIVE}
		</div>
		<div>
			<label for="type_name">{$smarty.const.STR_CMMN_NAME}:</label>
			<input type="text" size="50" maxlength="50" name="type_name" value="{$VAL_NAME}">
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_OK}">
			<input type="button" onclick="location.href = '{$URL_BACK}';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>