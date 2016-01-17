{extends file="_Layout.tpl"}
{block name=title}{$TXT_FUNCTION|escape}{/block}
{block name=css}
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
{/block}
{block name=content}
<form class="form-horizontal" method="post" name="submitForm" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
	{if $VAL_CONTACTID}<input type="hidden" name="contact_id" value="{$VAL_CONTACTID}">{/if}
	{if $VAL_CONTACTLICENSEID}<input type="hidden" name="contact_license_id" value="{$VAL_CONTACTLICENSEID}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		{if $VAL_CONTACTID}<div class="help">{$VAL_FIRSTNAME|escape} {$VAL_LASTNAME|escape}</div>{/if}
		{dcl_form_control id=product_id controlsize=10 label="Product" required=true}
		{dcl_select_product default="$VAL_PRODUCTID" active="$ACTIVE_ONLY" name="product_id"}
		{/dcl_form_control}
		{dcl_form_control id=product_version controlsize=4 label="Version" required=false}
		{dcl_input_text id=product_version maxlength=20 value=$VAL_VERSION}
		{/dcl_form_control}
		{dcl_form_control id=license_id controlsize=10 label="License #" required=true}
		{dcl_input_text id=license_id maxlength=50 value=$VAL_LICENSEID}
		{/dcl_form_control}
		{dcl_form_control id=registered_on controlsize=2 label="Registration Date" required=true}
		{dcl_input_date id=registered_on value=$VAL_REGISTERDON}
		{/dcl_form_control}
		{dcl_form_control id=expires_on controlsize=2 label="Expiration Date" required=true}
		{dcl_input_date id=expires_on value=$VAL_EXPIRESON}
		{/dcl_form_control}
		{dcl_form_control id=license_notes controlsize=10 label="Notes" required=false}
			<textarea class="form-control" name="license_notes" id="license_notes" rows="4" wrap valign="top">{$VAL_NOTES|escape}</textarea>
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
		$("#product_version").focus();
		$("#content").find("select").select2({ minimumResultsForSearch: 10 });
		$("input[data-input-type=date]").datepicker();
	});

function validateAndSubmitForm(form)
{
	var aValidators = [
			new ValidatorSelection(form.elements["product_id"], "Product"),
			new ValidatorString(form.elements["license_id"], "License #"),
			new ValidatorDate(form.elements["registered_on"], "Registration Date"),
			new ValidatorDate(form.elements["expires_on"], "Expiration Date")
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