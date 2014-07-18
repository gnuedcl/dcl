<h4>[{$VAL_HOTLISTID}] {$VAL_NAME|escape}</h4>
{include file="HotlistProjectOptionsControl.tpl"}
<div class="container">
<div class="row top12">
	<div class="col-lg-6"><img src="{$URL_MAIN_PHP}?menuAction=HotlistImage.StatusChart&id={$VAL_HOTLISTID}"></div>
	<div class="col-lg-6"><img src="{$URL_MAIN_PHP}?menuAction=HotlistImage.DepartmentChart&id={$VAL_HOTLISTID}"></div>
</div>
<div class="row top12">
	<div class="col-lg-6"><img src="{$URL_MAIN_PHP}?menuAction=HotlistImage.SeverityChart&id={$VAL_HOTLISTID}"></div>
	<div class="col-lg-6"><img src="{$URL_MAIN_PHP}?menuAction=HotlistImage.PriorityChart&id={$VAL_HOTLISTID}"></div>
</div>
<div class="row top12">
	<div class="col-lg-6"><img src="{$URL_MAIN_PHP}?menuAction=HotlistImage.ModuleChart&id={$VAL_HOTLISTID}"></div>
	<div class="col-lg-6"><img src="{$URL_MAIN_PHP}?menuAction=HotlistImage.TypeChart&id={$VAL_HOTLISTID}"></div>
</div>
</div>