{dcl_calendar_init}
{dcl_validator_init}
<script language="JavaScript">
{literal}
function validateAndSubmitForm(form)
{
	{/literal}
	var aValidators = new Array(
			new ValidatorInteger(form.elements["responsible"], "{$smarty.const.STR_WO_RESPONSIBLE}", true)
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
<form class="styled" name="reassign" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$menuAction}">
	{if $VAL_VIEW}{$VAL_VIEW}{/if}
	{section loop=$selected name=item}<input type="hidden" name="selected[]" value="{$selected[item]}">{/section}
	{if $jcn}<input type="hidden" name="jcn" value="{$jcn}">{/if}
	{if $seq}<input type="hidden" name="seq" value="{$seq}">{/if}
	{if $return_to}<input type="hidden" name="return_to" value="{$return_to|escape}">{/if}
	{if $project}<input type="hidden" name="project" value="{$project|escape}">{/if}
	<fieldset>
		<legend>{$TXT_TITLE}</legend>
		<div>
			<label for="responsible">{$smarty.const.STR_WO_RESPONSIBLE}:</label>
			{$CMB_RESPONSIBLE}
		</div>
		<div>
			<label for="priority">{$smarty.const.STR_WO_PRIORITY}:</label>
			{$CMB_PRIORITY}
		</div>
		<div>
			<label for="severity">{$smarty.const.STR_WO_SEVERITY}:</label>
			{$CMB_SEVERITY}
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="reset" value="{$smarty.const.STR_CMMN_RESET}">
		</div>
	</fieldset>
</form>
