<!-- $Id$ -->
{dcl_calendar_init}

<form class="styled" name="searchForm" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="boSecAudit.ShowResults">
	<fieldset>
		<legend>{$smarty.const.STR_SEC_SECLOG}</legend>
		<div>
			<label for="bytype">{$smarty.const.STR_SEC_GENERATEREPORTFOR}:</label>
			{$CMB_USERS}
		</div>
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_SEC_DATERANGE}</legend>
		<div>
			<label for="begindate">{$smarty.const.STR_SEC_BEGIN}:</label>
			{dcl_calendar name="begindate" value="$VAL_BEGINDATE"}
		</div>
		<div>
			<label for="enddate">{$smarty.const.STR_SEC_ENDING}:</label>
			{dcl_calendar name="enddate" value="$VAL_ENDDATE"}
		</div>
	</fieldset>
	<fieldset>
		<div class="submit"><input type="submit" value="{$smarty.const.STR_CMMN_GO}"></div>
	</fieldset>
</form>
<!-- $Id$ -->
{dcl_calendar_init}

<form class="styled" name="searchForm" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="boSecAudit.ShowResults">
	<fieldset>
		<legend>{$smarty.const.STR_SEC_SECLOG}</legend>
		<div>
			<label for="bytype">{$smarty.const.STR_SEC_GENERATEREPORTFOR}:</label>
			{$CMB_USERS}
		</div>
	</fieldset>
	<fieldset>
		<legend>{$smarty.const.STR_SEC_DATERANGE}</legend>
		<div>
			<label for="begindate">{$smarty.const.STR_SEC_BEGIN}:</label>
			{dcl_calendar name="begindate" value="$VAL_BEGINDATE"}
		</div>
		<div>
			<label for="enddate">{$smarty.const.STR_SEC_ENDING}:</label>
			{dcl_calendar name="enddate" value="$VAL_ENDDATE"}
		</div>
	</fieldset>
	<fieldset>
		<div class="submit"><input type="submit" value="{$smarty.const.STR_CMMN_GO}"></div>
	</fieldset>
</form>
