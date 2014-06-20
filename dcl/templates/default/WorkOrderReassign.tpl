{dcl_calendar_init}
{dcl_validator_init}
<script language="JavaScript">

function validateAndSubmitForm(form)
{

	var aValidators = new Array(
			new ValidatorInteger(form.elements["responsible"], "{$smarty.const.STR_WO_RESPONSIBLE}", true),
			new ValidatorSelection(form.elements["priority"], "{$smarty.const.STR_WO_PRIORITY}", true),
			new ValidatorSelection(form.elements["severity"], "{$smarty.const.STR_WO_SEVERITY}", true),
			new ValidatorDecimal(form.elements["esthours"], "{$smarty.const.STR_WO_ESTHOURS}", true),
			new ValidatorDecimal(form.elements["etchours"], "{$smarty.const.STR_WO_ETCHOURS}", true),
			new ValidatorDate(form.elements["deadlineon"], "{$smarty.const.STR_WO_DEADLINE}", true),
			new ValidatorDate(form.elements["eststarton"], "{$smarty.const.STR_WO_ESTSTART}", true),
			new ValidatorDate(form.elements["estendon"], "{$smarty.const.STR_WO_ESTEND}", true)
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
<form class="styled" name="reassign" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$menuAction}">
	{if is_array($selected)}{section loop=$section name=item}<input type="hidden" name="selected[]" value="{$section[item]}">{/section}{/if}
	{if $jcn}<input type="hidden" name="jcn" value="{$jcn}">{/if}
	{if $seq}<input type="hidden" name="seq" value="{$seq}">{/if}
	{if $return_to}<input type="hidden" name="return_to" value="{$return_to|escape}">{/if}
	{if $project}<input type="hidden" name="project" value="{$project|escape}">{/if}
	<fieldset>
		<legend>{$TXT_TITLE}</legend>
		<div class="required">
			<label for="responsible">{$smarty.const.STR_WO_RESPONSIBLE}:</label>
			{$CMB_RESPONSIBLE}
		</div>
		<div class="required">
			<label for="deadlineon">{$smarty.const.STR_WO_DEADLINE}:</label>
			{dcl_calendar name=deadlineon value=$VAL_DEADLINEON}
		</div>
		<div class="required">
			<label for="eststarton">{$smarty.const.STR_WO_ESTSTART}:</label>
			{dcl_calendar name=eststarton value=$VAL_ESTSTARTON}
		</div>
		<div class="required">
			<label for="estendon">{$smarty.const.STR_WO_ESTEND}:</label>
			{dcl_calendar name=estendon value=$VAL_ESTENDON}
		</div>
		<div class="required">
			<label for="esthours">{$smarty.const.STR_WO_ESTHOURS}:</label>
			<input type="text" name="esthours" size="6" maxlength="6" value="{$VAL_ESTHOURS}">
		</div>
		<div class="required">
			<label for="etchours">{$smarty.const.STR_WO_ETCHOURS}:</label>
			<input type="text" name="etchours" size="6" maxlength="6" value="{$VAL_ETCHOURS}">
		</div>
		<div class="required">
			<label for="priority">{$smarty.const.STR_WO_PRIORITY}:</label>
			{$CMB_PRIORITY}
		</div>
		<div class="required">
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
