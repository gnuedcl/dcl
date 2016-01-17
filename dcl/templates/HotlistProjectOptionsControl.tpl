<tr><th colspan="4">
<div class="btn-group">
{strip}
<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlHotlistProjectDashboard.show&id={$VAL_HOTLISTID}">Dashboard</a>
<a class="btn btn-default" class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlHotlistProject.View&id={$VAL_HOTLISTID}">Tasks</a>
<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Hotlist.Prioritize&hotlist_id={$VAL_HOTLISTID}">Prioritize</a>
<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlHotlistBrowse.show">Manage Hotlists</a>
{if false}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=boWatches.add&typeid=2&whatid1={$VAL_HOTLISTID}">Watch</a>{/if}
{if false}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlHotlistProjectTimeline.GetCriteria&id={$VAL_HOTLISTID}">Timeline</a>{/if}
{if false}{if $PERM_AUDIT}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlAudit.show&type={$smarty.const.DCL_ENTITY_HOTLIST}&id={$VAL_HOTLISTID}">Audit</a>{/if}{/if}
<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Hotlist.Edit&hotlist_id={$VAL_HOTLISTID}">{$smarty.const.STR_CMMN_EDIT}</a>
<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Hotlist.Delete&hotlist_id={$VAL_HOTLISTID}">{$smarty.const.STR_CMMN_DELETE}</a>
{/strip}
</div></th></tr>