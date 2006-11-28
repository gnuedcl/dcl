<!-- $Id: htmlAccountActivity.tpl,v 1.1.1.1 2006/11/27 05:30:38 mdean Exp $ -->
{dcl_calendar_init}
<form class="styled" name="searchForm" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="reportAccountActivity.showActivity">
	<input type="hidden" name="account_id" value="{$account_id}">
	<fieldset>
		<legend>Account Work Order Activity</legend>
		<div class="required">
			<label for="date_begin">From:</label>
			{dcl_calendar name="date_begin" value="$VAL_BEGINDATE"}
		</div>
		<div class="required">
			<label for="date_end">To:</label>
			{dcl_calendar name="date_end" value="$VAL_ENDDATE"}
		</div>
		<div>
			<label for="is_public">Public Only:</label>
			<input type="checkbox" name="is_public" id="is_public" value="Y">
		</div>
		<div>
			<label for="entity_source_id">Source:</label>
			{$CMB_SOURCE}
		</td>
	</tr>
	</fieldset>
	<fieldset><div class="submit"><input type="submit" value="Execute"></div></fieldset>
</form>