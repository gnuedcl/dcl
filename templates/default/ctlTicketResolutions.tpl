<div class="dcl_detail">
<table width="100%" class="styled">
	<caption class="spacer">Ticket Resolutions</caption>
	{if $PERM_ACTION}<thead>
		<tr class="toolbar"><th colspan="4"><ul><li class="first"><a href="{$URL_MAIN_PHP}?menuAction=boTicketresolutions.add&ticketid={$VAL_TICKETID}">{$smarty.const.STR_CMMN_NEW}</a></li></ul></th></tr>
	</thead>{/if}
	<tbody>
{section name=tr loop=$VAL_RESOLUTIONS}
{if $PERM_MODIFY_TR && !$IS_DELETE && $VAL_EDITRESID == $VAL_RESOLUTIONS[tr].resid}
	<tr><td colspan="4">
	<form class="styled" name="resolutionForm" id="resolutionForm" method="POST" action="{$URL_MAIN_PHP}">
		<input type="hidden" name="menuAction" value="htmlTicketresolutions.submitModify">
		<input type="hidden" name="actionby" value="{$VAL_RESOLUTIONS[tr].loggedby_id}">
		<input type="hidden" name="resid" value="{$VAL_EDITRESID}">
		<input type="hidden" name="ticketid" value="{$VAL_TICKETID}">
		<fieldset>
			<legend>Edit Ticket Resolution</legend>
			<div class="required">
				<label for="actionon">{$smarty.const.STR_CMMN_DATE}:</label>
				{dcl_calendar name=actionon value=$VAL_RESOLUTIONS[tr].loggedon|escape}
			</div>
			<div>
				<label>{$smarty.const.STR_TCK_LOGGEDBY}:</label>
				{$VAL_RESOLUTIONS[tr].loggedby|escape}
			</div>
			<div>
				<label for="is_public">{$smarty.const.STR_CMMN_PUBLIC}</label>
				<input type="checkbox" name="is_public" id="is_public"{if $VAL_RESOLUTIONS[tr].is_public == "Y"} checked{/if}>
			</div>
			<div class="required">
				<label for="status">{$smarty.const.STR_TCK_STATUS}:</label>
				{dcl_select_status active="N" setid=$VAL_SETID default=$VAL_RESOLUTIONS[tr].status_id}
			</div>
			<div>
				<label>{$smarty.const.STR_TCK_APPROXTIME}:</label>
				{$VAL_RESOLUTIONS[tr].seconds|escape}
			</div>
			<div class="required">
				<label for="resolution">{$smarty.const.STR_TCK_RESOLUTION}:</label>
				<textarea rows="4" id="resolution" name="resolution">{$VAL_RESOLUTIONS[tr].resolution|escape}</textarea>
			</div>
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
			<th colspan="3">({if $VAL_RESOLUTIONS[tr].is_public == "Y"}{$smarty.const.STR_CMMN_PUBLIC}{else}Private{/if}) {$VAL_RESOLUTIONS[tr].loggedby|escape}: {$VAL_RESOLUTIONS[tr].loggedon|escape}</th>
			<td class="options">
			{if $IS_DELETE && $VAL_EDITRESID == $VAL_RESOLUTIONS[tr].resid}
				<form method="post" action="{$URL_MAIN_PHP}">
					<input type="hidden" name="menuAction" value="htmlTicketresolutions.submitDelete">
					<input type="hidden" name="resid" VALUE="{$VAL_RESOLUTIONS[tr].resid}">
					<input type="submit" style="width:60px;" value="{$smarty.const.STR_CMMN_DELETE}">
					<input type="button" style="width:60px;" value="{$smarty.const.STR_CMMN_CANCEL}" onclick="history.back();">
				</form>
			{else}
			{if $PERM_MODIFY_TR || $PERM_DELETE_TR}{strip}
				<ul>
				{if $PERM_MODIFY_TR}<li class="first"><a href="{$URL_MAIN_PHP}?menuAction=htmlTicketresolutions.modify&id={$VAL_RESOLUTIONS[tr].resid}">{$smarty.const.STR_CMMN_EDIT}</a></li>{/if}
				{if $PERM_DELETE_TR}<li{if !$PERM_MODIFY_TR} class="first"{/if}><a href="{$URL_MAIN_PHP}?menuAction=htmlTicketresolutions.delete&resid={$VAL_RESOLUTIONS[tr].resid}">{$smarty.const.STR_CMMN_DELETE}</a></li>{/if}
				</ul>
			{/strip}{/if}
			{/if}
			</td>
		</tr>
		<tr>
			<th>{$smarty.const.STR_TCK_STATUS}:</th>
			<td>{$VAL_RESOLUTIONS[tr].status|escape}</td>
			<th>{$smarty.const.STR_TCK_APPROXTIME}:</th>
			<td>{$VAL_RESOLUTIONS[tr].seconds|escape}</td>
		</tr>
		<tr>
			<th>{$smarty.const.STR_TCK_RESOLUTION}:</th>
			<td colspan="3">{$VAL_RESOLUTIONS[tr].resolution|escape:"link"}</td>
		</tr>
{/if}
{/section}
	</tbody>
</table>
</div>