{extends file="_Layout.tpl"}
{block name=title}{$smarty.const.DCL_MENU_PREFERENCES|escape}{/block}
{block name=content}
<form class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="htmlPreferences.submitModify">
	{dcl_anti_csrf_token}
	<fieldset>
		<legend>{$smarty.const.DCL_MENU_PREFERENCES|escape}</legend>
{if $PERM_MODIFYCONTACT}
	{dcl_form_control id=esthours controlsize=2 label=$smarty.const.STR_CMMN_CONTACT}
		<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlContactDetail.show&contact_id={$VAL_CONTACTID}">{$smarty.const.STR_CMMN_EDIT|escape}</a>
		<span class="help-block">Edit your contact record.</span>
	{/dcl_form_control}
{/if}
	{dcl_form_control id=DCL_PREF_LANGUAGE controlsize=4 label=$smarty.const.STR_CFG_LANGUAGE}
	{$CMB_DEFAULTLANGUAGE}
		<span class="help-block">Select your default language to use when you are logged in.</span>
	{/dcl_form_control}
	{dcl_form_control id=DCL_PREF_NOTIFY_DEFAULT controlsize=1 label="Copy Me on Notification"}
		<input type="checkbox" id="DCL_PREF_NOTIFY_DEFAULT" name="DCL_PREF_NOTIFY_DEFAULT" value="Y"{if $VAL_NOTIFYDEFAULT == 'Y'} checked{/if}>
	{/dcl_form_control}
	{dcl_form_control id=DCL_PREF_CREATED_WATCH_OPTION controlsize=4 label="Watch Activity for Items I Create"}
	{dcl_select_watch_action name=DCL_PREF_CREATED_WATCH_OPTION default=$VAL_CREATEDWATCHOPTION}
	{/dcl_form_control}
{if $PERM_VIEWWORKSPACE}
	{dcl_form_control id=DCL_PREF_DEFAULT_WORKSPACE controlsize=4 label="Default Workspace"}
	{dcl_select_workspace name=DCL_PREF_DEFAULT_WORKSPACE default=$DCL_PREF_DEFAULT_WORKSPACE class="form-control input-sm"}
		<span class="help-block">Select your default workspace to use when you log in.</span>
	{/dcl_form_control}
{/if}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input type="submit" class="btn btn-primary" value="{$smarty.const.STR_CMMN_SAVE}">
				<input type="reset" class="btn btn-link" value="{$smarty.const.STR_CMMN_RESET}">
			</div>
		</div>
	</fieldset>
</form>
{/block}