{dcl_validator_init}
<form class="form-horizontal" name="FAQFORM" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
	{dcl_anti_csrf_token}
	{if $VAL_FAQID}<input type="hidden" name="faqid" value="{$VAL_FAQID}">{/if}
	<fieldset>
		<legend>{if $IS_EDIT}{$smarty.const.STR_FAQ_EDIT|escape}{else}{$smarty.const.STR_FAQ_ADDNEW|escape}{/if}</legend>
		{dcl_form_control id=active controlsize=10 label=$smarty.const.STR_CMMN_ACTIVE}
			<input type="checkbox" id="active" name="active" value="Y"{if $VAL_ACTIVE == "Y"} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=name controlsize=10 label=$smarty.const.STR_FAQ_NAME required=true}
		{dcl_input_text id=name maxlength=100 value=$VAL_NAME}
		{/dcl_form_control}
		{dcl_form_control id=description controlsize=10 label=$smarty.const.STR_FAQ_DESCRIPTION required=true}
			<textarea class="form-control" id="description" name="description" rows="6" wrap valign="top">{$VAL_DESCRIPTION|escape}</textarea>
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
				<input class="btn btn-link" type="button" onclick="history.back();" value="{$smarty.const.STR_CMMN_CANCEL}">
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript">
	$(function() {
		$("#name").focus();
	});

	function validateAndSubmitForm(form)
	{
		var aValidators = [
			new ValidatorString(form.elements["name"], "{$smarty.const.STR_FAQ_NAME}"),
			new ValidatorString(form.elements["description"], "{$smarty.const.STR_FAQ_DESCRIPTION}")
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
