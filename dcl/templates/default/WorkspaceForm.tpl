{dcl_validator_init}
{dcl_selector_init}
<script language="JavaScript">
function validateAndSubmitForm(form)
{literal}
{
{/literal}
	var aValidators = new Array(
			new ValidatorString(form.elements["name"], "{$smarty.const.STR_CMMN_NAME}")
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
	{if $VAL_ID}<input type="hidden" name="workspace_id" value="{$VAL_ID}">{/if}
	<fieldset>
		<legend>{$VAL_TITLE}</legend>
		<div class="required">
			<label for="name">{$smarty.const.STR_CMMN_NAME}:</label>
			<input type="text" size="50" maxlength="50" id="workspace_name" name="workspace_name" value="{$VAL_NAME|escape}">
		</div>
		<div class="required">
			<label for="active">{$smarty.const.STR_CMMN_ACTIVE}:</label>
			<input type="checkbox" id="active" name="active" value="Y"{if $VAL_ACTIVE == 'Y'} checked{/if}>
		</div>
	</fieldset>
	<fieldset>
		<legend>Products</legend>
		<div class="help">When a user switches to this workspace, all views will be limited to the products listed here.</div>
		<div>
			<label for="products">Products:</label>
			{dcl_selector_product name="products" value="$VAL_PRODUCTS" decoded="$VAL_PRODUCTNAMES" multiple="Y"}
		</div>
		<div class="noinput">
			<div id="div_products" style="width: 100%;"><script language="JavaScript">render_a_products();</script></div>
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=htmlWorkspaceBrowse.show';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>