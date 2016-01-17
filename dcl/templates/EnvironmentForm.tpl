{extends file="_Layout.tpl"}
{block name=title}{$TXT_FUNCTION|escape}{/block}
{block name=css}
	<link rel="stylesheet" src="vendor/bootstrapvalidator/css/bootstrapValidator.min.css" />
{/block}
{block name=content}
{if $VAL_ERRORS}{dcl_validator_errors errors=$VAL_ERRORS}{/if}
<form id="theForm" class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$menuAction|escape}">
	{if $id}<input type="hidden" name="id" value="{$id}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		{dcl_form_control id=active controlsize=4 label=$smarty.const.STR_CMMN_ACTIVE required=true}
		<select name="active" id="active">
			<option value="Y"{if $active == 'Y'} selected{/if}>{$smarty.const.STR_CMMN_YES|escape}</option>
			<option value="N"{if $active == 'N'} selected{/if}>{$smarty.const.STR_CMMN_NO|escape}</option>
		</select>
		{/dcl_form_control}
		{dcl_form_control id=environment_name controlsize=4 label=$smarty.const.STR_CMMN_NAME required=true}
			<input class="form-control" type="text" maxlength="32" id="environment_name" name="environment_name" value="{$VAL_NAME|escape}">
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input name="submitForm" type="submit" class="btn btn-primary" value="{$smarty.const.STR_CMMN_SAVE|escape}">
				<input type="button" class="btn btn-link" onclick="location.href = '{dcl_url_action controller=Environment action=Index}';" value="{$smarty.const.STR_CMMN_CANCEL|escape}">
			</div>
		</div>
	</fieldset>
</form>
{/block}
{block name=script}
<script type="text/javascript" src="vendor/bootstrapvalidator/js/bootstrapValidator.min.js"></script>
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
				active: {
					validators: {
						notEmpty: {
							message: "Active is required."
						}
					}
				},
				environment_name: {
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

		$("#environment_name").focus();
	});
</script>
{/block}