{extends file="_Layout.tpl"}
{block name=title}{$TXT_FUNCTION|escape}{/block}
{block name=css}
	<link rel="stylesheet" src="vendor/bootstrapvalidator/css/bootstrapValidator.min.css" />
	<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
	<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
	<link rel="stylesheet" href="{$DIR_VENDOR}timepicker/jquery-ui-timepicker-addon.css">
	<link rel="stylesheet" type="text/css" href="{$DIR_VENDOR}freejqgrid/css/ui.jqgrid.css" />
	<style type="text/css">
		#selected_orgs span { margin-right: 4px; margin-bottom: 4px; }
	</style>
{/block}
{block name=content}
{dcl_validator_errors errors=$ERRORS}
<form id="theForm" class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$menuAction|escape}">
	{if $ViewData->OutageId}<input type="hidden" name="id" value="{$ViewData->OutageId}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		{dcl_form_control id=outage_title controlsize=4 label=$smarty.const.STR_CMMN_TITLE required=true}
			<input class="form-control" type="text" maxlength="100" id="outage_title" name="outage_title" value="{$ViewData->Title|escape}">
		{/dcl_form_control}
		{dcl_form_control id=outage_type_id controlsize=4 label="Outage Type" required=true}
		{dcl_select_outage_type default=$ViewData->OutageType active=$ACTIVE_ONLY}
		{/dcl_form_control}
		{dcl_form_control controlsize=10 label="Severity Level" required=false}
			<div class="radio">
				<label>
					<input type="radio" name="sev_level" id="sev_level_1" value="1"{if $ViewData->SeverityLevel == 1} checked{/if}>
					1 - Complete outage
				</label>
			</div>
			<div class="radio">
				<label>
					<input type="radio" name="sev_level" id="sev_level_2" value="2"{if $ViewData->SeverityLevel == 2} checked{/if}>
					2 - Major functionality affected; possible loss of revenue
				</label>
			</div>
			<div class="radio">
				<label>
					<input type="radio" name="sev_level" id="sev_level_3" value="3"{if $ViewData->SeverityLevel == 3} checked{/if}>
					3 - Minor issue
				</label>
			</div>
			<div class="radio">
				<label>
					<input type="radio" name="sev_level" id="sev_level_4" value="4"{if $ViewData->SeverityLevel == 4} checked{/if}>
					4 - Workaround available
				</label>
			</div>
			<div class="radio">
				<label>
					<input type="radio" name="sev_level" id="sev_level_5" value="5"{if $ViewData->SeverityLevel == 5} checked{/if}>
					5 - False report; error in monitoring
				</label>
			</div>
		{/dcl_form_control}
		{if $IS_EDIT}
			{dcl_form_control id=outage_status_id controlsize=4 label="Outage Status" required=true}
			{dcl_select_outage_status default=$ViewData->Status isplanned=$ViewData->IsPlanned}
			{/dcl_form_control}
		{/if}
		{dcl_form_control id=outage_sched_start controlsize=3 label="Scheduled Start" required=true}
			<input type="text" class="form-control" data-input-type="datetime" maxlength="16" id="outage_sched_start" name="outage_sched_start" value="{$ViewData->SchedStart|escape}">
		{/dcl_form_control}
		{dcl_form_control id=outage_sched_end controlsize=3 label="Scheduled End" required=false}
			<input type="text" class="form-control" data-input-type="datetime" maxlength="16" id="outage_sched_end" name="outage_sched_end" value="{$ViewData->SchedEnd|escape}">
		{/dcl_form_control}
		{dcl_form_control id=outage_start controlsize=3 label="Start" required=true}
			<input type="text" class="form-control" data-input-type="datetime" maxlength="16" id="outage_start" name="outage_start" value="{$ViewData->Start|escape}">
		{/dcl_form_control}
		{dcl_form_control id=outage_end controlsize=3 label="End" required=false}
			<input type="text" class="form-control" data-input-type="datetime" maxlength="16" id="outage_end" name="outage_end" value="{$ViewData->End|escape}">
		{/dcl_form_control}
		{dcl_form_control id=outage_description controlsize=10 label=Description required=true}
			<textarea class="form-control" name="outage_description" id="outage_description" rows="4" wrap valign="top">{$ViewData->Description|escape}</textarea>
		{/dcl_form_control}
		{dcl_form_control id=environment_id controlsize=10 label=Environments help="Selecting an environment will automatically select all organizations that were installed on that environment at the start time of the outage."}
		{dcl_select_environment default=$ViewData->Environments size=8 active=N}
		{/dcl_form_control}
		{dcl_form_control id=orgs controlsize=2 label=$smarty.const.STR_CMMN_ORGANIZATION}
			<a id="orgsLink" href="javascript:;">Select</a>
			<input type="hidden" id="outage_orgs" name="outage_orgs" value="{$ViewData->Orgs}">
		{/dcl_form_control}
		<div id="selected_orgs" class="col-xs-offset-2 col-xs-10">
		</div>
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input name="submitForm" type="submit" class="btn btn-primary" value="{$smarty.const.STR_CMMN_SAVE|escape}">
				<input type="button" class="btn btn-link" onclick="location.href = '{dcl_url_action controller=Outage action=Index}';" value="{$smarty.const.STR_CMMN_CANCEL|escape}">
			</div>
		</div>
	</fieldset>
