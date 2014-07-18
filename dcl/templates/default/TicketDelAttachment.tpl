<form class="form-horizontal" method="post" action="{$URL_MAIN_PHP}">
	<input type="hidden" name="menuAction" value="boTickets.dodeleteattachment">
	<input type="hidden" name="ticketid" value="{$VAL_TICKETID}">
	<input type="hidden" name="filename" value="{$VAL_FILENAME|escape}">
	<fieldset>
		<legend>{$smarty.const.STR_TCK_DELETEATTACHMENT|escape}</legend>
		<p class="alert alert-warning">{$TXT_DELCONFIRM|escape}</p>
	</fieldset>
	<fieldset>
		<div class="row">
			<div class="col-sm-offset-2">
				<input class="btn btn-danger" type="submit" value="{$smarty.const.STR_CMMN_YES|escape}">
				<a class="btn btn-success" href="{dcl_url_action controller=boTickets action=view params="ticketid={$VAL_TICKETID}"}">{$smarty.const.STR_CMMN_NO|escape}</a>
			</div>
		</div>
	</fieldset>
</form>