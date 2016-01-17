{extends file="_Layout.tpl"}
{block name=title}[{$VAL_ID}] {$VAL_NAME|escape}{/block}
{block name=content}
<h4>[{$VAL_ID}] {$VAL_NAME|escape}</h4>
<div class="btn-group">
	<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Product.Detail&id={$VAL_ID}">Summary</a></li>
	<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlProductDashboard.Show&id={$VAL_ID}">Dashboard</a>
	<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlProductDashboard.ShowTicket&id={$VAL_ID}">(Tickets)</a>
	{if $PERM_VIEWWO}
		<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Product.DetailWorkOrder&id={$VAL_ID}">{$smarty.const.STR_PROD_VIEWWO}</a>
		<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=boWatches.addWorkorder&typeid=1&whatid1={$VAL_ID}">(Watch)</a>
	{/if}
	{if $PERM_VIEWTCK}
		<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Product.DetailTicket&id={$VAL_ID}">{$smarty.const.STR_PROD_VIEWTICKETS}</a>
		<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=boWatches.addTicket&typeid=4&whatid1={$VAL_ID}">(Watch)</a>
	{/if}
	<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlProductModules.PrintAll&product_id={$VAL_ID}">{$smarty.const.STR_PROD_VIEWMODULES}</a>
	{if $PERM_WIKI}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=htmlWiki.show&name=FrontPage&type={$smarty.const.DCL_ENTITY_PRODUCT}&id={$VAL_ID}">{$smarty.const.STR_CMMN_WIKI}</a>{/if}
	{if $PERM_EDIT}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Product.Edit&id={$VAL_ID}">{$smarty.const.STR_CMMN_EDIT}</a>{/if}
	{if $PERM_DELETE}<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Product.Delete&id={$VAL_ID}">{$smarty.const.STR_CMMN_DELETE}</a>{/if}
</div>
<div class="container">
	<div class="row top12">
		<div class="col-lg-6"><img src="{$URL_MAIN_PHP}?menuAction=ProductImage.WorkOrderStatusChart&id={$VAL_ID}"></div>
		<div class="col-lg-6"><img src="{$URL_MAIN_PHP}?menuAction=ProductImage.WorkOrderDepartmentChart&id={$VAL_ID}"></div>
	</div>
	<div class="row top12">
		<div class="col-lg-6"><img src="{$URL_MAIN_PHP}?menuAction=ProductImage.WorkOrderSeverityChart&id={$VAL_ID}"></div>
		<div class="col-lg-6"><img src="{$URL_MAIN_PHP}?menuAction=ProductImage.WorkOrderPriorityChart&id={$VAL_ID}"></div>
	</div>
	<div class="row top12">
		<div class="col-lg-6"><img src="{$URL_MAIN_PHP}?menuAction=ProductImage.WorkOrderModuleChart&id={$VAL_ID}"></div>
		<div class="col-lg-6"><img src="{$URL_MAIN_PHP}?menuAction=ProductImage.WorkOrderTypeChart&id={$VAL_ID}"></div>
	</div>
</div>
{/block}