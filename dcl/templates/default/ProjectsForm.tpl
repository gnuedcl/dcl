{dcl_calendar_init}
{dcl_selector_init}
{dcl_validator_init}
<script type="text/javascript">
{literal}
function validateAndSubmitForm(form)
{
{/literal}
		var aValidators = new Array(
			new ValidatorInteger(form.elements["reportto"], "{$smarty.const.STR_PRJ_LEAD}"),
			new ValidatorSelection(form.elements["status"], "{$smarty.const.STR_PRJ_STATUS}"),
			new ValidatorString(form.elements["name"], "{$smarty.const.STR_PRJ_NAME}"),
			new ValidatorString(form.elements["description"], "{$smarty.const.STR_PRJ_DESCRIPTION}")
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
<form class="styled" name="PROJECTFORM" method="post" action="{$URL_MAIN_PHP}">
{if $IS_EDIT}
	<input type="hidden" name="menuAction" value="Project.Update">
	<input type="hidden" name="projectid" value="{$ViewData->Id}">
	<fieldset>
		<legend>{$smarty.const.STR_PRJ_EDIT}</legend>
{else}
	<input type="hidden" name="menuAction" value="Project.Insert">
	<fieldset>
		<legend>{$smarty.const.STR_PRJ_ADD}</legend>
{/if}
		<div class="required">
			<label for="name">{$smarty.const.STR_PRJ_NAME}:</label>
			<input type="text" size="50" maxlength="100" id="name" name="name" value="{$ViewData->Name|escape}">
		</div>
{if $IS_EDIT}
		<div class="required">
			<label for="status">{$smarty.const.STR_PRJ_STATUS}:</label>
			{dcl_select_status default=`$ViewData->StatusId`}
		</div>
{/if}
		<div class="required">
			<label for="reportto">{$smarty.const.STR_PRJ_LEAD}:</label>
			{dcl_select_personnel name=reportto default=`$ViewData->ResponsibleId`}
		</div>
		<div>
			<label for="deadline">{$smarty.const.STR_PRJ_DEADLINE}:</label>
			{dcl_calendar name="projectdeadline" value="`$ViewData->Deadline`"}
		</div>
		<div>
			<label for="parentprojectid">{$smarty.const.STR_PRJ_PARENTPRJ}:</label>
			{dcl_selector_project name="parentprojectid" value="`$ViewData->ParentId`" decoded="`$ViewData->ParentName`"}
		</div>
		<div class="required">
			<label for="description">{$smarty.const.STR_PRJ_DESCRIPTION}:</label>
			<textarea name="description" rows="4" cols="70" wrap valign="top">{$ViewData->Description|escape}</textarea>
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="button" onclick="history.go(-1);" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>
<script type="text/javascript">{literal}
	$(function() {
		$("#name").focus();
    });
{/literal}</script>