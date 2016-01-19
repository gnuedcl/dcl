{extends file="_Layout.tpl"}
{block title}Outages{/block}
{block css}
	<link rel="stylesheet" type="text/css" href="{$DIR_VENDOR}freejqgrid/css/ui.jqgrid.css" />
	<style type="text/css">
		#selected_orgs span { margin-right: 4px; margin-bottom: 4px; }
	</style>
{/block}
{block content}
<form id="unplannedOutageForm" class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="Outage.ReportResults">
	<fieldset>
		<legend>Unplanned Outage Report</legend>
		{dcl_form_control id=outage_start controlsize=3 label="Start" required=true}
			<input type="text" class="form-control" data-input-type="date" maxlength="10" id="unplanned_outage_start" name="unplanned_outage_start" value="{$ViewData->Start|escape}">
		{/dcl_form_control}
		{dcl_form_control id=outage_end controlsize=3 label="End" required=true}
			<input type="text" class="form-control" data-input-type="date" maxlength="10" id="unplanned_outage_end" name="unplanned_outage_end" value="{$ViewData->End|escape}">
		{/dcl_form_control}
		{dcl_form_control id=orgs controlsize=2 label=$smarty.const.STR_CMMN_ORGANIZATION}
			<a id="orgsLink" href="javascript:;">Select</a>
			<input type="hidden" id="outage_orgs" name="outage_orgs" value="{$ViewData->Orgs}">
		{/dcl_form_control}
		<div id="selected_orgs" class="col-xs-offset-2 col-xs-10">
		</div>
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-xs-offset-2">
				<input name="submitForm" type="submit" class="btn btn-primary" value="{$smarty.const.STR_CMMN_GO|escape}">
				<input type="button" class="btn btn-link" onclick="location.href = '{dcl_url_action controller=Outage action=Index}';" value="{$smarty.const.STR_CMMN_CANCEL|escape}">
			</div>
		</div>
	</fieldset>
</form>
{dcl_dialog_org}
{/block}
{block script}
<script type="text/javascript" src="{$DIR_VENDOR}freejqgrid/js/i18n/grid.locale-en.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}freejqgrid/js/jquery.jqgrid.min.js"></script>
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript" src="{$DIR_JS}dialog-org.js"></script>
<script type="text/javascript">
	$(function() {
		$("input[data-input-type=date]").datepicker();
		$("#outage_orgs").dclOrgSelector();
	});
</script>
{/block}