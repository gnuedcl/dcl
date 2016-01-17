{extends file="_Layout.tpl"}
{block name=title}{$TXT_TITLE|escape}{/block}
{block name=css}
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
{/block}
{block name=content}
<form class="form-horizontal" name="reassign" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$menuAction}">
	{if is_array($selected)}{section loop=$section name=item}<input type="hidden" name="selected[]" value="{$section[item]}">{/section}{/if}
	{if $jcn}<input type="hidden" name="jcn" value="{$jcn}">{/if}
	{if $seq}<input type="hidden" name="seq" value="{$seq}">{/if}
	{if $return_to}<input type="hidden" name="return_to" value="{$return_to|escape}">{/if}
	{if $project}<input type="hidden" name="project" value="{$project|escape}">{/if}
	<fieldset>
		<legend>{$TXT_TITLE|escape}</legend>
		{dcl_form_control id=responsible controlsize=4 label=$smarty.const.STR_WO_RESPONSIBLE required=true}
		{$CMB_RESPONSIBLE}
		{/dcl_form_control}
		{dcl_form_control id=deadlineon controlsize=2 label=$smarty.const.STR_WO_DEADLINE required=true}
		{dcl_input_date id=deadlineon value=$VAL_DEADLINEON}
		{/dcl_form_control}
		{dcl_form_control id=eststarton controlsize=2 label=$smarty.const.STR_WO_ESTSTART required=true}
		{dcl_input_date id=eststarton value=$VAL_ESTSTARTON}
		{/dcl_form_control}
		{dcl_form_control id=estendon controlsize=2 label=$smarty.const.STR_WO_ESTEND required=true}
		{dcl_input_date id=estendon value=$VAL_ESTENDON}
		{/dcl_form_control}
		{dcl_form_control id=esthours controlsize=2 label=$smarty.const.STR_WO_ESTHOURS required=true}
		{dcl_input_text id=esthours maxlength=6 value=$VAL_ESTHOURS}
		{/dcl_form_control}
		{dcl_form_control id=etchours controlsize=2 label=$smarty.const.STR_WO_ETCHOURS required=true}
		{dcl_input_text id=etchours maxlength=6 value=$VAL_ETCHOURS}
		{/dcl_form_control}
		{dcl_form_control id=priority controlsize=4 label=$smarty.const.STR_WO_PRIORITY required=true}
		{$CMB_PRIORITY}
		{/dcl_form_control}
		{dcl_form_control id=severity controlsize=4 label=$smarty.const.STR_WO_SEVERITY required=true}
		{$CMB_SEVERITY}
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE|escape}">
				<input class="btn btn-link" type="reset" value="{$smarty.const.STR_CMMN_RESET|escape}">
			</div>
		</div>
	</fieldset>
</form>
{/block}
{block name=script}
{dcl_validator_init}
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript">
	$(function() {
		$("input[data-input-type=date]").datepicker();
		$("#content").find("select").select2({ minimumResultsForSearch: 10 });
	});

	function validateAndSubmitForm(form)
	{
		var aValidators = [
			new ValidatorInteger(form.elements["responsible"], "{$smarty.const.STR_WO_RESPONSIBLE}", true),
			new ValidatorSelection(form.elements["priority"], "{$smarty.const.STR_WO_PRIORITY}", true),
			new ValidatorSelection(form.elements["severity"], "{$smarty.const.STR_WO_SEVERITY}", true),
			new ValidatorDecimal(form.elements["esthours"], "{$smarty.const.STR_WO_ESTHOURS}", true),
			new ValidatorDecimal(form.elements["etchours"], "{$smarty.const.STR_WO_ETCHOURS}", true),
			new ValidatorDate(form.elements["deadlineon"], "{$smarty.const.STR_WO_DEADLINE}", true),
			new ValidatorDate(form.elements["eststarton"], "{$smarty.const.STR_WO_ESTSTART}", true),
			new ValidatorDate(form.elements["estendon"], "{$smarty.const.STR_WO_ESTEND}", true)
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
{/block}