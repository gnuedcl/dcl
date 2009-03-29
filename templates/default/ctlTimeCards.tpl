<!-- $Id$ -->
<div class="dcl_detail">
<table width="100%" class="styled">
	<caption class="spacer">Time Cards</caption>
	{if $PERM_ACTION}<thead>
		<tr class="toolbar"><th colspan="4"><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=boTimecards.add&jcn={$VAL_JCN}&seq={$VAL_SEQ}">{$smarty.const.STR_CMMN_NEW}</a></li></ul></th></tr>
	</thead>{/if}
	<tbody>
{section name=tc loop=$VAL_TIMECARDS}
{if $PERM_MODIFY_TC && !$VAL_FORDELETE && $VAL_EDITTCID == $VAL_TIMECARDS[tc].id}
	<tr><td colspan="4">
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
		<tr class="group">
			<th colspan="3">{$VAL_TIMECARDS[tc].actionon} ({$VAL_TIMECARDS[tc].actionby|escape}) {$VAL_TIMECARDS[tc].summary|escape}</th>
			<td class="options">
			{if $VAL_FORDELETE && $VAL_EDITTCID == $VAL_TIMECARDS[tc].id}
				<form method="post" action="{$URL_MAIN_PHP}">
				<input type="hidden" name="menuAction" value="boTimecards.dbdelete">
				<input type="hidden" name="id" VALUE="{$VAL_TIMECARDS[tc].id}">
				<input type="submit" value="{$smarty.const.STR_CMMN_DELETE}">
				<input type="button" value="{$smarty.const.STR_CMMN_CANCEL}" onclick="location.href='{$URL_MAIN_PHP}?menuAction=boWorkorders.viewjcn&jcn={$VAL_JCN}&seq={$VAL_SEQ}';">
				</form>
			{else}
			{if $PERM_MODIFY_TC || $PERM_DELETE_TC}{strip}
				<ul>
				{if $PERM_MODIFY_TC}<li class="first"><a href="{$URL_MAIN_PHP}?menuAction=boTimecards.modify&id={$VAL_TIMECARDS[tc].id}">{$smarty.const.STR_CMMN_EDIT}</a></li>{/if}
				{if $PERM_DELETE_TC}<li{if !$PERM_MODIFY_TC} class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=boTimecards.delete&id={$VAL_TIMECARDS[tc].id}">{$smarty.const.STR_CMMN_DELETE}</a></li>{/if}
				</ul>
			{/strip}{/if}
			{/if}
			</td>
		</tr>
		<tr>
			<th>{$smarty.const.STR_TC_STATUS}:</th>
			<td class="highlight">{$VAL_TIMECARDS[tc].status|escape}</td>
			<th>{$smarty.const.STR_TC_HOURS}:</th>
			<td>{$VAL_TIMECARDS[tc].hours|escape}</td>
		</tr>
		<tr>
			<th>{$smarty.const.STR_TC_ACTION}:</th>
			<td>{$VAL_TIMECARDS[tc].action|escape}{if $VAL_TIMECARDS[tc].is_public != "Y"} (Private){/if}</td>
		</tr>
		{if !$IS_PUBLIC && ($VAL_TIMECARDS[tc].reassign_from_id || $VAL_TIMECARDS[tc].reassign_to_id)}
		<tr>
			<th>{$smarty.const.STR_CMMN_REASSIGN}:</th>
			<td>{$VAL_TIMECARDS[tc].reassign_from_id|escape}</td>
			<th>{$smarty.const.STR_CMMN_TO}:</th>
			<td>{$VAL_TIMECARDS[tc].reassign_to_id|escape}</td>
		</tr>
		{/if}
		{if $VAL_TIMECARDS[tc].description != "" && (!$PERM_MODIFY_TC || $VAL_EDITTCID != $VAL_TIMECARDS[tc].id)}
		<tr>
			<th>{$smarty.const.STR_TC_DESCRIPTION}:</th>
			<td colspan="3">{$VAL_TIMECARDS[tc].description|escape:"link"}</td>
		</tr>
		{/if}
{/if}
{sectionelse}
{if !$PERM_ACTION}<tr><td>No Time Cards Found</td></tr>{/if}
{/section}
	</tbody>
</table>
</div>