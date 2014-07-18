{dcl_validator_init}
<form class="form-horizontal" method="post" name="submitForm" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
	{if $VAL_ORGID}<input type="hidden" name="org_id" value="{$VAL_ORGID}">{/if}
	{if $VAL_ORGPHONEID}<input type="hidden" name="org_phone_id" value="{$VAL_ORGPHONEID}">{/if}
	{if $VAL_CONTACTID}<input type="hidden" name="contact_id" value="{$VAL_CONTACTID}">{/if}
	{if $VAL_CONTACTPHONEID}<input type="hidden" name="contact_phone_id" value="{$VAL_CONTACTPHONEID}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		{dcl_form_control controlsize=10 label=$smarty.const.STR_CMMN_NAME}
		<span class="form-control">{if $VAL_ORGID}{$VAL_ORGNAME|escape}{else}{$VAL_FIRSTNAME|escape} {$VAL_LASTNAME|escape}{/if}</span>
		{/dcl_form_control}
		{dcl_form_control id=preferred controlsize=10 label="Primary"}
		{if $VAL_PREFERRED == "Y"}<span>This is the preferred phone number.  If you do not want this to be the preferred phone number, select another phone number as the preferred number.</span>
		{else}<input type="checkbox" id="preferred" name="preferred" value="Y">{/if}
		{/dcl_form_control}
		{dcl_form_control id=phone_type_id controlsize=4 label="Type" required=true}
		{$CMB_PHONETYPE}
		{/dcl_form_control}
		{dcl_form_control id=phone_number controlsize=10 label="Phone Number" required=true}
		{dcl_input_text id=phone_number maxlength=30 value=$VAL_PHONENUMBER|trim}
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
<script type="text/javascript">
	$(function() {
		$("#phone_number").focus();
	});

	function validateAndSubmitForm(form)
	{
		var aValidators = [
				new ValidatorSelection(form.elements["phone_type_id"], "Type"),
				new ValidatorString(form.elements["phone_number"], "Phone Number")
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