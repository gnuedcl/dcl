{dcl_validator_init}
<script language="JavaScript">
function validateAndSubmitForm(form)
{literal}
{
{/literal}
	var aValidators = new Array(
			new ValidatorString(form.elements["module_name"], "{$smarty.const.STR_CMMN_NAME}")
		);
{literal}
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
{/literal}
</script>
<form class="styled" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$menuAction}">
	<input type="hidden" name="product_id" value="{$product_id}">
	{if $product_module_id}<input type="hidden" name="product_module_id" value="{$product_module_id}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION}</legend>
		<div class="required">
			<label for="active">{$smarty.const.STR_CMMN_ACTIVE}:</label>
			{$CMB_ACTIVE}
		</div>
		<div class="required">
			<label for="module_name">{$smarty.const.STR_CMMN_NAME}:</label>
			<input type="text" size="50" maxlength="50" id="module_name" name="module_name" value="{$VAL_NAME|escape}">
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=htmlProductModules.PrintAll&product_id={$product_id}';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>
<script language="JavaScript">
if (document.getElementById("module_name"))
	document.getElementById("module_name").focus();
</script>