{extends file="_Layout.tpl"}
{block name=title}{$smarty.const.STR_WOST_TICKETACTIVITY|escape}{/block}
{block name=css}
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
{/block}
{block name=content}
<form class="form-horizontal" name="searchForm" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="reportTicketActivity.execute">
	<fieldset>
		<legend>{$smarty.const.STR_WOST_TICKETACTIVITY|escape}</legend>
		{dcl_form_control id=bytype controlsize=4 label=$smarty.const.STR_WOST_GENERATEREPORTFOR}
		{$CMB_RESPONSIBLE}
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_WOST_DATERANGE}</legend>
		{dcl_form_control id=begindate controlsize=2 label=$smarty.const.STR_WOST_BEGIN}
			<input type="text" class="form-control" data-input-type="date" maxlength="10" id="begindate" name="begindate" value="{$VAL_BEGINDATE|escape}">
		{/dcl_form_control}
		{dcl_form_control id=enddate controlsize=2 label=$smarty.const.STR_WOST_ENDING}
			<input type="text" class="form-control" data-input-type="date" maxlength="10" id="enddate" name="enddate" value="{$VAL_ENDDATE|escape}">
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