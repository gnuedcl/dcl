<!-- $Id: htmlConfig.tpl,v 1.4.2.2.2.8 2003/10/20 03:45:50 mdean Exp $ -->
{dcl_validator_init}
<script language="JavaScript">
{literal}
function validateAndSubmitForm(form)
{
{/literal}
	var aValidators = new Array(
			new ValidatorString(form.elements["product_build_descr"], "{$smarty.const.STR_BM_RELEASEDATE_DESC}")
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
<form class="styled" name="theForm" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="boBuildManager.GetBuildInfoSubmit">
	<input type="hidden" name="which" value="{$VAL_WHICH}">
	<input type="hidden" name="product_id" value="{$VAL_PRODUCTID}">
	<input type="hidden" name="product_version_id" value="{$VAL_VERSIONID}">
	<input type="hidden" name="product_build_id" value="{$VAL_BUILDID}">
	<input type="hidden" name="init" value="{$HID_INIT}">
	<fieldset>
		<legend>{if $VAL_VERSIONID}{$smarty.const.STR_BM_MOD_BUILD}{else}{$smarty.const.STR_BM_ADD_BUILD|escape}{/if}</legend>
		<div class="required">
			<label for="product_name">{$smarty.const.STR_BM_PRODUCT|escape}:</label>
			<input type="text" readonly="true" value="{$VAL_PRODUCT|escape}">
		</div>
		<div class="required">
			<label for="product_name">{$smarty.const.STR_BM_VERSION|escape}:</label>
			<input type="text" readonly="true" value="{$VAL_VERSION|escape}">
		</div>
		<div class="required">
			<label for="product_build_descr">{$smarty.const.STR_BM_RELEASEDATE_DESC|escape}:</label>
			<input type="text" id="product_build_descr" name="product_build_descr" size="50" maxlength="100" value="{$VAL_BM_BUILDNAME|escape}">
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=Product.DetailBuild&product_version_id={$VAL_VERSIONID}&product_id={$VAL_PRODUCTID}';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>
<script language="javascript">{literal}
	var fnOnLoad = window.onload;
	if (fnOnLoad)
		window.onload = function() { fnOnLoad(); document.forms.theForm.elements.product_build_descr.focus(); }
	else
		window.onload = function() { document.forms.theForm.elements.product_build_descr.focus(); }
{/literal}</script>