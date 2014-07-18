<form class="form-horizontal" name="fileupload" method="post" action="{$URL_MAIN_PHP}" enctype="multipart/form-data">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
	<input type="hidden" name="MAX_FILE_SIZE" value="{$VAL_MAXUPLOADFILESIZE}">
	<input type="hidden" name="dcl_chklst_tpl_id" value="{$VAL_ID}">
	<fieldset>
		<legend>Upload Checklist Template: {if $VAL_ID}{$VAL_NAME|escape}{else}New Template{/if}</legend>
		{dcl_form_control id=active controlsize=2 label=$smarty.const.STR_CMMN_ACTIVE required=true}
		{$CMB_ACTIVE}
		{/dcl_form_control}
		{dcl_form_control id=userfile controlsize=10 label="File" required=true}
			<input type="file" id="userfile" name="userfile">
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input class="btn btn-primary" type="submit" value="{$smarty.const.STR_CMMN_SAVE}">
				<input class="btn btn-link" type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=boChecklistTpl.show';" value="{$smarty.const.STR_CMMN_CANCEL}">
			</div>
		</div>
	</fieldset>
</form>