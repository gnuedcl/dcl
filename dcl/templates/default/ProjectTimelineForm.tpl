{dcl_validator_init}
<form class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="htmlProjectTimeline.Render">
	<input type="hidden" name="projectid" value="{$VAL_PROJECTID}">
	<fieldset>
		<legend>Show Project Timeline</legend>
		{dcl_form_control controlsize=10 label="Project"}
		<span>{$VAL_PROJECTNAME|escape}</span>
		{/dcl_form_control}
		{dcl_form_control id=days controlsize=1 label="Show for # Days" required=true}
		{dcl_input_text id=days maxlength=3 value=$VAL_DAYS}
		{/dcl_form_control}
		{dcl_form_control id=endon controlsize=2 label="Ending On" required=true}
		{dcl_input_date id=endon value=$VAL_ENDON}
		{/dcl_form_control}
		{dcl_form_control id=scope controlsize=10 label="Scope Changes" required=true}
			<input type="checkbox" name="scope" id="scope"{if $VAL_SCOPE == "Y"} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=timecards controlsize=10 label="Time Cards" required=true}
			<input type="checkbox" name="timecards" id="timecards"{if $VAL_TIMECARDS == "Y"} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=code controlsize=10 label="Code Changes" required=true}
			<input type="checkbox" name="code" id="code"{if $VAL_CODE == "Y"} checked{/if}>
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_GO}">
				<a class="btn btn-link" href="{dcl_url_action controller=Project action=Detail params="id={$VAL_PROJECTID}"}">{$smarty.const.STR_CMMN_CANCEL|escape}</a>
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript">
	$(function() {
		$("input[data-input-type=date]").datepicker();
	});

	function validateAndSubmitForm(form)
	{
		var aValidators = [
				new ValidatorDate(form.elements["endon"], "Ending On"),
				new ValidatorInteger(form.elements["days"], "Show for # Days", true)
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
