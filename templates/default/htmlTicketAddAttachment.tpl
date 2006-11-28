<!-- $Id: htmlTicketAddAttachment.tpl,v 1.3 2006/11/27 06:00:52 mdean Exp $ -->
<form class="styled" name="fileupload" method="post" action="{$URL_MAIN_PHP}" enctype="multipart/form-data">
	<input type="hidden" name="menuAction" value="boTickets.doupload">
	<input type="hidden" name="MAX_FILE_SIZE" value="{$VAL_MAXUPLOADFILESIZE}">
	<input type="hidden" name="ticketid" value="{$VAL_TICKETID}">
	<fieldset>
		<legend>{$smarty.const.STR_TCK_UPLOADATTACHMENT}</legend>
		<div>
			<label for="userfile">{$smarty.const.STR_TCK_ATTACHFILE}:</label>
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