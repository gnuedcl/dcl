<!-- $Id$ -->
{dcl_calendar_init}
<form class="styled" name="searchForm" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="htmlMetricsWorkOrders.showall">
	<fieldset>
		<legend>Work Order Metrics</legend>
		<div>
			<label for="products">Products:</label>
			{$CMB_PRODUCTS}
		</div>
		<div>
			<label for="childProjects">Include Child Projects:</label>
			<input type="checkbox" name="childProjects" id="childProjects" value="1" />
		</div>
		<div>
			<label for="projects">Projects:</label>
			{$CMB_PROJECTS}
		</div>
		<div>
			<label for="begindate">Begin Date:</label>
			{dcl_calendar name=begindate value=$VAL_BEGINDATE}
		</div>
		<div>
			<label for="enddate">End Date:</label>
			{dcl_calendar name=enddate value=$VAL_ENDDATE}
		</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="submit" value="{$smarty.const.STR_CMMN_GO}">
			<input type="reset" value="{$smarty.const.STR_CMMN_RESET}">
		</div>
	</fieldset>
</form>