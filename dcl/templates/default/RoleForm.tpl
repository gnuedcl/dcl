<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
<style type="text/css">
	label.role-label { padding: 2px; border-radius: 5px; }
</style>
<form class="form-horizontal" method="post" action="{$WWW_ROOT}main.php">
<input type="hidden" name="menuAction" value="{$menuAction}">
<input type="hidden" name="role_id" value="{$VAL_ROLEID}">
	<fieldset>
		<legend>{$VAL_TITLE|escape}</legend>
		{dcl_form_control id=role_desc controlsize=1 label=$smarty.const.STR_CMMN_ACTIVE}
			<input type="checkbox" name="active" id="active" value="Y"{if $VAL_ROLEACTIVE == "Y"} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=role_desc controlsize=5 label=$smarty.const.STR_CMMN_NAME required=true}
			<input type="text" class="form-control" maxlength="50" name="role_desc" id="role_desc" value="{$VAL_ROLEDESC|escape}">
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<legend>Permissions</legend>
		{foreach item=entityPerms key=entityName from=$Permissions name=entity}
			{dcl_form_control id="role_desc$entityPerm" controlsize=10 label=$entityName}
				<select multiple class="form-control" name="rolePerms[]" id="rolePerm{$smarty.foreach.entity.index}">
				{foreach item=permItem key=entityPerm from=$entityPerms}
					<option value="{$entityPerm}"{if $permItem.selected == "true"} selected{/if}>{$permItem.desc|escape}</option>
				{/foreach}
				</select>
			{/dcl_form_control}
		{/foreach}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input type="button" class="btn btn-primary" onclick="validateAndSubmit(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
				<input type="button" class="btn btn-link" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=Role.Index';" value="{$smarty.const.STR_CMMN_CANCEL}">
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript">

	$(document).ready(function() {
		$("#content").find("select").select2({ minimumResultsForSearch: 10 });
	});

	function validateAndSubmit(f)
	{
		f.submit();
	}

</script>
