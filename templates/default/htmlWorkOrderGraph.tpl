<!-- $Id: htmlWorkOrderGraph.tpl,v 1.1.1.1 2006/11/27 05:30:38 mdean Exp $ -->
{dcl_calendar_init}
<form class="styled" name="theForm" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="boWorkorders.showgraph">
	<fieldset>
		<legend>{$smarty.const.STR_WO_ACTIVITYGRAPH}</legend>
		<div>
			<label for="product">{$smarty.const.STR_WO_PRODUCT}:</label>
			{$CMB_PRODUCTS}
		</div>
		<div>
			<label for="days">{$smarty.const.STR_WO_SHOWGRAPHFOR}:</label>
			{$CMB_DAYS}
		</div>
		<div>
			<label for="dateFrom">{$smarty.const.STR_WO_ENDINGON}:</label>
			{dcl_calendar name="dateFrom" value="$VAL_TODAY"}
		</div>
	</fieldset>
	<fieldset>
		<div class="submit"><input type="submit" value="{$smarty.const.STR_CMMN_GO}"></div>
	</fieldset>
</form>