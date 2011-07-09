<!-- $Id$ -->
<center>
<table border="0" cellspacing="0" style="width: 80%;">
	<tr><th class="detailTitle"><a href="{$URL_MAIN_PHP}?menuAction=Faq.Detail&faqid={$VAL_FAQID}">{$VAL_FAQNAME|escape}</a> : <a href="{$URL_MAIN_PHP}?menuAction=boFaqtopics.view&topicid={$VAL_TOPICID}">{$VAL_TOPICNAME|escape}</a></th>
		<th class="detailLinks">{if $PERM_ADDANSWER}<a class="adark" href="{$URL_MENULINK}?menuAction=FaqAnswer.Create&questionid={$VAL_QUESTIONID}">{$smarty.const.STR_CMMN_NEW}</a>{else}&nbsp;{/if}</th>
	</tr>
	<tr><td colspan="2">{$VAL_QUESTIONTEXT|escape|nl2br}</td></tr>
{section name=answer loop=$VAL_ANSWERS}
	{if $smarty.section.answer.first}
		<tr><td colspan="2"><ol>
	{/if}
		<li>{$VAL_ANSWERS[answer].answertext|escape|nl2br}{if $PERM_MODIFY || $PERM_DELETE}&nbsp;[&nbsp;
		{if $PERM_MODIFY}<a href="{$URL_MAIN_PHP}?menuAction=FaqAnswer.Edit&answerid={$VAL_ANSWERS[answer].answerid}">{$smarty.const.STR_CMMN_EDIT}</a>{/if}
		{if $PERM_DELETE}{if $PERM_MODIFY}&nbsp;|&nbsp;{/if}<a href="{$URL_MAIN_PHP}?menuAction=FaqAnswer.Delete&answerid={$VAL_ANSWERS[answer].answerid}">{$smarty.const.STR_CMMN_DELETE}</a>{/if}
		&nbsp;]
		{/if}</li>
	{if $smarty.section.answer.last}
		</ol></td></tr>
	{/if}
{sectionelse}
	<tr><td colspan="2">{$smarty.const.STR_FAQ_NOANSWERS}</td></tr>
{/section}
</table>
</center>