{extends file="_Layout.tpl"}
{block name=title}{$TXT_FUNCTION|escape}{/block}
{block name=css}
	<link rel="stylesheet" src="vendor/bootstrapvalidator/css/bootstrapValidator.min.css" />
	<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
	<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
	<link rel="stylesheet" href="{$DIR_VENDOR}timepicker/jquery-ui-timepicker-addon.css">
{/block}
{block name=content}
{dcl_validator_errors errors=$ERRORS}
<form id="theForm" class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$menuAction|escape}">
	{if $ViewData->OrganizationId}<input type="hidden" name="id" value="{$ViewData->EnvironmentOrgId}">{/if}
	<input type="hidden" name="org_id" value="{$ViewData->OrganizationId}">
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		{dcl_form_control id=environment_id controlsize=4 label=Environment required=true}
			{dcl_select_environment default=$ViewData->EnvironmentId active=$ACTIVE_ONLY}
		{/dcl_form_control}
		{dcl_form_control id=begin_dt controlsize=2 label="Tenancy Start" required=true}
			<input type="text" class="form-control" data-input-type="datetime" maxlength="16" id="begin_dt" name="begin_dt" value="{$ViewData->BeginDt|escape}">
		{/dcl_form_control}
		{dcl_form_control id=end_dt controlsize=2 label="Tenancy End" required=false}
			<input type="text" class="form-control" data-input-type="datetime" maxlength="16" id="end_dt" name="end_dt" value="{$ViewData->EndDt|escape}">
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input name="submitForm" type="submit" class="btn btn-primary" value="{$smarty.const.STR_CMMN_SAVE|escape}">
				<input type="button" class="btn btn-link" onclick="location.href = '{dcl_url_action controller=Organization action=Detail params="org_id={$ViewData->OrganizationId}"}';" value="{$smarty.const.STR_CMMN_CANCEL|escape}">
			</div>
		</div>
	</fieldset>
</form>
{/block}
{block name=script}
<script type="text/javascript" src="vendor/bootstrapvalidator/js/bootstrapValidator.min.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}timepicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript">
	$(function() {
		$("input[data-input-type=date]").datepicker();
		$("input[data-input-type=datetime]").datetimepicker();
		$("#content").find("select").select2({ minimumResultsForSearch: 10 });

		$("#theForm").bootstrapValidator({
			container: "tooltip",
			feedbackIcons: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
			fields: {
				environment_id: {
					validators: {
						notEmpty: {
							message: "Environment is required."
						}
					}
				},
				begin_dt: {
					validators: {
						notEmpty: {
							message: "Begin date is required."
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
	});
</script>
{/block}