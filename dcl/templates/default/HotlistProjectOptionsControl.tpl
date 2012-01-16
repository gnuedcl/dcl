<tr class="toolbar"><th colspan="4">
<ul>
{strip}
{assign var="ctlHotlistProjectOptions_isfirst" value="true"}
<li {if $ctlHotlistProjectOptions_isfirst == "true"}{assign var="ctlHotlistProjectOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=htmlHotlistProjectDashboard.show&id={$VAL_HOTLISTID}">Dashboard</a></li>
<li {if $ctlHotlistProjectOptions_isfirst == "true"}{assign var="ctlHotlistProjectOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=htmlHotlistProject.View&id={$VAL_HOTLISTID}">Tasks</a></li>
<li {if $ctlHotlistProjectOptions_isfirst == "true"}{assign var="ctlHotlistProjectOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=Hotlist.Prioritize&hotlist_id={$VAL_HOTLISTID}">Prioritize</a></li>
<li {if $ctlHotlistProjectOptions_isfirst == "true"}{assign var="ctlHotlistProjectOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=htmlHotlistBrowse.show">Manage Hotlists</a></li>
{if false}<li {if $ctlHotlistProjectOptions_isfirst == "true"}{assign var="ctlHotlistProjectOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=boWatches.add&typeid=2&whatid1={$VAL_HOTLISTID}">Watch</a></li>{/if}
{if false}<li {if $ctlHotlistProjectOptions_isfirst == "true"}{assign var="ctlHotlistProjectOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=htmlHotlistProjectTimeline.GetCriteria&id={$VAL_HOTLISTID}">Timeline</a></li>{/if}
{if false}{if $PERM_AUDIT}<li {if $ctlHotlistProjectOptions_isfirst == "true"}{assign var="ctlHotlistProjectOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=htmlAudit.show&type={$smarty.const.DCL_ENTITY_HOTLIST}&id={$VAL_HOTLISTID}">Audit</a></li>{/if}{/if}
<li {if $ctlHotlistProjectOptions_isfirst == "true"}{assign var="ctlHotlistProjectOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=htmlHotlistForm.modify&hotlist_id={$VAL_HOTLISTID}">{$smarty.const.STR_CMMN_EDIT}</a></li>
<li {if $ctlHotlistProjectOptions_isfirst == "true"}{assign var="ctlHotlistProjectOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=htmlHotlistForm.delete&hotlist_id={$VAL_HOTLISTID}">{$smarty.const.STR_CMMN_DELETE}</a></li>
{/strip}
</ul></th></tr>