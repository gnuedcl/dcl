{extends file="_Layout.tpl"}
{block name=title}Work Order Metrics{/block}
{block name=css}
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
{/block}
{block name=content}
<form class="form form-horizontal" name="searchForm" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="htmlMetricsWorkOrders.showall">
	<fieldset>
		<legend>Work Order Metrics</legend>
		{dcl_form_control id=products controlsize=10 label=$smarty.const.STR_WO_PRODUCT}
		{$CMB_PRODUCTS}
		{/dcl_form_control}
		{dcl_form_control id=childProjects controlsize=1 label="Include Child Projects"}
			<input class="form-control" type="checkbox" name="childProjects" id="childProjects" value="1" />
		{/dcl_form_control}
		{dcl_form_control id=projects controlsize=10 label="Projects"}
		{$CMB_PROJECTS}
		{/dcl_form_control}
		{dcl_form_control id=begindate controlsize=2 label="Begin Date"}
			<input type="text" class="form-control" data-input-type="date" maxlength="10" id="begindate" name="begindate" value="{$VAL_BEGINDATE|escape}">
		{/dcl_form_control}
		{dcl_form_control id=enddate controlsize=2 label="End Date"}
			<input type="text" class="form-control" data-input-type="date" maxlength="10" id="enddate" name="enddate" value="{$VAL_ENDDATE|escape}">
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input type="submit" class="btn btn-primary" value="{$smarty.const.STR_CMMN_GO}">
				<input type="reset" class="btn btn-link" value="{$smarty.const.STR_CMMN_RESET}">
			</div>
		</div>
	</fieldset>
</form>
{/block}
{block name=script}
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#content").find("select").select2({ minimumResultsForSearch: 10 });
		$("input[data-input-type=date]").datepicker();
	});
</script>
{/block}