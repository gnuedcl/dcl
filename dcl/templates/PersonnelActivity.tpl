{extends file="_Layout.tpl"}
{block name=title}{$smarty.const.STR_WOST_PERSONNELACTIVITY|escape}{/block}
{block name=css}
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
{/block}
{block name=content}
<form class="form form-horizontal" name="searchForm" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="reportPersonnelActivity.execute">
	<fieldset>
		<legend>{$smarty.const.STR_WOST_PERSONNELACTIVITY|escape}</legend>
		{dcl_form_control id=bytype controlsize=4 label=$smarty.const.STR_WOST_GENERATEREPORTFOR}
		{$CMB_BYTYPE}{$CMB_RESPONSIBLE}{$CMB_DEPARTMENTS}
		{/dcl_form_control}
		{dcl_form_control id=status controlsize=10 label="Statuses"}
		{$CMB_STATUSES}
		{/dcl_form_control}
		{dcl_form_control id=groupby controlsize=4 label=$smarty.const.STR_CMMN_GROUPING}
		{$CMB_GROUPBY}
		{/dcl_form_control}
		{dcl_form_control id=timesheet controlsize=1 label=$smarty.const.STR_WOST_FORMATASTIMESHEET}
			<input type="checkbox" name="timesheet" id="timesheet" value="Y"{if $VAL_TIMESHEET == 'Y'} checked="true"{/if}>
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

		$("#bytype").change(function() {
			if ($(this).val() == "1") {
				$("#responsible").select2("container").show();
				$("#department").select2("container").hide();
			}
			else {
				$("#department").select2("container").show();
				$("#responsible").select2("container").hide();
			}
		}).triggerChange();
	});
</script>
{/block}