<tr>
	<td>
		<dl>
			<dt><strong>{dcl_personnel_link text=$VAL_ACTIONBY id=`$TimeCard->actionby`}</strong> {$VAL_SUMMARY|escape}</dt>
			<dd><strong>{$VAL_ACTION|escape}</strong> @ <strong>{$VAL_ACTIONON}</strong> for <strong>{$VAL_HOURS}</strong> Hours
{if $IS_DELETE}
		<form method="post" action="{$URL_MAIN_PHP}">
		<input type="hidden" name="menuAction" value="boTimecards.dbdelete">
		<input type="hidden" name="id" VALUE="{$VAL_TIMECARDID}">
		<input type="submit" value="{$smarty.const.STR_CMMN_DELETE}">
		<input type="button" value="{$smarty.const.STR_CMMN_CANCEL}" onclick="history.back();">
		</form>
{else}
	{if $PERM_MODIFY}
		<a href="{$URL_MAIN_PHP}?menuAction=boTimecards.modify&id={$VAL_TIMECARDID}">{$smarty.const.STR_CMMN_EDIT}</a>
		{if $PERM_DELETE}&nbsp;|&nbsp;{/if}
	{/if}
	{if $PERM_DELETE}
		<a href="{$URL_MAIN_PHP}?menuAction=boTimecards.delete&id={$VAL_TIMECARDID}">{$smarty.const.STR_CMMN_DELETE}</a>
	{/if}
{/if}
			</dd>
{if $VAL_REASSIGNFROM || $VAL_REASSIGNTO}
		<dd>Reassign <strong>{$VAL_REASSIGNFROM|escape}</strong> to <strong>{$VAL_REASSIGNTO|escape}</strong></dd>
{/if}
		</dl>
	</td>
	{if $VAL_DESCRIPTION != ""}<td>{$VAL_DESCRIPTION|escape:"link"}</td>{/if}
</tr>
