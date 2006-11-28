<!-- $Id: htmlWorkOrderTypeForm.tpl,v 1.1.1.1 2006/11/27 05:30:38 mdean Exp $ -->
{dcl_validator_init}
<script language="JavaScript">
{literal}
	function validateAndSubmitForm(form){
{/literal}
		if (checkString(form.elements["type_name"], "{$smarty.const.STR_CMMN_NAME}"))
{literal}
		{
			form.submit();
		}
	}
{/literal}
</script>
<form class="styled" method="post" action="{$URL_MAIN_PHP}">
{if $IS_EDIT}
	<input type="hidden" name="menuAction" value="htmlWorkOrderType.submitModify">
	<input type="hidden" name="wo_type_id" value="{$VAL_WO_TYPE_ID}">
	<fieldset>
		<legend>{$smarty.const.STR_WO_EDITWORKORDERTYPE}</legend>
{else}
	<input type="hidden" name="menuAction" value="htmlWorkOrderType.submitAdd">
	<fieldset>
		<legend>{$smarty.const.STR_WO_ADDWORKORDERTYPE}</legend>
{/if}
		<div>
			<label for="active">{$smarty.const.STR_CMMN_ACTIVE}:</label>
			{$CMB_ACTIVE}
		</div>
		<div>
			<label for="type_name">{$smarty.const.STR_CMMN_NAME}:</label>
			<input type="text" size="50" maxlength="50" name="type_name" value="{$VAL_NAME}">
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_OK}">
			<input type="button" onclick="location.href = '{$URL_BACK}';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>