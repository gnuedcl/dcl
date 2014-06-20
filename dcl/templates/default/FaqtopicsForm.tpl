{dcl_validator_init}
<script language="JavaScript">
function validateAndSubmitForm(form)

{

	var aValidators = new Array(
			new ValidatorInteger(form.elements["seq"], "{$smarty.const.STR_FAQ_DISPLAYSEQ}", true),
			new ValidatorString(form.elements["name"], "{$smarty.const.STR_FAQ_NAME}"),
			new ValidatorString(form.elements["description"], "{$smarty.const.STR_FAQ_DESCRIPTION}")
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
<form class="styled" name="FAQFORM" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{if $IS_EDIT}FaqTopic.Update{else}FaqTopic.Insert{/if}">
	{if $IS_EDIT}<input type="hidden" name="topicid" value="{$VAL_TOPICID}">{/if}
	<input type="hidden" name="faqid" value="{$VAL_FAQID}">
	<fieldset>
		<legend>{if $IS_EDIT}{$smarty.const.STR_FAQ_EDITFAQTOPIC}{else}{$smarty.const.STR_FAQ_ADDFAQTOPIC}{/if}</legend>
		<div class="required">
			<label for="seq">{$smarty.const.STR_FAQ_DISPLAYSEQ}:</label>
			<input type="text" size="5" maxlength="10" id="seq" name="seq" value="{$VAL_SEQ}">
		</div>
		<div class="required">
			<label for="name">{$smarty.const.STR_FAQ_NAME}:</label>
			<input type="text" size="50" maxlength="100" id="name" name="name" value="{$VAL_NAME|escape}">
		</div>
		<div class="required">
			<label for="description">{$smarty.const.STR_FAQ_DESCRIPTION}:</label>
			<textarea id="description" name="description" rows="6" cols="70" wrap valign="top">{$VAL_DESCRIPTION|escape}</textarea>
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="button" value="{$smarty.const.STR_CMMN_CANCEL}" onclick="location.href='{$URL_MAIN_PHP}?menuAction=Faq.Detail&faqid={$VAL_FAQID}';">
		</div>
	</fieldset>
</form>