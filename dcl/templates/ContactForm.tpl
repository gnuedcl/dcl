{extends file="_Layout.tpl"}
{block name=title}[{$Contact->contact_id}] {$Contact->first_name|escape} {$Contact->last_name|escape}{/block}
{block name=css}
	<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
	<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
{/block}
{block name=content}
<form class="form-horizontal" method="post" name="submitForm" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
	{if $VAL_CONTACTID}<input type="hidden" name="contact_id" value="{$VAL_CONTACTID}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		{dcl_form_control id=active controlsize=10 label=$smarty.const.STR_CMMN_ACTIVE}
			<input type="checkbox" id="active" name="active" value="Y"{if $VAL_ACTIVE == 'Y'} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=first_name controlsize=10 label=$smarty.const.STR_CMMN_FIRSTNAME required=true}
		{dcl_input_text id=first_name maxlength=50 value=$VAL_FIRSTNAME}
		{/dcl_form_control}
		{dcl_form_control id=middle_name controlsize=10 label="Middle Name" required=false}
		{dcl_input_text id=middle_name maxlength=50 value=$VAL_MIDDLENAME}
		{/dcl_form_control}
		{dcl_form_control id=last_name controlsize=10 label=$smarty.const.STR_CMMN_LASTNAME required=true}
		{dcl_input_text id=last_name maxlength=50 value=$VAL_LASTNAME}
		{/dcl_form_control}
		{dcl_form_control id=contact_type_id controlsize=10 label="Type"}
			<select class="form-control" id="contact_type_id" name="contact_type_id[]" multiple>
				{foreach item=typeItem key=typeItemID from=$contactTypes}
					<option value="{$typeItemID|escape}"{if $typeItem.selected == "true"} selected{/if}>{$typeItem.desc|escape}</option>
				{/foreach}
			</select>
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
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript">
	$(function() {
		$("#first_name").focus();
		$("#content").find("select").select2({ minimumResultsForSearch: 10 });
	});

function validateAndSubmitForm(form)
{
	var aValidators = [
		new ValidatorString(form.elements["first_name"], "First Name"),
		new ValidatorString(form.elements["last_name"], "Last Name")
	];

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