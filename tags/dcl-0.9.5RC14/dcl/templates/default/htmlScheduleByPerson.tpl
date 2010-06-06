<!-- $Id$ -->
<form class="styled" name="searchForm" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="scheduleByPerson.ScheduleOpenTasksByPerson">
	<fieldset>
		<legend>{$smarty.const.STR_WOST_SCHEDULETITLE}</legend>
		<div>
			<label for="personID">{$smarty.const.STR_WOST_SCHEDULEFOR}</label>
			{$CMB_PERSON}
		</div>
	</fieldset>
	<fieldset>
		<div class="submit"><input type="submit" value="{$smarty.const.STR_CMMN_OK}"></div>
	</fieldset>
</form>