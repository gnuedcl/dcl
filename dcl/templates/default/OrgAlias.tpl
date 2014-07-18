{dcl_validator_init}
<form class="form-horizontal" method="post" name="submitForm" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
	<input type="hidden" name="org_id" value="{$VAL_ORGID}">
	{if $VAL_ORGALIASID}<input type="hidden" name="org_alias_id" value="{$VAL_ORGALIASID}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		{dcl_form_control controlsize=10 label=$smarty.const.STR_CMMN_NAME}
		<span class="form-control">{$VAL_ORGNAME|escape}</span>
		{/dcl_form_control}
		{dcl_form_control id=alias controlsize=10 label="Alias" required=true}
		{dcl_input_text id=alias maxlength=50 value=$VAL_ALIAS}
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
				<input class="btn btn-link" type="button" onclick="location.href = '{$URL_BACK}';" value="{$smarty.const.STR_CMMN_CANCEL}">
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript">
$(function() {
	$("#alias").focus();
});

function validateAndSubmitForm(form)
{
	var aValidators = new Array(
			new ValidatorString(form.elements["alias"], "Alias")
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