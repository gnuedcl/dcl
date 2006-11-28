<!-- $Id: htmlNewChecklist.tpl,v 1.3 2006/11/27 06:00:51 mdean Exp $ -->
{dcl_validator_init}
<script language="JavaScript">
function validateAndSubmitForm(form)
{literal}
{
{/literal}
	var aValidators = new Array(
			new ValidatorString(form.elements["dcl_chklst_summary"], "{$smarty.const.STR_CHK_SUMMARY}")
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
<form class="styled" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="boChecklists.dbadd">
	<input type="hidden" name="dcl_chklst_tpl_id" value="{$dcl_chklst_tpl_id}">
	<fieldset>
		<legend>{$smarty.const.STR_CHK_INITIATECHECKLIST}</legend>
		<div class="help">{$smarty.const.STR_CHK_TEMPLATE}: {$VAL_TPLNAME|escape}</div>
		<div class="required">
			<label for="dcl_chklst_summary">{$smarty.const.STR_CHK_SUMMARY}:</label>
			<input type="text" size="50" maxlength="255" id="dcl_chklst_summary" name="dcl_chklst_summary" value="{$VAL_SUMMARY|escape}">
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=boChecklistTpl.show';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>
<script language="JavaScript">
if (document.getElementById("dcl_chklst_summary"))
	document.getElementById("dcl_chklst_summary").focus();
</script>