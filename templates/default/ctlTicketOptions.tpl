{assign var="HAS_PERM" value="$PERM_ACTION || $PERM_ASSIGN || $PERM_COPYTOWO || $PERM_ATTACHFILE || $PERM_VIEW || $PERM_VIEWWIKI || $PERM_MODIFY || $PERM_DELETE || $PERM_AUDIT"}
{if $HAS_PERM && !$IS_DELETE}
<tr class="toolbar"><th colspan="4">
<ul>
{strip}
{assign var="ctlTicketOptions_isfirst" value="true"}
{if $PERM_ACTION}<li {if $ctlTicketOptions_isfirst == "true"}{assign var="ctlTicketOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=boTicketresolutions.add&ticketid={$VAL_TICKETID}">{$smarty.const.STR_TCK_OPTADDRESOLUTION}</a></li>{/if}
{if $PERM_ASSIGN}<li {if $ctlTicketOptions_isfirst == "true"}{assign var="ctlTicketOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=boTickets.reassign&ticketid={$VAL_TICKETID}">{$smarty.const.STR_TCK_OPTREASSIGN}</a></li>{/if}
{if $PERM_COPYTOWO}<li {if $ctlTicketOptions_isfirst == "true"}{assign var="ctlTicketOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=boTickets.copyToWO&ticketid={$VAL_TICKETID}">{$smarty.const.STR_TCK_OPTCOPYTOWO}</a></li>{/if}
{if $PERM_ATTACHFILE}<li {if $ctlTicketOptions_isfirst == "true"}{assign var="ctlTicketOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=boTickets.upload&ticketid={$VAL_TICKETID}">{$smarty.const.STR_TCK_OPTUPLOADFILE}</a></li>{/if}
{if $PERM_VIEW}<li {if $ctlTicketOptions_isfirst == "true"}{assign var="ctlTicketOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=boWatches.add&typeid={$VAL_WATCHTYPE}&whatid1={$VAL_TICKETID}">{$smarty.const.STR_TCK_OPTWATCH}</a></li>{/if}
{if $PERM_VIEWWIKI}<li {if $ctlTicketOptions_isfirst == "true"}{assign var="ctlTicketOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=htmlWiki.show&type={$VAL_WIKITYPE}&id={$VAL_TICKETID}">{$smarty.const.STR_CMMN_WIKI}</a></li>{/if}
{if $PERM_AUDIT}<li {if $ctlTicketOptions_isfirst == "true"}{assign var="ctlTicketOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=htmlAudit.show&type={$smarty.const.DCL_ENTITY_TICKET}&id={$VAL_TICKETID}">{$smarty.const.STR_CMMN_AUDIT}</a></li>{/if}
{if $PERM_MODIFY}<li {if $ctlTicketOptions_isfirst == "true"}{assign var="ctlTicketOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=boTickets.modify&ticketid={$VAL_TICKETID}">{$smarty.const.STR_CMMN_EDIT}</a></li>{/if}
{if $PERM_DELETE}<li {if $ctlTicketOptions_isfirst == "true"}{assign var="ctlTicketOptions_isfirst" value="false"}class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=boTickets.delete&ticketid={$VAL_TICKETID}">{$smarty.const.STR_CMMN_DELETE}</a></li>{/if}
{/strip}
</ul></th></tr>
{/if}