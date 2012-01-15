<div class="dcl_detail">
<table width="100%" class="styled timecard">
	<caption class="spacer">{$VAL_TIMECARDS|@count} Time Cards</caption>
	{if $PERM_ACTION}<thead>
		<tr class="toolbar"><th><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=boTimecards.add&jcn={$VAL_JCN}&seq={$VAL_SEQ}">{$smarty.const.STR_CMMN_NEW}</a></li></ul></th></tr>
	</thead>{/if}
	<tbody>{strip}
{section name=tc loop=$VAL_TIMECARDS}
{if $PERM_MODIFY_TC && !$VAL_FORDELETE && $VAL_EDITTCID == $VAL_TIMECARDS[tc].id}
	<tr><td>
	<form class="styled" name="timeCardForm" id="timeCardForm" method="POST" action="{$URL_MAIN_PHP}">
		<input type="hidden" name="menuAction" value="boTimecards.dbmodify">
		<input type="hidden" name="actionby" value="{$VAL_TIMECARDS[tc].actionby_id}">
		<input type="hidden" name="id" value="{$VAL_EDITTCID}">
		<input type="hidden" name="jcn" value="{$VAL_JCN}">
		<input type="hidden" name="seq" value="{$VAL_SEQ}">
		<fieldset>
			<legend>Edit Time Card</legend>
			<div><label for="actionon">{$smarty.const.STR_TC_DATE}:</label>{dcl_calendar name="actionon" value=$VAL_TIMECARDS[tc].actionon}</div>
			<div><label for="actionbytext">{$smarty.const.STR_TC_BY}:</label><input type="text" size="20" id="actionbytext" name="actionbytext" value="{$VAL_TIMECARDS[tc].actionby|escape}" disabled="true"></div>
			<div><label for="is_public">{$smarty.const.STR_CMMN_PUBLIC}:</label><input type="checkbox" name="is_public" id="is_public" value="Y"{if $VAL_TIMECARDS[tc].public == "Y"} checked{/if}></div>
			<div><label for="action">{$smarty.const.STR_TC_ACTION}:</label>{dcl_select_action active="N" setid=$VAL_SETID default=$VAL_TIMECARDS[tc].action_id}</div>
			<div><label for="status">{$smarty.const.STR_TC_STATUS}:</label>{dcl_select_status active="N" setid=$VAL_SETID default=$VAL_TIMECARDS[tc].status_id}</div>
			<div><label for="hours">{$smarty.const.STR_TC_HOURS}:</label><input type="text" size="6" maxlength="6" id="hours" name="hours" value="{$VAL_TIMECARDS[tc].hours|escape}"></div>
			<div><label for="summary">{$smarty.const.STR_TC_SUMMARY}:</label><input type="text" size="50" maxlength="100" id="summary" name="summary" value="{$VAL_TIMECARDS[tc].summary|escape}"></div>
			<div><label for="description">{$smarty.const.STR_TC_DESCRIPTION}:</label><textarea style="width:100%;" rows="6" id="description" name="description">{$VAL_TIMECARDS[tc].description|escape}</textarea></div>
		</fieldset>
		<fieldset>
			<div class="submit">
				<input type="button" value="{$smarty.const.STR_CMMN_SAVE}" onclick="validateAndSubmitForm(this.form);">
				<input type="button" value="{$smarty.const.STR_CMMN_CANCEL}" onclick="history.back();">
			</div>
		</fieldset>
	</form>
	</td></tr>
{else}
<tr>
	<td>
		<dl>
			<dt>{dcl_gravatar userId=$VAL_TIMECARDS[tc].actionby_id style="float:left;margin-right:2px;"}<strong>{$VAL_TIMECARDS[tc].actionby|escape}</strong> <span class="status-type-{$VAL_TIMECARDS[tc].dcl_status_type}">{$VAL_TIMECARDS[tc].status|escape}</span> {$VAL_TIMECARDS[tc].summary|escape}</dt>
			<dd><strong>{$VAL_TIMECARDS[tc].action|escape}</strong> on <strong>{$VAL_TIMECARDS[tc].actionon}</strong> for <strong>{$VAL_TIMECARDS[tc].hours}</strong> Hours
{if $VAL_FORDELETE && $VAL_EDITTCID == $VAL_TIMECARDS[tc].id}
		<form method="post" action="{$URL_MAIN_PHP}">
		<input type="hidden" name="menuAction" value="boTimecards.dbdelete">
		<input type="hidden" name="id" VALUE="{$VAL_TIMECARDS[tc].id}">
		<input type="submit" value="{$smarty.const.STR_CMMN_DELETE}">
		<input type="button" value="{$smarty.const.STR_CMMN_CANCEL}" onclick="location.href='{$URL_MAIN_PHP}?menuAction=WorkOrder.Detail&jcn={$VAL_JCN}&seq={$VAL_SEQ}';">
		</form>
{else}{if $PERM_MODIFY_TC || $PERM_DELETE_TC}
	&nbsp;|&nbsp;
	{if $PERM_MODIFY_TC}
		<a href="{$URL_MAIN_PHP}?menuAction=boTimecards.modify&id={$VAL_TIMECARDS[tc].id}">{$smarty.const.STR_CMMN_EDIT}</a>
		{if $PERM_DELETE}&nbsp;|&nbsp;{/if}
	{/if}
	{if $PERM_DELETE_TC}
		<a href="{$URL_MAIN_PHP}?menuAction=boTimecards.delete&id={$VAL_TIMECARDS[tc].id}">{$smarty.const.STR_CMMN_DELETE}</a>
	{/if}{/if}
{/if}
			</dd>
{if !$IS_PUBLIC && ($VAL_TIMECARDS[tc].reassign_from_id || $VAL_TIMECARDS[tc].reassign_to_id)}
		<dd>Reassign <strong>{$VAL_TIMECARDS[tc].reassign_from_id|escape}</strong> to <strong>{$VAL_TIMECARDS[tc].reassign_to_id|escape}</strong></dd>
{/if}
		</dl>
		{if $VAL_TIMECARDS[tc].description != "" && (!$PERM_MODIFY_TC || $VAL_EDITTCID != $VAL_TIMECARDS[tc].id)}<blockquote>{$VAL_TIMECARDS[tc].description|escape:"link"}</blockquote>{/if}
	</td>
</tr>
{/if}
{sectionelse}
{if !$PERM_ACTION}<tr><td>No Time Cards Found</td></tr>{/if}
{/section}
	{/strip}</tbody>
</table>
</div>