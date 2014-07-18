{dcl_validator_init}
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
<form class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$menuAction}">
	{if $id}<input type="hidden" name="id" value="{$id}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		{dcl_form_control id=active controlsize=2 label=$smarty.const.STR_CMMN_ACTIVE required=true}
		{$CMB_ACTIVE}
		{/dcl_form_control}
		{dcl_form_control id=short controlsize=4 label=$smarty.const.STR_PROD_SHORT required=true}
		{dcl_input_text id=short maxlength=10 value=$VAL_SHORT|trim}
		{/dcl_form_control}
		{dcl_form_control id=name controlsize=10 label=$smarty.const.STR_CMMN_NAME required=true}
		{dcl_input_text id=name maxlength=50 value=$VAL_NAME}
		{/dcl_form_control}
		{dcl_form_control id=reportto controlsize=4 label=$smarty.const.STR_PROD_REPORTTO required=true}
		{$CMB_REPORTTO}
		{/dcl_form_control}
		{dcl_form_control id=ticketsto controlsize=4 label=$smarty.const.STR_PROD_TICKETSTO required=true}
		{$CMB_TICKETSTO}
		{/dcl_form_control}
		{dcl_form_control id=wosetid controlsize=4 label=$smarty.const.STR_PROD_WOATTRIBUTESET required=true}
		{$CMB_WOATTRIBUTESET}
		{/dcl_form_control}
		{dcl_form_control id=tcksetid controlsize=4 label=$smarty.const.STR_PROD_TICKETATTRIBUTESET required=true}
		{$CMB_TCKATTRIBUTESET}
		{/dcl_form_control}
		{dcl_form_control id=is_versioned controlsize=2 label="Versioned" required=true}
		{$CMB_ISVERSIONED}
		{/dcl_form_control}
		{dcl_form_control id=is_project_required controlsize=2 label="Project Required" required=true}
		{$CMB_ISPROJECTREQUIRED}
		{/dcl_form_control}
		{dcl_form_control id=is_public controlsize=2 label=$smarty.const.STR_CMMN_PUBLIC required=true}
		{$CMB_ISPUBLIC}
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
				{if $id}<input class="btn btn-link" type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=Product.Detail&id={$id}';" value="{$smarty.const.STR_CMMN_CANCEL}">
				{else}<input class="btn btn-link" type="button" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=Product.Index';" value="{$smarty.const.STR_CMMN_CANCEL}">
				{/if}
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript">
	$(function() {
		$("#short").focus();
		$("#content").find("select").select2({ minimumResultsForSearch: 10 });
	});

	function validateAndSubmitForm(form)
	{
		var aValidators = [
			new ValidatorString(form.elements["short"], "{$smarty.const.STR_PROD_SHORT}"),
			new ValidatorString(form.elements["name"], "{$smarty.const.STR_CMMN_NAME}"),
			new ValidatorSelection(form.elements["reportto"], "{$smarty.const.STR_PROD_REPORTTO}"),
			new ValidatorSelection(form.elements["ticketsto"], "{$smarty.const.STR_PROD_TICKETSTO}"),
			new ValidatorSelection(form.elements["wosetid"], "{$smarty.const.STR_PROD_WOATTRIBUTESET}"),
			new ValidatorSelection(form.elements["tcksetid"], "{$smarty.const.STR_PROD_TICKETATTRIBUTESET}")
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
