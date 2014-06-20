{if $VAL_MERGECONTACTID}
<form class="styled" method="post" name="contactMergeForm" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="htmlContact.doMerge">
	<input type="hidden" name="merge_contact_id" value="{$VAL_MERGECONTACTID}">
{if $VAL_LASTPAGE}
	<input type="hidden" name="chainMenuAction" value="htmlContactBrowse.Page">
	{$VAL_LASTPAGE.VAL_VIEWSETTINGS}
	<input type="hidden" id="startrow" name="startrow" value="{$VAL_LASTPAGE.VAL_STARTROW|escape}" />
	<input type="hidden" id="numrows" name="numrows" value="{$VAL_LASTPAGE.VAL_NUMROWS|escape}" />
	<input type="hidden" id="jumptopage" name="jumptopage" value="{$VAL_LASTPAGE.VAL_JUMPTOPAGE|escape}" />
	<input type="hidden" id="filterStartsWith" name="filterStartsWith" value="{$VAL_LASTPAGE.VAL_FILTERSTART|escape}" />
	<input type="hidden" id="filterActive" name="filterActive" value="{$VAL_LASTPAGE.VAL_FILTERACTIVE|escape}" />
	<input type="hidden" id="filterSearch" name="filterSearch" value="{$VAL_LASTPAGE.VAL_FILTERSEARCH|escape}" />
	{if $VAL_LASTPAGE.VAL_FILTERORGID != ""}<input type="hidden" name="org_id" id="org_id" value="{$VAL_LASTPAGE.VAL_FILTERORGID|escape}">{/if}
{/if}
	<fieldset>
		<legend>Merge Contacts</legend>
		<div>
			<fieldset>
				<legend>Select Destination Contact</legend>
				<div>
					{section name=contact loop=$VAL_CONTACTS}
						<span style="display:block;"><input type="radio" name="contact_id" id="contact_id_{$VAL_CONTACTS[contact].contact_id}" value="{$VAL_CONTACTS[contact].contact_id}"{if $smarty.section.contact.first} checked{/if}><label for="contact_id_{$VAL_CONTACTS[contact].contact_id}">{$VAL_CONTACTS[contact].name|escape} {$VAL_CONTACTS[contact].phone|escape}</label></span>
					{/section}
				</div>
			</fieldset>
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="this.form.submit();" value="{$smarty.const.STR_CMMN_OK}">
			<input type="button" onclick="location.href='{$WWW_ROOT}main.php?menuAction=htmlContactDetail.show&contact_id={$VAL_CONTACTID}';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>
{else}
{dcl_selector_init}
{dcl_validator_init}
<script language="JavaScript">

function validateAndSubmitForm(form)
{

	var aValidators = new Array(
			new ValidatorSelector(form.elements["merge_contact_id"], "{$smarty.const.STR_CMMN_CONTACT}")
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

</script>
<form class="styled" method="post" name="contactMergeForm" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="htmlContact.doMerge">
	<input type="hidden" name="contact_id" value="{$VAL_CONTACTID}">
	<fieldset>
		<legend>Merge Contacts</legend>
		<div>
			<label>Destination Contact:</label>
			<input type="text" disabled="true" value="{$VAL_CONTACT.name|escape}" size="50">
		</div>
		<div class="required">
			<label>Contacts to Merge:</label>
			{dcl_selector_contact name=merge_contact_id multiple=Y}
		</div>
		<div class="noinput">
			<div id="div_merge_contact_id" style="width: 100%;"><script language="JavaScript">render_a_merge_contact_id();</script></div>
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="this.form.submit();" value="{$smarty.const.STR_CMMN_OK}">
			<input type="button" onclick="location.href='{$WWW_ROOT}main.php?menuAction=htmlContactDetail.show&contact_id={$VAL_CONTACTID}';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>
{/if}
