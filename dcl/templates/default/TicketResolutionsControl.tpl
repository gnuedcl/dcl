<h4>Ticket Resolutions <span class="badge{if count($VAL_RESOLUTIONS) > 0} alert-info{/if}">{$VAL_RESOLUTIONS|@count}</span>{if $PERM_ACTION}<a href="{$URL_MAIN_PHP}?menuAction=boTicketresolutions.add&ticketid={$VAL_TICKETID}" class="pull-right btn btn-success btn-xs" title="{$smarty.const.STR_CMMN_NEW|escape}">
		<span class="glyphicon glyphicon-plus"></span>
	</a>{/if}</h4>
{section name=tr loop=$VAL_RESOLUTIONS}
	<div class="panel panel-info">
		{if $PERM_MODIFY_TR && !$IS_DELETE && $VAL_EDITRESID == $VAL_RESOLUTIONS[tr].resid}
			<div class="panel-heading">{$smarty.const.STR_CMMN_EDIT|escape}</div>
			<div class="panel-body">
				<form class="form-horizontal" role="form" name="resolutionForm" id="resolutionForm" method="POST" action="{$URL_MAIN_PHP}">
					{dcl_anti_csrf_token}
					<input type="hidden" name="menuAction" value="htmlTicketresolutions.submitModify">
					<input type="hidden" name="actionby" value="{$VAL_RESOLUTIONS[tr].loggedby_id}">
					<input type="hidden" name="resid" value="{$VAL_EDITRESID}">
					<input type="hidden" name="ticketid" value="{$VAL_TICKETID}">
					<div class="form-group">
						<label for="actionon" class="col-sm-2 control-label">{$smarty.const.STR_CMMN_DATE}:</label>
						<div class="col-sm-2">
							<input type="text" class="form-control input-sm" maxlength="10" id="actionon" name="actionon" value="{$VAL_RESOLUTIONS[tr].loggedon|escape}">
						</div>
					</div>
					<div class="form-group">
						<label for="loggedby" class="col-sm-2 control-label">{$smarty.const.STR_TCK_LOGGEDBY}</label>
						<div class="col-sm-4">
							<input type="text" class="form-control input-sm" id="actionbytext" name="actionbytext" value="{$VAL_RESOLUTIONS[tr].loggedby|escape}" disabled="true">
						</div>
					</div>
					<div class="form-group">
						<label for="is_public" class="col-sm-2 control-label">{$smarty.const.STR_CMMN_PUBLIC}:</label>
						<div class="col-sm-1">
							<input type="checkbox" class="input-sm" name="is_public" id="is_public" value="Y"{if $VAL_RESOLUTIONS[tr].is_public == "Y"} checked{/if}>
						</div>
					</div>
					<div class="form-group">
						<label for="status" class="col-sm-2 control-label">{$smarty.const.STR_TCK_STATUS}:</label>
						<div class="col-sm-3">
							{dcl_select_status active="N" setid=$VAL_SETID default=$VAL_RESOLUTIONS[tr].status_id}
						</div>
					</div>
					<div class="form-group">
						<label for="secondsText" class="col-sm-2 control-label">{$smarty.const.STR_TCK_APPROXTIME}</label>
						<div class="col-sm-4">
							<input type="text" class="form-control input-sm" id="secondsText" name="secondsText" value="{$VAL_RESOLUTIONS[tr].seconds|escape}" disabled="true">
						</div>
					</div>
					<div class="form-group">
						<label for="resolution" class="col-sm-2 control-label">{$smarty.const.STR_TCK_RESOLUTION}:</label>
						<div class="col-sm-5">
							<textarea class="form-control input-sm" rows="4" id="resolution" name="resolution">{$VAL_RESOLUTIONS[tr].resolution|escape}</textarea>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-3">
							<input type="button" class="btn btn-primary" value="{$smarty.const.STR_CMMN_SAVE}" onclick="validateAndSubmitForm(this.form);">
							<input type="button" class="btn btn-link" value="{$smarty.const.STR_CMMN_CANCEL}" onclick="history.back();">
						</div>
					</div>
				</form>
				<script>
					$(function() {
						$("#actionon").datepicker();
					});
				</script>
			</div>
		{else}
			<div class="panel-heading">
				{dcl_gravatar userId=$VAL_RESOLUTIONS[tr].loggedby_id size=24 class="img-rounded"} {dcl_personnel_link text=$VAL_RESOLUTIONS[tr].loggedby id=$VAL_RESOLUTIONS[tr].loggedby_id}: {if !$IS_PUBLIC}{if $VAL_RESOLUTIONS[tr].is_public == "Y"}<span class="glyphicon glyphicon-eye-open"></span>{else}<span class="glyphicon glyphicon-lock"></span>{/if}{/if} on {$VAL_RESOLUTIONS[tr].loggedon}{if $VAL_RESOLUTIONS[tr].time > 0} for {$VAL_RESOLUTIONS[tr].time} Seconds{/if}
				{if $PERM_MODIFY_TR || $PERM_DELETE_TR}<div class="pull-right">
					{if $PERM_MODIFY_TR}<a href="{$URL_MAIN_PHP}?menuAction=htmlTicketresolutions.modify&id={$VAL_RESOLUTIONS[tr].resid}" class="btn btn-primary btn-xs" title="{$smarty.const.STR_CMMN_EDIT|escape}">
							<span class="glyphicon glyphicon-pencil"></span>
						</a>{/if}
					{if $PERM_DELETE_TR}<a href="javascript:;" class="btn btn-danger btn-xs dcl-delete-tr" data-tr-id="{$VAL_RESOLUTIONS[tr].resid}}" title="{$smarty.const.STR_CMMN_DELETE|escape}">
							<span class="glyphicon glyphicon-trash"></span>
						</a>{/if}
					</div>{/if}
			</div>
			<div class="panel-body">
				{$VAL_RESOLUTIONS[tr].resolution|escape|dcl_link}
			</div>
			<div class="panel-footer">
			<div>{$VAL_RESOLUTIONS[tr].status|escape}</div>
			</div>{/if}
	</div>
{/section}
{if $PERM_DELETE_TR && count($VAL_TIMECARDS) > 0}<script>
	$(function() {
		$(".dcl-delete-tr").click(function(event) {
			event.preventDefault();
			if (confirm('Are you sure you want to delete this resolution?')) {
				var id = $(this).attr('data-tr-id');
				$('<form method="POST" action="{$URL_MAIN_PHP}"><input type="hidden" name="menuAction" value="htmlTicketresolutions.delete"><input type="hidden" name="id" value="' + id + '"></form>').submit();
			}
		});
	});
</script>{/if}
