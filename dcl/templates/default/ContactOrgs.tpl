{dcl_selector_init}
{dcl_validator_init}
<script language="JavaScript">

function validateAndSubmitForm(form)
{

	var aValidators = new Array(
			new ValidatorSelector(form.elements["org_id"], "{$smarty.const.STR_CMMN_ORGANIZATION}")
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
<form class="styled" name="theForm" method="post" action="{$smarty.const.DCL_WWW_ROOT}main.php">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
	<input type="hidden" name="contact_id" value="{$VAL_CONTACTID}">
	<fieldset>
		<legend>{$TXT_TITLE}</legend>
		<div class="help">{$VAL_FIRSTNAME|escape} {$VAL_LASTNAME|escape}</div>
		<div class="required">
			<label for="org_id">{$smarty.const.STR_CMMN_ORGANIZATION}:</label>
			{dcl_selector_org name="org_id" value="$VAL_ORGID" decoded="$VAL_ORGNAME" multiple="Y"}
		</div>
		<div class="noinput">
			<div id="div_org_id" style="width: 100%;"><script language="JavaScript">render_a_org_id();</script></div>
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="button" onclick="location.href = '{$URL_BACK}';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>
