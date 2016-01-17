{extends file="_Layout.tpl"}
{block name=title}{$smarty.const.STR_SEC_SECLOG|escape}{/block}
{block name=css}
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2.css">
<link rel="stylesheet" href="{$DIR_VENDOR}select2/select2-bootstrap.css">
{/block}
{block name=content}
<form class="form-horizontal" name="searchForm" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="boSecAudit.ShowResults">
	<fieldset>
		<legend>{$smarty.const.STR_SEC_SECLOG|escape}</legend>
		{dcl_form_control id=bytype controlsize=4 label=$smarty.const.STR_SEC_GENERATEREPORTFOR}
		{$CMB_USERS}
		{/dcl_form_control}
		{dcl_form_control id=begindate controlsize=2 label=$smarty.const.STR_SEC_BEGIN required=true}
		{dcl_input_date id=begindate value=$VAL_BEGINDATE}
		{/dcl_form_control}
		{dcl_form_control id=enddate controlsize=2 label=$smarty.const.STR_SEC_ENDING required=true}
		{dcl_input_date id=enddate value=$VAL_ENDDATE}
		{/dcl_form_control}
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-primary" type="submit" value="{$smarty.const.STR_CMMN_GO|escape}">
			</div>
		</div>
	</fieldset>
</form>
{/block}
{block name=script}
<script type="text/javascript" src="{$DIR_VENDOR}select2/select2.min.js"></script>
<script type="text/javascript">
	$(function() {
		$("input[data-input-type=date]").datepicker();
		$("#content").find("select").select2({ minimumResultsForSearch: 10 });
	});
</script>
{/block}