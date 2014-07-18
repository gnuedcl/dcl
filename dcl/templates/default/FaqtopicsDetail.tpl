<h3><a href="{$URL_MAIN_PHP}?menuAction=Faq.Detail&faqid={$VAL_FAQID}">{$VAL_FAQNAME|escape}</a> : {$VAL_TOPICNAME|escape}{if $PERM_ADDQUESTION}<div class="pull-right"><a href="{$URL_MENULINK}?menuAction=FaqQuestion.Create&topicid={$VAL_TOPICID}" class="pull-right btn btn-success btn-xs" title="{$smarty.const.STR_CMMN_NEW|escape}">
			<span class="glyphicon glyphicon-plus"></span></a></div>{/if}</h3>
<p>{$VAL_DESCRIPTION|escape|nl2br}</p>
{section name=question loop=$VAL_QUESTIONS}
	<div class="panel panel-default"><div class="panel-body">
		<a href="{$URL_MENULINK}?menuAction=FaqQuestion.Index&questionid={$VAL_QUESTIONS[question].questionid}">{$VAL_QUESTIONS[question].questiontext|escape|nl2br}</a>
		{if $PERM_MODIFY || $PERM_DELETE}<div class="pull-right">
			{if $PERM_MODIFY}<a href="{$URL_MAIN_PHP}?menuAction=FaqQuestion.Edit&questionid={$VAL_QUESTIONS[question].questionid}" class="btn btn-primary btn-xs" title="{$smarty.const.STR_CMMN_EDIT|escape}">
					<span class="glyphicon glyphicon-pencil"></span>
				</a>{/if}
			{if $PERM_DELETE}<a href="{$URL_MAIN_PHP}?menuAction=FaqQuestion.Delete&questionid={$VAL_QUESTIONS[question].questionid}" class="btn btn-danger btn-xs" title="{$smarty.const.STR_CMMN_DELETE|escape}">
					<span class="glyphicon glyphicon-trash"></span>
				</a>
			{/if}
		</div>{/if}
		</div></div>
	{sectionelse}
	<h4>{$smarty.const.STR_FAQ_NOQUESTIONS|escape}</h4>
{/section}