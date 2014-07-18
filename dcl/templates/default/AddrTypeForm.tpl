{dcl_validator_init}
<script language="JavaScript">
function validateAndSubmitForm(form)
{
	var aValidators = [
			new ValidatorString(form.elements["addr_type_name"], "{$smarty.const.STR_CMMN_NAME}")
		];
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
<form class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$menuAction}">
	{if $addr_type_id}<input type="hidden" name="addr_type_id" value="{$addr_type_id}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		{dcl_form_control id=addr_type_name controlsize=5 label=$smarty.const.STR_CMMN_NAME required=true}
			<input type="text" class="form-control" maxlength="50" id="addr_type_name" name="addr_type_name" value="{$VAL_NAME|escape}">
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input type="button" class="btn btn-primary" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
				<input type="button" class="btn btn-link" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=AddressType.Index';" value="{$smarty.const.STR_CMMN_CANCEL}">
			</div>
		</div>
	</fieldset>
</form>
<script language="JavaScript">
if (document.getElementById("addr_type_name"))
	document.getElementById("addr_type_name").focus();
</script>