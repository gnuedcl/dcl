<!-- $Id: htmlConfig.tpl,v 1.4.2.2.2.8 2003/10/20 03:45:50 mdean Exp $ -->
{dcl_validator_init}
{dcl_calendar_init}
<script language="JavaScript">
{literal}
function validateAndSubmitForm(form)
{
{/literal}
	var aValidators = new Array(
			new ValidatorDate(form.elements["product_version_target_date"], "{$smarty.const.STR_BM_RELEASEDATE}", true),
			new ValidatorString(form.elements["product_version_text"], "{$smarty.const.STR_BM_RELEASE_ALIAS_TITLE}"),
			new ValidatorString(form.elements["product_version_descr"], "{$smarty.const.STR_BM_RELEASEDATE_DESC}")
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
	<input type="hidden" name="product_id" value="{$VAL_PRODUCTID}">
	<fieldset>
		<legend>{$smarty.const.STR_BM_ADD_RELEASE|escape}</legend>
		<div class="required">
			<label for="product_name">{$smarty.const.STR_BM_PRODUCT|escape}:</label>
			<input type="text" readonly="true" value="{$VAL_PRODUCTNAME|escape}">
		</div>
		<div class="required">
			<label for="short">{$smarty.const.STR_BM_RELEASE_ALIAS_TITLE|escape}:</label>
			<input type="text" name="product_version_text" size="50" maxlength="50" value="{$VAL_VERSIONTEXT|escape}">
		</div>
		<div class="required">
			<label for="product_version_descr">{$smarty.const.STR_BM_RELEASEDATE_DESC|escape}:</label>
			<input type="text" name="product_version_descr" size="50" maxlength="100" value="{$VAL_VERSIONDESCR|escape}">
		</div>
		<div class="required">
			<label for="product_version_target_date">{$smarty.const.STR_BM_RELEASEDATE|escape}:</label>
			{dcl_calendar name="product_version_target_date" value="$VAL_VERSIONTARGETDATE"}
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE|escape}">
			<input type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=Department.Index';" value="{$smarty.const.STR_CMMN_CANCEL|escape}">
		</div>
	</fieldset>
</form>