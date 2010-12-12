<!-- $Id: htmlConfig.tpl,v 1.4.2.2.2.8 2003/10/20 03:45:50 mdean Exp $ -->
{dcl_calendar_init}
<script language="JavaScript">
	var calDateFormat = String("{$VAL_JSDATEFORMAT}").replace("y", "yyyy");
</script>
<script language="JavaScript" src="js/validator.js"></script>

<script language="JavaScript">
{literal}
function validateAndSubmitForm(form)
{
{/literal}
	var aValidators = new Array(
			new ValidatorDate(form.elements["product_version_target_date"], "Target Date"),
			new ValidatorDate(form.elements["product_version_actual_date"], "Actual Date")
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
<form class="styled" name="NewAction" method="post" action="{$URL_MAIN_PHP}" enctype="multipart/form-data">
	<input type="hidden" name="product_id" value="{$VAL_PRODUCTID}">
	<input type="hidden" name="product_version_id" value="{$VAL_VERSIONID}">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
	<fieldset>
		<legend>{$TXT_BM_ADD_RELEASE|escape}</legend>
		<div class="required">
			<label>{$TXT_BM_PRODUCT}:</label>
			<input type="text" size="50" value="{$VAL_PRODUCTNAME}" readonly="true">
		</div>
		<div class="required">
			<label for="active">{$smarty.const.STR_CMMN_ACTIVE}:</label>
			<input type="checkbox" id="active" name="active" value="Y"{if $VAL_ACTIVE == 'Y'} checked{/if}>
		</div>
		<div> 
			<label for="product_version_text">{$TXT_BM_RELEASE_ALIAS_TITLE}:</label>
			<input type="text" name="product_version_text" size="50" maxlength="50" value="{$VAL_VERSIONTEXT}">
		</div>
		<div>
			<label for="product_version_descr">{$TXT_BM_RELEASEDATE_DESC}:</label>
			<input type="text" name="product_version_descr" size="50" maxlength="100" value="{$VAL_VERSIONDESCR}">
		</div>
		<div>
			<label for="product_version_target_date">Target Date:</label>
			{dcl_calendar name="product_version_target_date" value="$VAL_VERSIONTARGETDATE"}
		</div>
		<div>
			<label for="product_version_actual_date">Actual Date:</label>
			{dcl_calendar name="product_version_actual_date" value="$VAL_VERSIONACTUALDATE"}
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE|escape}">
			<input type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=Product.DetailRelease&id={$VAL_PRODUCTID}';" value="{$smarty.const.STR_CMMN_CANCEL|escape}">
		</div>
	</fieldset>
</form>
