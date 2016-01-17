{extends file="_Layout.tpl"}
{block name=title}{if $IS_EDIT}{$smarty.const.STR_USR_EDIT|escape}{else}{$smarty.const.STR_USR_ADD|escape}{/if}{/block}
{block name=css}
	<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
	<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
{/block}
{block name=content}
{dcl_validator_errors errors=$ERRORS}
<form class="form-horizontal" name="userInputForm" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{if $IS_EDIT}Personnel.Update{else}Personnel.Insert{/if}">
	{if $IS_EDIT}<input type="hidden" name="id" value="{$VAL_PERSONNELID}">{/if}
	<fieldset>
		<legend>{if $IS_EDIT}{$smarty.const.STR_USR_EDIT|escape}{else}{$smarty.const.STR_USR_ADD|escape}{/if}</legend>
		{dcl_form_control id=short controlsize=3 label="Username" required=true}
		{dcl_input_text id=short maxlength=25 value=$VAL_SHORT}
		{/dcl_form_control}
		{dcl_form_control id=active controlsize=10 label=$smarty.const.STR_USR_ACTIVE}
			<input type="checkbox" name="active" id="active" value="Y"{if $VAL_ACTIVE == "Y"} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=pwd_change_required controlsize=10 label="Force Password Change On Next Login"}
			<input type="checkbox" name="pwd_change_required" id="pwd_change_required" value="Y"{if $VAL_PWDCHANGEREQUIRED == "Y"} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=is_locked controlsize=10 label="Account Locked"}
			<input type="checkbox" name="is_locked" id="is_locked" value="Y"{if $VAL_ISLOCKED == "Y"} checked{/if}>
			{if $VAL_ISLOCKED == "Y" && $VAL_LOCKEXPIRATION}Expires: {$VAL_LOCKEXPIRATION|escape}{/if}
		{/dcl_form_control}
		{dcl_form_control id=reportto controlsize=4 label=$smarty.const.STR_USR_REPORTTO required=true}
		{dcl_select_personnel name=reportto default=$VAL_REPORTTO}
		{/dcl_form_control}
		{dcl_form_control id=department controlsize=4 label=$smarty.const.STR_USR_DEPARTMENT required=true}
		{dcl_select_department name=department default=$VAL_DEPARTMENT}
		{/dcl_form_control}
		{dcl_form_control id=contact_id controlsize=10 label=$smarty.const.STR_CMMN_CONTACT required=true}
		{dcl_selector_contact name=contact_id value=$VAL_CONTACTID decoded=$VAL_CONTACTNAME}
		{/dcl_form_control}
{if $IS_EDIT == false}
	{dcl_form_control id=pwd controlsize=5 label=$smarty.const.STR_USR_PASSWORD required=true}
	{dcl_input_text id=pwd value=""}
	{/dcl_form_control}
	{dcl_form_control id=pwd2 controlsize=5 label=$smarty.const.STR_USR_CONFIRMPWD required=true}
	{dcl_input_text id=pwd2 value=""}
	{/dcl_form_control}
{/if}
	{dcl_form_control id=roles controlsize=10 label="Roles"}
	<select class="form-control" multiple id="roles" name="roles[]">
		{foreach item=roleItem key=roleName from=$Roles name=role}
			<option value="{$roleItem.role_id}"{if $roleItem.selected == "true"} selected{/if}>{$roleName|escape}</option>
		{/foreach}
	</select>
	{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
				<input class="btn btn-link" type="button" onclick="location.href='{$URL_MAIN_PHP}?menuAction=Personnel.Index&filterActive=Y';" value="{$smarty.const.STR_CMMN_CANCEL}">
			</div>
		</div>
	</fieldset>
</form>
{/block}
{block name=script}
{dcl_selector_init}
{dcl_validator_init}
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript">
	$(function() {
		$("#short").focus();
		$("#content").find("select").select2({ minimumResultsForSearch: 10 });
	});

	function validateAndSubmitForm(form)
	{
		var aValidators = [
			new ValidatorString(form.elements["short"], "{$smarty.const.STR_USR_LOGIN}"),
			new ValidatorString(form.elements["pwd"], "{$smarty.const.STR_USR_PASSWORD}"),
			new ValidatorString(form.elements["pwd2"], "{$smarty.const.STR_USR_CONFIRMPWD}"),
			new ValidatorSelection(form.elements["reportto"], "{$smarty.const.STR_USR_REPORTTO}"),
			new ValidatorSelection(form.elements["department"], "{$smarty.const.STR_USR_DEPARTMENT}"),
			new ValidatorSelector(form.elements["contact_id"], "{$smarty.const.STR_CMMN_CONTACT}")
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

		if (form.elements["pwd"] && form.elements["pwd2"])
		{
			if (form.elements["pwd"].value != form.elements["pwd2"].value)
			{
				alert("The passwords do not match!  Please enter them again.");
				form.elements["pwd"].value = "";
				form.elements["pwd2"].value = "";
				form.elements["pwd"].focus();
				return;
			}
		}

		form.submit();
	}
</script>
{/block}