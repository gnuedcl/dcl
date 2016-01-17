{extends file="_Layout.tpl"}
{block name=title}FAQ Question{/block}
<h3><a href="{$URL_MAIN_PHP}?menuAction=Faq.Detail&faqid={$VAL_FAQID}">{$VAL_FAQNAME|escape}</a> : <a href="{$URL_MAIN_PHP}?menuAction=FaqTopic.Index&topicid={$VAL_TOPICID}">{$VAL_TOPICNAME|escape}</a>{if $PERM_ADDANSWER}<div class="pull-right"><a href="{$URL_MENULINK}?menuAction=FaqAnswer.Create&questionid={$VAL_QUESTIONID}" class="pull-right btn btn-success btn-xs" title="{$smarty.const.STR_CMMN_NEW|escape}">
			<span class="glyphicon glyphicon-plus"></span></a></div>{/if}</h3>
<p>{$VAL_QUESTIONTEXT|escape|nl2br}</p>
{section name=answer loop=$VAL_ANSWERS}
	<div class="panel panel-default"><div class="panel-body">
			{$VAL_ANSWERS[answer].answertext|escape|nl2br}
			{if $PERM_MODIFY || $PERM_DELETE}<div class="pull-right">
				{if $PERM_MODIFY}<a href="{$URL_MAIN_PHP}?menuAction=FaqAnswer.Edit&answerid={$VAL_ANSWERS[answer].answerid}" class="btn btn-primary btn-xs" title="{$smarty.const.STR_CMMN_EDIT|escape}">
						<span class="glyphicon glyphicon-pencil"></span>
					</a>{/if}
				{if $PERM_DELETE}<a href="{$URL_MAIN_PHP}?menuAction=FaqAnswer.Delete&answerid={$VAL_ANSWERS[answer].answerid}" class="btn btn-danger btn-xs" title="{$smarty.const.STR_CMMN_DELETE|escape}">
						<span class="glyphicon glyphicon-trash"></span>
					</a>
				{/if}
				</div>{/if}
		</div></div>
	{sectionelse}
	<h4>{$smarty.const.STR_FAQ_NOANSWERS|escape}</h4>
{/section}