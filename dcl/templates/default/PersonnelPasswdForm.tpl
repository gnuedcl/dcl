{dcl_selector_init}
<form class="styled" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="Personnel.UpdatePassword">
	<fieldset>
		<legend>{$smarty.const.STR_USR_CHGPWD}</legend>
		<div>
		{if $PERM_ADMIN}
			<label class="required" for="userid">{$smarty.const.STR_USR_USER}:</label>
			{dcl_selector_personnel name="userid" value="$VAL_USERID" decoded="$VAL_USERNAME"}
			<span>Select the user you want to change the password for.</span>
		{else}
			<label for="original">{$smarty.const.STR_USR_CURRPWD}:</label>
			<input type="password" size="15" name="original" id="original">
			<span>Enter your current password here.</span>
		{/if}
		</div>
		<div>
			<label class="required" for="new">{$smarty.const.STR_USR_NEWPWD}:</label>
			<input type="password" size="15" name="new" id="new">
			<span>Enter your new password here.</span>
		</div>
		<div>
			<label class="required" for="confirm">{$smarty.const.STR_USR_CONFIRMPWD}:</label>
			<input type="password" size="15" name="confirm" id="confirm">
			<span>Confirm your new password.</span>
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="submit" value="{$smarty.const.STR_USR_CHANGE}">
			<input type="reset" value="{$smarty.const.STR_USR_CLEAR}">
		</div>
	</fieldset>
</form>