<!-- $Id$ -->
{dcl_validator_init}
<script language="JavaScript">
function validateAndSubmitForm(form)
{literal}
{
{/literal}
	var aValidators = new Array(
			new ValidatorString(form.elements["short"], "{$smarty.const.STR_PRIO_SHORT}"),
			new ValidatorString(form.elements["name"], "{$smarty.const.STR_CMMN_NAME}"),
			new ValidatorSelection(form.elements["weight"], "{$smarty.const.STR_PRIO_WEIGHT}")
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
	{if $id}<input type="hidden" name="id" value="{$id}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION}</legend>
		<div class="help">{$smarty.const.STR_CMMN_ATTRIBUTENOTE|escape}</div>
		<div class="required">
			<label for="active">{$smarty.const.STR_PRIO_ACTIVE}:</label>
			{$CMB_ACTIVE}
		</div>
		<div class="required">
			<label for="short">{$smarty.const.STR_PRIO_SHORT}:</label>
			<input type="text" size="10" maxlength="10" id="short" name="short" value="{$VAL_SHORT|escape}">
		</div>
		<div class="required">
			<label for="name">{$smarty.const.STR_PRIO_NAME}:</label>
			<input type="text" size="20" maxlength="20" id="name" name="name" value="{$VAL_NAME|escape}">
		</div>
		<div class="required">
			<label for="weight">{$smarty.const.STR_PRIO_WEIGHT}:</label>
			<input type="text" size="3" maxlength="11" id="weight" name="weight" value="{$VAL_WEIGHT|escape}">
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=boPriorities.showall';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>