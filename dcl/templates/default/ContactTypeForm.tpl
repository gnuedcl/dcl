{dcl_validator_init}
<form class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$menuAction}">
	{if $contact_type_id}<input type="hidden" name="contact_type_id" value="{$contact_type_id}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		{dcl_form_control id=contact_type_name controlsize=4 label=$smarty.const.STR_CMMN_NAME required=true}
		{dcl_input_text id=contact_type_name maxlength=30 value=$VAL_NAME}
		{/dcl_form_control}
		{dcl_form_control id=contact_type_is_main controlsize=10 label="Main" required=true help="Check this box to display contacts of this type on organization details."}
			<input type="checkbox" id="contact_type_is_main" name="contact_type_is_main" value="Y"{if $VAL_MAIN == 'Y'} checked{/if}>
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
				<input class="btn btn-link" type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=ContactType.Index';" value="{$smarty.const.STR_CMMN_CANCEL}">
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript">
	$(function() {
		$("#contact_type_name").focus();
	});

	function validateAndSubmitForm(form)
	{
		var aValidators = [
			new ValidatorString(form.elements["contact_type_name"], "{$smarty.const.STR_CMMN_NAME}")
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