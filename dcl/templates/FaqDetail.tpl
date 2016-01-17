{extends file="_Layout.tpl"}
{block name=title}{$VAL_NAME|escape}{/block}
{block name=content}
<h3>{$VAL_NAME|escape}{if $PERM_ADDTOPIC}<div class="pull-right"><a href="{$URL_MENULINK}?menuAction=FaqTopic.Create&faqid={$VAL_FAQID}" class="pull-right btn btn-success btn-xs" title="{$smarty.const.STR_CMMN_NEW|escape}">
			<span class="glyphicon glyphicon-plus"></span></a></div>{/if}</h3>
<p>{$VAL_DESCRIPTION|escape|nl2br}</p>
{section name=topic loop=$VAL_TOPICS}
	<div class="panel panel-info">
		<div class="panel-heading">
			<a href="{$URL_MENULINK}?menuAction=FaqTopic.Index&topicid={$VAL_TOPICS[topic].topicid}">{$VAL_TOPICS[topic].name|escape}</a>
			{if $PERM_MODIFY || $PERM_DELETE}<div class="pull-right">
				{if $PERM_MODIFY}<a href="{$URL_MAIN_PHP}?menuAction=FaqTopic.Edit&topicid={$VAL_TOPICS[topic].topicid}" class="btn btn-primary btn-xs" title="{$smarty.const.STR_CMMN_EDIT|escape}">
					<span class="glyphicon glyphicon-pencil"></span>
				</a>{/if}
				{if $PERM_DELETE}<a href="{$URL_MAIN_PHP}?menuAction=FaqTopic.Delete&topicid={$VAL_TOPICS[topic].topicid}" class="btn btn-danger btn-xs" title="{$smarty.const.STR_CMMN_DELETE|escape}">
						<span class="glyphicon glyphicon-trash"></span>
					</a>
				{/if}
			</div>{/if}
		</div>
		<div class="panel-body">{$VAL_TOPICS[topic].description|escape|nl2br}</div>
	</div>
{sectionelse}
	<h4>{$smarty.const.STR_FAQ_NOTOPICS|escape}</h4>
{/section}
{/block}