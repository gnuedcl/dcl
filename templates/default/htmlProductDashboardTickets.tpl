<!-- $Id$ -->
<div class="dcl_detail" style="width:100%;">
	<table class="styled">
		<caption>[{$VAL_ID}] {$VAL_NAME}</caption>
		<thead>
			<tr class="toolbar"><th colspan="4"><ul>{strip}
				<li class="first"><a href="{$URL_MAIN_PHP}?menuAction=boProducts.view&id={$VAL_ID}">Summary</a></li>
				<li><a href="{$URL_MAIN_PHP}?menuAction=htmlProductDashboard.Show&id={$VAL_ID}">Dashboard</a> (<a href="{$URL_MAIN_PHP}?menuAction=htmlProductDashboard.ShowTicket&id={$VAL_ID}">Tickets</a>)</li>
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
			<tr><td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=imgProduct.byStatusTicket&id={$VAL_ID}"></td>
				<td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=imgProduct.byModuleTicket&id={$VAL_ID}"></td>
			</tr>
			<tr><td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=imgProduct.byTypeTicket&id={$VAL_ID}"></td>
				<td colspan="2"><img src="{$URL_MAIN_PHP}?menuAction=imgProduct.byPriorityTicket&id={$VAL_ID}"></td>
			<tr>
		</tbody>
	</table>
</div>