{dcl_validator_init}
<script language="JavaScript">

function validateAndSubmitForm(form)
{

	var aValidators = new Array(new ValidatorString(form.elements["name"], "{$smarty.const.STR_CMMN_NAME}"));

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
<form class="styled" method="post" name="submitForm" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
			{if $VAL_ORGID}<input type="hidden" name="org_id" value="{$VAL_ORGID}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION}</legend>
		<div class="required">
			<label for="active">{$smarty.const.STR_CMMN_ACTIVE}:</label>
			<input type="checkbox" id="active" name="active" value="Y"{if $VAL_ACTIVE == 'Y'} checked{/if}>
		</div>
		<div class="required">
			<label for="">{$smarty.const.STR_CMMN_NAME}:</label>
			<input type="text" size="50" maxlength="50" id="name" name="name" value="{$VAL_NAME|escape}">
		</div>
		<div>
			<fieldset>
				<legend>Type</legend>
				<div>
					{foreach item=typeItem key=typeItemID from=$orgTypes}
						{strip}
						<span style="white-space: nowrap;">
						<input type="checkbox" name="org_type_id[]" id="org_type_id_{$typeItemID}" value="{$typeItemID}"{if $typeItem.selected == "true"} checked{/if}>
						<label id="org_type_id_{$typeItemID}_label" for="org_type_id_{$typeItemID}">{$typeItem.desc}</label>&nbsp;
						</span>
						{/strip}
					{/foreach}
				</div>
			</fieldset>
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_OK}">
			<input type="button" onclick="location.href = '{$URL_BACK}';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>
<script language="JavaScript">
if (document.forms["submitForm"].elements["name"])
	document.forms["submitForm"].elements["name"].focus();
</script>