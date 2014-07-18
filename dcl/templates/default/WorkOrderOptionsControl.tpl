{assign var="HAS_PERM" value="$PERM_ACTION || $PERM_ASSIGN || $PERM_ADD || $PERM_ADDTASK || $PERM_VIEWCHANGELOG || $PERM_REMOVETASK || $PERM_ATTACHFILE || $PERM_VIEW || $PERM_VIEWWIKI || $PERM_MODIFY || $PERM_DELETE || $PERM_AUDIT"}
{if $HAS_PERM}
	<li class="dropdown">
		<a href="javascript:;" id="options" class="dropdown-toggle" data-toggle="dropdown">{$smarty.const.STR_CMMN_OPTIONS} <b class="caret"></b></a>
		<ul class="dropdown-menu" role="menu" aria-labelledby="options">
{strip}
{if $PERM_ACTION}<li><a href="{$URL_MAIN_PHP}?menuAction=boTimecards.add&jcn={$VAL_JCN}&seq={$VAL_SEQ}" tabindex="-1">Time Card</a></li>{/if}
{if $PERM_ASSIGN}<li><a href="{$URL_MAIN_PHP}?menuAction=WorkOrder.Reassign&jcn={$VAL_JCN}&seq={$VAL_SEQ}" tabindex="-1">Assign</a></li>{/if}
{if $PERM_ADD}
<li ><a href="{$URL_MAIN_PHP}?menuAction=WorkOrder.CreateSequence&jcn={$VAL_JCN}" tabindex="-1">Sequence</a></li>
<li><a href="{$URL_MAIN_PHP}?menuAction=WorkOrder.Copy&jcn={$VAL_JCN}&seq={$VAL_SEQ}" tabindex="-1">Copy as New</a></li>
<li><a href="{$URL_MAIN_PHP}?menuAction=WorkOrder.CopyAsSequence&jcn={$VAL_JCN}&seq={$VAL_SEQ}" tabindex="-1">Copy as Sequence</a></li>
{/if}
{if $PERM_ADDTASK}<li><a href="{$URL_MAIN_PHP}?menuAction=Project.AddTask&jcn={$VAL_JCN}&seq={$VAL_SEQ}" tabindex="-1">Project</a></li>{/if}
{if $PERM_VIEWCHANGELOG}<li><a href="{$URL_MAIN_PHP}?menuAction=WorkOrder.ChangeLog&jcn={$VAL_JCN}&seq={$VAL_SEQ}" tabindex="-1">ChangeLog</a></li>{/if}
{if $PERM_REMOVETASK}<li><a href="{$URL_MAIN_PHP}?menuAction=Project.RemoveTask&jcn={$VAL_JCN}&seq={$VAL_SEQ}" tabindex="-1">Remove from Project</a></li>{/if}
{if $PERM_VIEW}<li><a href="{$URL_MAIN_PHP}?menuAction=boWatches.add&typeid={$VAL_WATCHTYPE}&whatid1={$VAL_JCN}&whatid2={$VAL_SEQ}" tabindex="-1">Watch</a></li>{/if}
{if $PERM_VIEWWIKI}<li><a href="{$URL_MAIN_PHP}?menuAction=htmlWiki.show&type={$smarty.const.DCL_ENTITY_WORKORDER}&id={$VAL_JCN}&id2={$VAL_SEQ}" tabindex="-1">{$smarty.const.STR_CMMN_WIKI}</a></li>{/if}
{if $PERM_MODIFY}<li><a href="{$URL_MAIN_PHP}?menuAction=WorkOrder.Edit&jcn={$VAL_JCN}&seq={$VAL_SEQ}" tabindex="-1">{$smarty.const.STR_CMMN_EDIT}</a></li>{/if}
{if $PERM_DELETE}<li><a href="{$URL_MAIN_PHP}?menuAction=WorkOrder.Delete&jcn={$VAL_JCN}&seq={$VAL_SEQ}" tabindex="-1">{$smarty.const.STR_CMMN_DELETE}</a></li>{/if}
{if $PERM_AUDIT}<li><a href="{$URL_MAIN_PHP}?menuAction=htmlAudit.show&type={$smarty.const.DCL_ENTITY_WORKORDER}&id={$VAL_JCN}&id2={$VAL_SEQ}" tabindex="-1">{$smarty.const.STR_CMMN_AUDIT}</a></li>{/if}
{/strip}
		</ul>
	</li>
{/if}