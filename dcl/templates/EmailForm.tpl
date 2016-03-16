{extends file="_Layout.tpl"}
{block name=title}{$TXT_FUNCTION|escape}{/block}
{block name=content}
<form class="form-horizontal" method="post" name="submitForm" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
	{dcl_anti_csrf_token}
	{if $VAL_ORGID}<input type="hidden" name="org_id" value="{$VAL_ORGID}">{/if}
	{if $VAL_ORGEMAILID}<input type="hidden" name="org_email_id" value="{$VAL_ORGEMAILID}">{/if}
	{if $VAL_CONTACTID}<input type="hidden" name="contact_id" value="{$VAL_CONTACTID}">{/if}
	{if $VAL_CONTACTEMAILID}<input type="hidden" name="contact_email_id" value="{$VAL_CONTACTEMAILID}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		{dcl_form_control controlsize=10 label=$smarty.const.STR_CMMN_NAME}
		<span>{if $VAL_ORGID}{$VAL_ORGNAME|escape}{else}{$VAL_FIRSTNAME|escape} {$VAL_LASTNAME|escape}{/if}</span>
		{/dcl_form_control}
		{dcl_form_control id=preferred controlsize=10 label="Primary"}
		{if $VAL_PREFERRED == "Y"}<span>This is the preferred email address.  If you do not want this to be the preferred email address, select another email address as the preferred email.</span>
		{else}<input type="checkbox" id="preferred" name="preferred" value="Y">{/if}
		{/dcl_form_control}
		{dcl_form_control id=email_type_id controlsize=4 label="Type" required=true}
		{$CMB_EMAILTYPE}
		{/dcl_form_control}
		{dcl_form_control id=email_addr controlsize=10 label="E-Mail" required=true}
		{dcl_input_text id=email_addr maxlength=100 value=$VAL_EMAILADDR}
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
		$("#email_addr").focus();
	});

	function validateAndSubmitForm(form)
	{
		var aValidators = [
			new ValidatorSelection(form.elements["email_type_id"], "Type"),
			new ValidatorString(form.elements["email_addr"], "E-Mail")
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