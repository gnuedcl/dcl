<!-- $Id$ -->
{assign var="HAS_PERM" value="$PERM_ACTION || $PERM_ASSIGN || $PERM_ADD || $PERM_ADDTASK || $PERM_VIEWCHANGELOG || $PERM_REMOVETASK || $PERM_ATTACHFILE || $PERM_VIEW || $PERM_VIEWWIKI || $PERM_MODIFY || $PERM_DELETE || $PERM_AUDIT"}
{if $HAS_PERM}
<tr class="toolbar"><th colspan="4">
<ul>
{strip}
{assign var="ctlWorkOrderOptions_isfirst" value="true"}
{if $PERM_ACTION}<li {if $ctlWorkOrderOptions_isfirst == "true"}{assign var="ctlWorkOrderOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=boTimecards.add&jcn={$VAL_JCN}&seq={$VAL_SEQ}">Time Card</a></li>{/if}
{if $PERM_ASSIGN}<li {if $ctlWorkOrderOptions_isfirst == "true"}{assign var="ctlWorkOrderOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=boWorkorders.reassign&jcn={$VAL_JCN}&seq={$VAL_SEQ}">Assign</a></li>{/if}
{if $PERM_ADD}<li {if $ctlWorkOrderOptions_isfirst == "true"}{assign var="ctlWorkOrderOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=boWorkorders.newseq&jcn={$VAL_JCN}">Sequence</a></li>{/if}
{if $PERM_ADDTASK}<li {if $ctlWorkOrderOptions_isfirst == "true"}{assign var="ctlWorkOrderOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=boProjects.addtoproject&jcn={$VAL_JCN}&seq={$VAL_SEQ}">Project</a></li>{/if}
{if $PERM_VIEWCHANGELOG}<li {if $ctlWorkOrderOptions_isfirst == "true"}{assign var="ctlWorkOrderOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=htmlWorkorders.changeLog&jcn={$VAL_JCN}&seq={$VAL_SEQ}">ChangeLog</a></li>{/if}
{if $PERM_REMOVETASK}<li {if $ctlWorkOrderOptions_isfirst == "true"}{assign var="ctlWorkOrderOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=boProjects.unmap&jcn={$VAL_JCN}&seq={$VAL_SEQ}">Remove from Project</a></li>{/if}
{if $PERM_VIEW}<li {if $ctlWorkOrderOptions_isfirst == "true"}{assign var="ctlWorkOrderOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=boWatches.add&typeid={$VAL_WATCHTYPE}&whatid1={$VAL_JCN}&whatid2={$VAL_SEQ}">Watch</a></li>{/if}
{if $PERM_VIEWWIKI}<li {if $ctlWorkOrderOptions_isfirst == "true"}{assign var="ctlWorkOrderOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=htmlWiki.show&type={$smarty.const.DCL_ENTITY_WORKORDER}&id={$VAL_JCN}&id2={$VAL_SEQ}">{$smarty.const.STR_CMMN_WIKI}</a></li>{/if}
{if $PERM_MODIFY}<li {if $ctlWorkOrderOptions_isfirst == "true"}{assign var="ctlWorkOrderOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=boWorkorders.modifyjcn&jcn={$VAL_JCN}&seq={$VAL_SEQ}">{$smarty.const.STR_CMMN_EDIT}</a></li>{/if}
{if $PERM_DELETE}<li {if $ctlWorkOrderOptions_isfirst == "true"}{assign var="ctlWorkOrderOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=boWorkorders.delete&jcn={$VAL_JCN}&seq={$VAL_SEQ}">{$smarty.const.STR_CMMN_DELETE}</a></li>{/if}
{if $PERM_AUDIT}<li {if $ctlWorkOrderOptions_isfirst == "true"}{assign var="ctlWorkOrderOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=htmlAudit.show&type={$smarty.const.DCL_ENTITY_WORKORDER}&id={$VAL_JCN}&id2={$VAL_SEQ}">{$smarty.const.STR_CMMN_AUDIT}</a></li>{/if}
{/strip}
</ul></th></tr>
{/if}