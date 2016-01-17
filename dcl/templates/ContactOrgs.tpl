{extends file="_Layout.tpl"}
{block name=title}{$TXT_TITLE|escape}{/block}
{block name=css}
	<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
	<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
{/block}
{block name=content}
<form class="form-horizontal" name="theForm" method="post" action="{$smarty.const.DCL_WWW_ROOT}main.php">
	<input type="hidden" name="menuAction" value="{$VAL_MENUACTION}">
	<input type="hidden" name="contact_id" value="{$VAL_CONTACTID}">
	<fieldset>
		<legend>{$TXT_TITLE|escape}</legend>
		{dcl_form_control controlsize=10 label=$smarty.const.STR_CMMN_NAME}
		<span>{$VAL_FIRSTNAME|escape} {$VAL_LASTNAME|escape}</span>
		{/dcl_form_control}
		{dcl_form_control id=org_id controlsize=10 label=$smarty.const.STR_CMMN_ORGANIZATION required=true}
		{dcl_select_org name="org_id" default=$VAL_ORGID size=8}
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
{/block}
{block name=script}
	{dcl_validator_init}
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#content").find("select").select2({ minimumResultsForSearch: 10 });
	});

	function validateAndSubmitForm(form)
	{
		var aValidators = [
			new ValidatorSelector(form.elements["org_id"], "{$smarty.const.STR_CMMN_ORGANIZATION}")
		];

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