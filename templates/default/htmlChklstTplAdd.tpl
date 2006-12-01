<!-- $Id$ -->
<form class="styled" name="fileupload" method="post" action="{$URL_MAIN_PHP}" enctype="multipart/form-data">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
	<input type="hidden" name="MAX_FILE_SIZE" value="{$VAL_MAXUPLOADFILESIZE}">
	<input type="hidden" name="dcl_chklst_tpl_id" value="{$VAL_ID}">
	<fieldset>
		<legend>Upload Checklist Template</legend>
		<div class="help">{if $VAL_ID}{$VAL_NAME}{else}New Template{/if}</div>
		<div>
			<label for="active">{$smarty.const.STR_CMMN_ACTIVE}:</label>
			{$CMB_ACTIVE}
		</div>
		<div>
			<label for="userfile">File:</label>
			<input type="file" id="userfile" name="userfile">
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="submit" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=boChecklistTpl.show';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>