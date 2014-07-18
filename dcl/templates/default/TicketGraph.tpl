<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
<form class="form-horizontal" name="theForm" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="boTickets.showgraph">
	<fieldset>
		<legend>{$smarty.const.STR_TCK_ACTIVITYGRAPH}</legend>
		{dcl_form_control id=product controlsize=4 label=$smarty.const.STR_TCK_PRODUCT}
		{$CMB_PRODUCTS}
		{/dcl_form_control}
		{dcl_form_control id=days controlsize=2 label=$smarty.const.STR_TCK_SHOWGRAPHFOR}
		{$CMB_DAYS}
		{/dcl_form_control}
		{dcl_form_control id=dateFrom controlsize=2 label=$smarty.const.STR_TCK_ENDINGON}
			<input type="text" class="form-control" data-input-type="date" maxlength="10" id="dateFrom" name="dateFrom" value="{$VAL_TODAY|escape}">
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input type="submit" class="btn btn-primary" value="{$smarty.const.STR_CMMN_GO}">
			</div>
		</div>
	</fieldset>
</form>
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#content").find("select").select2({ minimumResultsForSearch: 10 });
		$("input[data-input-type=date]").datepicker();
	});
</script>