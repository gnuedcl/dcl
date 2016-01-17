{extends file="_Layout.tpl"}
{block name=title}{if $IS_EDIT}{$smarty.const.STR_FAQ_EDITFAQANSWER|escape}{else}{$smarty.const.STR_FAQ_ADDFAQANSWER|escape}{/if}{/block}
{block name=content}
<form class="form-horizontal" name="FAQFORM" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{if $IS_EDIT}FaqAnswer.Update{else}FaqAnswer.Insert{/if}">
	{if $IS_EDIT}<input type="hidden" name="answerid" value="{$VAL_ANSWERID}">{/if}
	<input type="hidden" name="questionid" value="{$VAL_QUESTIONID}">
	<fieldset>
		<legend>{if $IS_EDIT}{$smarty.const.STR_FAQ_EDITFAQANSWER|escape}{else}{$smarty.const.STR_FAQ_ADDFAQANSWER|escape}{/if}</legend>
		{dcl_form_control id=answertext controlsize=10 label=$smarty.const.STR_FAQ_ANSWER required=true}
			<textarea class="form-control" id="answertext" name="answertext" rows="6" wrap valign="top">{$VAL_ANSWERTEXT|escape}</textarea>
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
				<input class="btn btn-link" type="button" value="{$smarty.const.STR_CMMN_CANCEL}" onclick="location.href='{$URL_MAIN_PHP}?menuAction=FaqQuestion.Index&questionid={$VAL_QUESTIONID}';">
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
		var aValidators = new Array(
				new ValidatorString(form.elements["answertext"], "{$smarty.const.STR_FAQ_ANSWER}")
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