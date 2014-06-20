{dcl_validator_init}
<script language="JavaScript">
function validateAndSubmitForm(form)

{

	var aValidators = new Array(
			new ValidatorInteger(form.elements["seq"], "{$smarty.const.STR_FAQ_DISPLAYSEQ}", true),
			new ValidatorString(form.elements["questiontext"], "{$smarty.const.STR_FAQ_QUESTION}")
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
	<input type="hidden" name="menuAction" value="{if $IS_EDIT}FaqQuestion.Update{else}FaqQuestion.Insert{/if}">
	{if $IS_EDIT}<input type="hidden" name="questionid" value="{$VAL_QUESTIONID}">{/if}
	<input type="hidden" name="topicid" value="{$VAL_TOPICID}">
	<fieldset>
		<legend>{if $IS_EDIT}{$smarty.const.STR_FAQ_EDITFAQQUESTION}{else}{$smarty.const.STR_FAQ_ADDFAQQUESTION}{/if}</legend>
		<div class="required">
			<label for="seq">{$smarty.const.STR_FAQ_DISPLAYSEQ}:</label>
			<input type="text" size="5" maxlength="10" id="seq" name="seq" value="{$VAL_SEQ|escape}">
		</div>
		<div class="required">
			<label for="questiontext">{$smarty.const.STR_FAQ_QUESTION}</label>
			<textarea id="questiontext" name="questiontext" rows="6" cols="70" wrap valign="top">{$VAL_QUESTIONTEXT|escape}</textarea>
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="button" value="{$smarty.const.STR_CMMN_CANCEL}" onclick="location.href='{$URL_MAIN_PHP}?menuAction=FaqTopic.Index&topicid={$VAL_TOPICID}';">
		</div>
	</fieldset>
</form>