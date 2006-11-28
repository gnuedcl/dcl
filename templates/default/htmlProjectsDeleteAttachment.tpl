<!-- $Id: htmlProjectsDeleteAttachment.tpl,v 1.3 2006/11/27 06:00:52 mdean Exp $ -->
<form class="styled" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="boProjects.dodeleteattachment">
	<input type="hidden" name="projectid" value="{$VAL_PROJECTID}">
	<input type="hidden" name="filename" value="{$VAL_FILENAME|escape}">
	<fieldset>
		<legend>{$smarty.const.STR_PRJ_DELATTACH}</legend>
		<div class="confirm">{$TXT_DELCONFIRM|escape}</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="submit" value="{$smarty.const.STR_CMMN_YES}">
			<input type="button" onclick="javascript: history.back();" value="{$smarty.const.STR_CMMN_NO}">
		</div>
	</fieldset>
</form>