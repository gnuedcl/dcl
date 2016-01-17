{extends file="_Layout.tpl"}
{block name=title}{$TXT_FUNCTION|escape}{/block}
{block name=css}
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
{/block}
{block name=content}
<form class="form-horizontal" method="post" name="submitForm" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
			{if $VAL_ORGID}<input type="hidden" name="org_id" value="{$VAL_ORGID}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		{dcl_form_control id=active controlsize=10 label=$smarty.const.STR_CMMN_ACTIVE}
		<input type="checkbox" id="active" name="active" value="Y"{if $VAL_ACTIVE == 'Y'} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=name controlsize=10 label=$smarty.const.STR_CMMN_NAME required=true}
		{dcl_input_text id=name maxlength=50 value=$VAL_NAME}
		{/dcl_form_control}
		{dcl_form_control id="org_type_id" controlsize=10 label=Type required=true}
			<select id="org_type_id" name="org_type_id[]" class="form-control" multiple>
				{foreach item=typeItem key=typeItemID from=$orgTypes}
					<option value="{$typeItemID|escape}"{if $typeItem.selected == "true"} selected{/if}>{$typeItem.desc|escape}</option>
				{/foreach}
			</select>
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_OK}">
				<input class="btn btn-link" type="button" onclick="location.href = '{$URL_BACK}';" value="{$smarty.const.STR_CMMN_CANCEL}">
			</div>
		</div>
	</fieldset>
</form>
{/block}
{block name=script}
{dcl_validator_init}
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript">
	$(function() {
		$("#name").focus();
		$("#content").find("select").select2({ minimumResultsForSearch: 10 });
	});

	function validateAndSubmitForm(form)
	{
		var aValidators = new Array(new ValidatorString(form.elements["name"], "{$smarty.const.STR_CMMN_NAME|escape:"javascript"}"));

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