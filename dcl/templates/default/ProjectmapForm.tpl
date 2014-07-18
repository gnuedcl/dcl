{dcl_selector_init}
<form class="form-horizontal" method="post" action="{$URL_MAIN_PHP|escape}">
	<input type="hidden" name="menuAction" value="{$menuAction|escape}">
	{if $jcn && is_array($jcn)}
		{section loop=$jcn name=row}
			<input type="hidden" name="selected[]" value="{$jcn[row]|escape}">
		{/section}
	{else}
		{if $jcn}<input type="hidden" name="jcn" value="{$jcn|escape}">{/if}
		{if $seq}<input type="hidden" name="seq" value="{$seq|escape}">{/if}
	{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		{dcl_form_control controlsize=10 label=$smarty.const.STR_PM_CHOOSEPRJ required=true}
		{dcl_selector_project name="projectid" value="$VAL_PROJECTS"}
		{/dcl_form_control}
		{dcl_form_control id=addall controlsize=10 label=$smarty.const.STR_PM_ADDALLSEQ}
			<input type="checkbox" id="addall" name="addall" value="1">
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="submit" value="{$smarty.const.STR_CMMN_SAVE}">
				<input class="btn btn-link" type="reset" value="{$smarty.const.STR_CMMN_RESET}">
			</div>
		</div>
	</fieldset>
</form>