{dcl_selector_init}
<form class="styled" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$menuAction}">
	{if $jcn && is_array($jcn)}
		{section loop=$jcn name=row}
			<input type="hidden" name="selected[]" value="{$jcn[row]}">
		{/section}
	{else}
		{if $jcn}<input type="hidden" name="jcn" value="{$jcn}">{/if}
		{if $seq}<input type="hidden" name="seq" value="{$seq}">{/if}
	{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION}</legend>
		<div class="required">
			<label for="active">{$smarty.const.STR_PM_CHOOSEPRJ}:</label>
			{dcl_selector_project name="projectid" value="$VAL_PROJECTS"}
		</div>
		<div>
			<label for="addall">{$smarty.const.STR_PM_ADDALLSEQ}</label>
			<input type="checkbox" id="addall" name="addall" value="1">
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="submit" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="reset" value="{$smarty.const.STR_CMMN_RESET}">
		</div>
	</fieldset>
</form>