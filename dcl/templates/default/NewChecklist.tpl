{dcl_validator_init}
<script language="JavaScript">
function validateAndSubmitForm(form)
{
	var aValidators = [
			new ValidatorString(form.elements["dcl_chklst_summary"], "{$smarty.const.STR_CHK_SUMMARY}")
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
	<input type="hidden" name="menuAction" value="boChecklists.dbadd">
	<input type="hidden" name="dcl_chklst_tpl_id" value="{$dcl_chklst_tpl_id}">
	<fieldset>
		<legend>{$smarty.const.STR_CHK_INITIATECHECKLIST|escape}: {$VAL_TPLNAME|escape}</legend>
		{dcl_form_control id=dcl_chklst_summary controlsize=10 label=$smarty.const.STR_CHK_SUMMARY required=true}
			<input class="form-control" type="text" maxlength="255" id="dcl_chklst_summary" name="dcl_chklst_summary" value="{$VAL_SUMMARY|escape}">
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input class="btn btn-primary" type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE|escape}">
				<input class="btn btn-link" type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=boChecklistTpl.show';" value="{$smarty.const.STR_CMMN_CANCEL}">
			</div>
		</div>
	</fieldset>
</form>
<script language="JavaScript">
if (document.getElementById("dcl_chklst_summary"))
	document.getElementById("dcl_chklst_summary").focus();
</script>