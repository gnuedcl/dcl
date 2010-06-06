<!-- $Id$ -->
{dcl_validator_init}
{dcl_calendar_init}
<script language="JavaScript">
function validateAndSubmitForm(form)
{literal}
{
{/literal}
	var aValidators = new Array(
			new ValidatorDate(form.elements["endon"], "Ending On"),
			new ValidatorInteger(form.elements["days"], "Show for # Days", true)
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
	<input type="hidden" name="menuAction" value="htmlHotlistProjectTimeline.Render">
	<input type="hidden" name="id" value="{$VAL_HOTLISTID}">
	<fieldset>
		<legend>Show Hotlist Timeline</legend>
		<div class="help">{$VAL_HOTLISTNAME|escape}</div>
		<div class="required">
			<label for="days">Show for # Days:</label>
			<input type="text" maxlength="3" size="3" name="days" id="days" value="{$VAL_DAYS}">
		</div>
		<div class="required">
			<label for="endon">Ending On:</label>
			{dcl_calendar name="endon" value="$VAL_ENDON"}
		</div>
		<div class="required">
			<label for="scope">Scope Changes:</label>
			<input type="checkbox" name="scope" id="scope"{if $VAL_SCOPE == "Y"} checked{/if}>
		</div>
		<div class="required">
			<label for="timecards">Time Cards:</label>
			<input type="checkbox" name="timecards" id="timecards"{if $VAL_TIMECARDS == "Y"} checked{/if}>
		</div>
		<div class="required">
			<label for="code">Code Changes:</label>
			<input type="checkbox" name="code" id="code"{if $VAL_CODE == "Y"} checked{/if}>
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_GO}">
		</div>
	</fieldset>
</form>