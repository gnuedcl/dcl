<!-- $Id: htmlTicketReassignForm.tpl,v 1.3 2006/11/27 06:00:52 mdean Exp $ -->
{dcl_validator_init}
<script language="JavaScript">
function validateAndSubmitForm(form)
{literal}
{
{/literal}
	var aValidators = new Array(
			new ValidatorSelection(form.elements["responsible"], "{$smarty.const.STR_TCK_RESPONSIBLE}"),
			new ValidatorSelection(form.elements["type"], "{$smarty.const.STR_TCK_TYPE}"),
			new ValidatorSelection(form.elements["priority"], "{$smarty.const.STR_TCK_PRIORITY}")
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
	<input type="hidden" name="menuAction" value="boTickets.dbreassign">
	<input type="hidden" name="ticketid" value="{$VAL_TICKETID}">
	<fieldset>
		<legend>{$TXT_TITLE}</legend>
		<div class="required">
			<label for="responsible">{$smarty.const.STR_TCK_RESPONSIBLE}:</label>
			{$CMB_RESPONSIBLE}
		</div>
		<div class="required">
			<label for="responsible">{$smarty.const.STR_TCK_PRIORITY}:</label>
			{$CMB_PRIORITY}
		</div>
		<div class="required">
			<label for="responsible">{$smarty.const.STR_TCK_TYPE}:</label>
			{$CMB_TYPE}
		</div>
	</tr>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="button" onclick="location.href='{$URL_MAIN_PHP}?menuAction=boTickets.view&ticketid={$VAL_TICKETID}';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>