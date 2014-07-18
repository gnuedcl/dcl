{dcl_validator_init}
<form class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$menuAction}">
	{if $VAL_ID}<input type="hidden" name="hotlist_id" value="{$VAL_ID}">{/if}
	<fieldset>
		<legend>{$VAL_TITLE|escape}</legend>
		{dcl_form_control id=active controlsize=10 label=$smarty.const.STR_CMMN_ACTIVE}
			<input type="checkbox" id="active" name="active" value="Y"{if $VAL_ACTIVE == 'Y'} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=hotlist_tag controlsize=10 label=$smarty.const.STR_CMMN_NAME required=true}
		{dcl_input_text id=hotlist_tag maxlength=20 value=$VAL_NAME}
		{/dcl_form_control}
		{dcl_form_control id=hotlist_desc controlsize=10 label=Description required=true}
			<textarea class="form-control" id="hotlist_desc" name="hotlist_desc" rows="4" wrap valign="top">{$VAL_DESCRIPTION|escape}</textarea>
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
				<input class="btn btn-link" type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=htmlHotlistBrowse.show';" value="{$smarty.const.STR_CMMN_CANCEL}">
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript">
	function validateAndSubmitForm(form)
	{
		var aValidators = [
				new ValidatorString(form.elements["hotlist_tag"], "{$smarty.const.STR_CMMN_NAME}"),
				new ValidatorString(form.elements["hotlist_desc"], "{$smarty.const.STR_WO_DESCRIPTION}")
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
