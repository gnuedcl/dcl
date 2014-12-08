{dcl_bootstrapvalidator}
{if $VAL_ERRORS}{dcl_validator_errors errors=$VAL_ERRORS}{/if}
<form id="theForm" class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="OrganizationOutageSla.Update">
	<input type="hidden" name="org_id" value="{$ViewData->OrgId}">
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		{dcl_form_control id=outage_sla controlsize=2 label="Outage SLA" required=true inputGroup=true}
			<input class="form-control" type="text" maxlength="10" id="outage_sla" name="outage_sla" value="{$ViewData->OutageSla|escape}">
			<span class="input-group-addon">%</span>
		{/dcl_form_control}
		{dcl_form_control id=outage_sla_warn controlsize=2 label="Outage SLA Warning" inputGroup=true}
			<input class="form-control" type="text" maxlength="10" id="outage_sla_warn" name="outage_sla_warn" value="{$ViewData->OutageSlaWarn|escape}">
			<span class="input-group-addon">%</span>
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input name="submitForm" type="submit" class="btn btn-primary" value="{$smarty.const.STR_CMMN_SAVE|escape}">
				<input type="button" class="btn btn-link" onclick="location.href = '{dcl_url_action controller=Organization action=Detail params="org_id={$ViewData->OrgId}"}';" value="{$smarty.const.STR_CMMN_CANCEL|escape}">
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript">
	$(function() {
		$("#theForm").bootstrapValidator({
			container: "tooltip",
			feedbackIcons: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
			fields: {
				outage_sla: {
					validators: {
						notEmpty: {
							message: "Outage SLA is required and cannot be empty."
						},
						numeric: {
							message: "Outage SLA must be numeric."
						},
						between: {
							inclusive: true,
							message: "Outage SLA must be between 0 and 100",
							min: 0,
							max: 100
						}
					}
				},
				outage_sla_warn: {
					validators: {
						numeric: {
							message: "Outage SLA warning must be numeric."
						},
						between: {
							inclusive: true,
							message: "Outage SLA warning must be between 0 and 100",
							min: 0,
							max: 100
						}
					}
				}
			}
		}).on('error.field.bv', function(e, data) {
			var $parent = data.element.parents('.form-group'),
					$icon   = $parent.find('.form-control-feedback[data-bv-icon-for="' + data.field + '"]'),
					title   = $icon.data('bs.tooltip').getTitle();

			$icon.tooltip('destroy').tooltip({
				html: true,
				placement: 'right',
				title: title,
				container: 'body'
			});
		});

		$("#unit_name").focus();
	});
</script>