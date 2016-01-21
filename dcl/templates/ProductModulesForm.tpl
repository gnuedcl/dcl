{extends file="_Layout.tpl"}
{block name=title}{$TXT_FUNCTION|escape}{/block}
{block name=content}
<form class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$menuAction}">
	<input type="hidden" name="product_id" value="{$product_id}">
	{if $product_module_id}<input type="hidden" name="product_module_id" value="{$product_module_id}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		{dcl_form_control id=active controlsize=2 label=$smarty.const.STR_CMMN_ACTIVE}
		{$CMB_ACTIVE}
		{/dcl_form_control}
		{dcl_form_control id=module_name controlsize=5 label=$smarty.const.STR_CMMN_NAME required=true}
		{dcl_input_text id=module_name maxlength=50 value=$VAL_NAME}
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
				<input class="btn btn-link" type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=htmlProductModules.PrintAll&product_id={$product_id}';" value="{$smarty.const.STR_CMMN_CANCEL}">
			</div>
		</div>
	</fieldset>
</form>
{/block}
{block name=script}
{dcl_validator_init}
<script type="text/javascript">
	$(function() {
		$("#module_name").focus();
	});

function validateAndSubmitForm(form)
{
	var aValidators = new Array(
			new ValidatorString(form.elements["module_name"], "{$smarty.const.STR_CMMN_NAME}")
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