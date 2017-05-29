{extends file="_Layout.tpl"}
{block name=title}{$TXT_FUNCTION|escape}{/block}
{block name=content}
<form class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{$menuAction}">
	{if $id}<input type="hidden" name="id" value="{$id}">{/if}
	<fieldset>
		<legend>{$TXT_FUNCTION|escape}</legend>
		{dcl_form_control id=active controlsize=2 label=$smarty.const.STR_CMMN_ACTIVE required=true}
		{$CMB_ACTIVE}
		{/dcl_form_control}
		{dcl_form_control id=active controlsize=3 label=$smarty.const.STR_ATTR_SHORT required=true}
			<input type="text" class="form-control" maxlength="10" id="short" name="short" value="{$VAL_SHORT|escape|trim}">
		{/dcl_form_control}
		{dcl_form_control id=name controlsize=4 label=$smarty.const.STR_CMMN_NAME required=true}
			<input type="text" class="form-control" maxlength="20" id="name" name="name" value="{$VAL_NAME|escape}">
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input type="button" class="btn btn-primary" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
				<input type="button" class="btn btn-link" onclick="location.href = '{$URL_MAIN_PHP}?menuAction=AttributeSet.Index';" value="{$smarty.const.STR_CMMN_CANCEL}">
			</div>
		</div>
	</fieldset>
</form>
{/block}
{block name=script}
	{dcl_validator_init}
	<script type="text/javascript">
		function validateAndSubmitForm(form)
		{
			var aValidators = [
				new ValidatorString(form.elements["short"], "{$smarty.const.STR_ACTN_SHORT}"),
				new ValidatorString(form.elements["name"], "{$smarty.const.STR_CMMN_NAME}")
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