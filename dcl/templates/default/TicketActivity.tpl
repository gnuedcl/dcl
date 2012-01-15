{dcl_calendar_init}
<form class="styled" name="searchForm" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="reportTicketActivity.execute">
	<fieldset>
		<legend>{$smarty.const.STR_WOST_TICKETACTIVITY}</legend>
		<div class="required">
			<label for="bytype">{$smarty.const.STR_WOST_GENERATEREPORTFOR}:</label>
			{$CMB_BYTYPE}{$CMB_RESPONSIBLE}{$CMB_DEPARTMENTS}
		</div>
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_WOST_DATERANGE}</legend>
		<div class="required">
			<label for="begindate">{$smarty.const.STR_WOST_BEGIN}:</label>
			{dcl_calendar name="begindate" value="$VAL_BEGINDATE"}
		</div>
		<div class="required">
			<label for="enddate">{$smarty.const.STR_WOST_ENDING}:</label>
			{dcl_calendar name="enddate" value="$VAL_ENDDATE"}
		</div>
	</fieldset>
	<fieldset>
		<div class="submit"><input type="submit" value="{$smarty.const.STR_CMMN_GO}"><input type="reset" value="{$smarty.const.STR_CMMN_RESET}"></div>
	</fieldset>
</form>