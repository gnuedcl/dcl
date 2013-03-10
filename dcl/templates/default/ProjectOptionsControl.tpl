<tr class="toolbar"><th colspan="4">
<ul>
{strip}
{assign var="ctlProjectOptions_isfirst" value="true"}
<li {if $ctlProjectOptions_isfirst == "true"}{assign var="ctlProjectOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=Project.Dashboard&id={$VAL_PROJECTID}">Dashboard</a></li>
<li {if $ctlProjectOptions_isfirst == "true"}{assign var="ctlProjectOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=Project.Detail&id={$VAL_PROJECTID}">Tasks</a></li>
<li {if $ctlProjectOptions_isfirst == "true"}{assign var="ctlProjectOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=Project.Tree&project={$VAL_PROJECTID}">Tree</a></li>
<li {if $ctlProjectOptions_isfirst == "true"}{assign var="ctlProjectOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=boWatches.add&typeid=2&whatid1={$VAL_PROJECTID}">Watch</a></li>
<li {if $ctlProjectOptions_isfirst == "true"}{assign var="ctlProjectOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=htmlWiki.show&name=FrontPage&type=1&id={$VAL_PROJECTID}">{$smarty.const.STR_CMMN_WIKI}</a></li>
<li {if $ctlProjectOptions_isfirst == "true"}{assign var="ctlProjectOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=htmlProjectTimeline.GetCriteria&projectid={$VAL_PROJECTID}">Timeline</a></li>
{if $PERM_AUDIT}<li {if $ctlProjectOptions_isfirst == "true"}{assign var="ctlProjectOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=htmlAudit.show&type={$smarty.const.DCL_ENTITY_PROJECT}&id={$VAL_PROJECTID}">Audit</a></li>{/if}
<li {if $ctlProjectOptions_isfirst == "true"}{assign var="ctlProjectOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=Project.Edit&id={$VAL_PROJECTID}">{$smarty.const.STR_CMMN_EDIT}</a></li>
<li {if $ctlProjectOptions_isfirst == "true"}{assign var="ctlProjectOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=Project.Delete&id={$VAL_PROJECTID}">{$smarty.const.STR_CMMN_DELETE}</a></li>
{/strip}
</ul></th></tr>