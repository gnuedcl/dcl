<form class="form-horizontal" method="post" name="submitForm" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
	{if $VAL_ORGID}<input type="hidden" name="org_id" value="{$VAL_ORGID}">{/if}
	{if $VAL_CONTACTID}<input type="hidden" name="contact_id" value="{$VAL_CONTACTID}">{/if}
	{if $VAL_ORGADDRID}<input type="hidden" name="org_addr_id" value="{$VAL_ORGADDRID}">{/if}
	{if $VAL_CONTACTADDRID}<input type="hidden" name="contact_addr_id" value="{$VAL_CONTACTADDRID}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		{dcl_form_control controlsize=10 label=$smarty.const.STR_CMMN_NAME}
		<span class="form-control">{if $VAL_ORGNAME}{$VAL_ORGNAME|escape}{else}{$VAL_FIRSTNAME|escape} {$VAL_LASTNAME|escape}{/if}</span>
		{/dcl_form_control}
		{dcl_form_control id=preferred controlsize=10 label="Primary"}
		{if $VAL_PREFERRED == "Y"}<span>This is the preferred address.  If you do not want this to be the preferred address, select another address as the preferred address.</span>
		{else}<input type="checkbox" id="preferred" name="preferred" value="Y">{/if}
		{/dcl_form_control}
		{dcl_form_control id=addr_type_id controlsize=4 label="Type" required=true}
		{$CMB_ADDRTYPE}
		{/dcl_form_control}
		{dcl_form_control id=add1 controlsize=10 label="Address"}
		{dcl_input_text id=add1 maxlength=50 value=$VAL_ADD1}
		{/dcl_form_control}
		{dcl_form_control id=add2 controlsize=10 label="Address 2"}
		{dcl_input_text id=add2 maxlength=50 value=$VAL_ADD2}
		{/dcl_form_control}
		{dcl_form_control id=city controlsize=10 label="City"}
		{dcl_input_text id=city maxlength=50 value=$VAL_CITY}
		{/dcl_form_control}
		{dcl_form_control id=state controlsize=10 label="State"}
		{dcl_input_text id=state maxlength=30 value=$VAL_STATE}
		{/dcl_form_control}
		{dcl_form_control id=zip controlsize=10 label="Zip"}
		{dcl_input_text id=zip maxlength=20 value=$VAL_ZIP}
		{/dcl_form_control}
		{dcl_form_control id=country controlsize=10 label="Country"}
		{dcl_input_text id=country maxlength=40 value=$VAL_COUNTRY}
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
	$("#add1").focus();
});

function validateAndSubmitForm(form)
{
	if (form.elements["addr_type_id"].selectedIndex < 1)
	{
		alert("Address type is required.");
		return;
	}

	var bSubmit = false;
	var aCheck = ["add1", "add2", "city", "state", "zip", "country"];
	for (var sID in aCheck)
	{
		if (form.elements[sID].value != "")
		{
			bSubmit = true;
			break;
		}
	}

	if (bSubmit)
		form.submit();
	else
		alert("At least one of the address fields needs is required to be populated.");
}

</script>