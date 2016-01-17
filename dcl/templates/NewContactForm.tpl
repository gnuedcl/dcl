{extends file="_Layout.tpl"}
{block name=title}Add New Contact{/block}
{block name=css}
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
{/block}
{block name=content}
<form class="form-horizontal" name="submitForm" method="POST" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="htmlContactForm.submitAdd">
	{if $org_id}<input type="hidden" name="org_id" value="{$org_id}">{/if}
	{if $hideMenu}<input type="hidden" name="hideMenu" value="{$hideMenu}">{/if}
	{if $return_to}<input type="hidden" name="return_to" value="{$return_to|escape}">{/if}
	{if $fromBrowse}<input type="hidden" name="fromBrowse" value="{$fromBrowse|escape}">{/if}
	<fieldset>
		<legend>Add New Contact</legend>
		{dcl_form_control id=first_name controlsize=6 label="First Name" required=true}
		{dcl_input_text id=first_name maxlength=50 value=$VAL_FIRSTNAME}
		{/dcl_form_control}
		{dcl_form_control id=middle_name controlsize=6 label="Middle Name" required=false}
		{dcl_input_text id=middle_name maxlength=50 value=$VAL_MIDDLENAME}
		{/dcl_form_control}
		{dcl_form_control id=last_name controlsize=6 label="Last Name" required=true}
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
		<legend>Organization</legend>
		{dcl_form_control id=org_id controlsize=10 label="Organization" required=false}
		{dcl_selector_org name=org_id window_name=_dcl_selector_org_}
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<legend>Primary Address</legend>
		{dcl_form_control id=addr_type_id controlsize=4 label="Type"}
		{dcl_select_addresstype}
		{/dcl_form_control}
		{dcl_form_control id=add1 controlsize=6 label="Address"}
		{dcl_input_text id=add1 maxlength=50 value=$VAL_ADD1}
		{/dcl_form_control}
		{dcl_form_control id=add2 controlsize=6 label="Address 2"}
		{dcl_input_text id=add2 maxlength=50 value=$VAL_ADD2}
		{/dcl_form_control}
		{dcl_form_control id=city controlsize=6 label="City"}
		{dcl_input_text id=city maxlength=50 value=$VAL_CITY}
		{/dcl_form_control}
		{dcl_form_control id=state controlsize=7 label="State"}
		{dcl_input_text id=state maxlength=30 value=$VAL_STATE}
		{/dcl_form_control}
		{dcl_form_control id=zip controlsize=5 label="Zip"}
		{dcl_input_text id=zip maxlength=20 value=$VAL_ZIP}
		{/dcl_form_control}
		{dcl_form_control id=country controlsize=6 label="Country"}
		{dcl_input_text id=country maxlength=40 value=$VAL_COUNTRY}
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<legend>Primary Phone</legend>
		{dcl_form_control id=phone_type_id controlsize=4 label="Type"}
		{dcl_select_phonetype}
		{/dcl_form_control}
		{dcl_form_control id=phone_number controlsize=5 label="Number"}
		{dcl_input_text id=phone_number maxlength=30 value=$VAL_PHONE}
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<legend>Primary Email</legend>
		{dcl_form_control id=email_type_id controlsize=4 label="Type"}
		{dcl_select_emailtype}
		{/dcl_form_control}
		{dcl_form_control id=email_addr controlsize=10 label="Address"}
		{dcl_input_text id=email_addr maxlength=100 value=$VAL_EMAIL}
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<legend>Primary URL</legend>
		{dcl_form_control id=url_type_id controlsize=4 label="Type"}
		{dcl_select_urltype}
		{/dcl_form_control}
		{dcl_form_control id=url_addr controlsize=10 label="URL"}
		{dcl_input_text id=url_addr maxlength=150 value=$VAL_URL}
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
{dcl_selector_init}
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
			new ValidatorString(form.elements["first_name"], "{$VAL_FIRSTNAME|escape}"),
			new ValidatorString(form.elements["last_name"], "{$VAL_LASTNAME|escape}"),
			new ValidatorInteger(form.elements["org_id"], "Organization", true)
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