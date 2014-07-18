<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
<form class="form-horizontal" name="theForm" method="post" action="{$smarty.const.DCL_WWW_ROOT}main.php">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION|escape}">
	<input type="hidden" name="org_id" value="{$VAL_ORGID|escape}">
	<fieldset>
		<legend>{$TXT_TITLE|escape}</legend>
		{dcl_form_control controlsize=10 label=$smarty.const.STR_CMMN_NAME}
		<span class="form-control">{$VAL_ORGNAME|escape}</span>
		{/dcl_form_control}
		{dcl_form_control id=product_id controlsize=10 label="Products" required=true}
		{dcl_select_product name="product_id" default=$VAL_PRODUCTID size=8 active=N}
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
				<input class="btn btn-link" type="button" onclick="location.href = '{$URL_BACK}';" value="{$smarty.const.STR_CMMN_CANCEL}">
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript">
	$(function() {
		$("#name").focus();
		$("#content").find("select").select2({ minimumResultsForSearch: 10 });
	});

	function validateAndSubmitForm(form) {
		form.submit();
	}
</script>