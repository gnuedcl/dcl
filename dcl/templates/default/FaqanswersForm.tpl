{dcl_validator_init}
<script language="JavaScript">
function validateAndSubmitForm(form)
{literal}
{
{/literal}
	var aValidators = new Array(
			new ValidatorString(form.elements["answertext"], "{$smarty.const.STR_FAQ_ANSWER}")
		);
{literal}
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
{/literal}
</script>
<form class="styled" name="FAQFORM" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{if $IS_EDIT}FaqAnswer.Update{else}FaqAnswer.Insert{/if}">
	{if $IS_EDIT}<input type="hidden" name="answerid" value="{$VAL_ANSWERID}">{/if}
	<input type="hidden" name="questionid" value="{$VAL_QUESTIONID}">
	<fieldset>
		<legend>{if $IS_EDIT}{$smarty.const.STR_FAQ_EDITFAQANSWER}{else}{$smarty.const.STR_FAQ_ADDFAQANSWER}{/if}</legend>
		<div class="required">
			<label for="answertext">{$smarty.const.STR_FAQ_ANSWER}:</label>
			<textarea id="answertext" name="answertext" rows="6" cols="70" wrap valign="top">{$VAL_ANSWERTEXT|escape}</textarea>
		</tr>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
			<input type="button" value="{$smarty.const.STR_CMMN_CANCEL}" onclick="location.href='{$URL_MAIN_PHP}?menuAction=FaqQuestion.Index&questionid={$VAL_QUESTIONID}';">
		</div>
	</fieldset>
</form>