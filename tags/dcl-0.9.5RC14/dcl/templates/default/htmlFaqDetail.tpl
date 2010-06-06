<!-- $Id$ -->
<center>
<table border="0" cellspacing="0" style="width: 80%;">
	<tr><th class="detailTitle">{$VAL_NAME|escape}</th>
		<th class="detailLinks">{if $PERM_ADDTOPIC}<a class="adark" href="{$URL_MENULINK}?menuAction=boFaqtopics.add&faqid={$VAL_FAQID}">{$smarty.const.STR_CMMN_NEW}</a>{else}&nbsp;{/if}</th>
	</tr>
	<tr><td colspan="2">{$VAL_DESCRIPTION|escape|nl2br}</td></tr>
{section name=topic loop=$VAL_TOPICS}
	{if $smarty.section.topic.first}
		<tr><td colspan="2"><ol>
	{/if}
		<li><a class="adark" href="{$URL_MENULINK}?menuAction=boFaqtopics.view&topicid={$VAL_TOPICS[topic].topicid}">{$VAL_TOPICS[topic].name}</a>
			<br />{$VAL_TOPICS[topic].description|escape|nl2br}
			{if $PERM_MODIFY || $PERM_DELETE}&nbsp;[&nbsp;
			{if $PERM_MODIFY}<a href="{$URL_MAIN_PHP}?menuAction=boFaqtopics.modify&topicid={$VAL_TOPICS[topic].topicid}">{$smarty.const.STR_CMMN_EDIT}</a>{/if}
			{if $PERM_DELETE}{if $PERM_MODIFY}&nbsp;|&nbsp;{/if}<a href="{$URL_MAIN_PHP}?menuAction=boFaqtopics.delete&topicid={$VAL_TOPICS[topic].topicid}">{$smarty.const.STR_CMMN_DELETE}</a>{/if}
			&nbsp;]
			{/if}
		</li>
	{if $smarty.section.topic.last}
		</ol></td></tr>
	{/if}
{sectionelse}
	<tr><td colspan="2">{$smarty.const.STR_FAQ_NOTOPICS}</td></tr>
{/section}
</table>
</center>
