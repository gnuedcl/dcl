<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
{dcl_validator_errors errors=$ERRORS}
<form class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="Personnel.UpdatePassword">
	<fieldset>
		<legend>{$smarty.const.STR_USR_CHGPWD}</legend>
		{if $PERM_ADMIN}
			{dcl_form_control id=product controlsize=4 label=$smarty.const.STR_USR_USER required=true}
			{dcl_select_personnel name=userid default=$VAL_USERID}
				<span class="help-block">Select the user you want to change the password for.</span>
			{/dcl_form_control}
		{else}
			{dcl_form_control id=original controlsize=4 label=$smarty.const.STR_USR_CURRPWD required=true}
				<input class="form-control" type="password" size="15" name="original" id="original">
				<span class="help-block">Enter your current password here.</span>
			{/dcl_form_control}
		{/if}
		{dcl_form_control id=new controlsize=4 label=$smarty.const.STR_USR_NEWPWD required=true}
			<input class="form-control" type="password" size="15" name="new" id="new">
			<span class="help-block">Enter the new password here.</span>
		{/dcl_form_control}
		{dcl_form_control id=confirm controlsize=4 label=$smarty.const.STR_USR_CONFIRMPWD required=true}
			<input class="form-control" type="password" size="15" name="confirm" id="confirm">
			<span class="help-block">Confirm the new password.</span>
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input type="submit" class="btn btn-primary" value="{$smarty.const.STR_CMMN_SAVE}">
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript">
	$(function() {
		$("#content").find("select").select2({ minimumResultsForSearch: 10 });
	});
</script>