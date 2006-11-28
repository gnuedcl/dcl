<!-- $Id: htmlFaqtopicsDetail.tpl,v 1.3 2006/11/27 06:00:51 mdean Exp $ -->
<center>
<table border="0" cellspacing="0" style="width: 80%;">
	<tr><th class="detailTitle"><a href="{$URL_MAIN_PHP}?menuAction=boFaq.view&faqid={$VAL_FAQID}">{$VAL_FAQNAME|escape}</a> : {$VAL_TOPICNAME}</th>
		<th class="detailLinks">{if $PERM_ADDQUESTION}<a class="adark" href="{$URL_MENULINK}?menuAction=boFaqquestions.add&topicid={$VAL_TOPICID}">{$smarty.const.STR_CMMN_NEW}</a>{else}&nbsp;{/if}</th>
	</tr>
	<tr><td colspan="2">{$VAL_DESCRIPTION|escape|nl2br}</td></tr>
{section name=question loop=$VAL_QUESTIONS}
	{if $smarty.section.question.first}
		<tr><td colspan="2"><ol>
	{/if}
		<li><a class="adark" href="{$URL_MAIN_PHP}?menuAction=boFaqquestions.view&questionid={$VAL_QUESTIONS[question].questionid}">{$VAL_QUESTIONS[question].questiontext|escape|nl2br}</a>
		{if $PERM_MODIFY || $PERM_DELETE}&nbsp;[&nbsp;
		{if $PERM_MODIFY}<a href="{$URL_MAIN_PHP}?menuAction=boFaqquestions.modify&questionid={$VAL_QUESTIONS[question].questionid}">{$smarty.const.STR_CMMN_EDIT}</a>{/if}
		{if $PERM_DELETE}{if $PERM_MODIFY}&nbsp;|&nbsp;{/if}<a href="{$URL_MAIN_PHP}?menuAction=boFaqquestions.delete&questionid={$VAL_QUESTIONS[question].questionid}">{$smarty.const.STR_CMMN_DELETE}</a>{/if}
		&nbsp;]
		{/if}
		</li>
	{if $smarty.section.topic.last}
		</ol></td></tr>
	{/if}
{sectionelse}
	<tr><td colspan="2">{$smarty.const.STR_FAQ_NOQUESTIONS}</td></tr>
{/section}
</table>
</center>