<!-- $Id$ -->
{dcl_validator_init}
<script language="JavaScript">
{literal}
function validateAndSubmitForm(form)
{
{/literal}
	var aValidators = new Array(
			new ValidatorSelection(form.elements["status"], "{$smarty.const.STR_TCK_STATUS}"),
			new ValidatorString(form.elements["resolution"], "{$smarty.const.STR_TCK_RESOLUTION}")
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
<form class="styled" name="resform" method="post" action="{$URL_MAIN_PHP}">
{if $IS_EDIT}
	<input type="hidden" name="resid" value="{$resid|escape}">
{/if}
	<input type="hidden" name="menuAction" value="{$menuAction|escape}">
	<input type="hidden" name="startedon" value="{$startedon|escape}">
	<input type="hidden" name="ticketid" value="{$ticketid|escape}">
	<fieldset>
		<legend>{$TXT_TITLE}</legend>
		<div class="required">
			<label for="status">{$smarty.const.STR_TCK_STATUS}:</label>
			{$CMB_STATUS}
		</div>
		<div class="required">
			<label for="is_public">{$smarty.const.STR_CMMN_PUBLIC}:</label>
			<input type="checkbox" id="is_public" name="is_public" value="Y"{if $VAL_ISPUBLIC == "Y"} checked{/if}>
		</div>
		<div class="required">
			<label for="copy_me_on_notification">Copy Me on Notification:</label>
			<input type="checkbox" id="copy_me_on_notification" name="copy_me_on_notification" value="Y">
		</div>
		{if !$PERM_ASSIGN && !$IS_EDIT}
		<div>
			<label for="escalate">{$smarty.const.STR_TCK_ESCALATE}</label>
			<input type="checkbox" id="escalate" name="escalate" value="1">
		</div>
		{/if}
		<div class="required">
			<label for="resolution">{$smarty.const.STR_TCK_RESOLUTION}:</label>
			<textarea id="resolution" name="resolution" rows="6" cols="70" wrap>{$VAL_RESOLUTION|escape}</textarea>
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="reset" value="{$smarty.const.STR_CMMN_RESET}">
		</div>
	</fieldset>
</form>
