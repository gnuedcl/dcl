<!-- $Id$ -->
<div class="dcl_detail" style="width:100%;">
	<table class="styled">
		<caption>[{$VAL_ID}] {$VAL_NAME}</caption>
		<thead>
			<tr class="toolbar"><th colspan="4"><ul>{strip}
				<li class="first"><a href="{$URL_MAIN_PHP}?menuAction=boProducts.view&id={$VAL_ID}">Summary</a></li>
				{if $PERM_VIEWWO}<li><a href="{$URL_MAIN_PHP}?menuAction=boProducts.viewWO&id={$VAL_ID}">{$smarty.const.STR_PROD_VIEWWO}</a>
					&nbsp;(<a href="{$URL_MAIN_PHP}?menuAction=boWatches.addWorkorder&typeid=1&whatid1={$VAL_ID}">Watch</a>)</li>
				{/if}
				{if $PERM_VIEWTCK}<li><a href="{$URL_MAIN_PHP}?menuAction=boProducts.viewTck&id={$VAL_ID}">{$smarty.const.STR_PROD_VIEWTICKETS}</a>
					&nbsp;(<a href="{$URL_MAIN_PHP}?menuAction=boWatches.addTicket&typeid=4&whatid1={$VAL_ID}">Watch</a>)</li>
				{/if}
				<li><a href="{$URL_MAIN_PHP}?menuAction=htmlProductModules.PrintAll&product_id={$VAL_ID}">{$smarty.const.STR_PROD_VIEWMODULES}</a></li>
				{if $PERM_WIKI}<li><a href="{$URL_MAIN_PHP}?menuAction=htmlWiki.show&name=FrontPage&type={$smarty.const.DCL_ENTITY_PRODUCT}&id={$VAL_ID}">{$smarty.const.STR_CMMN_WIKI}</a></li>{/if}
				{if $PERM_VERSIONS}<li><a href="{$URL_MAIN_PHP}?menuAction=boProducts.viewRelease&id={$VAL_ID}">Versions</a></li>{/if}
				{if $PERM_EDIT}<li><a href="{$URL_MAIN_PHP}?menuAction=htmlProducts.modify&id={$VAL_ID}">{$smarty.const.STR_CMMN_EDIT}</a></li>{/if}
				{if $PERM_DELETE}<li><a href="{$URL_MAIN_PHP}?menuAction=htmlProducts.delete&id={$VAL_ID}">{$smarty.const.STR_CMMN_DELETE}</a></li>{/if}
{/strip}</ul></th></tr></thead>
		<tbody>
			<tr><th>{$smarty.const.STR_CMMN_ACTIVE}:</th><td>{if $VAL_ACTIVE == "Y"}{$smarty.const.STR_CMMN_YES}{else}{$smarty.const.STR_CMMN_NO}{/if}</td>
				<th>{$smarty.const.STR_PROD_REPORTTO}:</th><td>{$VAL_REPORTTO|escape}</td>
			</tr>
			<tr><th>{$smarty.const.STR_CMMN_PUBLIC}:</th><td>{if $VAL_PUBLIC == "Y"}{$smarty.const.STR_CMMN_YES}{else}{$smarty.const.STR_CMMN_NO}{/if}</td>
				<th>{$smarty.const.STR_PROD_TICKETSTO}:</th><td>{$VAL_TICKETSTO|escape}</td>
			</tr>
		</tbody>
	</table>
</div>
<div class="clear"></div>