<script language="JavaScript">
{literal}
	function validateAndSubmitForm(form)
	{
		if (form.elements["addr_type_id"].selectedIndex < 1)
		{
			alert("Address type is required.");
			return;
		}

		var bSubmit = false;
		var aCheck = new Array("add1", "add2", "city", "state", "zip", "country");
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
{/literal}
</script>
<form class="styled" method="post" name="submitForm" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
	{if $VAL_ORGID}<input type="hidden" name="org_id" value="{$VAL_ORGID}">{/if}
	{if $VAL_CONTACTID}<input type="hidden" name="contact_id" value="{$VAL_CONTACTID}">{/if}
	{if $VAL_ORGADDRID}<input type="hidden" name="org_addr_id" value="{$VAL_ORGADDRID}">{/if}
	{if $VAL_CONTACTADDRID}<input type="hidden" name="contact_addr_id" value="{$VAL_CONTACTADDRID}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION}</legend>
		<div class="help">
			{if $VAL_ORGNAME}{$VAL_ORGNAME}{else}{$VAL_FIRSTNAME} {$VAL_LASTNAME}{/if}
		</div>
		<div>
			<label for="preferred">Primary:</label>
			<input type="checkbox" id="preferred" name="preferred" value="Y"{if $VAL_PREFERRED == "Y"} checked="true" onclick="return false;"{/if}>
			{if $VAL_PREFERRED == "Y"}<span>This is the preferred address.  If you do not want this to be the preferred address, select another address as the preferred address.</span>{/if}
		</div>
		<div class="required">
			<label for="addr_type_id">Type:</label>
			{$CMB_ADDRTYPE}
		</div>
		<div>
			<label for="add1">Address:</label>
			<input type="text" id="add1" name="add1" size="30" maxlength="50" value="{$VAL_ADD1}">
		</div>
		<div>
			<label for="add2">Address 2:</label>
			<input type="text" id="add2" name="add2" size="30" maxlength="50" value="{$VAL_ADD2}">
		</div>
		<div>
			<label for="city">City:</label>
			<input type="text" id="city" name="city" size="30" maxlength="50" value="{$VAL_CITY}">
		</div>
		<div>
			<label for="state">State:</label>
			<input type="text" id="state" name="state" size="30" maxlength="30" value="{$VAL_STATE}">
		</div>
		<div>
			<label for="zip">Zip:</label>
			<input type="text" id="zip" name="zip" size="20" maxlength="20" value="{$VAL_ZIP}">
		</div>
		<div>
			<label for="country">Country:</label>
			<input type="text" id="country" name="country" size="30" maxlength="40" value="{$VAL_COUNTRY}">
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_OK}">
			<input type="button" onclick="location.href = '{$URL_BACK}';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>
<script language="JavaScript">
if (document.getElementById("add1"))
	document.getElementById("add1").focus();
</script>