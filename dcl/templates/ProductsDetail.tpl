{extends file="_Layout.tpl"}
{block name=title}[{$VAL_ID}] {$VAL_NAME|escape}{/block}
{block name=content}
<div class="panel panel-info">
	<div class="panel-heading"><h3>[{$VAL_ID}] {$VAL_NAME|escape}</h3></div>
	<div class="panel-body">
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
		<div class="container-fluid">
			<div class="row">
				<div class="col-xs-6">
					<h4>Details</h4>
					<ul class="list-unstyled">
						<li><span class="glyphicon glyphicon-cog"></span> {$smarty.const.STR_CMMN_ACTIVE|escape}: {if $VAL_ACTIVE == "Y"}{$smarty.const.STR_CMMN_YES|escape}{else}{$smarty.const.STR_CMMN_NO|escape}{/if}</li>
						<li><span class="glyphicon glyphicon-cog"></span> {$smarty.const.STR_CMMN_PUBLIC|escape}: {if $VAL_PUBLIC == "Y"}{$smarty.const.STR_CMMN_YES|escape}{else}{$smarty.const.STR_CMMN_NO|escape}{/if}</li>
						<li><span class="glyphicon glyphicon-cog"></span> Project Required: {if $VAL_ISPROJECTREQUIRED == "Y"}{$smarty.const.STR_CMMN_YES|escape}{else}{$smarty.const.STR_CMMN_NO|escape}{/if}</li>
					</ul>
				</div>
				<div class="col-xs-6">
					<h4>Owners</h4>
					<ul class="list-unstyled">
						<li><span class="glyphicon glyphicon-user"></span> {$smarty.const.STR_PROD_REPORTTO|escape}: {$VAL_REPORTTO|escape}</li>
						<li><span class="glyphicon glyphicon-user"></span> {$smarty.const.STR_PROD_TICKETSTO|escape}: {$VAL_TICKETSTO|escape}</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
{/block}