<!-- $Id$ -->
{dcl_calendar_init}
<form class="styled" method="post" name="frmAggNav" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="htmlAgg.ShowReport">
	<fieldset>
		<legend>{$smarty.const.DCL_MENU_AGGREGATESTATS}</legend>
		<div class="required">
			<label for="group">{$TXT_AGGREGATE}:</label>
			{section name=grp loop=$groups}
			{if $smarty.section.grp.first}<select name="group" id="group">{/if}
			<option value="{$groups[grp].key|escape}">{$groups[grp].desc|escape}</option>
			{if $smarty.section.grp.last}</select>{/if}
			{/section}
		</div>
		<div class="required">
			<label for="group">{$TXT_BY}:</label>
			{section name=sub loop=$subgroups}
			{if $smarty.section.sub.first}<select name="sub" id="sub">{/if}
			<option value="{$subgroups[sub].key|escape}">{$subgroups[sub].desc|escape}</option>
			{if $smarty.section.sub.last}</select>{/if}
			{/section}
		</div>
		<div>
			<label for="chkLimitByDate">{$TXT_FORDATES}:</label>
			<input type="checkbox" value="1" name="chkLimitByDate" id="chkLimitByDate"{$VAL_CHKLIMIT}>
		</div>
		<div>
			<label for="dateFrom">{$smarty.const.STR_CMMN_FROM}:</label>
			{dcl_calendar name=dateFrom value=$VAL_DATEFROM}
		</div>
		<div>
			<label for="dateTo">{$smarty.const.STR_CMMN_TO}:</label>
			{dcl_calendar name=dateTo value=$VAL_DATETO}
		</div>
	</fieldset>
	<fieldset><div class="submit"><input type="submit" value="{$smarty.const.STR_CMMN_GO}"></div></fieldset>
</form>