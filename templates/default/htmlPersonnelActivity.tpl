<!-- $Id$ -->
{dcl_calendar_init}
<script language="JavaScript">
{literal}
function onChangeByType()
{
	var oP = document.searchForm.responsible;
	var oD = document.searchForm.department;
	if (document.searchForm.bytype.selectedIndex == 0)
	{
		oP.style.display = '';
		oD.style.display = 'none';
	}
	else
	{
		oP.style.display = 'none';
		oD.style.display = '';
	}
}
{/literal}
</script>
<form class="styled" name="searchForm" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="reportPersonnelActivity.execute">
	<fieldset>
		<legend>{$smarty.const.STR_WOST_PERSONNELACTIVITY}</legend>
		<div>
			<label for="bytype">{$smarty.const.STR_WOST_GENERATEREPORTFOR}:</label>
			{$CMB_BYTYPE}{$CMB_RESPONSIBLE}{$CMB_DEPARTMENTS}
		</div>
		<div>
			<label for="groupby">{$smarty.const.STR_CMMN_GROUPING}:</label>
			{$CMB_GROUPBY}
		</div>
		<div>
			<label for="timesheet">{$smarty.const.STR_WOST_FORMATASTIMESHEET}:</label>
			<input type="checkbox" name="timesheet" id="timesheet" value="Y">
		</div>
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_WOST_DATERANGE}</legend>
		<div>
			<label for="begindate">{$smarty.const.STR_WOST_BEGIN}:</label>
			{dcl_calendar name="begindate" value="$VAL_BEGINDATE"}
		</div>
		<div>
			<label for="enddate">{$smarty.const.STR_WOST_ENDING}:</label>
			{dcl_calendar name="enddate" value="$VAL_ENDDATE"}
		</div>
	</fieldset>
	<fieldset>
		<div class="submit"><input type="submit" value="{$smarty.const.STR_CMMN_GO}"></div>
	</fieldset>
</form>
<script language="JavaScript">onChangeByType();</script>
