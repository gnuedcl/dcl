{extends file="_Layout.tpl"}
{block name=title}{$TXT_TITLE|escape}{/block}
{block name=css}
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
{/block}
{block name=content}
<form class="form-horizontal" name="reassign" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="boTickets.dbreassign">
	<input type="hidden" name="ticketid" value="{$VAL_TICKETID}">
	<fieldset>
		<legend>{$TXT_TITLE|escape}</legend>
		{dcl_form_control id=responsible controlsize=4 required=true label=$smarty.const.STR_TCK_RESPONSIBLE}
		{$CMB_RESPONSIBLE}
		{/dcl_form_control}
		{dcl_form_control id=priority controlsize=4 label=$smarty.const.STR_TCK_PRIORITY required=true}
		{$CMB_PRIORITY}
		{/dcl_form_control}
		{dcl_form_control id=type controlsize=4 label=$smarty.const.STR_TCK_TYPE required=true}
		{$CMB_TYPE}
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="button" class="inputSubmit" value="{$smarty.const.STR_CMMN_SAVE}" onclick="validateAndSubmitForm(this.form);">
				<input class="btn btn-link" type="button" class="inputSubmit" value="{$smarty.const.STR_CMMN_CANCEL}" onclick="location.href='{$URL_MAIN_PHP}?menuAction=boTickets.view&ticketid={$VAL_TICKETID}';">
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
		$("#content").find("select").select2();
	});

	function validateAndSubmitForm(form) {

		var aValidators = [
				new ValidatorSelection(form.elements["responsible"], "{$smarty.const.STR_TCK_RESPONSIBLE}"),
				new ValidatorSelection(form.elements["type"], "{$smarty.const.STR_TCK_TYPE}"),
				new ValidatorSelection(form.elements["priority"], "{$smarty.const.STR_TCK_PRIORITY}")
		];

		for (var i in aValidators) {
			if (!aValidators[i].isValid()) {
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