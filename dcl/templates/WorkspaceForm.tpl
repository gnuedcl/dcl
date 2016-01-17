{extends file="_Layout.tpl"}
{block name=title}{$VAL_TITLE|escape}{/block}
{block name=css}
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
{/block}
{block name=content}
<form class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$menuAction}">
	{if $VAL_ID}<input type="hidden" name="workspace_id" value="{$VAL_ID}">{/if}
	<fieldset>
		<legend>{$VAL_TITLE|escape}</legend>
		{dcl_form_control id=active controlsize=10 label=$smarty.const.STR_CMMN_ACTIVE required=false}
			<input type="checkbox" id="active" name="active" value="Y"{if $VAL_ACTIVE == 'Y'} checked{/if}>
		{/dcl_form_control}
		{dcl_form_control id=workspace_name controlsize=10 label=$smarty.const.STR_CMMN_NAME required=true}
		{dcl_input_text id=workspace_name maxlength=50 value=$VAL_NAME}
		{/dcl_form_control}
		{dcl_form_control id=products controlsize=10 label="Products" required=false help="When a user switches to this workspace, all views will be limited to the products listed here."}
		{dcl_select_product name="products" default=$VAL_PRODUCTS size=8}
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
				<input class="btn btn-link" type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=htmlWorkspaceBrowse.show';" value="{$smarty.const.STR_CMMN_CANCEL}">
			</div>
		</div>
	</fieldset>
</form>
{/block}
{block name=script}
{dcl_validator_init}
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript">
	$(function() {
		$("#workspace_name").focus();
		$("#content").find("select").select2({ minimumResultsForSearch: 10 });
	});

	function validateAndSubmitForm(form)
	{
		var aValidators = new Array(
				new ValidatorString(form.elements["name"], "{$smarty.const.STR_CMMN_NAME}")
		);

		for (var i in aValidators)
		{
			if (!aValidators[i].isValid())
			{
				alert(aValidators[i].getError());
				if (typeof(aValidators[i]._Element.focus) == "function")
					aValidators[i]._Element.focus();
				return;
			}
		}

		form.submit();
	}
</script>
{/block}