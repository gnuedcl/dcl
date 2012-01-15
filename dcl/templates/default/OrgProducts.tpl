{dcl_selector_init}
{dcl_validator_init}
<script language="JavaScript">
{literal}
function validateAndSubmitForm(form)
{
	form.submit();
}
{/literal}
</script>
<form class="styled" name="theForm" method="post" action="{$smarty.const.DCL_WWW_ROOT}main.php">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
	<input type="hidden" name="org_id" value="{$VAL_ORGID}">
	<fieldset>
		<legend>{$TXT_TITLE}</legend>
		<div class="help">{$VAL_ORGNAME|escape}</div>
		<div class="required">
			<label for="product_id">{$smarty.const.STR_CMMN_PRODUCTS}Products:</label>
			{dcl_selector_product name="product_id" value="$VAL_PRODUCTID" decoded="$VAL_PRODUCTNAME" multiple="Y"}
		</div>
		<div class="noinput">
			<div id="div_product_id" style="width: 100%;"><script language="JavaScript">render_a_product_id();</script></div>
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="button" onclick="location.href = '{$URL_BACK}';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>