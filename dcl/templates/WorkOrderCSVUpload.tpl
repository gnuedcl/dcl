{extends file="_Layout.tpl"}
{block name=title}{$smarty.const.STR_WO_CSVTITLE|escape}{/block}
{block name=content}
<form class="form form-horizontal" name="fileupload" method="post" action="{$URL_MAIN_PHP}" enctype="multipart/form-data">
	<input type="hidden" name="menuAction" value="WorkOrder.ImportFile">
	<input type="hidden" name="MAX_FILE_SIZE" value="{$VAL_MAXUPLOADFILESIZE}">
	<fieldset>
		<legend>{$smarty.const.STR_WO_CSVTITLE|escape}</legend>
		{dcl_form_control id=userfile controlsize=10 label=$smarty.const.STR_WO_CSVFILE}
			<input type="file" id="userfile" name="userfile">
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input type="submit" class="btn btn-primary" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
				<input type="reset" class="btn btn-link" value="{$smarty.const.STR_CMMN_RESET}">
			</div>
		</div>
	</fieldset>
</form>
{/block}