<div class="btn-group">
{strip}
<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Project.Dashboard&id={$VAL_PROJECTID}">Dashboard</a>
<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Project.Detail&id={$VAL_PROJECTID}">Tasks</a>
<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=boWatches.add&typeid=2&whatid1={$VAL_PROJECTID}">Watch</a>
<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlWiki.show&name=FrontPage&type=1&id={$VAL_PROJECTID}">{$smarty.const.STR_CMMN_WIKI}</a>
<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlProjectTimeline.GetCriteria&projectid={$VAL_PROJECTID}">Timeline</a>
{if $PERM_AUDIT}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlAudit.show&type={$smarty.const.DCL_ENTITY_PROJECT}&id={$VAL_PROJECTID}">Audit</a>{/if}
<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Project.Edit&id={$VAL_PROJECTID}">{$smarty.const.STR_CMMN_EDIT}</a>
<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Project.Delete&id={$VAL_PROJECTID}">{$smarty.const.STR_CMMN_DELETE}</a>
{/strip}
</div>