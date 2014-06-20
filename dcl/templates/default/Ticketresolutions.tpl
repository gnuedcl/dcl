<tr>
	<th class="formTitle">{$VAL_LOGGEDBY|escape} ({$VAL_LOGGEDON}): {$VAL_STATUS|escape}</th>
	<th class="formLinks" style="width: 5%; white-space: nowrap;">
{if $IS_DELETE}
		<form method="post" action="{$URL_MAIN_PHP}">
		<input type="hidden" name="menuAction" value="htmlTicketresolutions.submitDelete">
		<input type="hidden" name="id" VALUE="{$VAL_RESOLUTIONID}">
		<input type="submit" value="{$smarty.const.STR_CMMN_DELETE}">
		<input type="button" value="{$smarty.const.STR_CMMN_CANCEL}" onclick="history.back();">
		</form>
{else}
	{if $PERM_MODIFY}
		<a class="adark" href="{$URL_MAIN_PHP}?menuAction=htmlTicketresolutions.modify&id={$VAL_RESOLUTIONID}">{$smarty.const.STR_CMMN_EDIT}</a>
		{if $PERM_DELETE}&nbsp;|&nbsp;{/if}
	{/if}
	{if $PERM_DELETE}
		<a class="adark" href="{$URL_MAIN_PHP}?menuAction=htmlTicketresolutions.delete&id={$VAL_RESOLUTIONID}">{$smarty.const.STR_CMMN_DELETE}</a>
	{/if}
{/if}
	</th>
</tr>
<tr><td colspan="2" class="formContainer">
	<table width="100%" border="0" cellpadding="2" cellspacing="2">
	<tr>
		<td class="detailBox"><b>{$smarty.const.STR_CMMN_PUBLIC}:</b> {$VAL_PUBLIC}</td>
		<td class="detailBox"><b>{$smarty.const.STR_TCK_APPROXTIME}:</b> {$VAL_HOURSTEXT}</td>
	</tr>
	<tr><td colspan="2"><b>{$smarty.const.STR_TCK_RESOLUTION}:</b> {$VAL_RESOLUTION|escape|dcl_link}</td></tr>
	</table>
</td></tr>