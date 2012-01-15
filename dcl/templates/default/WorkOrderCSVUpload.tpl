<form class="styled" name="fileupload" method="post" action="{$URL_MAIN_PHP}" enctype="multipart/form-data">
	<input type="hidden" name="menuAction" value="WorkOrder.ImportFile">
	<input type="hidden" name="MAX_FILE_SIZE" value="{$VAL_MAXUPLOADFILESIZE}">
	<fieldset>
		<legend>{$smarty.const.STR_WO_CSVTITLE}</legend>
		<div>
			<label for="userfile">{$smarty.const.STR_WO_CSVFILE}:</label>
			<input type="file" id="userfile" name="userfile">
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="submit" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="reset" value="{$smarty.const.STR_CMMN_RESET}">
		</div>
	</fieldset>
</form>