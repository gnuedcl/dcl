<!-- $Id: htmlWorkOrderAddAttachment.tpl,v 1.4 2006/11/27 06:00:52 mdean Exp $ -->
<form class="styled" name="fileupload" method="post" action="{$URL_MAIN_PHP}" enctype="multipart/form-data">
	<input type="hidden" name="menuAction" value="boWorkorders.doupload">
	<input type="hidden" name="MAX_FILE_SIZE" value="{$VAL_MAXUPLOADFILESIZE}">
	<input type="hidden" name="jcn" value="{$VAL_JCN}">
	<input type="hidden" name="seq" value="{$VAL_SEQ}">
	<fieldset>
		<legend>{$smarty.const.STR_WO_UPLOADTITLE}</legend>
		<div>
			<label for="userfile">{$smarty.const.STR_WO_ATTACHFILE}:</label>
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