</form>
{dcl_dialog_org}
{/block}
{block name=script}
<script type="text/javascript" src="{$DIR_VENDOR}bootstrapvalidator/js/bootstrapValidator.min.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}freejqgrid/js/i18n/grid.locale-en.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}freejqgrid/js/jquery.jqgrid.min.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}bettergrow/jquery.BetterGrow.min.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}timepicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="{$DIR_JS}dialog-org.js"></script>
<script type="text/javascript">
	$(function() {
		$("#outage_title").focus();
		$("input[data-input-type=date]").datepicker();
		$("input[data-input-type=datetime]").datetimepicker();
		$("textarea").BetterGrow();
		$("#content").find("select").select2({ minimumResultsForSearch: 10 });
		$("#outage_orgs").dclOrgSelector({
			useEnvironment: true
		});

		var $form = $("#theForm");

		$form.bootstrapValidator({
			container: "tooltip",
			feedbackIcons: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
			fields: {
				outage_title: {
					validators: {
						notEmpty: {
							message: "Title is required."
						}
					}
				}
			}
		}).on('error.field.bv', function (e, data) {
			var $parent = data.element.parents('.form-group'),
					$icon = $parent.find('.form-control-feedback[data-bv-icon-for="' + data.field + '"]'),
					title = $icon.data('bs.tooltip').getTitle();

			$icon.tooltip('destroy').tooltip({
				html: true,
				placement: 'right',
				title: title,
				container: 'body'
			});
		});

		// Outage type selection
		var $scheduledControls = $("#outage_sched_start,#outage_sched_end");
		var $scheduledControlsGroups = $scheduledControls.parents("div.form-group");
		var $startControlGroup = $("#outage_start").parents("div.form-group:first");
		var $sevLvlControlGroup = $("#sev_level_1").parents("div.form-group:first");
		var $sevLvlControls = $sevLvlControlGroup.find("input[type='radio']");

		function updateFormForOutageType() {
			var isScheduled = $("#outage_type_id").find(":selected[data-is-scheduled]").attr("data-is-scheduled");
			if (isScheduled == "Y") {
				$scheduledControls.removeAttr("disabled");
				$scheduledControlsGroups.addClass("required");
				$startControlGroup.removeClass("required");
				$sevLvlControlGroup.removeClass("required").addClass("text-muted");
				$sevLvlControls.attr("disabled", true);
			} else {
				$scheduledControls.attr("disabled", true);
				$scheduledControlsGroups.removeClass("required");
				$startControlGroup.addClass("required");
				$sevLvlControlGroup.addClass("required").removeClass("text-muted");
				$sevLvlControls.removeAttr("disabled");
			}
		}

		$form.on("change", "#outage_type_id", updateFormForOutageType);
		updateFormForOutageType();

	});
</script>
{/block}