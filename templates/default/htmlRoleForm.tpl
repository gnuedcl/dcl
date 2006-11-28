<!-- $Id: htmlRoleForm.tpl,v 1.1.1.1 2006/11/27 05:30:37 mdean Exp $ -->
<script language="JavaScript">
{literal}
var sLastLayer = 'div1';
function validateAndSubmit(f)
{
	f.submit();
}
function showHide(sLayer)
{
	if (sLastLayer == sLayer)
		return;

	var oDiv = document.getElementById(sLayer);
	var oDivCurrent = document.getElementById(sLastLayer);

	if (oDivCurrent)
		oDivCurrent.style.display = 'none';

	if (oDiv)
		oDiv.style.display = '';

	sLastLayer = sLayer;
}
function togglePerm(oControl)
{
	if (!oControl)
		return;

	var oLabel = document.getElementById(oControl.id + '_label');
	if (!oLabel)
		return;

	oLabel.style.color = oControl.checked ? "#000000" : "#555555";
	oLabel.style.fontWeight = oControl.checked ? "bold" : "normal";
}
{/literal}
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
				<input type="checkbox" name="rolePerms[]" onclick="togglePerm(this);" id="rolePerm{$entityPerm}" value="{$entityPerm}"{if $permItem.selected == "true"} checked{/if}>
				<label id="rolePerm{$entityPerm}_label" for="rolePerm{$entityPerm}" style="{if $permItem.selected == "true"}color: #000000; font-weight: bold;{else}color: #555555; font-weight: normal;{/if}">{$permItem.desc}</label>&nbsp;
				</span>
				{/strip}
			{/foreach}
			</td>
		</tr>
		{/foreach}
		<tr class="formFooter">
			<td style="text-align: right;" colspan="2">
				<input type="button" onclick="validateAndSubmit(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
				<input type="button" onclick="location.href='{$WWW_ROOT}main.php?menuAction=htmlRole.show';" value="{$smarty.const.STR_CMMN_CANCEL}">
			</td>
		</tr>
	</table>
	</td>
</tr>
</table>
</form>
