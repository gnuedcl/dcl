{dcl_validator_init}
<form class="form-horizontal" name="FAQFORM" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="{if $IS_EDIT}FaqQuestion.Update{else}FaqQuestion.Insert{/if}">
	{if $IS_EDIT}<input type="hidden" name="questionid" value="{$VAL_QUESTIONID}">{/if}
	<input type="hidden" name="topicid" value="{$VAL_TOPICID}">
	<fieldset>
		<legend>{if $IS_EDIT}{$smarty.const.STR_FAQ_EDITFAQQUESTION|escape}{else}{$smarty.const.STR_FAQ_ADDFAQQUESTION|escape}{/if}</legend>
		{dcl_form_control id=seq controlsize=2 label=$smarty.const.STR_FAQ_DISPLAYSEQ required=true}
		{dcl_input_text id=seq maxlength=10 value=$VAL_SEQ}
		{/dcl_form_control}
		{dcl_form_control id=questiontext controlsize=10 label=$smarty.const.STR_FAQ_QUESTION required=true}
			<textarea class="form-control" id="questiontext" name="questiontext" rows="6" wrap valign="top">{$VAL_QUESTIONTEXT|escape}</textarea>
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="button" onclick="validateAndSubmitForm(this.form);" value="{$smarty.const.STR_CMMN_SAVE}">
				<input class="btn btn-link" type="button" value="{$smarty.const.STR_CMMN_CANCEL}" onclick="location.href='{$URL_MAIN_PHP}?menuAction=FaqTopic.Index&topicid={$VAL_TOPICID}';">
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript">
	function validateAndSubmitForm(form)
	{
		var aValidators = [
				new ValidatorInteger(form.elements["seq"], "{$smarty.const.STR_FAQ_DISPLAYSEQ}", true),
				new ValidatorString(form.elements["questiontext"], "{$smarty.const.STR_FAQ_QUESTION}")
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
