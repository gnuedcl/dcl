{dcl_validator_init}
<script language="JavaScript">
function validateAndSubmitForm(form)
{literal}
{
{/literal}
	var aValidators = new Array(
			new ValidatorString(form.elements["short"], "{$smarty.const.STR_PROD_SHORT}"),
			new ValidatorString(form.elements["name"], "{$smarty.const.STR_CMMN_NAME}"),
			new ValidatorSelection(form.elements["reportto"], "{$smarty.const.STR_PROD_REPORTTO}"),
			new ValidatorSelection(form.elements["ticketsto"], "{$smarty.const.STR_PROD_TICKETSTO}"),
			new ValidatorSelection(form.elements["wosetid"], "{$smarty.const.STR_PROD_WOATTRIBUTESET}"),
			new ValidatorSelection(form.elements["tcksetid"], "{$smarty.const.STR_PROD_TICKETATTRIBUTESET}")
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
		<div class="required">
			<label for="active">{$smarty.const.STR_CMMN_ACTIVE}:</label>
			{$CMB_ACTIVE}
		</div>
		<div class="required">
			<label for="short">{$smarty.const.STR_PROD_SHORT}:</label>
			<input type="text" size="10" maxlength="10" id="short" name="short" value="{$VAL_SHORT|escape|trim}">
		</div>
		<div class="required">
			<label for="name">{$smarty.const.STR_CMMN_NAME}:</label>
			<input type="text" size="50" maxlength="50" id="name" name="name" value="{$VAL_NAME|escape}">
		</div>
		<div class="required">
			<label for="reportto">{$smarty.const.STR_PROD_REPORTTO}:</label>
			{$CMB_REPORTTO}
		</div>
		<div class="required">
			<label for="ticketsto">{$smarty.const.STR_PROD_TICKETSTO}:</label>
			{$CMB_TICKETSTO}
		</div>
		<div class="required">
			<label for="wosetid">{$smarty.const.STR_PROD_WOATTRIBUTESET}:</label>
			{$CMB_WOATTRIBUTESET}
		</div>
		<div class="required">
			<label for="tcksetid">{$smarty.const.STR_PROD_TICKETATTRIBUTESET}:</label>
			{$CMB_TCKATTRIBUTESET}
		</div>
		<div class="required">
			<label for="is_versioned">Versioned:</label>
			{$CMB_ISVERSIONED}
		</div>
		<div class="required">
			<label for="is_project_required">Project Required:</label>
			{$CMB_ISPROJECTREQUIRED}
		</div>
		<div class="required">
			<label for="is_public">{$smarty.const.STR_CMMN_PUBLIC}:</label>
			{$CMB_ISPUBLIC}
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			{if $id}<input type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=Product.Detail&id={$id}';" value="{$smarty.const.STR_CMMN_CANCEL}">
			{else}<input type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=Product.Index';" value="{$smarty.const.STR_CMMN_CANCEL}">
			{/if}
		</div>
	</fieldset>
</form>