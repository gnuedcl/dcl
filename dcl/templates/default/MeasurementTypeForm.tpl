{dcl_bootstrapvalidator}
{if $VAL_ERRORS}{dcl_validator_errors errors=$VAL_ERRORS}{/if}
<form id="theForm" class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$menuAction|escape}">
	{if $id}<input type="hidden" name="id" value="{$id}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		{dcl_form_control id=measurement_unit_id controlsize=4 label="Measurement Unit" required=true}
			{dcl_select_measurement_unit default=$VAL_UNITID}
		{/dcl_form_control}
		{dcl_form_control id=measurement_name controlsize=4 label=$smarty.const.STR_CMMN_NAME required=true}
			<input class="form-control" type="text" maxlength="50" id="measurement_name" name="measurement_name" value="{$VAL_NAME|escape}">
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input name="submitForm" type="submit" class="btn btn-primary" value="{$smarty.const.STR_CMMN_SAVE|escape}">
				<input type="button" class="btn btn-link" onclick="location.href = '{dcl_url_action controller=MeasurementType action=Index}';" value="{$smarty.const.STR_CMMN_CANCEL|escape}">
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
				measurement_unit_id: {
					validators: {
						notEmpty: {
							message: "Measurement unit is required and cannot be empty."
						}
					}
				},
				measurement_name: {
					validators: {
						notEmpty: {
							message: "Name is required and cannot be empty."
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