{dcl_bootstrapvalidator}
{if $VAL_ERRORS}{dcl_validator_errors errors=$VAL_ERRORS}{/if}
<form id="theForm" class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$menuAction|escape}">
	{if $id}<input type="hidden" name="id" value="{$id}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		{dcl_form_control id=unit_name controlsize=4 label=$smarty.const.STR_CMMN_NAME required=true}
			<input class="form-control" type="text" maxlength="50" id="unit_name" name="unit_name" value="{$VAL_NAME|escape}">
		{/dcl_form_control}
		{dcl_form_control id=unit_abbr controlsize=2 label=$smarty.const.STR_CMMN_ABBR required=true}
			<input class="form-control" type="text" maxlength="5" id="unit_abbr" name="unit_abbr" value="{$VAL_ABBR|escape}">
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input name="submitForm" type="submit" class="btn btn-primary" value="{$smarty.const.STR_CMMN_SAVE|escape}">
				<input type="button" class="btn btn-link" onclick="location.href = '{dcl_url_action controller=MeasurementUnit action=Index}';" value="{$smarty.const.STR_CMMN_CANCEL|escape}">
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
				unit_name: {
					validators: {
						notEmpty: {
							message: "Name is required and cannot be empty."
						}
					}
				},
				unit_abbr: {
					validators: {
						notEmpty: {
							message: "Abbreviation is required and cannot be empty."
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