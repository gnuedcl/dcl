<style type="text/css">
	label.role-label { padding: 2px; border-radius: 5px; }
</style>
<script language="JavaScript">

$(document).ready(function() {
	$("input.permission").on("change", function() {
		if ($(this).prop("checked")) {
			$("#" + $(this).attr("id") + "_label").css({color: "#ffffff", backgroundColor: "#006500"});
		}
		else {
			$("#" + $(this).attr("id") + "_label").css({color: "#555555", backgroundColor: "transparent"});
		}
	}).trigger("change");
});

function validateAndSubmit(f)
{
	f.submit();
}

</script>
<form method="post" action="{$WWW_ROOT}main.php">
<table cellpadding="2" cellspacing="0">
<input type="hidden" name="menuAction" value="{$menuAction}">
<input type="hidden" name="role_id" value="{$VAL_ROLEID}">
<tr><th class="formTitle">{$VAL_TITLE}: <input style="font-weight: normal;" type="text" size="50" maxlength="50" name="role_desc" id="role_desc" value="{$VAL_ROLEDESC}"/></th>
	<th class="formLinks"><input type="checkbox" name="active" id="active" value="Y"{if $VAL_ROLEACTIVE == "Y"} checked{/if}><label for="active">Active</label></th>
</tr>
<tr><td class="formContainer" colspan="2">
	<table cellpadding="2" cellspacing="0" style="height: 400px; width: 600px;">
		{foreach item=entityPerms key=entityName from=$Permissions name=entity}
		<tr style="color: #000065; background-color: {cycle values="#dedee9,#ffffff"};">
			<td style="vertical-align: top; font-weight: bold;">{$entityName}:</td><td valign="top">
			{foreach item=permItem key=entityPerm from=$entityPerms}
				{strip}
				<span style="white-space: nowrap;">
				<input type="checkbox" class="permission" name="rolePerms[]" id="rolePerm{$entityPerm}" value="{$entityPerm}"{if $permItem.selected == "true"} checked{/if}>
				<label class="role-label" id="rolePerm{$entityPerm}_label" for="rolePerm{$entityPerm}">{$permItem.desc}</label>&nbsp;
				</span>
				{/strip}
			{/foreach}
			</td>
		</tr>
		{/foreach}
		<tr class="formFooter">
			<td style="text-align: right;" colspan="2">
				<input type="button" onclick="validateAndSubmit(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
				<input type="button" onclick="location.href='{$WWW_ROOT}main.php?menuAction=Role.Index';" value="{$smarty.const.STR_CMMN_CANCEL}">
			</td>
		</tr>
	</table>
	</td>
</tr>
</table>
</form>
