{extends file="_Layout.tpl"}
{block name=title}{$TXT_TITLE|escape}{/block}
{block name=css}
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
{/block}
{block name=content}
<form class="form form-horizontal" name="reassign" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$menuAction}">
	{if $VAL_VIEW}{$VAL_VIEW}{/if}
	{section loop=$selected name=item}<input type="hidden" name="selected[]" value="{$selected[item]}">{/section}
	{if $jcn}<input type="hidden" name="jcn" value="{$jcn}">{/if}
	{if $seq}<input type="hidden" name="seq" value="{$seq}">{/if}
	{if $return_to}<input type="hidden" name="return_to" value="{$return_to|escape}">{/if}
	{if $project}<input type="hidden" name="project" value="{$project|escape}">{/if}
	<fieldset>
		<legend>{$TXT_TITLE|escape}</legend>
		{dcl_form_control id=responsible controlsize=4 label=$smarty.const.STR_WO_RESPONSIBLE}
		{$CMB_RESPONSIBLE}
		{/dcl_form_control}
		{dcl_form_control id=priority controlsize=4 label=$smarty.const.STR_WO_PRIORITY}
		{$CMB_PRIORITY}
		{/dcl_form_control}
		{dcl_form_control id=severity controlsize=4 label=$smarty.const.STR_WO_SEVERITY}
		{$CMB_SEVERITY}
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input type="button" class="btn btn-primary" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
				<input type="reset" class="btn btn-link" value="{$smarty.const.STR_CMMN_RESET}">
			</div>
		</div>
	</fieldset>
</form>
{/block}
{block name=script}
{dcl_validator_init}
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#content").find("select").select2({ minimumResultsForSearch: 10 });
	});

	function validateAndSubmitForm(form)
	{

		var aValidators = new Array(
				new ValidatorInteger(form.elements["responsible"], "{$smarty.const.STR_WO_RESPONSIBLE}", true)
		);

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