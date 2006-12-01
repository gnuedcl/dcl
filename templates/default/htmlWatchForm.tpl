<!-- $Id$ -->
<form class="styled" name="AddWatch" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="typeid" value="{$VAL_TYPEID}">
	<input type="hidden" name="whatid1" value="{$VAL_WHATID1}">
	<input type="hidden" name="whatid2" value="{$VAL_WHATID2}">
	<input type="hidden" name="whoid" value="{$VAL_WHOID}">
{if $IS_EDIT}
	<input type="hidden" name="menuAction" value="boWatches.dbmodify">
	<input type="hidden" name="watchid" value="{$VAL_WATCHID}">
{else}
	<input type="hidden" name="menuAction" value="boWatches.dbadd">
{/if}
	<fieldset>
		<legend>{$TXT_TITLE|escape}</legend>
		<div class="help">{$VAL_DESC|escape}</div>
		<div class="required">
			<label for="actions">{$smarty.const.STR_WTCH_ACTIONS}:</label>
			{$CMB_ACTIONS}
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="submit" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="reset" value="{$smarty.const.STR_CMMN_RESET}">
		</div>
	</fieldset>
</form>