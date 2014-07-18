<form class="form-horizontal" name="fileupload" method="post" action="{$URL_MAIN_PHP}" enctype="multipart/form-data">
	<input type="hidden" name="menuAction" value="WorkOrder.UploadAttachment">
	<input type="hidden" name="MAX_FILE_SIZE" value="{$VAL_MAXUPLOADFILESIZE}">
	<input type="hidden" name="jcn" value="{$VAL_JCN}">
	<input type="hidden" name="seq" value="{$VAL_SEQ}">
	<fieldset>
		<legend>{$smarty.const.STR_WO_UPLOADTITLE|escape}</legend>
		{dcl_form_control id=userfile controlsize=10 label=$smarty.const.STR_WO_ATTACHFILE required=true}
			<input type="file" id="userfile" name="userfile">
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="submit" value="{$smarty.const.STR_CMMN_SAVE}">
				<input class="btn btn-link" type="reset" value="{$smarty.const.STR_CMMN_RESET}">
			</div>
		</div>
	</fieldset>
</form>