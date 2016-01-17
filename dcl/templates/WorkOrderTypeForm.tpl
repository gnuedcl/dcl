{extends file="_Layout.tpl"}
{block name=title}{if $IS_EDIT}{$smarty.const.STR_WO_EDITWORKORDERTYPE|escape}{else}{$smarty.const.STR_WO_ADDWORKORDERTYPE|escape}{/if}{/block}
{block name=content}
<form class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
{if $IS_EDIT}
	<input type="hidden" name="menuAction" value="WorkOrderType.Update">
	<input type="hidden" name="wo_type_id" value="{$VAL_WO_TYPE_ID}">
	<fieldset>
		<legend>{$smarty.const.STR_WO_EDITWORKORDERTYPE|escape}</legend>
{else}
	<input type="hidden" name="menuAction" value="WorkOrderType.Insert">
	<fieldset>
		<legend>{$smarty.const.STR_WO_ADDWORKORDERTYPE|escape}</legend>
{/if}
		{dcl_form_control id=active controlsize=4 label=$smarty.const.STR_CMMN_ACTIVE required=true}
		{$CMB_ACTIVE}
		{/dcl_form_control}
		{dcl_form_control id=type_name controlsize=4 label=$smarty.const.STR_CMMN_NAME required=true}
		{dcl_input_text id=type_name maxlength=50 value=$VAL_NAME}
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
				<input class="btn btn-link" type="button" onclick="location.href = '{$URL_BACK}';" value="{$smarty.const.STR_CMMN_CANCEL}">
			</div>
		</div>
	</fieldset>
</form>
{/block}
{block name=script}
{dcl_validator_init}
<script type="text/javascript">
	$(function() {
		$("#type_name").focus();
	});

	function validateAndSubmitForm(form){

		var aValidators = new Array(new ValidatorString(form.elements["type_name"], "{$smarty.const.STR_CMMN_NAME}"));

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
{/block}