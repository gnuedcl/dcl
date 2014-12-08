{dcl_bootstrapvalidator}
{if $VAL_ERRORS}{dcl_validator_errors errors=$VAL_ERRORS}{/if}
<form id="theForm" class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$menuAction|escape}">
	{if $id}<input type="hidden" name="id" value="{$id}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		{dcl_form_control id=outage_type_name controlsize=4 label=$smarty.const.STR_CMMN_NAME required=true}
			<input class="form-control" type="text" maxlength="64" id="outage_type_name" name="outage_type_name" value="{$VAL_NAME|escape}">
		{/dcl_form_control}
		{dcl_form_control id=is_down controlsize=4 label="Down?" required=true}
			<select name="is_down" id="is_down">
				<option value="Y"{if $IS_DOWN == 'Y'} selected{/if}>{$smarty.const.STR_CMMN_YES|escape}</option>
				<option value="N"{if $IS_DOWN == 'N'} selected{/if}>{$smarty.const.STR_CMMN_NO|escape}</option>
			</select>
		{/dcl_form_control}
		{dcl_form_control id=is_infrastructure controlsize=4 label="Infrastructure?" required=true}
			<select name="is_infrastructure" id="is_infrastructure">
				<option value="Y"{if $IS_INFRASTRUCTURE == 'Y'} selected{/if}>{$smarty.const.STR_CMMN_YES|escape}</option>
				<option value="N"{if $IS_INFRASTRUCTURE == 'N'} selected{/if}>{$smarty.const.STR_CMMN_NO|escape}</option>
			</select>
		{/dcl_form_control}
		{dcl_form_control id=is_planned controlsize=4 label="Planned?" required=true}
			<select name="is_planned" id="is_planned">
				<option value="Y"{if $IS_PLANNED == 'Y'} selected{/if}>{$smarty.const.STR_CMMN_YES|escape}</option>
				<option value="N"{if $IS_PLANNED == 'N'} selected{/if}>{$smarty.const.STR_CMMN_NO|escape}</option>
			</select>
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input name="submitForm" type="submit" class="btn btn-primary" value="{$smarty.const.STR_CMMN_SAVE|escape}">
				<input type="button" class="btn btn-link" onclick="location.href = '{dcl_url_action controller=OutageType action=Index}';" value="{$smarty.const.STR_CMMN_CANCEL|escape}">
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
				is_down: {
					validators: {
						notEmpty: {
							message: "Down is required."
						}
					}
				},
				is_infrastructure: {
					validators: {
						notEmpty: {
							message: "Infrastructure is required."
						}
					}
				},
				is_planned: {
					validators: {
						notEmpty: {
							message: "Planned is required."
						}
					}
				},
				outage_type_name: {
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

		$("#outage_type_name").focus();
	});
</script>