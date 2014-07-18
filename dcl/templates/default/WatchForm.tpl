<form class="form-horizontal" name="AddWatch" method="post" action="{$URL_MAIN_PHP}">
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
		{dcl_form_control controlsize=10 label=$smarty.const.STR_CMMN_NAME}
		<span>{$VAL_DESC|escape}</span>
		{/dcl_form_control}
		{dcl_form_control id=actions controlsize=10 label=$smarty.const.STR_WTCH_ACTIONS required=true}
		{$CMB_ACTIONS}
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="submit" value="{$smarty.const.STR_CMMN_SAVE}">
				<input class="btn btn-default" type="reset" value="{$smarty.const.STR_CMMN_RESET}">
			</div>
		</div>
	</fieldset>
</form>