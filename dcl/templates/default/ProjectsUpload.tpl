<form class="form-horizontal" name="fileupload" method="post" action="{$URL_MAIN_PHP}" enctype="multipart/form-data">
	<input type="hidden" name="menuAction" value="Project.UploadAttachment">
	<input type="hidden" name="MAX_FILE_SIZE" value="{$VAL_MAXUPLOADFILESIZE}">
	<input type="hidden" name="projectid" value="{$VAL_PROJECTID}">
	<fieldset>
		<legend>{$smarty.const.STR_PRJ_UPLATTACH|escape}</legend>
		{dcl_form_control id=userfile controlsize=10 label=$smarty.const.STR_PRJ_ATTACH required=true}
			<input type="file" id="userfile" name="userfile">
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="submit" value="{$smarty.const.STR_CMMN_SAVE|escape}">
				<input class="btn btn-link" type="button" onclick="location.href = '{$LNK_CANCEL}';" value="{$smarty.const.STR_CMMN_CANCEL|escape}">
			</div>
		</div>
	</fieldset>
</form>