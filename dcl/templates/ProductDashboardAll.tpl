{extends file="_Layout.tpl"}
{block name=title}Products{/block}
{block name=content}
<h4>Products</h4>
<div class="btn-group">
	<a class="btn btn-default" href="{$URL_MAIN_PHP}?menuAction=Product.Index">Browse</a></li>
</div>
<div class="container">
	<div class="row top12">
		<div class="col-lg-6"><img src="{$URL_MAIN_PHP}?menuAction=ProductImage.WorkOrderProductChart"></div>
		<div class="col-lg-6"><img src="{$URL_MAIN_PHP}?menuAction=ProductImage.TicketProductChart"></div>
	</div>
</div>
{/block}