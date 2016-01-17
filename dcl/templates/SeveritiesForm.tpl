{extends file="_Layout.tpl"}
{block name=title}{$TXT_FUNCTION|escape}{/block}
{block name=content}
<form class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$menuAction}">
	{if $id}<input type="hidden" name="id" value="{$id}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		<p class="alert alert-info">{$smarty.const.STR_CMMN_ATTRIBUTENOTE|escape}</p>
		{dcl_form_control id=active controlsize=10 label=$smarty.const.STR_SEV_ACTIVE required=true}
		{$CMB_ACTIVE}
		{/dcl_form_control}
		{dcl_form_control id=short controlsize=4 label=$smarty.const.STR_SEV_SHORT required=true}
		{dcl_input_text id=short maxlength=10 value=$VAL_SHORT|trim}
		{/dcl_form_control}
		{dcl_form_control id=name controlsize=4 label=$smarty.const.STR_CMMN_NAME required=true}
		{dcl_input_text id=name maxlength=20 value=$VAL_NAME}
		{/dcl_form_control}
		{dcl_form_control id=weight controlsize=4 label=$smarty.const.STR_SEV_WEIGHT required=true}
		{dcl_input_text id=weight maxlength=11 value=$VAL_WEIGHT}
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
				<input class="btn btn-link" type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=Severity.Index';" value="{$smarty.const.STR_CMMN_CANCEL}">
			</div>
		</div>
	</fieldset>
</form>
{/block}
{block name=script}
{dcl_validator_init}
<script type="text/javascript">
	$(function() {
		$("#short").focus();
	});

	function validateAndSubmitForm(form)
	{
		var aValidators = [
			new ValidatorString(form.elements["short"], "{$smarty.const.STR_SEV_SHORT}"),
			new ValidatorString(form.elements["name"], "{$smarty.const.STR_CMMN_NAME}"),
			new ValidatorSelection(form.elements["weight"], "{$smarty.const.STR_SEV_WEIGHT}")
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