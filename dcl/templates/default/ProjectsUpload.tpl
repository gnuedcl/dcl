<form class="styled" name="fileupload" method="post" action="{$URL_MAIN_PHP}" enctype="multipart/form-data">
	<input type="hidden" name="menuAction" value="boProjects.doupload">
	<input type="hidden" name="MAX_FILE_SIZE" value="{$VAL_MAXUPLOADFILESIZE}">
	<input type="hidden" name="projectid" value="{$VAL_PROJECTID}">
	<fieldset>
		<legend>{$smarty.const.STR_PRJ_UPLATTACH}</legend>
		<div>
			<label for="userfile">{$smarty.const.STR_PRJ_ATTACH}:</label>
			<input type="file" id="userfile" name="userfile">
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="submit" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="button" onclick="location.href = '{$LNK_CANCEL}';" value="{$smarty.const.STR_CMMN_CANCEL}">
		</div>
	</fieldset>
</form>