{extends file="_Layout.tpl"}
{block name=title}{$TXT_FUNCTION|escape}{/block}
{block name=css}
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
{/block}
{block name=content}
<form class="form-horizontal" name="submitForm" method="POST" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
	{dcl_anti_csrf_token}
	{if $org_id}<input type="hidden" name="org_id" value="{$org_id}">{/if}
	{if $hideMenu}<input type="hidden" name="hideMenu" value="{$hideMenu}">{/if}
	{if $return_to}<input type="hidden" name="return_to" value="{$return_to|escape}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		{dcl_form_control id=name controlsize=5 label=$smarty.const.STR_CMMN_NAME required=true}
			<input class="form-control" type="text" maxlength="50" name="name" value="{$VAL_NAME|escape}">
		{/dcl_form_control}
		{dcl_form_control id=name controlsize=5 label="Alias"}
			<input class="form-control" type="text" name="alias" id="alias" maxlength="50" value="{$VAL_ALIAS|escape}">
		{/dcl_form_control}
		{dcl_form_control id=org_type_id controlsize=10 label="Type"}
			<select class="form-control" multiple name="org_type_id[]" id="org_type_id">
				{foreach item=typeItem key=typeItemID from=$orgTypes}
					<option value="{$typeItemID}"{if $typeItem.selected == "true"} selected{/if}>{$typeItem.desc|escape}</option>
				{/foreach}
			</select>
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<legend>Primary Address</legend>
		{dcl_form_control id=addr_type_id controlsize=4 label="Type"}
		{$CMB_ADDRTYPE}
		{/dcl_form_control}
		{dcl_form_control id=add1 controlsize=5 label="Address"}
			<input type="text" name="add2" id="add1" class="form-control" maxlength="50" value="{$VAL_ADD1|escape}">
		{/dcl_form_control}
		{dcl_form_control id=add1 controlsize=5 label="Address 2"}
			<input type="text" name="add2" id="add2" class="form-control" maxlength="50" value="{$VAL_ADD2|escape}">
		{/dcl_form_control}
		{dcl_form_control id=city controlsize=5 label="City"}
			<input type="text" name="city" id="city" class="form-control" maxlength="50" value="{$VAL_CITY|escape}">
		{/dcl_form_control}
		{dcl_form_control id=state controlsize=3 label="State"}
			<input type="text" name="state" id="state" class="form-control" maxlength="30" value="{$VAL_STATE|escape}">
		{/dcl_form_control}
		{dcl_form_control id=zip controlsize=3 label="Zip"}
			<input type="text" name="zip" id="zip" class="form-control" maxlength="20" value="{$VAL_ZIP|escape}">
		{/dcl_form_control}
		{dcl_form_control id=country controlsize=3 label="Country"}
			<input type="text" name="country" id="country" class="form-control" maxlength="40" value="{$VAL_COUNTRY|escape}">
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<legend>Primary Phone</legend>
		{dcl_form_control id=phone_type_id controlsize=3 label="Type"}
		{$CMB_PHONETYPE}
		{/dcl_form_control}
		{dcl_form_control id=phone_number controlsize=3 label="Number"}
			<input type="text" name="phone_number" id="phone_number" class="form-control" maxlength="30" value="{$VAL_PHONE|escape}">
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<legend>Primary Email</legend>
		{dcl_form_control id=email_type_id controlsize=3 label="Type"}
		{$CMB_EMAILTYPE}
		{/dcl_form_control}
		{dcl_form_control id=email_addr controlsize=5 label="Address"}
			<input type="text" name="email_addr" id="email_addr" class="form-control" maxlength="100" value="{$VAL_EMAIL|escape}">
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<legend>Primary URL</legend>
		{dcl_form_control id=url_type_id controlsize=3 label="Type"}
		{$CMB_URLTYPE}
		{/dcl_form_control}
		{dcl_form_control id=url_addr controlsize=10 label="URL"}
			<input type="text" name="url_addr" id="url_addr" class="form-control" maxlength="100" value="{$VAL_URL|escape}">
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input class="btn btn-primary" type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_OK|escape}">
				<input class="btn btn-link" type="button" onclick="location.href = '{$URL_BACK}';" value="{$smarty.const.STR_CMMN_CANCEL|escape}">
			</div>
		</div>
	</fieldset>
</form>
{/block}
{block name=script}
{dcl_validator_init}
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript">
	function validateAndSubmitForm(form)
	{

		var aValidators = new Array(
				new ValidatorString(form.elements["name"], "{$TXT_NAME}")
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

	$(document).ready(function() {
		$("#name").focus();
		$("#content").find("#org_type_id").select2({ minimumResultsForSearch: 10 });
	});

	function validateAndSubmit(f)
	{
		f.submit();
	}
</script>
{/block}