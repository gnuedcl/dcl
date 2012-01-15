<form class="styled" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="boTickets.dodeleteattachment">
	<input type="hidden" name="ticketid" value="{$VAL_TICKETID}">
	<input type="hidden" name="filename" value="{$VAL_FILENAME|escape}">
	<fieldset>
		<legend>{$smarty.const.STR_TCK_DELETEATTACHMENT}</legend>
		<div class="confirm">{$TXT_DELCONFIRM|escape}</div>
	</fieldset>
	<fieldset>
		<div class="submit">
			<input type="submit" value="{$smarty.const.STR_CMMN_YES}">
			<input type="button" onclick="javascript: history.back();" value="{$smarty.const.STR_CMMN_NO}">
		</div>
	</fieldset>
</form>