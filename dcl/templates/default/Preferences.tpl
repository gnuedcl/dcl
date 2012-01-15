{dcl_validator_init}
<script language="JavaScript">
{literal}
function validateAndSubmit(f)
{
	f.submit();
}
{/literal}
</script>
<form class="styled" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="htmlPreferences.submitModify">
	<fieldset>
		<legend>{$smarty.const.DCL_MENU_PREFERENCES}</legend>
{if $PERM_MODIFYCONTACT}
		<div class="required">
			<label for="email">{$smarty.const.STR_CMMN_CONTACT}:</label>
			<input type="button" onclick="location.href='{$URL_MAIN_PHP}?menuAction=htmlContactDetail.show&contact_id={$VAL_CONTACTID}'" value="{$smarty.const.STR_CMMN_EDIT}">
			<span>Edit your contact record.</span>
		</div>
{/if}
		<div class="required">
			<label for="DCL_PREF_TEMPLATE_SET">{$smarty.const.STR_CFG_DEFTEMPLATESET}:</label>
			{$CMB_DEFAULTTEMPLATESET}
			<span>This is the template set you wish to use when you are logged in.</span>
		</div>
		<div class="required">
			<label for="DCL_PREF_LANGUAGE">{$smarty.const.STR_CFG_LANGUAGE}:</label>
			{$CMB_DEFAULTLANGUAGE}
			<span>Select your default language to use when you are logged in.</span>
		</div>
		<div class="required">
			<label for="DCL_PREF_NOTIFY_DEFAULT">Copy Me on Notification:</label>
			<input type="checkbox" id="DCL_PREF_NOTIFY_DEFAULT" name="DCL_PREF_NOTIFY_DEFAULT" value="Y"{if $VAL_NOTIFYDEFAULT == 'Y'} checked{/if}>
		</div>
		<div class="required">
			<label for="DCL_PREF_CREATED_WATCH_OPTION">Watch Activity for Items I Create:</label>
			{dcl_select_watch_action name=DCL_PREF_CREATED_WATCH_OPTION default=$VAL_CREATEDWATCHOPTION}
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmit(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="reset" value="{$smarty.const.STR_CMMN_RESET}">
		</div>
	</fieldset>
</form>