{extends file="_Layout.tpl"}
{block name=title}{if $IS_EDIT}{$smarty.const.STR_FAQ_EDITFAQTOPIC|escape}{else}{$smarty.const.STR_FAQ_ADDFAQTOPIC|escape}{/if}{/block}
{block name=content}
<form class="form-horizontal" name="FAQFORM" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{if $IS_EDIT}FaqTopic.Update{else}FaqTopic.Insert{/if}">
	{if $IS_EDIT}<input type="hidden" name="topicid" value="{$VAL_TOPICID}">{/if}
	<input type="hidden" name="faqid" value="{$VAL_FAQID}">
	<fieldset>
		<legend>{if $IS_EDIT}{$smarty.const.STR_FAQ_EDITFAQTOPIC|escape}{else}{$smarty.const.STR_FAQ_ADDFAQTOPIC|escape}{/if}</legend>
		{dcl_form_control id=seq controlsize=2 label=$smarty.const.STR_FAQ_DISPLAYSEQ required=true}
		{dcl_input_text id=seq maxlength=10 value=$VAL_SEQ}
		{/dcl_form_control}
		{dcl_form_control id=name controlsize=10 label=$smarty.const.STR_FAQ_NAME required=true}
		{dcl_input_text id=name maxlength=100 value=$VAL_NAME}
		{/dcl_form_control}
		{dcl_form_control id=description controlsize=10 label=$smarty.const.STR_FAQ_DESCRIPTION required=true}
			<textarea class="form-control" id="description" name="description" rows="6" wrap valign="top">{$VAL_DESCRIPTION|escape}</textarea>
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
				<input class="btn btn-link" type="button" value="{$smarty.const.STR_CMMN_CANCEL}" onclick="location.href='{$URL_MAIN_PHP}?menuAction=Faq.Detail&faqid={$VAL_FAQID}';">
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
			new ValidatorInteger(form.elements["seq"], "{$smarty.const.STR_FAQ_DISPLAYSEQ}", true),
			new ValidatorString(form.elements["name"], "{$smarty.const.STR_FAQ_NAME}"),
			new ValidatorString(form.elements["description"], "{$smarty.const.STR_FAQ_DESCRIPTION}")
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