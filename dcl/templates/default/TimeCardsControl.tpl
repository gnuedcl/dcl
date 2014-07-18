<h4>Time Cards <span class="badge{if count($VAL_TIMECARDS) > 0} alert-info{/if}">{$VAL_TIMECARDS|@count}</span>{if $PERM_ACTION}<a href="{$URL_MAIN_PHP}?menuAction=boTimecards.add&jcn={$VAL_JCN}&seq={$VAL_SEQ}" class="pull-right btn btn-success btn-xs" title="{$smarty.const.STR_CMMN_NEW|escape}">
		<span class="glyphicon glyphicon-plus"></span>
	</a>{/if}</h4>
{section name=tc loop=$VAL_TIMECARDS}
	<div class="panel panel-info">
		{if $PERM_MODIFY_TC && $VAL_EDITTCID == $VAL_TIMECARDS[tc].id}
			<div class="panel-heading">{$smarty.const.STR_CMMN_EDIT|escape}</div>
			<div class="panel-body">
				<form class="form-horizontal" role="form" name="timeCardForm" id="timeCardForm" method="POST" action="{$URL_MAIN_PHP}">
					<input type="hidden" name="menuAction" value="boTimecards.dbmodify">
					<input type="hidden" name="actionby" value="{$VAL_TIMECARDS[tc].actionby_id}">
					<input type="hidden" name="id" value="{$VAL_EDITTCID}">
					<input type="hidden" name="jcn" value="{$VAL_JCN}">
					<input type="hidden" name="seq" value="{$VAL_SEQ}">
					<div class="form-group">
						<label for="actionon" class="col-sm-2 control-label">{$smarty.const.STR_TC_DATE}:</label>
						<div class="col-sm-2">
							<input type="text" class="form-control input-sm" maxlength="10" id="actionon" name="actionon" value="{$VAL_TIMECARDS[tc].actionon|escape}">
						</div>
					</div>
					<div class="form-group">
						<label for="actionbytext" class="col-sm-2 control-label">{$smarty.const.STR_TC_BY}:</label>
						<div class="col-sm-2">
							<input type="text" class="form-control input-sm" id="actionbytext" name="actionbytext" value="{$VAL_TIMECARDS[tc].actionby|escape}" disabled="true">
						</div>
					</div>
					<div class="form-group">
						<label for="is_public" class="col-sm-2 control-label">{$smarty.const.STR_CMMN_PUBLIC}:</label>
						<div class="col-sm-1">
							<input type="checkbox" class="form-control input-sm" name="is_public" id="is_public" value="Y"{if $VAL_TIMECARDS[tc].public == "Y"} checked{/if}>
						</div>
					</div>
					<div class="form-group">
						<label for="action" class="col-sm-2 control-label">{$smarty.const.STR_TC_ACTION}:</label>
						<div class="col-sm-3">
							{dcl_select_action active="N" setid=$VAL_SETID default=$VAL_TIMECARDS[tc].action_id}
						</div>
					</div>
					<div class="form-group">
						<label for="status" class="col-sm-2 control-label">{$smarty.const.STR_TC_STATUS}:</label>
						<div class="col-sm-3">
							{dcl_select_status active="N" setid=$VAL_SETID default=$VAL_TIMECARDS[tc].status_id}
						</div>
					</div>
					<div class="form-group">
						<label for="hours" class="col-sm-2 control-label">{$smarty.const.STR_TC_HOURS}:</label>
						<div class="col-sm-1">
							<input type="text" class="form-control input-sm" size="6" maxlength="6" id="hours" name="hours" value="{$VAL_TIMECARDS[tc].hours|escape}">
						</div>
					</div>
					<div class="form-group">
						<label for="summary" class="col-sm-2 control-label">{$smarty.const.STR_TC_SUMMARY}:</label>
						<div class="col-sm-5">
							<input type="text" class="form-control input-sm" size="50" maxlength="100" id="summary" name="summary" value="{$VAL_TIMECARDS[tc].summary|escape}">
						</div>
					</div>
					<div class="form-group">
						<label for="description" class="col-sm-2 control-label">{$smarty.const.STR_TC_DESCRIPTION}:</label>
						<div class="col-sm-5">
							<textarea class="form-control input-sm" rows="6" id="description" name="description">{$VAL_TIMECARDS[tc].description|escape}</textarea>
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
			{dcl_gravatar userId=$VAL_TIMECARDS[tc].actionby_id size=24 class="img-rounded"} {dcl_personnel_link text=$VAL_TIMECARDS[tc].actionby id=$VAL_TIMECARDS[tc].actionby_id}: {if !$IS_PUBLIC}{if $VAL_TIMECARDS[tc].is_public == "Y"}<span class="glyphicon glyphicon-eye-open"></span>{else}<span class="glyphicon glyphicon-lock"></span>{/if}{/if} {$VAL_TIMECARDS[tc].action|escape} on {$VAL_TIMECARDS[tc].actionon}{if $VAL_TIMECARDS[tc].hours > 0} for {$VAL_TIMECARDS[tc].hours} Hours{/if}
			{if $PERM_MODIFY_TC || $PERM_DELETE_TC}<div class="pull-right">
				{if $PERM_MODIFY_TC}<a href="{$URL_MAIN_PHP}?menuAction=boTimecards.modify&id={$VAL_TIMECARDS[tc].id}" class="btn btn-primary btn-xs" title="{$smarty.const.STR_CMMN_EDIT|escape}">
						<span class="glyphicon glyphicon-pencil"></span>
					</a>{/if}
				{if $PERM_DELETE_TC}<a href="javascript:;" class="btn btn-danger btn-xs dcl-delete-tc" data-tc-id="{$VAL_TIMECARDS[tc].id}" title="{$smarty.const.STR_CMMN_DELETE|escape}">
						<span class="glyphicon glyphicon-trash"></span>
					</a>{/if}
				</div>{/if}
		</div>
		<div class="panel-body">
			<div><strong>{$VAL_TIMECARDS[tc].summary|escape}</strong></div>
			{if $VAL_TIMECARDS[tc].description != "" && (!$PERM_MODIFY_TC || $VAL_EDITTCID != $VAL_TIMECARDS[tc].id)}<div>{$VAL_TIMECARDS[tc].description|escape|dcl_link}</div>{/if}
		</div>
		<div class="panel-footer">
			<div><span class="status-type-{$VAL_TIMECARDS[tc].dcl_status_type}">{$VAL_TIMECARDS[tc].status|escape}</span></div>
			{if !$IS_PUBLIC && ($VAL_TIMECARDS[tc].reassign_from_id || $VAL_TIMECARDS[tc].reassign_to_id)}
				<div>Reassign <strong>{dcl_personnel_link text=$VAL_TIMECARDS[tc].reassign_from_id id=$VAL_TIMECARDS[tc].reassign_from_id_int}</strong> to <strong>{dcl_personnel_link text=$VAL_TIMECARDS[tc].reassign_to_id id=$VAL_TIMECARDS[tc].reassign_to_id_int}</strong></div>
			{/if}
		</div>{/if}
	</div>
{/section}
{if $PERM_DELETE_TC && count($VAL_TIMECARDS) > 0}<script>
	$(function() {
		$(".dcl-delete-tc").click(function(event) {
			event.preventDefault();
			if (confirm('Are you sure you want to delete this time card?')) {
				var id = $(this).attr('data-tc-id');
				$('<form method="POST" action="{$URL_MAIN_PHP}"><input type="hidden" name="menuAction" value="boTimecards.dbdelete"><input type="hidden" name="id" value="' + id + '"></form>').submit();
			}
		});
	});
</script>{/if}
